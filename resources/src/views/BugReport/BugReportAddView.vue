<template>
	<div class="container">
		<div v-if="needWait > 0" class="alert alert-danger" v-html="$t('bug_report.waiting', {count: needWait})" />
		<div class="alert alert-danger" v-html="$t('bug_report.notRelated')" />
		<div class="alert alert-info" v-html="$t('bug_report.info')" />

		<form @submit.prevent="submit">
			<div class="form-group">
				<label>{{$t('bug_report.list.title')}}</label>
				<input v-model.trim="form.title" v-acceptance-selector:input="'title'" :class="{'is-valid': form.title.length >= 3}" class="form-control" required type="text">
				<small class="form-text text-muted">{{$t('bug_report.list.title_requirements')}}</small>
			</div>

			<div v-acceptance-selector:input="'description'" class="form-group">
				<label>{{$t('bug_report.description')}}</label>
				<classic-ckeditor v-model="form.description" />
			</div>

			<label>
				<input v-model="checkbox" v-acceptance-selector:input="'checkbox'" type="checkbox"> <span v-html="$t('bug_report.agreement')" />
			</label>

			<div class="text-center marginTop">
				<input v-acceptance-selector:input="'save'" :disabled="!checkbox || form.title.length < 3 || needWait > 0" :value="$t('words.save')" class="btn btn-primary" type="submit">
			</div>
		</form>
	</div>
</template>

<script>
import axios from 'axios';
import ClassicCkeditor from '../../components/ClassicCkeditor';
import {formatSEOTitle} from '../../utils/string';

export default {
	name: 'BugReportAddView',
	components: { ClassicCkeditor },
	data() {
		return {
			checkbox: false,
			interval: null,
			needWait: 0,
			form: {
				title: '',
				description: '',
			},
		};
	},
	beforeDestroy() {
		this.destroyInterval();
	},
	methods: {
		destroyInterval() {
			if (this.interval) {
				window.clearInterval(this.interval);
				this.interval = null;
			}
		},
		submit() {
			if (!this.checkbox || this.form.title.length < 3 || this.needWait > 0) {
				return;
			}

			axios
				.post('/bug-reports/', this.form)
				.then(({ data }) => {
					if (data.need_wait) {
						this.needWait = data.need_wait;
						this.destroyInterval();
						this.interval = window.setInterval(() => {
							this.needWait--;
							if (this.needWait <= 0) {
								this.destroyInterval();
							}
						}, 1_000);
					}
					else {
						this.$router.push({
							name: 'bug-report',
							params: {
								id: data.id,
								title: formatSEOTitle(data.title),
							},
						});
					}
				})
				.catch(() => {
					this.$notify({
						type: 'error',
						text: this.$t('error.default'),
					});
				});
		},
	},
};
</script>