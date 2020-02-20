<template>
    <div :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="row mb-30">
            <div class="col-sm">
                <nav class="navbar navbar-expand-xl navbar-dark" style="background-color: #454545">
                    <a class="navbar-brand d-flex align-items-center" href="/admin">
                        <h4>{{ $t('administrador.navbar.titulo') }}</h4>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedExample" aria-controls="navbarSupportedExample" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedExample">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-wrench"></i> {{ $t('administrador.navbar.mi_configuracion.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/profile">{{ $t('administrador.navbar.mi_configuracion.perfil') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-users"></i> {{ $t('administrador.navbar.usuarios.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/users">{{ $t('administrador.navbar.usuarios.lista_usuarios') }}</a>
                                    <!-- <a class="dropdown-item" href="/admin/experts">{{ $t('administrador.navbar.usuarios.lista_expertos') }}</a> -->
                                    <a class="dropdown-item" href="/admin/organizations">{{ $t('administrador.navbar.usuarios.lista_organizaciones') }}</a>
                                    <a class="dropdown-item" href="/admin/user">{{ $t('administrador.navbar.usuarios.crear_usuario') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-clipboard-list"></i> {{ $t('administrador.navbar.proyectos.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/projects">{{ $t('administrador.navbar.proyectos.lista') }}</a>
                                    <a class="dropdown-item" href="/admin/project">{{ $t('administrador.navbar.proyectos.crear') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-file-signature"></i> {{ $t('administrador.navbar.consultas.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/public_consultations">{{ $t('administrador.navbar.consultas.lista') }}</a>
                                    <a class="dropdown-item" href="/admin/public_consultation">{{ $t('administrador.navbar.consultas.crear') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-bullhorn"></i> {{ $t('administrador.navbar.propuestas.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/proposals">{{ $t('administrador.navbar.propuestas.lista') }}</a>
                                    <a class="dropdown-item" href="/admin/proposal">{{ $t('administrador.navbar.propuestas.crear') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-book"></i> {{ $t('administrador.navbar.taxonomias.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/taxonomies">{{ $t('administrador.navbar.taxonomias.lista') }}</a>
                                    <a class="dropdown-item" href="/admin/taxonomy">{{ $t('administrador.navbar.taxonomias.crear') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-balance-scale-left"></i> {{ $t('administrador.navbar.moderaciones.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/waiting">{{ $t('administrador.navbar.moderaciones.espera') }}</a>
                                    <a class="dropdown-item" href="/admin/blocked">{{ $t('administrador.navbar.moderaciones.bloqueados') }}</a>
                                    <a class="dropdown-item" href="/admin/classified">{{ $t('administrador.navbar.moderaciones.clasificados') }}</a>
                                    <a class="dropdown-item" href="/admin/denounces">{{ $t('administrador.navbar.moderaciones.denuncias') }}</a>
                                    <a class="dropdown-item" href="/admin/offensive-words">{{ $t('administrador.navbar.moderaciones.palabras_ofensivas') }}</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-clipboard-list"></i> {{ $t('administrador.navbar.contenido.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/pages">{{ $t('administrador.navbar.contenido.lista') }}</a>
                                    <a class="dropdown-item" href="/admin/page">{{ $t('administrador.navbar.contenido.crear') }}</a>
                                    
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="btn-text"><i class="fas fa-tools"></i> {{ $t('administrador.navbar.configuracion.titulo') }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navDropdown">
                                    <a class="dropdown-item" href="/admin/configuration">{{ $t('administrador.navbar.configuracion.config') }}</a>
                                    <a class="dropdown-item" href="/admin/menu">{{ $t('administrador.navbar.configuracion.menu') }}</a>
                                    <a class="dropdown-item" href="/admin/footer">{{ $t('administrador.navbar.configuracion.footer') }}</a>
                                    <a class="dropdown-item" href="/admin/google-analytics">{{ $t('administrador.navbar.configuracion.google_analytics') }}</a>
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
                mode: String,
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

    .dark {
        color: #fff;
        background: rgb(13, 0, 82);
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