import axios from 'axios';
import {NavbarPlugin, TooltipPlugin} from 'bootstrap-vue';
import Vue from 'vue';
import InfiniteLoading from 'vue-infinite-loading';
import Notifications from 'vue-notification';
import App from './App.vue';
import acceptanceTest from './directives/acceptanceTest';
import i18n, {initI18n} from './i18n';
import router from './router';
import store from './store';

Vue.config.productionTip = false;

require('webpack-jquery-ui/draggable');
require('webpack-jquery-ui/droppable');

axios.defaults.baseURL = '/api/';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.use(NavbarPlugin);
Vue.use(TooltipPlugin);
Vue.use(Notifications);
Vue.use(InfiniteLoading, {
	slots: {
		noResults: {
			render(h) {
				return h('div', { attrs: { class: 'alert alert-info' } }, this.$t(this.$parent.$attrs.type + '.noResults'));
			},
		},
		noMore: {
			render(h) {
				return h('div', { attrs: { class: 'alert alert-info' } }, this.$t(this.$parent.$attrs.type + '.noMore'));
			},
		},
		error: {
			render(h) {
				return h('div', { attrs: { class: 'alert alert-danger' } }, this.$t('infiniteLoading.error'));
			},
		},
	},
});

// directives
Vue.use(acceptanceTest);

initI18n(() => {
	new Vue({
		el: '#app',
		i18n,
		router,
		store,
		render: h => h(App),
	});
});