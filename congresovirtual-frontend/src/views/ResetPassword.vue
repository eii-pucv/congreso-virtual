<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('recuperar_contrasena.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('recuperar_contrasena.breadcumb.recuperar') }}</li>
            </ol>
        </nav>
        <div class="container">
            <h3 class="display-5 mb-30 text-center">{{ $t('recuperar_contrasena.contenido.titulo') }}</h3>
            <div class="row justify-content-center">
                <form @submit.prevent="reset" class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <input
                                    v-model="email"
                                    class="form-control"
                                    type="email"
                                    required
                                    :placeholder="$t('recuperar_contrasena.contenido.placeholders.correo')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>
                            <input
                                    v-model="password"
                                    class="form-control"
                                    required
                                    :type="passwordFieldType"
                                    :placeholder="$t('recuperar_contrasena.contenido.placeholders.nueva_contrasena')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" @click="switchPasswordVisibility" style="width: 55px;">
                                    <i v-if="passwordFieldType === 'password'" class="fas fa-eye-slash fa-lg"></i>
                                    <i v-else class="fas fa-eye fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>
                            <input
                                    v-model="password_confirmation"
                                    class="form-control"
                                    type="password"
                                    required
                                    :placeholder="$t('recuperar_contrasena.contenido.placeholders.confirmar_contrasena')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                        </div>
                    </div>
                    <div class="col my-10">
                        <strong>{{ $t('recuperar_contrasena.contenido.consejos') }}</strong>
                        <ol>
                            <li>{{ $t('recuperar_contrasena.contenido.consejo1') }}</li>
                            <li>{{ $t('recuperar_contrasena.contenido.consejo2') }}</li>
                            <li>{{ $t('recuperar_contrasena.contenido.consejo3') }}</li>
                            <li>{{ $t('recuperar_contrasena.contenido.consejo4') }}</li>
                        </ol>
                    </div>
                    <button class="btn btn-primary btn-block mb-20 vld-parent" type="submit">
                        <span class="btn-text">{{ $t('recuperar_contrasena.contenido.recuperar') }}</span>
                        <Loading
                                :active.sync="loadBtnReset"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="'#ffffff'"
                        ></Loading>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ResetPassword',
        components: {
            Loading
        },
        data() {
            return {
                token: null,
                email: null,
                password: null,
                password_confirmation: null,
                passwordFieldType: 'password',
                loadBtnReset: false,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }
        },
        methods:{
            reset() {
                this.loadBtnReset = true;
                if(this.$route.query.token) {
                    if(this.password === this.password_confirmation) {
                        if(this.password.length >= 8 && this.password.length <= 20) {
                            axios
                                .post('/password/reset', {
                                    email: this.email,
                                    password: this.password,
                                    password_confirmation: this.password_confirmation,
                                    token: this.$route.query.token
                                })
                                .then(() => {
                                    this.loadBtnReset = false;
                                    this.$toastr('success', this.$t('recuperar_contrasena.mensajes.exito.generico.cuerpo'), this.$t('recuperar_contrasena.mensajes.exito.generico.titulo'));
                                    this.$router.push('/login');
                                })
                                .catch(() => {
                                    this.loadBtnReset = false;
                                    this.$toastr('error', this.$t('recuperar_contrasena.mensajes.fallido.generico.cuerpo'), this.$t('recuperar_contrasena.mensajes.fallido.generico.titulo'));
                                });
                        } else {
                            this.loadBtnReset = false;
                            this.$toastr('error', this.$t('recuperar_contrasena.mensajes.fallido.contrasena_no_valida.cuerpo'), this.$t('recuperar_contrasena.mensajes.fallido.contrasena_no_valida.titulo'));
                        }
                    } else {
                        this.loadBtnReset = false;
                        this.$toastr('error', this.$t('recuperar_contrasena.mensajes.fallido.contrasena_no_coincide.cuerpo'), this.$t('recuperar_contrasena.mensajes.fallido.contrasena_no_coincide.titulo'));
                    }
                } else {
                    this.loadBtnReset = false;
                    this.$toastr('error', this.$t('recuperar_contrasena.mensajes.fallido.sin_token.cuerpo'), this.$t('recuperar_contrasena.mensajes.fallido.sin_token.titulo'));
                }
            },
            switchPasswordVisibility() {
                this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
            }
        }
    }
</script>