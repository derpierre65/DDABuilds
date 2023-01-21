<template>
	<div class="container">
		<table v-if="bugReports.length" :class="{'table-dark': $store.state.darkMode}" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th v-if="showActionColumn" class="columnStatus">{{$t('bug_report.list.action')}}</th>
					<th class="columnDate">{{$t('bug_report.list.created')}}</th>
					<th class="columnText">{{$t('bug_report.list.title')}}</th>
					<th style="width:15%;">{{$t('bug_report.list.status')}}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="report in bugReports" :key="report.ID">
					<td v-if="showActionColumn" class="columnStatus">
						<button v-if="report.status !== 2" class="btn btn-primary" @click="closeBugReport(report)">
							<i class="fa fa-lock" />
						</button>
					</td>
					<td class="columnDate">{{formatDate(report.time)}}</td>
					<td class="columnText">
						<router-link :to="report.link">{{report.title}}</router-link>
					</td>
					<td>{{$t('bug_report.status.' + (report.status === 2 ? 'closed' : 'open'))}}</td>
				</tr>
			</tbody>
		</table>
		<div v-else class="alert alert-info">
			{{$t('bug_report.list.noEntries')}}
		</div>

		<app-pagination :current-page="page" :pages="pages" />
	</div>
</template>

<script>
import axios from 'axios';
import AppPagination from '../../components/AppPagination';
import {hidePageLoader, showPageLoader} from '../../store';
import formatDate from '../../utils/date';
import {closeBugReport} from '../../utils/bug-report';
import {formatSEOTitle} from '../../utils/string';

export default {
	name: 'BugReportListView',
	components: { AppPagination },
	props: {
		mineList: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			pages: 0,
			page: 0,
			bugReports: [],
		};
	},
	computed: {
		showActionColumn() {
			return !this.mineList;
		},
	},
	watch: {
		'$route.params.page'() {
			this.fetchList();
		},
	},
	created() {
		this.fetchList();
	},
	methods: {
		formatDate,
		closeBugReport,
		fetchList() {
			showPageLoader();

			const params = {
				page: this.$route.params.page || 0,
			};
			if (this.mineList) {
				params.mine = 1;
			}

			axios
				.get('/bug-reports/', {
					params,
				})
				.then(({ data: { data, pagination } }) => {
					for (let bugReport of data) {
						bugReport.link = {
							name: 'bug-report',
							params: {
								id: bugReport.ID,
								title: formatSEOTitle(bugReport.title),
							},
						};
					}

					this.bugReports = data;
					this.pages = pagination.last_page;
					this.page = pagination.current_page;
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
	},
};
</script>