<template>
    <div class="bg-primary" style="min-height: 75px;">
        <nav class="navbar navbar-expand-lg bg-primary container">
            <div class="accordion" id="accordion_1">
                <Slide ref="Slide" disableOutsideClick>
                    <a v-if="!isLoggedIn" class="nav-item pa-0 d-lg-none">
                        <router-link class="nav-link btn btn-outline-light mx-1 d-lg-none" :to="{ path: '/registro', query: { mode: this.mode } }">
                            <font-awesome-icon icon="user-plus"></font-awesome-icon>{{ $t('navbar.registrar') }}
                        </router-link>
                        <router-link class="nav-link btn btn-light mx-1 d-lg-none" :to="{ path: '/login' , query: { mode: this.mode } }">
                            <font-awesome-icon class="text-primary" icon="sign-in-alt"></font-awesome-icon>
                            <a class="text-primary">{{ $t('navbar.iniciar_sesion') }}</a>
                        </router-link>
                    </a>
                    <form v-on:submit.prevent="buscar" class="input-group md-form mt-0 ml-0 pl-2">
                        <input
                                class="form-control"
                                type="text"
                                :placeholder="$t('navbar.busqueda')"
                                v-model="searchText"
                                aria-label="Search"
                        />
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">
                                <font-awesome-icon class="text-white" icon="search"></font-awesome-icon>
                            </button>
                        </div>
                    </form>
                    <div>
                        <p class="text-white">{{ $t('navbar.modo_oscuro') }}</p>
                        <Toggle/>
                    </div>
                    <div
                            v-for="category in categories"
                            :key="category.id"
                            class="d-block border-top border-bottom border-light mr-5"
                    >
                        <div class="d-flex justify-content-between">
                            <a
                                    role="button"
                                    data-toggle="collapse"
                                    :href="'#collapse_'+ category.id"
                                    aria-expanded="false"
                                    class="collapsed text-white"
                            >{{ category.text }}</a>
                        </div>
                        <div
                                v-if="category.subcategorias"
                                :id="'collapse_' + category.id"
                                class="collapse text-white"
                                data-parent="#accordion_1"
                                role="tabpanel"
                        >
                            <a
                                    v-for="subcategory in category.subcategorias"
                                    :key="subcategory.id"
                                    class="card-body pa-0 my-10 ml-10 text-white d-block votable"
                                    style="text-decoration: underline;"
                                    :href="subcategory.url"
                            >{{ subcategory.text }}</a>
                        </div>
                    </div>
                    <div class="d-block" v-if="isMobileDevice">
                        <ul class="navbar-nav">
                            <li v-if="!isLoggedIn" class="nav-item mb-10">
                                <router-link class="nav-link btn btn-outline-light mx-1 d-none d-lg-block" :to="{ path: '/registro' , query: { mode: this.mode } }">
                                    <font-awesome-icon icon="user-plus" size="lg"></font-awesome-icon> {{ $t('navbar.registrar') }}
                                </router-link>
                            </li>
                            <li v-if="!isLoggedIn" class="nav-item mb-10">
                                <router-link class="nav-link btn btn-light mx-1 d-none d-lg-block" :to="{ path: '/login', query: { mode: this.mode } }">
                                    <font-awesome-icon class="text-primary" icon="sign-in-alt" size="lg"></font-awesome-icon>
                                    <a class="text-primary"> {{ $t('navbar.iniciar_sesion') }}</a>
                                </router-link>
                            </li>
                            <li v-if="isLoggedIn && userData.rol === 'ADMIN'" class="nav-item mb-10">
                                <a class="nav-link" href="/admin">
                                    <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.admin') }}
                                </a>
                            </li>
                            <li v-if="isLoggedIn && userData.rol === 'USER'" class="nav-item mb-10">
                                <a class="nav-link" href="/user">
                                    <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.perfil') }}
                                </a>
                            </li>
                            <li v-if="isLoggedIn && userData.rol === 'PARLAMENTARY'" class="nav-item mb-10">
                                <a class="nav-link" href="/user">
                                    <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.panel') }}
                                </a>
                            </li>
                            <li v-if="isLoggedIn" class="nav-item mb-10">
                                <a class="nav-link" @click="logout" style="cursor: pointer;">
                                    <font-awesome-icon icon="sign-out-alt" size="lg"></font-awesome-icon> {{ $t('navbar.cerrar_sesion') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <language></language>
                </Slide>
            </div>
            <router-link class="text-white navbar-brand h3 ml-80 my-2" :to="{ path: '/', query: { mode: this.mode } }" >
                <p>{{ site_name }}</p>
            </router-link>

            <ul class="navbar-nav top-right">
                <li v-if="!isLoggedIn" class="nav-item">
                    <router-link class="nav-link btn btn-outline-light mx-1 d-none d-lg-block" :to="{ path: '/registro', query: { mode: this.mode } }">
                        <font-awesome-icon icon="user-plus" size="lg"></font-awesome-icon> {{ $t('navbar.registrar') }}
                    </router-link>
                </li>
                <li v-if="!isLoggedIn && !isMobileDevice" class="nav-item">
                    <router-link class="nav-link btn btn-light mx-1 d-none d-lg-block" :to="{ path: '/login', query: { mode: this.mode } }">
                        <font-awesome-icon class="text-primary" icon="sign-in-alt" size="lg"></font-awesome-icon>
                        <a class="text-primary"> {{ $t('navbar.iniciar_sesion') }}</a>
                    </router-link>
                </li>
                <li v-if="isLoggedIn && userData.rol === 'ADMIN' && !isMobileDevice" class="nav-item">
                    <a class="nav-link" href="/admin">
                        <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.admin') }}
                    </a>
                </li>
                <li v-if="isLoggedIn && userData.rol === 'USER' && !isMobileDevice" class="nav-item">
                    <a class="nav-link" href="/user">
                        <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.perfil') }}
                    </a>
                </li>
                <li v-if="isLoggedIn && userData.rol === 'PARLAMENTARY' && !isMobileDevice" class="nav-item">
                    <a class="nav-link" href="/user">
                        <font-awesome-icon icon="user-circle" size="lg"></font-awesome-icon> {{ $t('navbar.panel') }}
                    </a>
                </li>
                <li v-if="isLoggedIn && !isMobileDevice" class="nav-item">
                    <a class="nav-link" @click="logout" style="cursor: pointer;">
                        <font-awesome-icon icon="sign-out-alt" size="lg"></font-awesome-icon> {{ $t('navbar.cerrar_sesion') }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    import { Slide } from "vue-burger-menu"; // import the CSS transitions you wish to use, in this case we are using `Slide`
    import jQuery from "jquery";
    window.jQuery = jQuery;
    window.$ = jQuery;

    require("../../src/assets/js/dropdown-bootstrap-extended");
    import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
    import { faBars } from "@fortawesome/free-solid-svg-icons/faBars";
    import { library } from "@fortawesome/fontawesome-svg-core";
    import axios from "../backend/axios";
    import Toggle from "../views/Toggle";
    import language from "./Language"

    library.add(faBars);
    export default {
        name: 'Navbar',
        components: { FontAwesomeIcon, Slide, Toggle, language },
        data: function() {
            return {
                searchText: '',
                checkPhone: false,
                categories: [],
                site_name: null,
                mode: String
            };
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            try {
                const res = await axios.get("/settings?key=site_name");
                this.site_name = res.data[0].value;
            } catch (error) {
                console.log(error);
            }

            try {
                const res = await axios.get("/settings?key=site_menu_principal");
                this.categories = JSON.parse(res.data[0].value).categorias;
            } catch (error) {
                console.log(error);
            }
        },
        methods: {
            logout: function() {
                axios
                    .get("/auth/logout")
                    .then(() => {
                        this.$toastr("info", "Has finalizado tu sesión", "Información");
                        localStorage.removeItem("access_token");
                        localStorage.removeItem("user");
                        delete axios.defaults.headers.common["Authorization"];
                        this.$store.dispatch("logout");
                        this.$router.replace(this.$route.query.redirect || "/login");
                    })
                    .catch(() => {
                        localStorage.removeItem("access_token");
                        localStorage.removeItem("user");
                        delete axios.defaults.headers.common["Authorization"];
                        this.$router.go();
                        this.$router.replace(this.$route.query.redirect || "/login");
                    });
            },
            buscar: function() {
                this.$router.push("/search?query=" + this.searchText);
                this.$refs.Slide.closeMenu();
            }
        },
        computed: {
            isLoggedIn() {
                return this.$store.getters.isLoggedIn;
            },
            userData() {
                return this.$store.getters.userData;
            },
            isMobileDevice() {
                return (
                    typeof window.orientation !== "undefined" ||
                    navigator.userAgent.indexOf("IEMobile") !== -1
                );
            },
        },
    };
</script>

<style scoped>
    .megamenu-li {
        position: relative;
    }
    .megamenu {
        position: absolute;
        width: 100%;
        left: 0;
        right: 0;
        padding: 15px;
    }
    .nav-link {
        color: white !important;
    }
    .my-button {
        background-color: #eee;
    }
    .my-button span.text {
        color: red;
    }
    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
    }
    .top-right {
        position: absolute;
        top: 18px;
        right: 16px;
    }
    .navbar-nav,
    .navbar-nav li {
        top: 18px;
        right: 16px;
        display: inline-block;
        margin-left: 10px;
    }
    .navbar-brand {
        margin-right: 40rem;
    }

    @media (max-width: 767px) {
        .navbar-nav,
        .navbar-nav li {
            top: 5px;
            right: 16px;
            display: inline-flex;
            margin-left: 10px;
        }
    }
</style>
