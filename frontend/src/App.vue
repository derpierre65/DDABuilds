<template>
  <router-view />
</template>

<script setup lang="ts">
import { useQuasar } from 'quasar';

useQuasar().dark.set(true);

if (import.meta.env.MODE === 'production') {
  gtag('js', new Date());
  gtag('config', 'UA-39334248-36');
}
</script>
