<template>
  <router-view />
</template>

<script setup lang="ts">

if (import.meta.env.MODE === 'production') {
  gtag('js', new Date());
  gtag('config', 'UA-39334248-36');
}
</script>
