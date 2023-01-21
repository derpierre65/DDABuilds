import axios from 'axios';
import {hideAjaxLoader, showAjaxLoader} from './index';

export default {
	namespaced: true,
	state: {
		user: window.APP.user || {
			id: 0,
			name: '',
			avatar_hash: '',
			is_maintainer: false,
			unread_notifications: 0,
		},
	},
	getters: {
		isLoggedIn(state) {
			return !!state.user.id;
		},
	},
	mutations: {
		ADD_UNREAD_NOTIFICATIONS(state, payload) {
			state.user.unread_notifications += payload;
		},
		SET_USER(state, payload) {
			for (const key in payload) {
				if (Object.prototype.hasOwnProperty.call(payload, key)) {
					state.user[key] = payload[key];
				}
			}
		},
	},
	actions: {
		logout({ commit }) {
			showAjaxLoader();

			let request = axios
				.delete('/auth/')
				.then(() => {
					commit('SET_USER', {
						id: 0,
					});
				});

			request.finally(() => {
				hideAjaxLoader();
			});

			return request;
		},
	},
};