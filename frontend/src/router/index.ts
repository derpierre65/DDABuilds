import { defineRouter } from '#q-app/wrappers';
import {
  createMemoryHistory,
  createRouter,
  createWebHashHistory,
  createWebHistory,
} from 'vue-router';
import routes from './routes';

export default defineRouter(function (/* { store, ssrContext } */) {
  return createRouter({
      scrollBehavior: () => ({ left: 0, top: 0 }),
      routes,
      history: createWebHistory(process.env.VUE_ROUTER_BASE),
  });
});
