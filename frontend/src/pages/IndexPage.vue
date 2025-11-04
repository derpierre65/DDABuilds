<template>
  <q-page>
    <div class="index-header-image" />

    <div class="q-mx-auto tw:max-w-6xl q-gutter-y-md q-mt-md">
      <h4>{{ $t('home.title') }}</h4>

      <h6 class="text-justify">
        {{ $t('home.line1') }}
      </h6>

      <p class="text-justify">
        <i18next :translation="$t('home.line2')">
          <template #link>
            <a
              href="https://dundefplanner.com/"
              rel="noopener noreferrer"
              target="_blank"
              class="link"
            >dundefplanner.com</a>
          </template>
        </i18next>
      </p>
    </div>
  </q-page>
</template>

<style lang="scss" scoped>
.index-header-image {
  background-image: url('/assets/images/index/1.jpg');
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  text-align: center;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  background-attachment: scroll;
  height:300px;
}
</style>
