<template>
  <q-layout view="lHh Lpr lFf">
    <q-header bordered>
      <q-toolbar>
        <q-toolbar-title>
          DD:A Builder v3.0.0
        </q-toolbar-title>

        <div v-if="authStore.user">
          {{ authStore.user.name }}

          <q-btn @click="logout" />
        </div>
        <div v-else>
          <a
            :href="loginUrl"
            rel="noopener noreferrer"
            @click.prevent="startLogin"
          >
            <img alt="Login" src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png">
          </a>
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import useAuthStore from 'src/stores/auth';
import axios from 'axios';
import { Loading, Notify, useInterval } from 'quasar';
import { noop } from 'src/lib/core';

const authStore = useAuthStore();
const closeListener = useInterval();

const loginUrl = computed(() => 'https://steamcommunity.com/openid/login?' + new URLSearchParams({
  'openid.return_to': (import.meta.env.VITE_API_URL || window.location.origin) + '/api/auth/steam/',
  'openid.realm': import.meta.env.VITE_API_URL || window.location.origin,
  'openid.mode': 'checkid_setup',
  'openid.ns': 'http://specs.openid.net/auth/2.0',
  'openid.identity': 'http://specs.openid.net/auth/2.0/identifier_select',
  'openid.claimed_id': 'http://specs.openid.net/auth/2.0/identifier_select',
}));

function startLogin() {
  const newWindow = window.open(loginUrl.value, 'ddaBuildsSteamLogin', 'height=500,width=600');
  // for popup blocker user, redirect directly to steam
  if (!newWindow || typeof newWindow.closed === 'undefined' || newWindow.closed) {
    window.location.href = loginUrl.value;
    return;
  }

  closeListener.registerInterval(() => {
    if (newWindow.closed) {
      Loading.hide('login');
      closeListener.removeInterval();
    }
  }, 500);
  Loading.show({
    group: 'login',
  });
  window.addEventListener('message', loginListener);
}

function loginListener(event: MessageEvent) {
  if (event.origin !== window.location.origin) {
    return;
  }

  if (event.data.type === 'LOGIN_FAILED') {
    Notify.create({
      color: 'negative',
      message: 'Something went wrong.', // TODO i18n
    });
    Loading.hide('login');
    return;
  }

  if (event.data.type !== 'LOGIN_SUCCESS') {
    return;
  }

  window.removeEventListener('message', loginListener);
  Loading.hide('login');
  authStore.login(event.data.user);
}

function logout() {
  Loading.show();
  axios
    .delete('/auth/')
    .then(() => {
      authStore.logout();
    })
    .catch(noop)
    .finally(() => Loading.hide());
}
</script>
