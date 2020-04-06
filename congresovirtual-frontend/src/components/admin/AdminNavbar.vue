<template>
    <div :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="row mb-30">
            <div class="col-sm">
                <nav class="navbar navbar-expand-xl navbar-dark" style="background-color: #454545">
                    <a class="navbar-brand d-xl-none" href="/admin">
                        <h4>{{ $t('administrador.navbar.titulo') }}</h4>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedExample" aria-controls="navbarSupportedExample" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedExample">
                        <ul class="nav navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i> {{ $t('administrador.navbar.mi_configuracion.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path: '/user' }">{{ $t('administrador.navbar.mi_configuracion.ver_perfil') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path: '/admin/profile' }">{{ $t('administrador.navbar.mi_configuracion.editar_perfil') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-users"></i> {{ $t('administrador.navbar.usuarios.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/users' }">{{ $t('administrador.navbar.usuarios.lista_usuarios') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/organizations' }">{{ $t('administrador.navbar.usuarios.lista_organizaciones') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/user' }">{{ $t('administrador.navbar.usuarios.crear_usuario') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-clipboard-list"></i> {{ $t('administrador.navbar.proyectos.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/projects' }">{{ $t('administrador.navbar.proyectos.lista') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/project' }">{{ $t('administrador.navbar.proyectos.crear') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-signature"></i> {{ $t('administrador.navbar.consultas.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/public_consultations' }">{{ $t('administrador.navbar.consultas.lista') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/public_consultation' }">{{ $t('administrador.navbar.consultas.crear') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bullhorn"></i> {{ $t('administrador.navbar.propuestas.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/proposals' }">{{ $t('administrador.navbar.propuestas.lista') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/proposal' }">{{ $t('administrador.navbar.propuestas.crear') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-book"></i> {{ $t('administrador.navbar.taxonomias.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/taxonomies' }">{{ $t('administrador.navbar.taxonomias.lista') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/taxonomy' }">{{ $t('administrador.navbar.taxonomias.crear') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-balance-scale-left"></i> {{ $t('administrador.navbar.moderaciones.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/waiting' }">{{ $t('administrador.navbar.moderaciones.espera') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/blocked' }">{{ $t('administrador.navbar.moderaciones.bloqueados') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/classified' }">{{ $t('administrador.navbar.moderaciones.clasificados') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/denounces' }">{{ $t('administrador.navbar.moderaciones.denuncias') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/offensive-words' }">{{ $t('administrador.navbar.moderaciones.palabras_ofensivas') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-clipboard-list"></i> {{ $t('administrador.navbar.pagina.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/pages' }">{{ $t('administrador.navbar.pagina.lista') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/page' }">{{ $t('administrador.navbar.pagina.crear') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-gamepad"></i> {{ $t('administrador.navbar.gamificacion.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/gamification/players' }">{{ $t('administrador.navbar.gamificacion.lista_jugadores') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/gamification/actions' }">{{ $t('administrador.navbar.gamificacion.lista_acciones') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/gamification/action' }">{{ $t('administrador.navbar.gamificacion.crear_accion') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/gamification/rewards' }">{{ $t('administrador.navbar.gamificacion.lista_recompensas') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/gamification/reward' }">{{ $t('administrador.navbar.gamificacion.crear_recompensa') }}</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-tools"></i> {{ $t('administrador.navbar.configuracion.titulo') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navDropdown">
                                    <router-link class="dropdown-item" :to="{ path:'/admin/general_settings' }">{{ $t('administrador.navbar.configuracion.config') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/menu_settings' }">{{ $t('administrador.navbar.configuracion.menu') }}</router-link>
                                    <router-link class="dropdown-item" :to="{ path:'/admin/footer_settings' }">{{ $t('administrador.navbar.configuracion.footer') }}</router-link>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        name: 'AdminNavbar',
        components: { },
        data() {
            return {
                mode: String
            };
        },
        methods: { },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }
        },
    }
</script>

<style scoped>
    .navbar-brand {
        display: inline-block;
        padding-top: .3125rem;
        padding-bottom: .3125rem;
        margin-right: 1rem;
        font-size: 1.25rem;
        line-height: inherit;
        white-space: nowrap;
    }

    .navbar-collapse {
        -ms-flex-preferred-size: 100%;
        flex-basis: 100%;
        -ms-flex-positive: 1;
        flex-grow: 1;
        -ms-flex-align: center;
        align-items: center;
        justify-content: center
    }

    .dropdown, .dropleft, .dropright, .dropup {
        position: relative;
    }

    @media (min-width: 1440px) {
        .navbar-expand-xl {
            -ms-flex-flow: row nowrap;
            flex-flow: row nowrap;
            -ms-flex-pack: start;
            justify-content: flex-start;
        }
    }

    @media (min-width: 1440px) {
        .navbar-expand-xl .navbar-collapse {
            display: -ms-flexbox!important;
            display: flex!important;
            -ms-flex-preferred-size: auto;
            flex-basis: auto;
        }
    }

    @media (min-width: 1440px) {
        .navbar-expand-xl .navbar-nav {
            -ms-flex-direction: row;
            flex-direction: row;
        }
    }
</style>