import Vue from 'vue';
import i18n from '../i18n';
import store from '../store';
import AuthView from '../views/AuthView';
import NotificationListView from '../views/NotificationListView';

const NotFound = () => import('../views/NotFound');
const IndexView = () => import('../views/IndexView');
const BuildListView = () => import('../views/Build/BuildListView');
const ChangelogView = () => import('../views/ChangelogView');
const BugReportListView = () => import('../views/BugReport/BugReportListView');
const BugReportView = () => import('../views/BugReport/BugReportView');
const BugReportAddView = () => import('../views/BugReport/BugReportAddView');
const BuildAddSelectView = () => import('../views/Build/BuildAddSelectView');
const BuildAddView = () => import('../views/Build/BuildAddView');

const routes = [
	{
		name: 'home',
		path: '/',
		component: IndexView,
		meta: {
			ignoreSection: true,
		},
	},
	{
		name: 'buildList',
		path: '/builds/:page?',
		component: BuildListView,
	},
	{
		name: 'changelog',
		path: '/changelog',
		component: ChangelogView,
	},
	// builds
	{
		name: 'buildAddSelect',
		path: '/build-add-select',
		component: BuildAddSelectView,
		meta: {
			requiredAuth: true,
		},
	},
	{
		name: 'buildAdd',
		path: '/build-add/:mapID-:name',
		component: BuildAddView,
		meta: {
			requiredAuth: true,
		},
	},
	{
		name: 'build',
		path: '/build/:id-:title',
		component: BuildAddView,
		props: {
			isView: true,
		},
	},
	// bug-reports
	{
		name: 'bug-reports',
		path: '/bug-reports/:page?',
		component: BugReportListView,
		meta: {
			requiredAuth: true,
		},
	},
	{
		name: 'bug-report-add',
		path: '/bug-report-add',
		component: BugReportAddView,
		meta: {
			requiredAuth: true,
		},
	},
	{
		name: 'bug-report',
		path: '/bug-report/:id-:title/:page?',
		component: BugReportView,
		meta: {
			requiredAuth: true,
		},
	},
	// user related pages
	{
		name: 'my-bug-reports',
		path: '/my-bug-reports/:page?',
		component: BugReportListView,
		meta: {
			requiredAuth: true,
		},
		props: {
			mineList: true,
		},
	},
	{
		name: 'myBuildList',
		path: '/my-builds/:page?',
		component: BuildListView,
		meta: {
			requiredAuth: true,
		},
		props: {
			fetchParams: { mine: 1 },
			hideFilter: true,
		},
	},
	{
		name: 'likedBuildList',
		path: '/liked-builds/:page?',
		component: BuildListView,
		meta: {
			requiredAuth: true,
		},
		props: {
			fetchParams: { liked: 1 },
			hideFilter: true,
		},
	},
	{
		name: 'favoriteBuildList',
		path: '/favorite-builds/:page?',
		component: BuildListView,
		meta: {
			requiredAuth: true,
		},
		props: {
			fetchParams: { watch: 1 },
			hideFilter: true,
		},
	},
	{
		name: 'notificationList',
		path: '/notifications/:page?',
		component: NotificationListView,
		meta: {
			requiredAuth: true,
		},
	},
	// auth
	{
		name: 'logout',
		path: '/logout',
		beforeEnter: (to, from, next) => {
			store
				.dispatch('authentication/logout')
				.then(() => {
					if (from.meta.requiredAuth) {
						return next({ name: 'home' });
					}

					next(from);
				})
				.catch(() => {
					Vue.notify({
						type: 'error',
						text: i18n.t('error.default'),
					});

					next(false);
				});
		},
	},
	{
		name: 'auth',
		path: '/auth',
		component: AuthView,
	},
	// not found
	{
		name: 'notFound',
		path: '*',
		component: NotFound,
	},
];

export default routes;