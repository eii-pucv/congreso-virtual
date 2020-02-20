<template>
  <div
    class="hk-pg-wrapper hk-auth-wrapper"
    :style="mode === 'dark' ? 'background: #080035; color: #fff' : ''"
    style="min-height: 720px;"
  >
    <nav aria-label="breadcrumb" class="container pt-20">
      <ol class="breadcrumb" :style="mode === 'dark'?'background: #0c0050; color: #fff' : ''">
        <li class="breadcrumb-item">
          <a href="/#" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('registro.breadcumb.inicio') }}</a>
        </li>
        <li
          class="breadcrumb-item active"
          aria-current="page"
          :style="mode === 'dark' ? 'color: #fff' : ''"
        >{{ $t('registro.breadcumb.registro') }}</li>
      </ol>
    </nav>
    <div class="container">
      <form @submit.prevent="signup">
        <h1 class="display-4 mb-10 text-center">{{ $t('registro.breadcumb.registro') }}</h1>
        <div class="alert alert-danger" v-if="error">{{ error }}</div>
        <div class="alert alert-danger" v-if="emailError">{{ emailError }}</div>
        <div
          class="alert alert-danger"
          v-if="password_confirmationError"
        >{{ password_confirmationError }}</div>
        <div class="alert alert-success" v-if="success">{{ success }}</div>
        <div class="row">
          <div class="col-sm-6 col-12">
            <p class="mb-30 text-center">{{ $t('registro.contenido.subtitulo1') }}</p>
            <div class="form-row">
              <div class="col-md-6 form-group">
                <input
                  v-model="name"
                  type="text"
                  id="inputName"
                  class="form-control"
                  placeholder="Nombre"
                  required
                  autofocus
                  :style="mode==='dark'?'background: #080035; color: #fff':''"
                />
              </div>
              <div class="col-md-6 form-group">
                <input
                  v-model="surname"
                  type="text"
                  id="inputSurname"
                  class="form-control"
                  placeholder="Apellido"
                  required
                  :style="mode==='dark'?'background: #080035; color: #fff':''"
                />
              </div>
            </div>
            <div class="form-group">
              <input
                v-model="email"
                type="email"
                id="inputEmail"
                class="form-control"
                placeholder="Correo electrónico"
                required
                :style="mode==='dark'?'background: #080035; color: #fff':''"
              />
            </div>
            <div class="form-group">
              <input
                v-model="password"
                type="password"
                id="inputPassword"
                class="form-control"
                placeholder="Contraseña"
                required
                :style="mode==='dark'?'background: #080035; color: #fff':''"
              />
            </div>
            <div class="form-group">
              <div class="input-group">
                <input
                  v-model="password_confirmation"
                  type="password"
                  id="inputPasswordConfirmation"
                  class="form-control"
                  placeholder="Confirmar contraseña"
                  required
                  :style="mode==='dark'?'background: #080035; color: #fff':''"
                />
                <div class="input-group-append">
                  <span class="input-group-text">
                    <span class="feather-icon">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-eye-off"
                      >
                        <path
                          d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                        />
                        <line x1="1" y1="1" x2="23" y2="23" />
                      </svg>
                    </span>
                  </span>
                </div>
              </div>
            </div>
            <div class="custom-control custom-checkbox mb-25">
              <input
                class="custom-control-input"
                id="same-address"
                type="checkbox"
                :checked="check"
                v-on:click="check = !check"
              />
              <label
                class="custom-control-label font-14"
                for="same-address"
                :class="mode === 'dark' ? 'text-white' : 'text-primary'"
              >
                {{ $t('registro.contenido.leido') }}
                <a
                  href
                  :class="mode === 'dark' ? 'text-white' : 'text-primary'"
                >
                  <u>{{ $t('registro.contenido.terminos') }}</u>
                </a>
              </label>
            </div>
            <div v-if="loadBotonIniciar" class="btn btn-primary d-block vld-parent pa-20">
              <loading
                :active.sync="loadBotonIniciar"
                :is-full-page="fullPage"
                :height="32"
                :color="color"
              ></loading>
            </div>
            <button
              v-if="!loadBotonIniciar"
              class="btn btn-primary btn-block"
              type="submit"
            >{{ $t('registro.contenido.registrar') }}</button>
            <p class="text-center pt-10" :class="mode === 'dark' ? 'text-white' : 'text-primary'">
              {{ $t('registro.contenido.cuenta') }}
              <router-link :to="{ path: '/login', query: { mode: this.mode } }">
                <a :class="mode === 'dark' ? 'text-white' : 'text-primary'">
                  <u>{{ $t('registro.contenido.identificar') }}</u>
                </a>
              </router-link>
            </p>
          </div>
          <div class="col-sm-6 col-12">
            <p class="mb-30 text-center pt-30 pt-sm-0">{{ $t('registro.contenido.subtitulo2') }}</p>
            <div id="Login">
              <a
                class="btn btn-indigo btn-block btn-wth-icon mt-30"
                :href="this.api_url + '/api/auth/facebook' "
              >
                <span class="icon-label">
                  <i class="fa fa-facebook"></i>
                </span>
                <span class="btn-text">Facebook</span>
              </a>

              <a
                class="btn btn-danger btn-block btn-wth-icon mt-15"
                :href="this.api_url + '/api/auth/google' "
              >
                <span class="icon-label">
                  <i class="fa fa-google"></i>
                </span>
                <span class="btn-text">Google</span>
              </a>
              <a
                class="btn btn-blue btn-block btn-wth-icon mt-15"
                :href="this.api_url + '/api/auth/twitter' "
              >
                <span class="icon-label">
                  <i class="fa fa-twitter"></i>
                </span>
                <span class="btn-text">Twitter</span>
              </a>
              <a
                class="btn btn-grey btn-block btn-wth-icon mt-15"
                :href="this.api_url + '/api/auth/github' "
              >
                <span class="icon-label">
                  <i class="fa fa-github"></i>
                </span>
                <span class="btn-text">Github</span>
              </a>

              <a
                class="btn btn-green btn-block btn-wth-icon disabled my-15"
                :href="this.api_url + '/api/auth/claveunica'"
              >
                <span class="icon-label">
                  <i class="fa fa-key"></i>
                </span>
                <span class="btn-text">{{ $t('registro.contenido.clave') }}</span>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios from "../backend/axios";
