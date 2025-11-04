import { defineBoot } from '#q-app/wrappers';
import I18nextVue from 'i18next-vue';
import i18next from 'i18next';
import I18nextLanguageDetector from 'i18next-browser-languagedetector';
import I18nextHttpBackend from 'i18next-http-backend';

const supportedLanguages = [
  'en',
  'de',
];

export default defineBoot(({ app, }) => {
  app.use(I18nextVue, {
    i18next,
  });

  i18next.use(I18nextLanguageDetector);
  i18next.use(I18nextHttpBackend);

  return i18next
    .init({
      supportedLngs: supportedLanguages,
      load: 'languageOnly',
      // fallbackLng: 'en',
      returnObjects: true,
      saveMissing: true,
      missingKeyHandler(lngs, ns, key) {
        console.error('[I18next] Missing key:', key);
      },
      backend: {
        loadPath: `/assets/locales/{{lng}}.json?t=${import.meta.env.VITE_BUILD_TIMESTAMP}`,
        addPath: `/assets/locales/{{lng}}.json?t=${import.meta.env.VITE_BUILD_TIMESTAMP}`,
      },
      detection: {
        lookupLocalStorage: 'language',
        lookupSessionStorage: 'language',
        caches: [ 'localStorage', ],
      },
    })
    .then(() => void 0)
    .catch((error) => {
      console.error('i18next initialization error', error);
    });
});
