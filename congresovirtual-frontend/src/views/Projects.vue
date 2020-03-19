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
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.etapa.titulo') }}</h5>
                        <div class="input-group">
                            <div class="w-100">
                                <multiselect
                                        v-model="selectedEtapas"
                                        track-by="id"
                                        label="label"
                                        :placeholder="$t('proyectos.contenido.etapa.seleccionar')"
                                        :options="etapas"
                                        :multiple="true"
                                        :showLabels="false"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.orden.titulo') }}</h5>
                        <select @change="sort" v-model="selectedSortId" class="form-control custom-select d-block">
                            <option
                                    v-for="optionSort in optionsSort"
                                    :key="'option-sort-' + optionSort.id"
                                    :value="optionSort.id"
                            >
                                {{ optionSort.label }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.tema_asociado.titulo') }}</h5>
                        <multiselect
                                v-model="selectedTerms"
                                group-values="terms"
                                group-label="value"
                                track-by="id"
                                label="value"
                                :placeholder="$t('proyectos.contenido.tema_asociado.seleccionar')"
                                :options="taxonomyTerms"
                                :loading="loadTaxonomyTerms"
                                :multiple="true"
                                :showLabels="false"
                                :limit="5"
                                :limit-text="limitTextTaxonomyTermsMultiselect"
                                :style="mode==='dark'?' color: #fff':''"
                        ></multiselect>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('proyectos.contenido.resultados') }}: {{ totalProjects }}</span>
                    <div class="btn-group btn-group-toggle btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn btn-outline-primary"
                                type="button"
                                :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('proyectos.contenido.limpiar') }}
                        </button>
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
                    <div class="row">
                        <div v-for="project in projects" :key="project.id" class="col-md-6 col-xl-4 pb-4 px-10">
                            <div
                                    v-if="project._object_type === 'projects'"
                                    class="card border-primary h-100"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                            >
                                <img
                                        class="card-img-top embed-responsive-item default-project-img"
                                        :src="getProjectImgUrl(project.id, project.imagen)"
                                        style="object-fit: cover;"
                                />
                                <div>
                                    <router-link
                                            v-if="project.etapa === 1 && getIsAvailableVoting(project.id)"
                                            :to="{ path: 'projects', query: { 'etapas[]': 1 } }"
                                            class="top-right badge badge-primary font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_general') }}
                                    </router-link>
                                    <router-link
                                            v-else-if="project.etapa === 2 && getIsAvailableVoting(project.id)"
                                            :to="{ path: 'projects', query: { 'etapas[]': 2 } }"
                                            class="top-right badge badge-success font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_particular') }}
                                    </router-link>
                                    <router-link
                                            v-else
                                            :to="{ path: 'projects', query: { 'etapas[]': 3 } }"
                                            class="top-right badge badge-danger font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_cerrada') }}
                                    </router-link>
                                </div>
                                <div class="card-header card-header-action" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('proyectos.contenido.tipo_objeto') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fa icon-like"></i>
                                            {{ project.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fa icon-dislike"></i>
                                            {{ project.votos_en_contra }}
                                        </div>
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
                                        >{{ project.resumen.substring(seeMoreLimitText) }}</span>
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
    import Multiselect from 'vue-multiselect';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Projects',
        components: {
            Multiselect,
            Loading
        },
        data() {
            return {
                projects: [],
                totalProjects: 0,
                query: '',
                selectedEtapas: [],
                etapas: [
                    { id: 1, value: 1, label: null },
                    { id: 2, value: 2, label: null },
                    { id: 3, value: 3, label: null }
                ],
                currentSort: null,
                selectedSortId: 0,
                optionsSort: [
                    { id: 0, field: null, type: null, label: null },
                    { id: 1, field: 'fecha_inicio', type: 'ASC', label: null },
                    { id: 2, field: 'fecha_inicio', type: 'DESC', label: null },
                    { id: 3, field: 'votos_a_favor', type: 'ASC', label: null },
                    { id: 4, field: 'votos_a_favor', type: 'DESC', label: null },
                    { id: 5, field: 'votos_en_contra', type: 'ASC', label: null },
                    { id: 6, field: 'votos_en_contra', type: 'DESC', label: null }
                ],
                selectedTerms: [],
                taxonomyTerms: [],
                limit: 12,
                offset: 0,
                currentMoment: this.$moment().local(),
                seeMoreLimitText: 150,
                loadTaxonomyTerms: true,
                loadBtnSearch: false,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#ffffff',
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
            this.getTaxonomyTerms();
            this.search();
        },
        methods: {
            configComponent() {
                this.etapas.forEach(etapa => {
                    etapa.label = this.$t('proyectos.contenido.etapa.opciones').find(etapaTrans => etapaTrans.id === etapa.id).label;
                });

                this.optionsSort.forEach(option => {
                    option.label = this.$t('proyectos.contenido.orden.opciones').find(optionTrans => optionTrans.id === option.id).label;
                });

                if(this.$route.query.query) {
                    this.query = this.$route.query.query;
                }

                if(this.$route.query['etapas[]']) {
                    if(Array.isArray(this.$route.query['etapas[]'])) {
                        let preSelectedEtapasValues = this.$route.query['etapas[]'];
                        preSelectedEtapasValues.forEach(preSelectedEtapaValue => {
                            let selectedEtapa = this.etapas.find(etapa => etapa.value == preSelectedEtapaValue);
                            if(selectedEtapa) {
                                this.selectedEtapas.push(selectedEtapa);
                            }
                        });
                    } else {
                        let preSelectedEtapa = this.etapas.find(etapa => etapa.value == this.$route.query['etapas[]']);
                        if(preSelectedEtapa) {
                            this.selectedEtapas.push(preSelectedEtapa);
                        }
                    }
                }

                if(this.$route.query['terms_id[]']) {
                    if(Array.isArray(this.$route.query['terms_id[]'])) {
                        let preSelectedTermsIds = this.$route.query['terms_id[]'];
                        preSelectedTermsIds.forEach(preSelectedTermId => {
                            this.selectedTerms.push({
                                id: preSelectedTermId
                            });
                        });
                    } else {
                        this.selectedTerms.push({
                            id: this.$route.query['terms_id[]']
                        });
                    }
                }
            },
            getTaxonomyTerms() {
                axios
                    .get('/taxonomies', {
                        params: {
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.taxonomyTerms = res.data.results;

                        if(this.selectedTerms.length > 0) {
                            let terms = [].concat(...this.taxonomyTerms.map(taxonomy => taxonomy.terms));
                            let auxSelectedTerms = [];
                            this.selectedTerms.forEach(selectedTerm => {
                                let preSelectedTerm = terms.find(term => term.id == selectedTerm.id);
                                if(preSelectedTerm) {
                                    auxSelectedTerms.push(preSelectedTerm);
                                }
                            });
                            this.selectedTerms = auxSelectedTerms;
                        }
                    })
                    .finally(() => {
                        this.loadTaxonomyTerms = false;
                    })
            },
            getResults() {
                let searchParams = {
                    query: this.query,
                    objects_types: ['projects'],
                    terms_id: this.selectedTerms.map(selectedTerm => selectedTerm.id),
                    limit: this.limit,
                    offset: this.offset
                };
                if(this.currentSort) {
                    searchParams.order = this.currentSort.type;
                    searchParams.order_by = this.currentSort.field;
                }
                if(this.selectedEtapas.length === 1 && this.selectedEtapas.find(selectedEtapa => selectedEtapa.value === 3)) {
                    searchParams.is_available_voting = 0;
                    searchParams.etapas = [];
                } else if(this.selectedEtapas.length > 0 && !this.selectedEtapas.find(selectedEtapa => selectedEtapa.value === 3)) {
                    searchParams.is_available_voting = 1;
                    searchParams.etapas = this.selectedEtapas.map(selectedEtapa => selectedEtapa.value);
                } else if(this.selectedEtapas.length > 0 && this.selectedEtapas.find(selectedEtapa => selectedEtapa.value === 3)) {
                    searchParams.is_available_voting = 0;
                    searchParams.include_etapas = this.selectedEtapas.map(etapa => etapa.value);
                }

                axios
                    .get('/search', {
                        params: searchParams
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
            sort(event) {
                let optionSortId = event.target.value;
                if(optionSortId != 0) {
                    let selectedOptionSort = this.optionsSort.find(optionSort => optionSort.id == optionSortId);
                    this.currentSort = {
                        field: selectedOptionSort.field,
                        type: selectedOptionSort.type
                    };
                } else {
                    this.currentSort = null;
                }
            },
            getProjectImgUrl(projectId, projectImage) {
                if(projectImage) {
                    return (API_URL + '/api/storage/projects/' + projectId + '/' + projectImage);
                }
                return null;
            },
            clearFilters() {
                this.query = '';
                this.selectedEtapas = [];
                this.currentSort = null;
                this.selectedSortId = 0;
                this.selectedTerms = [];
                this.limit = 10;
            },
            limitTextTaxonomyTermsMultiselect(count) {
                if(this.$i18n.locale === 'es') {
                    if(count === 1) {
                        return `y 1 tema más`;
                    } else {
                        return `y ${count} temas más`;
                    }
                } else if(count === 1) {
                    return `and 1 more topic`;
                } else {
                    return `and ${count} more topics`;
                }
            },
            getIsAvailableVoting(projectId) {
                let project = this.projects.find(project => project.id === projectId);
                let votingStartDate = this.$moment.utc(project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();

                return project.is_enabled && project.etapa !== 3 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
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

    .top-right {
        position: absolute;
        top: 6px;
        right: 8px;
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