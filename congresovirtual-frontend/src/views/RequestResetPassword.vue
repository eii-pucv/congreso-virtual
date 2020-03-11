<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('solicitar_recuperacion_contrasena.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('solicitar_recuperacion_contrasena.breadcumb.solicitud') }}</li>
            </ol>
        </nav>
        <div class="container">
            <h3 class="display-5 mb-30 text-center">{{ $t('solicitar_recuperacion_contrasena.contenido.correo') }}</h3>
            <div class="row justify-content-center">
                <form @submit.prevent="request" class="col-md-6">
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
                                    :placeholder="$t('solicitar_recuperacion_contrasena.contenido.placeholder')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block mb-20 vld-parent" type="submit">
                        <span class="btn-text">{{ $t('solicitar_recuperacion_contrasena.contenido.solicitar') }}</span>
                        <Loading
                                :active.sync="loadBtnRequest"
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
        name: 'RequestResetPassword',
        components: {
            Loading
        },
        data() {
            return {
                token: null,
                email: null,
                loadBtnRequest: false,
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
            request() {
                this.loadBtnRequest = true;
                axios
                    .post('/password/create', {
                        email: this.email
                    })
                    .then(() => {
                        this.loadBtnRequest = false;
                        this.$toastr('success', this.$t('solicitar_recuperacion_contrasena.mensajes.exito.generico.cuerpo'), this.$t('solicitar_recuperacion_contrasena.mensajes.exito.generico.titulo'));
                        this.$router.push('/');
                    })
                    .catch(() => {
                        this.loadBtnRequest = false;
                        this.$toastr('error', this.$t('solicitar_recuperacion_contrasena.mensajes.fallido.generico.cuerpo'), this.$t('solicitar_recuperacion_contrasena.mensajes.fallido.generico.titulo'));
                    });
            }
        }
    }
</script>