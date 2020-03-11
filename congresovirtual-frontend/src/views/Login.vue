<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('login.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('login.breadcumb.login') }}</li>
            </ol>
        </nav>
        <div class="container">
            <h1 class="display-4 text-center mb-10">{{ $t('login.contenido.titulo') }}</h1>
            <p class="text-center mb-30">
                {{ $t('login.contenido.subtitulo') }}
            </p>
            <div class="alert alert-danger" v-if="error">{{ error }}</div>
            <div class="row">
                <div class="col-sm-6 col-12">
                    <form @submit.prevent="login">
                        <div class="form-group">
                            <input
                                    v-model="userCredentials.email"
                                    type="email"
                                    class="form-control"
                                    :placeholder="$t('login.contenido.placeholders.correo')"
                                    required
                                    autofocus
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input
                                        v-model="userCredentials.password"
                                        class="form-control"
                                        :placeholder="$t('login.contenido.placeholders.contrasena')"
                                        required
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                        :type="passwordFieldType"
                                />
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" @click="switchPasswordVisibility" style="width: 55px;">
                                        <font-awesome-icon v-if="passwordFieldType === 'password'" icon="eye-slash" size="lg"></font-awesome-icon>
                                        <font-awesome-icon v-else icon="eye" size="lg"></font-awesome-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox mb-25">
                            <input
                                    id="remember_me"
                                    v-model="userCredentials.remember_me"
                                    class="custom-control-input"
                                    type="checkbox"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                            <label for="remember_me" class="custom-control-label font-14" :class="mode==='dark'?'text-white':'text-primary '">{{ $t('login.contenido.mantener') }}</label>
                        </div>
                        <div v-if="loadBtnLogin" class="btn btn-primary d-block vld-parent pa-20">
                            <loading
                                    :active.sync="loadBtnLogin"
                                    :is-full-page="fullPage"
                                    :height="32"
                                    :color="color"
                            >
                            </loading>
                        </div>
                        <button v-if="!loadBtnLogin" class="btn btn-primary btn-block" type="submit">
                            {{ $t('login.contenido.iniciar') }}
                        </button>
                        <p class="text-center mt-15" :style="mode==='dark'?'text-white':'text-primary '">
                            {{ $t('login.contenido.olvidar') }}
                            <a href="/request" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                <u>{{ $t('login.contenido.recuperar') }}</u>
                            </a>
                        </p>
                        <p class="text-center" :style="mode==='dark'?'text-white':'text-primary'">
                            {{ $t('login.contenido.sin_cuenta') }}
                            <a href="/signup" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                <u>{{ $t('login.contenido.registrar') }}</u>
                            </a>
                        </p>
                    </form>
                </div>
                <div class="col-sm-6 col-12">
                    <a class="btn btn-indigo btn-block btn-wth-icon" :href="API_URL + '/api/auth/facebook'">
                        <span class="icon-label"><i class="fa fa-facebook"></i></span>
                        <span class="btn-text">Facebook</span>
                    </a>
                    <a class="btn btn-danger btn-block btn-wth-icon mt-15" :href="API_URL + '/api/auth/google'">
                        <span class="icon-label"><i class="fa fa-google"></i></span>
                        <span class="btn-text">Google</span>
                    </a>
                    <a class="btn btn-blue btn-block btn-wth-icon mt-15" :href="API_URL + '/api/auth/twitter'">
                        <span class="icon-label"><i class="fa fa-twitter"></i></span>
                        <span class="btn-text">Twitter</span>
                    </a>
                    <a class="btn btn-grey btn-block btn-wth-icon mt-15" :href="API_URL + '/api/auth/github'">
                        <span class="icon-label"><i class="fa fa-github"></i></span>
                        <span class="btn-text">GitHub</span>
                    </a>
                    <a class="btn btn-green btn-block btn-wth-icon my-15" :href="API_URL + '/api/auth/clave_unica'">
                        <span class="icon-label"><i class="fa fa-key"></i></span>
                        <span class="btn-text">{{ $t('login.contenido.clave') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { API_URL } from '../backend/data_server';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Login',
        components: {
            Loading
        },
        data() {
            return {
                userCredentials: {
                    email: null,
                    password: null,
                    remember_me: false
                },
                error: false,
                passwordFieldType: 'password',
                loadBtnLogin: false,
                fullPage: false,
                color: '#ffffff',
                mode: String,
                API_URL
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }
        },
        methods: {
            login() {
                this.loadBtnLogin = true;
                this.$store.dispatch('login', this.userCredentials)
                    .then(() => {
                        this.$toastr('success', this.$t('login.contenido.mensajes.exito.generico.cuerpo'), this.$t('login.contenido.mensajes.exito.generico.titulo'));
                        this.loadBtnLogin = false;
                        this.error = false;
                        this.$router.push('/');
                    })
                    .catch(() => {
                        this.loadBtnLogin = false;
                        this.error = this.$t('login.contenido.mensajes.fallido.generico.titulo') + ': ' + this.$t('login.contenido.mensajes.fallido.generico.cuerpo') + '.';
                        this.$toastr('error', this.$t('login.contenido.mensajes.fallido.generico.cuerpo'), this.$t('login.contenido.mensajes.fallido.generico.titulo'));
                    });
            },
            switchPasswordVisibility() {
                this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
            }
        }
    };
</script>