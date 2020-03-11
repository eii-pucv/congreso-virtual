<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" style="min-height: 820px;" :style="mode === 'dark' ? 'background: #080035; color: #fff' : ''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode === 'dark'?'background: #0c0050; color: #fff' : ''">
                <li class="breadcrumb-item"><a href="/#" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('registro.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('registro.breadcumb.registro') }}</li>
            </ol>
        </nav>
        <div class="container">
            <form @submit.prevent="signup">
                <h1 class="display-4 mb-10 text-center">{{ $t('registro.breadcumb.registro') }}</h1>
                <div v-if="error" class="alert alert-danger">{{ error }}</div>
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <p class="mb-30 text-center">{{ $t('registro.contenido.subtitulo1') }}</p>
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <input
                                        v-model="user.name"
                                        type="text"
                                        class="form-control"
                                        :placeholder="$t('registro.contenido.placeholders.nombre')"
                                        required
                                        autofocus
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                            </div>
                            <div class="col-md-6 form-group">
                                <input
                                        v-model="user.surname"
                                        type="text"
                                        class="form-control"
                                        :placeholder="$t('registro.contenido.placeholders.apellido')"
                                        required
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <input
                                    v-model="user.username"
                                    type="text"
                                    class="form-control"
                                    :placeholder="$t('registro.contenido.placeholders.alias')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>
                        <div class="form-group">
                            <input
                                    v-model="user.email"
                                    type="email"
                                    class="form-control"
                                    :placeholder="$t('registro.contenido.placeholders.correo')"
                                    required
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input
                                        v-model="user.password"
                                        class="form-control"
                                        :placeholder="$t('registro.contenido.placeholders.contrasena')"
                                        required
                                        :type="passwordFieldType"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" @click="switchPasswordVisibility" style="width: 55px;">
                                        <font-awesome-icon v-if="passwordFieldType === 'password'" icon="eye-slash" size="lg"></font-awesome-icon>
                                        <font-awesome-icon v-else icon="eye" size="lg"></font-awesome-icon>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block" :style="mode==='dark'?'color: #fff':''">{{ $t('registro.contenido.consejo_contrasena') }}</small>
                        </div>
                        <div class="form-group">
                            <input
                                    v-model="user.password_confirmation"
                                    type="password"
                                    class="form-control"
                                    :placeholder="$t('registro.contenido.placeholders.confirmar_contrasena')"
                                    required
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>
                        <div class="custom-control custom-checkbox mb-25">
                            <input
                                    id="checked_terms_conditions"
                                    v-model="checkTermsAndConditions"
                                    type="checkbox"
                                    class="custom-control-input"
                            />
                            <label for="checked_terms_conditions" class="custom-control-label font-14" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                {{ $t('registro.contenido.leido') }}
                                <a href="/page/terms-and-conditions" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                    <u>{{ $t('registro.contenido.terminos') }}</u>
                                </a>
                                {{ $t('registro.contenido.y') }}
                                <a href="/page/privacy-policies" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                    <u>{{ $t('registro.contenido.politicas') }}</u>
                                </a>
                            </label>
                        </div>
                        <div v-if="loadBtnRegister" class="btn btn-primary d-block vld-parent pa-20">
                            <loading
                                    :active.sync="loadBtnRegister"
                                    :is-full-page="fullPage"
                                    :height="32"
                                    :color="color"
                            ></loading>
                        </div>
                        <button
                                v-if="!loadBtnRegister"
                                class="btn btn-primary btn-block"
                                type="submit"
                        >
                            {{ $t('registro.contenido.registrar') }}
                        </button>
                        <p class="text-center pt-10" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                            {{ $t('registro.contenido.cuenta') }}
                            <a href="/login" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                                <u>{{ $t('registro.contenido.identificar') }}</u>
                            </a>
                        </p>
                    </div>
                    <div class="col-sm-6 col-12">
                        <p class="mb-30 text-center pt-30 pt-sm-0">{{ $t('registro.contenido.subtitulo2') }}</p>
                        <a class="btn btn-indigo btn-block btn-wth-icon mt-30" :href="API_URL + '/api/auth/facebook'">
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
                            <span class="btn-text">{{ $t('registro.contenido.clave') }}</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import axios from "../backend/axios";
    import { API_URL } from "../backend/data_server";
    import Loading from "vue-loading-overlay";

    export default {
        name: 'Register',
        components: {
            Loading
        },
        data() {
            return {
                user: {
                    name: null,
                    surname: null,
                    username: null,
                    email: null,
                    password: null,
                    password_confirmation: null
                },
                checkTermsAndConditions: false,
                error: false,
                passwordFieldType: 'password',
                loadBtnRegister: false,
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
            signup() {
                this.loadBtnRegister = true;
                if(this.user.password === this.user.password_confirmation) {
                    if(this.user.password.length >= 8 && this.user.password.length <= 20) {
                        if(this.checkTermsAndConditions) {
                            axios
                                .post('/auth/signup', {
                                    name: this.user.name,
                                    surname: this.user.surname,
                                    username: this.user.username,
                                    email: this.user.email,
                                    password: this.user.password,
                                    password_confirmation: this.user.password_confirmation
                                })
                                .then(() => {
                                    this.$toastr('success', this.$t('registro.contenido.mensajes.exito.generico.cuerpo'), this.$t('registro.contenido.mensajes.exito.generico.titulo'));
                                })
                                .catch(error => {
                                    let errorType = error.response.data.error;
                                    switch (errorType) {
                                        case 'USER_EMAIL_EXISTS_ERROR':
                                            this.error = this.$t('registro.contenido.mensajes.fallido.correo_existe.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.correo_existe.cuerpo') + '.';
                                            this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.correo_existe.cuerpo'), this.$t('registro.contenido.mensajes.fallido.correo_existe.titulo'));
                                            break;
                                        case 'USER_USERNAME_EXISTS_ERROR':
                                            this.error = this.$t('registro.contenido.mensajes.fallido.alias_existe.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.alias_existe.cuerpo') + '.';
                                            this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.alias_existe.cuerpo'), this.$t('registro.contenido.mensajes.fallido.alias_existe.titulo'));
                                            break;
                                        default:
                                            this.error = this.$t('registro.contenido.mensajes.fallido.generico.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.generico.cuerpo') + '.';
                                            this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.generico.cuerpo'), this.$t('registro.contenido.mensajes.fallido.generico.titulo'));
                                            break;
                                    }
                                })
                                .finally(() => {
                                    this.loadBtnRegister = false;
                                });
                        } else {
                            this.loadBtnRegister = false;
                            this.error = this.$t('registro.contenido.mensajes.fallido.term_cond_no_aceptadas.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.term_cond_no_aceptadas.cuerpo') + '.';
                            this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.term_cond_no_aceptadas.cuerpo'), this.$t('registro.contenido.mensajes.fallido.term_cond_no_aceptadas.titulo'));
                        }
                    } else {
                        this.loadBtnRegister = false;
                        this.error = this.$t('registro.contenido.mensajes.fallido.contrasena_no_valida.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.contrasena_no_valida.cuerpo') + '.';
                        this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.contrasena_no_valida.cuerpo'), this.$t('registro.contenido.mensajes.fallido.contrasena_no_valida.titulo'));
                    }
                } else {
                    this.loadBtnRegister = false;
                    this.error = this.$t('registro.contenido.mensajes.fallido.contrasena_no_coincide.titulo') + ': ' + this.$t('registro.contenido.mensajes.fallido.contrasena_no_coincide.cuerpo') + '.';
                    this.$toastr('error', this.$t('registro.contenido.mensajes.fallido.contrasena_no_coincide.cuerpo'), this.$t('registro.contenido.mensajes.fallido.contrasena_no_coincide.titulo'));
                }
            },
            switchPasswordVisibility() {
                this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
            }
        }
    };
</script>