import { API_URL } from "../backend/data_server";
import Loading from "vue-loading-overlay";

export default {
  name: "Register",
  components: { Loading },
  props: {
    mode: String
  },
  data() {
    return {
      fullPage: false,
      loadBotonIniciar: false,
      color: "#fff",
      FB: undefined,
      personalID: "",
      name: "",
      nameF: Array,
      surname: "",
      email: "",
      password: "",
      password_confirmation: "",
      error: false,
      success: false,
      check: false,
      params: {
        client_id:
          "677015736128-he0efqhqat818rka11dd0ukg54egt4sj.apps.googleusercontent.com"
      },
      // only needed if you want to render the button with the google ui
      renderParams: {
        width: 230,
        height: 50,
        longtitle: true
      },
      api_url: API_URL,
      emailError: false,
      password_confirmationError: false
    };
  },
  mounted() {
    if (this.$store.getters.modo_oscuro == "dark") {
      this.mode = "dark";
    } else if (!this.mode) {
      this.mode = "light";
    }
  },
  methods: {
    AuthProvider(provider) {
      this.$router.Location = API_URL + "/api/auth/" + provider;
    },
    signup() {
      if (this.check) {
        this.loadBotonIniciar = true;
        axios
          .post("/auth/signup", {
            name: this.name,
            surname: this.surname,
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation
          })
          .then(response => this.signupSuccessful(response))
          .catch(e => {
            (this.password_confirmationError = false),
              (this.emailError = false),
              this.signupFailed(e);
          });
      } else {
        this.loadBotonIniciar = false;
        this.$toastr(
          "warning",
          "No ha aceptado los terminos y condiciones",
          "Registro fallido"
        );
      }
    },
    signupSuccessful(req) {
      this.$router.push("/#");
      this.$toastr(
        "success",
        "Te hemos enviado un correo de verificación.",
        "Registro exitoso"
      );
      localStorage.token = req.data.token;
      this.error = false;
      this.success = "Usuario registrado con éxito.";
      this.$router.replace(this.$route.query.redirect || "/");
    },
    signupSuccessfulrrss(req) {
      this.$toastr("success", "Registro exitoso");
      localStorage.token = req.data.token;
      this.error = false;
      this.success = "Usuario registrado con éxito.";
      this.$router.replace(this.$route.query.redirect || "/");
    },
    signupFailed(e) {
      this.loadBotonIniciar = false;
      this.error = "Registro fallido.";
      if (e.response.data.email) {
        this.emailError = "El correo ya se encuentra actualmente ocupado.";
        //console.log(e.response.data.email);
      }
      if (e.response.data.password) {
        this.password_confirmationError =
          "La confirmación de la contraseña no es igual a la primera.";
        //console.log(e.response.data.password);
      }
      console.log(e.response.data);
      delete localStorage.token;
    },
    onSuccess(googleUser) {
      console.log(googleUser);
      console.log("iniciado en google");
      console.log(googleUser.getId());
      axios
        .post("/auth/signup_rrss", {
          name: googleUser.getBasicProfile().ofa,
          surname: googleUser.getBasicProfile().wea,
          email: googleUser.getBasicProfile().U3,
          google_token: googleUser.El
        })
        .then(response => this.signupSuccessfulrrss(response))
        .catch(response => {
          +console.log(response);
        });

      // This only gets the user information: id, name, imageUrl and email
      //console.log(googleUser.getBasicProfile());
    },
    onFailure() {
      console.log("fallo");
      this.error = "Registro fallido.";
      delete localStorage.token;

      // This only gets the user information: id, name, imageUrl and email
      //console.log(googleUser.getBasicProfile());
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
            this.nameF = userInformation.name.split(" ");
            console.log(userInformation);
          }
        );
      } catch (e) {
        console.log(e);
      }
    },
    sdkLoaded(payload) {
      this.isConnected = payload.isConnected;
      this.FB = payload.FB;
      if (this.isConnected) this.getUserData();
    },
    onLogin() {
      this.isConnected = true;
      console.log(this.getUserData());
      if (this.FB !== undefined) {
        this.getUserData();
        //login
        axios
          .post("/auth/signup_rrss", {
            name: this.nameF[0] + " " + this.nameF[1],
            surname: this.nameF[2] + " " + this.nameF[3],
            email: this.emailF,
            facebook_token: this.personalID
          })
          .then(response => this.signupSuccessfulrrss(response))
          .catch(response => {
            console.log(response);
            console.log("Hola mundo");
            this.onLogoutFb();
          });
      }
    },
    onLogoutFb() {
      this.isConnected = false;
    }
  }
};
</script>

<style scoped></style>
