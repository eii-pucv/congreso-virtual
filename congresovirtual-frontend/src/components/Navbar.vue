<template>
    <div class="bg-primary" style="min-height: 75px;">
        <b-navbar class="bg-indigo-light-2">
            <div class="container">
                <div class="row">
                    <div class="col-2 col-md-1 my-auto">
                        <img class="img-fluid" src="@/assets/logo.png">
                    </div>
                    <div class="col-8 col-md-10 my-auto">
                        <h6 class="text-white text-center">Plataforma oficial de participación ciudadana de la Cámara de Diputadas y Diputados y el Senado de Chile.</h6>
                    </div>
                    <div class="col-2 col-md-1 my-auto">
                        <img class="img-fluid" src="@/assets/logo_diputados.png">
                    </div>
                </div>
            </div>
        </b-navbar>
        <nav class="navbar navbar-expand container-sm">
            <div class="accordion" id="accordion_1">
                <Slide ref="Slide" disableOutsideClick>
                    <div class="d-md-none d-block">
                        <div v-if="!isLoggedIn" class="btn-group btn-block">
                            <router-link :to="{ path: '/signup' }" class="btn btn-outline-light btn-sm btn-text-toggle">
                                <i class="fas fa-user-plus"></i> {{ $t('navbar.registrar') }}
                            </router-link>
                            <router-link :to="{ path: '/login' }" class="btn btn-light btn-sm">
                                <i class="fas fa-sign-in-alt"></i> {{ $t('navbar.iniciar_sesion') }}
                            </router-link>
                        </div>
                        <div v-else-if="isLoggedIn">
                            <div class="btn-group btn-block">
                                <router-link v-if="userData.rol === 'ADMIN'" :to="{ path: '/admin' }" class="btn btn-outline-light btn-sm btn-text-toggle">
                                    <i class="fas fa-user-circle"></i> {{ $t('navbar.admin.titulo') }}
                                </router-link>
                                <router-link v-else-if="userData.rol === 'USER'" :to="{ path: '/user' }" class="btn btn-outline-light btn-sm btn-text-toggle">
                                    <i class="fas fa-user-circle"></i> {{ $t('navbar.perfil') }}
                                </router-link>
                                <button @click.prevent="logout" class="btn btn-light btn-sm">
                                    <i class="fas fa-sign-out-alt"></i> {{ $t('navbar.cerrar_sesion.titulo') }}
                                </button>
                            </div>
                            <router-link v-if="isAvailableGamification" :to="{ path: '/notifications' }" class="btn btn-outline-light btn-sm btn-block btn-text-toggle">
                                <i class="fas fa-bell"></i> {{ $t('navbar.notificaciones.titulo') }}
                            </router-link>
                        </div>
                    </div>
                    <form @submit.prevent="search" class="input-group md-form">
                        <input
                                class="form-control"
                                type="text"
                                :placeholder="$t('navbar.busqueda')"
                                v-model="searchText"
                        />
                        <div class="input-group-append">
                            <button class="btn btn-secondary text-white" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div
                            v-for="(category, index) in categories"
                            :key="category.id"
                            class="d-block"
                            :class="index !== 0 ? 'border-light border-top' : ''"
                    >
                        <div class="d-flex justify-content-between">
                            <a
                                    role="button"
                                    data-toggle="collapse"
                                    :href="'#collapse_' + category.id"
                                    aria-expanded="false"
                                    class="collapsed text-white"
                            >
                                {{ category.text }}
                            </a>
                        </div>
                        <div
                                v-if="category.subcategorias"
                                :id="'collapse_' + category.id"
                                class="collapse text-white"
                                data-parent="#accordion_1"
                                role="tabpanel"
                        >
                            <router-link
                                    v-for="subcategory in category.subcategorias"
                                    :key="subcategory.id"
                                    :to="{ path: subcategory.url }"
                                    class="card-body pa-0 my-10 ml-10 text-white d-block"
                            >
                                {{ subcategory.text }}
                            </router-link>
                        </div>
                    </div>
                    <Language></Language>
                    <div>
                        <p class="text-white">{{ $t('navbar.modo_oscuro') }}</p>
                        <Toggle/>
                    </div>
                </Slide>
            </div>
            <a class="text-white navbar-brand h3 ml-80 my-2" href="/">
                <p>{{ siteName }}</p>
            </a>
            <div class="navbar-nav top-right d-none d-md-block">
                <div v-if="!isLoggedIn" class="btn-group btn-block">
                    <router-link :to="{ path: '/signup' }" class="btn btn-outline-light btn-text-toggle">
                        <i class="fas fa-user-plus"></i> {{ $t('navbar.registrar') }}
                    </router-link>
                    <router-link :to="{ path: '/login' }" class="btn btn-light">
                        <i class="fas fa-sign-in-alt"></i> {{ $t('navbar.iniciar_sesion') }}
                    </router-link>
                </div>
                <div v-else-if="isLoggedIn">
                    <NotificationsPopover v-if="isAvailableGamification"></NotificationsPopover>
                    <div v-if="userData.rol === 'ADMIN'" style="display: inline;">
                        <a class="btn text-white dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ $t('navbar.admin.titulo') }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navDropdown">
                            <router-link class="dropdown-item" :to="{ path: '/user' }">{{ $t('navbar.admin.perfil') }}</router-link>
                            <router-link class="dropdown-item" :to="{ path: '/admin' }">{{ $t('navbar.admin.administrar') }}</router-link>
                        </div>
                    </div>
                    <router-link v-else-if="userData.rol === 'USER'" :to="{ path: '/user' }" class="btn text-white">
                        <i class="fas fa-user-circle"></i> {{ $t('navbar.perfil') }}
                    </router-link>
                    <button @click.prevent="logout" class="btn text-white">
                        <i class="fas fa-sign-out-alt"></i> {{ $t('navbar.cerrar_sesion.titulo') }}
                    </button>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
    import { Slide } from 'vue-burger-menu';
    import jQuery from 'jquery';
    window.jQuery = jQuery;
    window.$ = jQuery;

    require('../../src/assets/js/dropdown-bootstrap-extended');
    import axios from '../backend/axios';
    import Toggle from './Toggle';
    import Language from './Language';
    import NotificationsPopover from './notifications/NotificationsPopover';

    export default {
        name: 'Navbar',
        components: {
            Slide,
            Toggle,
            Language,
            NotificationsPopover
        },
        data() {
            return {
                siteName: null,
                categories: [],
                searchText: '',
                checkPhone: false,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
        },
        methods: {
            configComponent() {
                this.getSiteName();
                this.getSiteMenuPrincipal();
            },
            getSiteName() {
                axios
                    .get('settings', {
                        params: {
                            key: 'site_name'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.siteName = res.data[0].value;
                        }
                    });
            },
            getSiteMenuPrincipal() {
                axios
                    .get('settings', {
                        params: {
                            key: 'site_menu_principal'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.categories = JSON.parse(res.data[0].value).categorias;
                        }
                    });
            },
            logout() {
                this.$router.push('/');
                this.$store.dispatch('logout')
                    .then(() => {
                        this.$toastr('success', this.$t('navbar.cerrar_sesion.mensajes.exito.generico.cuerpo'), this.$t('navbar.cerrar_sesion.mensajes.exito.generico.titulo'));
                    })
                    .catch(() => {
                        this.$toastr('error', this.$t('navbar.cerrar_sesion.mensajes.fallido.generico.cuerpo'), this.$t('navbar.cerrar_sesion.mensajes.fallido.generico.titulo'));
                    });
            },
            search() {
                this.$router.push('/search?query=' + this.searchText);
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
            isAvailableGamification() {
                return !!(this.userData.player && this.userData.player.active_gamification && this.$store.getters.activeGamification);
            }
        }
    }
</script>

<style scoped>
    .btn-text-toggle {
        color: #ffffff;
    }

    .btn-text-toggle:hover {
        color: #324148;
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