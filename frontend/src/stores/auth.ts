import { defineStore } from 'pinia';
import { ref } from 'vue';

type User = {
  id: string;
  name: string;
  avatar_hash: string;
  unread_notifications: number;
  is_maintainer: boolean;
};

const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);

  function login(newUser: User) {
    user.value = newUser;
    localStorage.setItem('ddabuilder_loggedIn', '1');
  }

  function logout() {
    user.value = null;
    localStorage.removeItem('ddabuilder_loggedIn');
  }

  return {
    user,
    login,
    logout,
  };
});

export default useAuthStore;
