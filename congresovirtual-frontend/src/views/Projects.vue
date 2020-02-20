<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row mt-20">
                <div class="col-md-3">
                    <h2 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.titulo') }}</h2>
                    <div class="form-group">
                        <div class="input-group">
                            <input
                                    v-model="query"
                                    v-on:keyup.enter="search"
                                    type="text"
                                    class="form-control"
                                    :placeholder="$t('proyectos.contenido.buscar_placeholder')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>
                    </div>
                    <div class="form-group" v-for="(taxonomy, index) in taxonomies" :key="'taxonomy' + index">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ taxonomy.value }}</h5>
                        <div class="input-group">
                            <div class="w-100">
                                <v-select
                                        v-model="selectedTerms[index]"
                                        multiple
                                        label="value"
                                        :options="taxonomy.terms"
                                        :reduce="term => term.id"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.etapa.titulo') }}</h5>
                        <div class="input-group">
                            <div class="w-100">
                                <v-select
                                        v-model.lazy="selectedEtapas"
                                        multiple
                                        :options="etapas"
                                        :reduce="etapa => etapa.value"
                                        label="label"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                            </div>
                        </div>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.resultados') }}: {{ totalProjects }}</span>
                    <div class="btn-group btn-group-toggle btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn btn-outline-primary"
                                type="button"
                                :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >{{ $t('proyectos.contenido.limpiar') }}</button>
                        <button class="vld-parent btn btn-primary" @click="search">{{ $t('proyectos.contenido.buscar') }}
                            <loading
                                    :active.sync="loadBtnSearch"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row ma-5">
                        <div class="card-columns">
                            <div v-for="project in projects" :key="project.id">
                                <div
                                        v-if="project._object_type === 'projects'"
                                        class="card border-primary mb-20"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                >
                                    <img
                                            class="card-img-top embed-responsive-item default-project-img"
                                            :src="getProjectImgUrl(project.id, project.imagen)"
                                            style="object-fit: cover;"
                                    />
                                    <div class="card-header card-header-action" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('proyectos.contenido.tipo_objeto') }}
                                        <div class="d-flex align-items-center card-action-wrap">
                                            <a
                                                    href="#"
                                                    class="inline-block refresh mr-15"
                                                    :style="mode==='dark'?'color: #fff':''"
                                            >
                                                <i class="fa icon-like"></i>
                                                {{ project.votos_a_favor }}
                                            </a>
                                            <a
                                                    href="#"
                                                    class="inline-block refresh"
                                                    :style="mode==='dark'?'color: #fff':''"
                                            >
                                                <i class="fa icon-dislike"></i>
                                                {{ project.votos_en_contra }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ project.titulo }}</h5>
                                        <p
                                                v-if="project.resumen"
                                                class="card-text"
                                                :style="mode==='dark'?'color: #fff':''"
                                        >
                                            {{ project.resumen.substring(0, seeMoreLimitText) }}
                                            <span
                                                    v-if="project.resumen.length > seeMoreLimitText"
                                                    :id="'dots-' + project.id"
                                            >... </span>
                                            <span
                                                    v-if="project.resumen.length > seeMoreLimitText"
                                                    :id="'seemore-' + project.id"
                                                    class="seemore"
                                            >{{ project.resumen.substring( seeMoreLimitText) }}</span>
                                            <span
                                                    v-if="project.resumen.length > seeMoreLimitText"
                                                    class="text-primary seemore-trigger"
                                                    href
                                                    @click="seeMoreToggle(project.id)"
                                                    :id="'myBtn-' + project.id"
                                            >{{ $t('proyectos.contenido.ver_mas') }}</span>
                                        </p>
                                        <router-link :to="'/project/' + project.id" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20" v-if="totalProjects > projects.length">
                        <button @click="loadMore" class="vld-parent btn btn-secondary btn-block">{{ $t('proyectos.contenido.cargar') }}
                            <loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div class="py-50 text-center border mb-2" v-if="totalProjects === 0 && !loadBtnSearch">{{ $t('proyectos.contenido.no_hay_resultados') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../backend/axios';
    import { API_URL } from '../backend/data_server';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Projects',
        components: {
            Loading
        },
        props: { },
        data: function() {
            return {
                mode: String,
                query: '',
                selectedTerms: [],
                selectedEtapas: [],
                projects: [],
                totalProjects: 0,
                limit: 10,
                offset: 0,
                taxonomies: [],
                etapas: [
                    { id: 1, value: 1, label: null },
                    { id: 2, value: 2, label: null },
                    { id: 3, value: 3, label: null }
                ],
                seeMoreLimitText: 150,
                loadBtnSearch: false,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#ffffff'
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
            this.getTaxonomies();
            this.search();
        },
        methods: {
            configComponent() {
                this.etapas.forEach(etapa => {
                    etapa.label = this.$t('proyectos.contenido.etapa.opciones').find(etapaTrans => etapaTrans.id === etapa.id).label;
                });

                if(this.$route.query.query) {
                    this.query = this.$route.query.query;
                }

                if(this.$route.query['terms_id[]']) {
                    this.selectedTermsList.push(this.$route.query['terms_id[]']);
                }
            },
            getResults: function() {
                axios
                    .get('/search', {
                        params: {
                            query: this.query,
                            objects_types: ['projects'],
                            terms_id: this.selectedTermsList,
                            etapas: this.selectedEtapas,
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.projects = this.projects.concat(res.data.results);
                        this.totalProjects = res.data.total_results;
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadBtnSearch = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            search() {
                if(this.projects.length > 0) {
                    this.projects = [];
                    this.totalProjects = 0;
                    this.offset = 0;
                }
                this.loadBtnSearch = true;
                this.getResults();
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getResults();
            },
            getProjectImgUrl(projectId, projectImage) {
                if(projectImage) {
                    return (API_URL + '/api/storage/projects/' + projectId + '/' + projectImage);
                }
                return null;
            },
            getTaxonomies() {
                axios
                    .get('/taxonomies')
                    .then(res => {
                        this.taxonomies = res.data.results;
                    })
                    .catch(() => this.loginFailed());
            },
            addSelectedTerms(terms) {
                this.selectedTerms.push.apply(this.selectedTerms, terms);
                this.selectedTerms = this.selectedTerms.filter(this.onlyUnique);
            },
            onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            },
            seeMoreToggle(elementIndex) {
                let dots = document.getElementById("dots-" + elementIndex);
                let moreText = document.getElementById("seemore-" + elementIndex);
                let btnText = document.getElementById("myBtn-" + elementIndex);

                if (dots.style.display === "none") {
                    dots.style.display = "inline";
                    btnText.innerHTML = "Ver más";
                    moreText.style.display = "none";
                } else {
                    dots.style.display = "none";
                    btnText.innerHTML = " Ver menos";
                    moreText.style.display = "inline";
                }
            },
            clearFilters() {
                this.query = '';
                this.selectedTerms = [];
                this.selectedEtapas = [];
                this.limit = 10;
            }
        },
        computed: {
            selectedTermsList() {
                let processedList = [];
                this.selectedTerms.forEach(item => {
                    processedList = processedList.concat(item);
                });
                return processedList;
            }
        }
    };
</script>

<style scoped>
    .seemore {
        display: none;
    }

    .seemore-trigger {
        text-decoration: underline;
        cursor: pointer;
    }

    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>