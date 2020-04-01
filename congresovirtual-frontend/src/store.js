import Vue from 'vue';
import Vuex from 'vuex';
import axios from './backend/axios';
import { i18n } from '@/plugins/i18n';
import { dark_mode } from './data/dark_mode';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        status: '',
        access_token: localStorage.getItem('access_token') || '',
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : { rol: '' },
        language: localStorage.getItem('language'),
        modo_oscuro: localStorage.getItem('modo_oscuro')
    },
    mutations: {
        auth_request(state) {
            state.status = 'loading';
        },
        auth_success(state, data) {
            state.status = 'success';
            state.access_token = data.access_token;
            state.user = data.user_data;
        },
        auth_error(state) {
            state.status = 'error';
        },
        logout(state) {
            state.status = '';
            state.access_token = '';
            state.user = { rol: '' };
        },
        setLanguage() {
            localStorage.setItem('language', i18n.locale);
        },
        setDarkMode() {
            localStorage.setItem('modo_oscuro', dark_mode.estado);
        }
    },
    actions: {
        login({commit}, userCredentials) {
            return new Promise((resolve, reject) => {
                commit('auth_request');
                axios
                    .post('/auth/login', userCredentials)
                    .then(res => {
                        const accessToken = res.data.access_token;
                        const userData = res.data.user;
                        localStorage.setItem('access_token', accessToken);
                        localStorage.setItem('user', JSON.stringify(userData));
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken;
                        commit('auth_success', { access_token: accessToken, user_data: userData });
                        resolve(res);
                    })
                    .catch(error => {
                        commit('auth_error');
                        localStorage.removeItem('access_token');
                        reject(error);
                    });
            });
        },
        loginRrss({commit}, accessToken) {
            return new Promise((resolve, reject) => {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken;
                commit('auth_request');
                axios
                    .get('/auth/user')
                    .then(res => {
                        const userData = res.data;
                        localStorage.setItem('access_token', accessToken);
                        localStorage.setItem('user', JSON.stringify(userData));
                        commit('auth_success', { access_token: accessToken, user_data: userData });
                        resolve(res);
                    })
                    .catch(error => {
                        delete axios.defaults.headers.common['Authorization'];
                        commit('auth_error');
                        reject(error);
                    });
            });
        },
        logout({commit}) {
            return new Promise((resolve, reject) => {
                commit('logout');
                axios
                    .get('/auth/logout')
                    .then(() => {
                        localStorage.removeItem('access_token');
                        localStorage.removeItem('user');
                        delete axios.defaults.headers.common['Authorization'];
                        resolve();
                    })
                    .catch(error => {
                        localStorage.removeItem('access_token');
                        localStorage.removeItem('user');
                        delete axios.defaults.headers.common['Authorization'];
                        commit('auth_error');
                        reject(error);
                    });
            });
        }
    },
    getters : {
        isLoggedIn: state => state.access_token && state.user,
        authStatus: state => state.status,
        userData: state => state.user,
        language: state => state.language,
        modo_oscuro: state => state.modo_oscuro
    }
});
