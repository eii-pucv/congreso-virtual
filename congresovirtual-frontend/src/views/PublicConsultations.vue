<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row mt-20">
                <div class="col-md-3">
                    <h2 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.contenido.titulo') }}</h2>
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
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.contenido.estado.titulo') }}</h5>
                        <div class="input-group">
                            <div class="w-100">
                                <multiselect
                                        v-model="selectedEstados"
                                        track-by="id"
                                        label="label"
                                        :placeholder="$t('consultas_publicas.contenido.estado.seleccionar')"
                                        :options="estados"
                                        :multiple="true"
                                        :showLabels="false"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.contenido.orden.titulo') }}</h5>
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
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.contenido.tema_asociado.titulo') }}</h5>
                        <multiselect
                                v-model="selectedTerms"
                                group-values="terms"
                                group-label="value"
                                track-by="id"
                                label="value"
                                :placeholder="$t('consultas_publicas.contenido.tema_asociado.seleccionar')"
                                :options="taxonomyTerms"
                                :loading="loadTaxonomyTerms"
                                :multiple="true"
                                :showLabels="false"
                                :limit="5"
                                :limit-text="limitTextTaxonomyTermsMultiselect"
                                :style="mode==='dark'?' color: #fff':''"
                        ></multiselect>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.contenido.resultados') }}: {{ totalPublicConsultations }}</span>
                    <div class="btn-group btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn btn-outline-primary"
                                type="button"
                                :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('consultas_publicas.contenido.limpiar') }}
                        </button>
                        <button class="vld-parent btn btn-primary" @click="search">{{ $t('consultas_publicas.contenido.buscar') }}
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
                        <div v-for="publicConsultation in publicConsultations" :key="publicConsultation.id" class="col-md-6 col-xl-4 pb-4 px-10">
                            <div
                                    v-if="publicConsultation._object_type === 'consultations'"
                                    class="card border-primary h-100"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                            >
                                <img
                                        class="card-img-top embed-responsive-item default-consultation-img"
                                        :src="getPublicConsultationImgUrl(publicConsultation.id, publicConsultation.imagen)"
                                        style="object-fit: cover;"
                                />
                                <div>
                                    <router-link
                                            v-if="getIsAvailableVoting(publicConsultation.id)"
                                            :to="{ path: 'public_consultations', query: { 'estados[]': 1 } }"
                                            class="top-right badge badge-primary font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_abierta') }}
                                    </router-link>
                                    <router-link
                                            v-else
                                            :to="{ path: 'public_consultations', query: { 'estados[]': '0' } }"
                                            class="top-right badge badge-danger font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_cerrada') }}
                                    </router-link>
                                </div>
                                <div class="card-header card-header-action" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('consultas_publicas.contenido.tipo_objeto') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fas icon-like"></i>
                                            {{ publicConsultation.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fas icon-dislike"></i>
                                            {{ publicConsultation.votos_en_contra }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ publicConsultation.titulo }}</h5>
                                    <p
                                            v-if="publicConsultation.resumen"
                                            class="card-text"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ publicConsultation.resumen.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="publicConsultation.resumen.length > seeMoreLimitText"
                                                :id="'dots-' + publicConsultation.id"
                                        >... </span>
                                        <span
                                                v-if="publicConsultation.resumen.length > seeMoreLimitText"
                                                :id="'seemore-' + publicConsultation.id"
                                                class="seemore"
                                        >{{ publicConsultation.resumen.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="publicConsultation.resumen.length > seeMoreLimitText"
                                                class="text-primary seemore-trigger"
                                                href
                                                @click="seeMoreToggle(publicConsultation.id)"
                                                :id="'myBtn-' + publicConsultation.id"
                                        >{{ $t('proyectos.contenido.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/public_consultation/' + publicConsultation.id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20" v-if="totalPublicConsultations > publicConsultations.length">
                        <button @click="loadMore" class="vld-parent btn btn-secondary btn-block">{{ $t('consultas_publicas.contenido.cargar') }}
                            <loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div class="py-50 text-center border mb-2" v-if="totalPublicConsultations === 0 && !loadBtnSearch">{{ $t('consultas_publicas.contenido.no_hay_resultados') }}</div>
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
        name: 'PublicConsultations',
        components: {
            Multiselect,
            Loading
        },
        data() {
            return {
                publicConsultations: [],
                totalPublicConsultations: 0,
                query: '',
                selectedEstados: [],
                estados: [
                    { id: 1, value: 1, label: null },
                    { id: 2, value: 0, label: null }
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
                this.estados.forEach(estado => {
                    estado.label = this.$t('consultas_publicas.contenido.estado.opciones').find(estadoTrans => estadoTrans.id === estado.id).label;
                });

                this.optionsSort.forEach(option => {
                    option.label = this.$t('consultas_publicas.contenido.orden.opciones').find(optionTrans => optionTrans.id === option.id).label;
                });

                if(this.$route.query.query) {
                    this.query = this.$route.query.query;
                }

                if(this.$route.query['estados[]']) {
                    if(Array.isArray(this.$route.query['estados[]'])) {
                        let preSelectedEstadosValues = this.$route.query['estados[]'];
                        preSelectedEstadosValues.forEach(preSelectedEstadoValue => {
                            let selectedEstado = this.estados.find(estado => estado.value == preSelectedEstadoValue);
                            if(selectedEstado) {
                                this.selectedEstados.push(selectedEstado);
                            }
                        });
                    } else {
                        let preSelectedEstado = this.estados.find(estado => estado.value == this.$route.query['estados[]']);
                        if(preSelectedEstado) {
                            this.selectedEstados.push(preSelectedEstado);
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
                    objects_types: ['consultations'],
                    terms_id: this.selectedTerms.map(selectedTerm => selectedTerm.id),
                    limit: this.limit,
                    offset: this.offset
                };
                if(this.currentSort) {
                    searchParams.order = this.currentSort.type;
                    searchParams.order_by = this.currentSort.field;
                }
                if(this.selectedEstados.length === 1 && this.selectedEstados.find(selectedEstado => selectedEstado.value === 0)) {
                    searchParams.is_available_voting = 0;
                } else if(this.selectedEstados.length > 0 && !this.selectedEstados.find(selectedEstado => selectedEstado.value === 0)) {
                    searchParams.is_available_voting = 1;
                }

                axios
                    .get('/search', {
                        params: searchParams
                    })
                    .then(res => {
                        this.publicConsultations = this.publicConsultations.concat(res.data.results);
                        this.totalPublicConsultations = res.data.total_results;
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadBtnSearch = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            search() {
                if(this.publicConsultations.length > 0) {
                    this.publicConsultations = [];
                    this.totalPublicConsultations = 0;
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
            getPublicConsultationImgUrl(publicConsultationId, publicConsultationImage) {
                if(publicConsultationImage) {
                    return (API_URL + '/api/storage/consultations/' + publicConsultationId + '/' + publicConsultationImage);
                }
                return null;
            },
            clearFilters() {
                this.query = '';
                this.selectedEstados = [];
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
            getIsAvailableVoting(publicConsultationId) {
                let publicConsultation = this.publicConsultations.find(publicConsultation => publicConsultation.id === publicConsultationId);
                let votingStartDate = this.$moment.utc(publicConsultation.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(publicConsultation.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();

                return publicConsultation.estado === 1 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
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

<style>
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
</style>
