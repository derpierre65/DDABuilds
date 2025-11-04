import { defineBoot } from '#q-app/wrappers';
import axios, { AxiosError } from 'axios';
import useAuthStore from 'src/stores/auth';

axios.defaults.baseURL = `${import.meta.env.VITE_API_URL || ''}/api/`;
axios.defaults.withCredentials = true;

export default defineBoot(async({ urlPath, redirect, }) => {
  // check for opener
  if (window.opener) {
    try {
      const { data, } = await axios.get('/auth');
      window.opener.postMessage({
        type: 'LOGIN_SUCCESS',
        user: data,
      }, window.location.origin);
    }
    catch(error) {
      window.opener.postMessage({
        type: 'LOGIN_FAILED',
        error,
      }, window.location.origin);
    }

    window.close();
    return;
  }

  if (urlPath === '/auth') {
    try {
      const { data, } = await axios.get('/auth');
      useAuthStore().login(data);
    }
    catch {
      // login failed, just redirect to home
    }

    return redirect('/');
  }


  if (window.localStorage.getItem('ddabuilder_loggedIn')) {
    try {
      const { data, } = await axios.get('/auth');
      useAuthStore().user = data;
    }
    catch(error) {
      if (error instanceof AxiosError && [
        401,
        403,
        404,
      ].includes(error.status)) {
        window.localStorage.removeItem('ddabuilder_loggedIn');
      }
    }
  }
});
