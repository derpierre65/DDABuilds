<template>
	<div v-acceptance-selector:page="'report-view'" class="container">
		<div v-if="isMaintainer && bugReport.status !== 2" v-acceptance-selector:action-menu class="text-right">
			<button class="btn btn-primary" @click="close">
				Close
			</button>
		</div>
		<table :class="{'table-dark': $store.state.darkMode}" class="table table-bordered marginTop">
			<tbody>
				<tr>
					<td>{{$t('bug_report.list.status')}}</td>
					<td v-acceptance-selector:field="'status'">{{$t('bug_report.status.' + (bugReport.status === 2 ? 'closed' : 'open'))}}</td>
				</tr>
				<tr>
					<td>{{$t('bug_report.list.created')}}</td>
					<td>{{formatDate(bugReport.created_at)}}</td>
				</tr>
				<tr>
					<td>{{$t('bug_report.createdBy')}}</td>
					<td>{{bugReport.user_name}}</td>
				</tr>
				<tr>
					<td style="width:10%;">{{$t('bug_report.list.title')}}</td>
					<td v-acceptance-selector:field="'title'">{{bugReport.title}}</td>
				</tr>
				<tr>
					<td>{{$t('bug_report.description')}}</td>
					<td v-acceptance-selector:field="'description'" class="user-content" v-html="bugReport.description" />
				</tr>
			</tbody>
		</table>

		<div v-if="needWait > 0" class="alert alert-danger">
			Please wait {{needWait}} seconds for the next comment.
		</div>
		<form v-else-if="bugReport.status !== 2" @submit.prevent="addComment">
			<div class="card">
				<div class="card-header text-center">
					{{$t('comment.write')}}
				</div>
				<div class="card-body">
					<classic-ckeditor v-model="form.description" />

					<div class="text-center marginTop">
						<input :disabled="needWait > 0 || form.description.length < 3" :value="$t('comment.send')" class="btn btn-primary" type="submit">
					</div>
				</div>
			</div>
		</form>

		<template v-if="comments.length">
			<div v-for="comment of comments" :key="comment.id" class="card">
				<h5 class="card-header">
					{{comment.user_name}} ({{formatDate(comment.created_at)}})
				</h5>
				<div class="card-body">
					<div class="card-text user-content" v-html="comment.description" />
				</div>
			</div>

			<app-pagination route-name="bug-report" :current-page="page" :pages="pages" :route-params="$route.params" />
		</template>
	</div>
</template>

<script>
import axios from 'axios';
import {mapState} from 'vuex';
import AppPagination from '../../components/AppPagination';
import ClassicCkeditor from '../../components/ClassicCkeditor';
import {hidePageLoader, showPageLoader} from '../../store';
import formatDate from '../../utils/date';
import {closeBugReport} from '../../utils/bug-report';
import {formatSEOTitle} from '../../utils/string';

export default {
	name: 'BugReportView',
	components: { ClassicCkeditor, AppPagination },
	data() {
		return {
			baseUrl: '/bug-reports/' + this.$route.params.id,
			bugReport: {},
			comments: [],
			pages: 0,
			page: 0,
			// comment
			interval: null,
			needWait: 0,
			form: {
				description: '',
			},
		};
	},
	computed: {
		...mapState({
			isMaintainer: (state) => state.authentication.user.isMaintainer,
		}),
	},
	watch: {
		'$route.params.id'() {
			this.fetch();
		},
		'$route.params.page'() {
			this.fetchComments();
		},
	},
	created() {
		this.fetch();
	},
	beforeDestroy() {
		this.destroyInterval();
	},
	methods: {
		formatDate,
		destroyInterval() {
			if (this.interval) {
				window.clearInterval(this.interval);
				this.interval = null;
			}
		},
		addComment() {
			if (this.form.description.length < 3 || this.needWait > 0) {
				return;
			}

			axios.post(this.baseUrl + '/comments', this.form).then(({ data }) => {
				this.form.description = '';

				if (data.need_wait) {
					this.needWait = data.need_wait;
					this.interval = window.setInterval(() => {
						this.needWait--;
						if (this.needWait <= 0) {
							this.destroyInterval();
						}
					}, 1_000);
				}

				if (data.comment) {
					this.comments.unshift(data.comment);
				}
			});
		},
		fetchComments() {
			this.page = this.$route.params.page || 0;
			this.pages = 0;

			return axios.get(this.baseUrl + '/comments?page=' + this.page).then(({ data: { data: comments, pagination} }) => {
				this.comments = comments;
				this.page = pagination.current_page;
				this.pages = pagination.last_page;
			});
		},
		fetch() {
			if (!this.$route.params.id) {
				return this.$router.push({ name: 'home' });
			}

			showPageLoader();

			axios
				.get(this.baseUrl)
				.then(({ data }) => {
					this.bugReport = data;

					return this.fetchComments();
				})
				.then(() => {
					let title = formatSEOTitle(this.bugReport.title);

					// redirect to correct title url, if title not equal to the url title
					if (this.$route.params.title !== title) {
						this.$router.push({
							name: this.$route.name,
							params: Object.assign({}, this.$route.params, { title }),
						});
					}
				})
				.catch(() => {
					this.$notify({
						type: 'error',
						text: this.$t('error.default'),
					});
					this.$router.push({ name: 'home' });
				})
				.finally(hidePageLoader);
		},
		close() {
			closeBugReport(this.bugReport);
		},
	},
};
</script>