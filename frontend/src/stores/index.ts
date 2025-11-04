import {defineStore} from "#q-app/wrappers";
import { createPinia } from 'pinia';

export default defineStore((/* { ssrContext } */) => {
    const pinia = createPinia();

    // add pinia plugins here

    return pinia;
});