import { defineBoot } from '#q-app/wrappers';
import { Dark } from 'quasar';

export default defineBoot(async() => {
  Dark.set(localStorage?.getItem('darkMode') !== '0');
});
