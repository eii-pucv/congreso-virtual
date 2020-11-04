<template>
    <div :class="mode==='dark'?'dark':'light'">
        <vue-scroll-progress-bar
                v-if="!loadPage && page"
                @complete="userReadPage"
                height="1rem"
                backgroundColor="#22af47"
        />
        <div class="container">
            <div class="row">
                <div v-if="loadPage" class="vld-parent">
                    <img src="../assets/img/loader2.gif" style="height: 600px; width: 1200px;" class="pl-0 ma-10 img-fluid col-12">
                </div>
                <PageHeader v-if="!loadPage" :page="page"></PageHeader>
                <nav aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('pagina.breadcumb.inicio') }}</a></li>
                        <li class="breadcrumb-item" aria-current="about" :style="mode==='dark'?'color: #fff':''">{{ $t('pagina.breadcumb.paginas') }}</li>
                        <li v-if="page" class="breadcrumb-item" aria-current="about" :style="mode==='dark'?'color: #fff':''">{{ page.title }} </li>
                    </ol>
                </nav>
                <div v-if="!loadPage" class="col-sm-12 hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <div class="container py-25">
                        <div class="row">
                            <div v-if="page" class="col-12 px-5" v-html="page.content">
                            </div>
                            <div v-else class="col-12 text-center">
                                <h1 class="mb-3">{{ $t('pagina.contenido.no_encontrada.mensaje1') }}</h1>
                                <p class="font-weight-light">{{ $t('pagina.contenido.no_encontrada.mensaje2') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PageHeader from '../components/pages/PageHeader';
    import axios from '../backend/axios';
    import { VueScrollProgressBar } from '@guillaumebriday/vue-scroll-progress-bar';

    export default {
        name: 'Page',
        components: {
            PageHeader,
            VueScrollProgressBar
        },
        props: {
            slug: String
        },
        data() {
            return {
                page: Object,
                readComplete: false,
                loadPage: true,
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getPage();
        },
        methods: {
            getPage() {
                axios
                    .get('pages', {
                        params: {
                            slug: this.slug
                        }
                    })
                    .then(res => {
                        this.page = res.data.results[0];
                    })
                    .finally(() => {
                        this.loadPage = false;
                    });
            },
            userReadPage() {
                if(!this.readComplete) {
                    this.readComplete = true;
                    axios
                        .post('/events',
                            {
                                action_type: 'READ',
                                page_id: this.page.id
                            }
                        )
                        .then(res => {
                            if(res.data.data && res.data.data.gamification_result && res.data.data.gamification_result.rewards.length > 0) {
                                this.$store.dispatch('hasNewNotifications', true);
                                this.$toastr('success', this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.cuerpo'), this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.titulo'));
                            }
                        });
                }
            }
        }
    }
</script>