<template>
  <div class="hk-pg-wrapper hk-auth-wrapper" :style="mode==='dark'?'background: #080035; color: #fff':''" style="min-height: 720px;">
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
                v-model="email"
                type="email"
                id="inputEmail"
                class="form-control"
                placeholder="Correo electrónico"
                required
                autofocus
                :style="mode==='dark'?'background: #080035; color: #fff':''"
              />
            </div>
            <div class="form-group">
              <div class="input-group">
                <input
                  v-model="password"
                  id="inputPassword"
                  class="form-control"
                  placeholder="Contraseña"
                  required
                  :style="mode==='dark'?'background: #080035; color: #fff':''"
                  :type="passwordFieldType"
                />
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" @click="switchVisibility" style="width: 55px;">
                    <font-awesome-icon v-if="passwordFieldType === 'password'" icon="eye-slash" size="lg"></font-awesome-icon>
                    <font-awesome-icon v-else icon="eye" size="lg"></font-awesome-icon>
                  </button>
                </div>
              </div>
            </div>
            <div class="custom-control custom-checkbox mb-25">
              <input
                class="custom-control-input"
                id="same-address"
                type="checkbox"
                checked=""
                :style="mode==='dark'?'background: #080035; color: #fff':''"
              />
              <label
                class="custom-control-label font-14" for="same-address"
                :class="mode==='dark'?'text-white':'text-primary '"
              >{{ $t('login.contenido.mantener') }}</label
              >
            </div>
            <div v-if="loadBotonIniciar" class="btn btn-primary d-block vld-parent pa-20">
              <loading
                :active.sync="loadBotonIniciar"
                :is-full-page="fullPage"
                :height="32"
                :color="color"
              >
              </loading>
            </div>
            <button v-if="!loadBotonIniciar" class="btn btn-primary btn-block" type="submit">
              {{ $t('login.contenido.iniciar') }}
            </button>
            <p class="text-center mt-15" :style="mode==='dark'?'text-white':'text-primary '">
              {{ $t('login.contenido.olvidar') }}
              <router-link :to="{ path: '/request', query: { mode: this.mode } }" :class="mode==='dark'?'text-white':'text-primary '">{{ $t('login.contenido.recuperar') }}</router-link>
            </p>
            <p class="text-center" :style="mode==='dark'?'text-white':'text-primary'">
              {{ $t('login.contenido.sin_cuenta') }}
              <router-link :to="{ path: '/registro', query: { mode: this.mode } }" :class="mode==='dark'?'text-white':'text-primary '">{{ $t('login.contenido.registrar') }}</router-link>
            </p>
          </form>
        </div>
        <div class="col-sm-6 col-12 my-sm-0 my-15">
          <div id="Login" class="">
            <a class="btn btn-indigo btn-block btn-wth-icon" :href="this.api_url + '/api/auth/facebook' ">
              <span class="icon-label">
                <i class="fa fa-facebook"></i>
              </span>
              <span class="btn-text">Facebook</span>
            </a>
            <a class="btn btn-danger btn-block btn-wth-icon mt-15" :href="this.api_url + '/api/auth/google' ">
              <span class="icon-label">
                <i class="fa fa-google"></i>
              </span>
              <span class="btn-text">Google</span>
            </a>
            <a class="btn btn-blue btn-block btn-wth-icon mt-15" :href="this.api_url + '/api/auth/twitter' ">
              <span class="icon-label">
                <i class="fa fa-twitter"></i>
              </span>
              <span class="btn-text">Twitter</span>
            </a>
            <a class="btn btn-grey btn-block btn-wth-icon mt-15" :href="this.api_url + '/api/auth/github' ">
              <span class="icon-label">
                <i class="fa fa-github"></i>
              </span>
              <span class="btn-text">GitHub</span>
            </a>
            <a class="btn btn-green btn-block btn-wth-icon disabled mt-15" :href="this.api_url + '/api/auth/claveunica' ">
              <span class="icon-label">
                <i class="fa fa-key"></i>
              </span>
              <span class="btn-text">{{ $t('login.contenido.clave') }}</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "../backend/axios";
import router from "../router";
import store from "../store";
import {API_URL} from "../backend/data_server";
import Loading from 'vue-loading-overlay';

