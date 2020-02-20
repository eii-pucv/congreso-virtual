import Vue from 'vue'
import Vuex from 'vuex'
import axios from "./backend/axios";
import { i18n } from '@/plugins/i18n'
import { dark_mode } from "./data/dark_mode"

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    status: '',
    access_token: localStorage.getItem('access_token') || '',
    user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : { rol: ""},
    language: localStorage.getItem("language"),
    modo_oscuro: localStorage.getItem("modo_oscuro")
  },
  mutations: {
    auth_request(state){
      state.status = 'loading'
    },
    auth_success(state, DATA){
      this.status = 'success';
      state.access_token = DATA.access_token;
      console.log(JSON.stringify(DATA.user_data));
      state.user = DATA.user_data;
      console.log(JSON.stringify(state.user));
    },
    auth_error(state){
      state.status = 'error'
    },
    logout(state){
      state.status = '';
      state.access_token = '';
    },
    setLanguage() {
      localStorage.setItem("language",i18n.locale)
    },
    setDarkMode() {
      localStorage.setItem("modo_oscuro",dark_mode.estado)
    }
  },
  actions: {
    login({commit}, user){
      return new Promise((resolve, reject) => {
        commit('auth_request')
        axios.post('/auth/login', user).then(resp => {
          const access_token = resp.data.access_token;
          const user = resp.data.user;
          localStorage.setItem("access_token", access_token);
          localStorage.setItem("user", JSON.stringify(user));
          axios.defaults.headers.common['Authorization'] = access_token;
          commit('auth_success', access_token, user);
          resolve(resp)
        })
            .catch(err => {
              commit('auth_error')
              localStorage.removeItem('access_token')
              reject(err)
            })
      })
    },
    logout({commit}){
      return new Promise((resolve, reject) => {
        commit('logout')
        localStorage.removeItem('access_token');
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        resolve()
      })
    },
  },
  getters : {
    isLoggedIn: state => !!state.access_token,
    authStatus: state => state.status,
    userData: state => state.user,
    language: state => state.language,
    modo_oscuro: state => state.modo_oscuro
  }
})
