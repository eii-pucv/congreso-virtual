<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('reiniciar_contrasena.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('reiniciar_contrasena.breadcumb.reinicio') }}</li>
            </ol>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 pa-0">
                    <div class="auth-form-wrap pt-xl-0 pt-70" style="height:70vh !important;">
                        <div class="auth-form w-xl-30 w-sm-50 w-100">
                            <form @submit.prevent="request">
                                <h1 class="display-5 mb-30 text-center">{{ $t('reiniciar_contrasena.contenido.titulo') }}</h1>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                        </div>
                                        <input v-model="email" class="form-control" placeholder="Correo electrónico" type="email" :style="mode==='dark'?'background: #080035; color: #fff':''">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </div>
                                        </div>
                                        <input v-model="password" class="form-control" placeholder="Nueva contraseña" type="password" :style="mode==='dark'?'background: #080035; color: #fff':''">
                                    </div>
                                    <p><small>{{ $t('reiniciar_contrasena.contenido.consejo1') }}</small>
                                    <br/>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo2') }}</small>
                                    <br/>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo3') }}</small>
                                    <br/>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo4') }}</small>
                                    <br/>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo5') }}</small></p>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </div>
                                        </div>
                                        <input v-model="password_confirmation" class="form-control" placeholder="Ingresa de nuevo la contraseña" type="password" :style="mode==='dark'?'background: #080035; color: #fff':''">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="feather-icon"><i data-feather="eye-off"></i></span></span>
                                        </div>
                                    </div>
                                </div>
                                <p><small>{{ $t('reiniciar_contrasena.contenido.consejo1') }}</small></p>
                                <button class="btn btn-primary btn-block mb-20" type="submit">
                                    <p class="text-capitalize">{{ $t('reiniciar_contrasena.contenido.reiniciar') }}</p>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "../backend/axios";
    import router from 'router'
    export default {
        name: "ResetPassword",
        props: {
            mode: String
        },
        data() {
            return {
                token: null,
                password_confirmation: "",
                password: "",
                email: ""
            };
        },
        methods:{
            request() {
                if(this.$route.query.token){
                    axios
                        .post("/password/reset",{
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.password_confirmation,
                            token: this.$route.query.token
                        })
                        .then(res => {
                            this.$toastr('success', '', 'Contraseña cambiada con éxito')
                            this.$router.push("/")
                        })
                        .catch((err) => {
                            this.$toastr('error', '', 'Datos incorrectos')
                            console.error(err)
                            this.$router.push("/")
                        });
                } else {
                    this.$toastr('error', '', 'Datos incorrectos')
                    this.$router.push("/login")
                }
            }
        },
        mounted() {
            if (this.$store.getters.modo_oscuro == "dark") {
                this.mode = "dark"
            } else if (!this.mode) {
                this.mode = "light"
            }
        },
    }
</script>

<style scoped>
    .consejos{
        Color: #c44d4d;
    }
</style>