export default {
  name: "Login",
  components:{ Loading },
  props: {
    mode: String
  },
  data() {
    return {
      fullPage: false,
      loadBotonIniciar: false,
      color:"#fff",
      email: "",
      emailF: "",
      password: "",
      passwordFieldType: 'password',
      error: false,
      onLogout: Object,
      faceData: Object,
      FB: undefined,
      personalID: "",
      api_url: API_URL
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
    switchVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
    },
    AuthProvider(provider) {
      router.Location = API_URL + "/api/auth/" + provider ;
    },
    SocialLogin(provider, response) {
      axios
        .get("/api/auth/" + provider + "/callback")
        .then(res => {
          console.log(res.data);
          console.log(response.data);
        })
        .catch(err => {
          console.log({ err: err });
        });
    },
    login() {
      this.loadBotonIniciar=true;
      let email = this.email;
      let password = this.password;
      this.$store.dispatch('login', { email, password })
      .then(() => {
        this.$toastr("success", "Iniciaste sesión con éxito", "Sesion iniciada");
        window.location.href = '/#';
      }).catch(err => {
        this.loadBotonIniciar=false;
        this.$toastr("error", "Comprueba tu correo y contraseña", "No has podido iniciar sesión", "Error");
        console.error(err);
      });
    },
    loginFailed() {
      this.loadBotonIniciar=false;
      this.$toastr("error", "No has podido iniciar sesión", "Comprueba tu correo y contraseña", "Error");
      this.error = "Identificación fallida";
      localStorage.removeItem("access_token");
    },
    onSuccess(googleUser) {
      axios
        .post("/auth/login_rrss", {
          google_token: googleUser.El
        })
        .then(res => {
          if (!res.data.access_token) {
            this.loginFailed();
            return;
          }
          let access_token = res.data.access_token;
          let user_data = res.data.user;
          localStorage.access_token = res.data.access_token;
          localStorage.user = JSON.stringify(res.data.user);
          axios.defaults.headers.common["Authorization"] =
            "Bearer " + res.data.access_token;
          this.$toastr("success", "Iniciaste sesión con éxito", "Sesión iniciada");
          store
            .dispatch("login", { access_token, user_data })
            .then(() => router.push("/"));
        })
        .catch(response => {
          console.error(response);
        });
    },
    signupFailed() {
      this.error = "Registro fallido.";
      delete localStorage.token;
    },
    signupSuccessful(req) {
      localStorage.token = req.data.token;
      this.error = false;
      this.success = "Usuario registrado con éxito.";

      this.$router.replace(this.$route.query.redirect || "/");
    },
    onFailure() {
      console.error("Fallo");
    },
    getUserData() {
      try {
        this.FB.api(
          "/me",
          "GET",
          { fields: "id,name,email" },
          userInformation => {
            this.personalID = userInformation.id;
            this.emailF = userInformation.email;
            this.name = userInformation.name;
          }
        );
      } catch (e) {
        console.error(e);
      }
    },
    sdkLoaded(payload) {
      this.isConnected = payload.isConnected;
      this.FB = payload.FB;
      if (this.isConnected) this.getUserData();
    },
    onLogin() {
      this.isConnected = true;
      if (this.FB !== undefined) {
        this.getUserData();
        if (this.personalID !== "") {
          axios
            .post("/auth/login_rrss", {
              facebook_token: this.personalID
            })
            .then(res => {
              if (!res.data.access_token) {
                this.loginFailed();
                this.onLogoutFb();
                return;
              }
              let access_token = res.data.access_token;
              let user_data = res.data.user;
              localStorage.access_token = res.data.access_token;
              localStorage.user = JSON.stringify(res.data.user);
              axios.defaults.headers.common["Authorization"] = "Bearer " + res.data.access_token;
              this.$toastr("success", "Iniciaste sesión con éxito", "Sesión iniciada");
              store
                .dispatch("login", { access_token, user_data })
                .then(() => router.push("/"));
            })
            .catch(response => {
              console.error(response);
              this.onLogoutFb();
            });
        }
      }
    },
    onLogoutFb() {
      this.isConnected = false;
    }
  }
};
</script>