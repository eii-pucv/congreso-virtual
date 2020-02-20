<template>
    <div class="hk-pg-wrapper hk-auth-wrapper" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('request_reset.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('request_reset.breadcumb.resetear') }}</li>
            </ol>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 pa-0">
                    <div class="auth-form-wrap pt-xl-0 pt-70" style="height:50vh !important;">
                        <div class="auth-form w-xl-30 w-sm-50 w-100">
                            <form  @submit.prevent="request">
                                <h1 class="display-5 mb-30 text-center">{{ $t('request_reset.contenido.correo') }}</h1>
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
                                <button class="btn btn-primary btn-block mb-20" :disabled="disabledBtn" type="submit">
                                    <p class="text-capitalize">{{ $t('request_reset.contenido.reiniciar') }}</p>
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
        name: "RequestResetPassword",
        props: {
            mode: String
        },
        data() {
            return {
                token: null,
                email: "",
                disabledBtn: false
            };
        },
        methods:{
          request() {
              this.disabledBtn = true
              axios
                  .post("/password/create",{email: this.email})
                  .then(res => {
                      this.$toastr('success', 'Te hemos enviado un correo de reinicio de contraseña.', 'Correo enviado')
                      this.disabledBtn = false
                      this.$router.push("/")
                  })
                  .catch((err) => {
                      this.$toastr('error', 'Comprueba que tu correo se encuentre registrado', 'Algo salió mal')
                      this.disabledBtn = false
                      console.error(err)
                      this.$router.push("/")
                  });
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

</style>