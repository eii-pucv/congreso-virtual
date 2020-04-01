<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row mt-20">
                <div class="col-md-3 col-sm-12">
                    <h2 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('search.titulo') }}</h2>
                    <div class="form-group">
                        <div class="input-group">
                            <input
                                    v-model="query"
                                    type="text"
                                    class="form-control"
                                    :placeholder="$t('search.buscar_placeholder')"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    v-on:keyup.enter="search"
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('search.filtrar') }}</h5>
                        <div v-for="objectType in objectTypes" class="input-group" :key="objectType.id">
                            <div class="custom-control custom-checkbox">
                                <input
                                        @change="refreshOptionsSortActives"
                                        type="checkbox"
                                        class="custom-control-input"
                                        :name="'check-' + objectType.value"
                                        :id="'check-' + objectType.value"
                                        v-model="selectedObjectTypes"
                                        :value="objectType.value"
                                        checked
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                                <label
                                        class="custom-control-label"
                                        :for="'check-'+ objectType.value"
                                        :style="mode==='dark'?'color: #fff':''"
                                >
                                    {{ objectType.label }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('search.orden.titulo') }}</h5>
                        <select @change="sort" v-model="selectedSortId" class="form-control custom-select d-block">
                            <option
                                    v-for="activeOptionSort in activeOptionsSort"
                                    :key="'option-sort-' + activeOptionSort.id"
                                    :value="activeOptionSort.id"
                            >
                                {{ activeOptionSort.label }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('search.tema_asociado.titulo') }}</h5>
                        <multiselect
                                v-model="selectedTerms"
                                group-values="terms"
                                group-label="value"
                                track-by="id"
                                label="value"
                                :placeholder="$t('search.tema_asociado.seleccionar')"
                                :options="taxonomyTerms"
                                :loading="loadTaxonomyTerms"
                                :multiple="true"
                                :showLabels="false"
                                :limit="5"
                                :limit-text="limitTextTaxonomyTermsMultiselect"
                                :style="mode==='dark'?' color: #fff':''"
                        ></multiselect>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('search.resultados') }}: {{ totalResults }}</span>
                    <div class="btn-group btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn"
                                type="button"
                                :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('search.limpiar') }}
                        </button>
                        <button class="vld-parent btn btn-primary" @click="search">{{ $t('search.buscar') }}
                            <loading
                                    :active.sync="loadBtnSearch"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                </div>
                <div class="col-md-9 col-sm-12">
                    <div class="row">
                        <div v-for="(object, index) in results" :key="'resultado-' + index" class="col-md-6 col-xl-4 pb-4 px-10">
                            <div
                                    v-if="object._object_type === 'projects'"
                                    class="card border-primary h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <img
                                        class="card-img-top embed-responsive-item default-project-img"
                                        :src="getImgUrl(object.id, object.imagen, object._object_type)"
                                        style="object-fit: cover;"
                                />
                                <div>
                                    <router-link
                                            v-if="object.etapa === 1 && getIsAvailableVoting(object)"
                                            :to="{ path: 'projects', query: { 'etapas[]': 1 } }"
                                            class="top-right badge badge-primary font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_general') }}
                                    </router-link>
                                    <router-link
                                            v-else-if="object.etapa === 2 && getIsAvailableVoting(object)"
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
                                <div class="card-header card-header-action">
                                    {{ $t('search.proyecto') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fas icon-like"></i>
                                            {{ object.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fas icon-dislike"></i>
                                            {{ object.votos_en_contra }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ object.titulo }}</h5>
                                    <p
                                            v-if="object.resumen"
                                            class="card-text"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ object.resumen.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                :id="'dots-' + index"
                                        >... </span>
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                :id="'seemore-' + index"
                                                class="seemore"
                                        >{{ object.resumen.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                class="seemore-trigger"
                                                href
                                                @click="seeMoreToggle(index)"
                                                :id="'myBtn-' + index"
                                                :class="mode==='dark'?'':'text-primary '"
                                        >{{ $t('search.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/project/' + object.id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                            <div
                                    v-if="object._object_type === 'ideas'"
                                    class="card h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <div class="card-header card-header-action">
                                    {{ $t('search.idea') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fas icon-like"></i>
                                            {{ object.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fas icon-dislike"></i>
                                            {{ object.votos_en_contra }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.project.titulo }}
                                        <small
                                                class="text-muted"
                                                :style="mode==='dark'?'color: #fff':''"
                                        >{{ object.titulo }}</small>
                                    </h5>
                                    <p v-if="object.detalle" class="card-text" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.detalle.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'dots-' + index"
                                        >... </span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'seemore-' + index"
                                                class="seemore"
                                        >{{ object.detalle.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                class="seemore-trigger"
                                                href
                                                @click="seeMoreToggle(index)"
                                                :id="'myBtn-' + index"
                                                :class="mode==='dark'?'':'text-primary '"
                                        >{{ $t('search.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/project/' + object.project_id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                            <div
                                    v-if="object._object_type === 'articles'"
                                    class="card h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <div class="card-header card-header-action" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('search.articulo') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fas icon-like"></i>
                                            {{ object.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fas icon-dislike"></i>
                                            {{ object.votos_en_contra }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.project.titulo }}
                                        <small
                                                class="text-muted"
                                                :style="mode==='dark'?'color: #fff':''"
                                        >{{ object.titulo }}</small>
                                    </h5>
                                    <p v-if="object.detalle" class="card-text" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.detalle.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'dots-' + index"
                                        >... </span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'seemore-' + index"
                                                class="seemore"
                                        >{{ object.detalle.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                class="seemore-trigger"
                                                href
                                                @click="seeMoreToggle(index)"
                                                :id="'myBtn-' + index"
                                                :class="mode==='dark'?'':'text-primary '"
                                        >{{ $t('search.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/project/' + object.project_id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                            <div
                                    v-if="object._object_type === 'consultations'"
                                    class="card h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <img
                                        class="card-img-top embed-responsive-item default-consultation-img"
                                        :src="getImgUrl(object.id, object.imagen, object._object_type)"
                                        style="object-fit: cover;"
                                />
                                <div>
                                    <router-link
                                            v-if="getIsAvailableVoting(object)"
                                            :to="{ path: 'consultations', query: { 'estado': 1 } }"
                                            class="top-right badge badge-primary font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_abierta') }}
                                    </router-link>
                                    <router-link
                                            v-else
                                            :to="{ path: 'consultations', query: { 'estado': 0 } }"
                                            class="top-right badge badge-danger font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('votacion_cerrada') }}
                                    </router-link>
                                </div>
                                <div class="card-header card-header-action">
                                    {{ $t('search.consulta') }}
                                    <div class="d-flex">
                                        <div class="inline-block text-success mr-15">
                                            <i class="fas icon-like"></i>
                                            {{ object.votos_a_favor }}
                                        </div>
                                        <div class="inline-block text-danger">
                                            <i class="fas icon-dislike"></i>
                                            {{ object.votos_en_contra }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ object.titulo }}</h5>
                                    <p v-if="object.resumen" class="card-text" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.resumen.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                :id="'dots-' + index"
                                        >... </span>
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                :id="'seemore-' + index"
                                                class="seemore"
                                        >{{ object.resumen.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="object.resumen.length > seeMoreLimitText"
                                                class="seemore-trigger"
                                                href
                                                @click="seeMoreToggle(index)"
                                                :id="'myBtn-' + index"
                                                :class="mode==='dark'?'':'text-primary '"
                                        >{{ $t('search.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/public_consultation/' + object.id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                            <div
                                    v-if="object._object_type === 'proposals'"
                                    class="card h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <img class="card-img-top embed-responsive-item default-proposal-img" />
                                <div>
                                    <router-link
                                            v-if="object.type === 1"
                                            :to="{ path: 'user-proposals', query: { 'type': 1 } }"
                                            class="top-right badge badge-primary font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('propuesta.componentes.header.proyecto_ley.titulo') }}
                                    </router-link>
                                    <router-link
                                            v-else-if="object.type === 2"
                                            :to="{ path: 'user-proposals', query: { 'type': 2 } }"
                                            class="top-right badge badge-warning font-12 m-1"
                                            style="opacity: 0.7;"
                                    >
                                        {{ $t('propuesta.componentes.header.urgencia.titulo') }}
                                    </router-link>
                                </div>
                                <div class="card-header card-header-action">
                                    {{ $t('search.propuesta') }}
                                    <div class="d-flex">
                                        <div v-if="object.type === 1" class="inline-block text-primary">
                                            <i class="fas icon-like"></i>
                                            {{ object.petitions }}
                                        </div>
                                        <div v-else-if="object.type === 2" class="inline-block text-warning">
                                            <i class="fas icon-like"></i>
                                            {{ object.urgencies }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ object.titulo }}</h5>
                                    <p v-if="object.detalle" class="card-text" :style="mode==='dark'?'color: #fff':''">
                                        {{ object.detalle.substring(0, seeMoreLimitText) }}
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'dots-' + index"
                                        >... </span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                :id="'seemore-' + index"
                                                class="seemore"
                                        >{{ object.detalle.substring(seeMoreLimitText) }}</span>
                                        <span
                                                v-if="object.detalle.length > seeMoreLimitText"
                                                class="seemore-trigger"
                                                href
                                                @click="seeMoreToggle(index)"
                                                :id="'myBtn-' + index"
                                                :class="mode==='dark'?'':'text-primary '"
                                        >{{ $t('search.ver_mas') }}</span>
                                    </p>
                                    <router-link :to="{ path: '/proposal/' + object.id }" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                </div>
                            </div>
                            <div
                                    v-if="object._object_type === 'pages'"
                                    class="card h-100"
                                    :class="mode==='dark'?'border-white':''"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <img class="card-img-top embed-responsive-item default-proposal-img" />
                                <div class="card-header">{{ $t('search.pagina') }}</div>
                                <div class="card-body">
                                    <h5 class="card-title" :style="mode==='dark'?'color: #fff':''">{{ object.title }}</h5>
                                    <router-link :to="{ path: '/page/' + object.slug }" class="btn btn-primary">{{ $t('ver') }}</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20" v-if="totalResults > results.length">
                        <button @click="loadMore" class="btn btn-secondary btn-block vld-parent">{{ $t('search.cargar') }}
                            <loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div v-if="totalResults === 0 && !loadBtnSearch" class="py-50 text-center border mb-2">{{ $t('search.no_hay_resultados') }}</div>
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
        name: 'Search',
        components: {
            Multiselect,
            Loading
        },
        data: function() {
            return {
                results: [],
                totalResults: 0,
                query: '',
                selectedObjectTypes: [],
                currentSort: null,
                selectedSortId: 0,
                optionsSort: [
                    { id: 0, field: null, type: null, label: null, valid_for: [] },
                    { id: 1, field: '_object_type', type: 'ASC', label: null, valid_for: [] },
                    { id: 2, field: '_object_type', type: 'DESC', label: null, valid_for: [] },
                    { id: 3, field: 'fecha_inicio', type: 'ASC', label: null, valid_for: ['projects', 'consultations'] },
                    { id: 4, field: 'fecha_inicio', type: 'DESC', label: null, valid_for: ['projects', 'consultations'] },
                    { id: 5, field: 'fecha_ingreso', type: 'ASC', label: null, valid_for: ['proposals'] },
                    { id: 6, field: 'fecha_ingreso', type: 'DESC', label: null, valid_for: ['proposals'] },
                    { id: 7, field: 'votos_a_favor', type: 'ASC', label: null, valid_for: ['projects', 'ideas', 'articles', 'consultations'] },
                    { id: 8, field: 'votos_a_favor', type: 'DESC', label: null, valid_for: ['projects', 'ideas', 'articles', 'consultations'] },
                    { id: 9, field: 'votos_en_contra', type: 'ASC', label: null, valid_for: ['projects', 'ideas', 'articles', 'consultations'] },
                    { id: 10, field: 'votos_en_contra', type: 'DESC', label: null, valid_for: ['projects', 'ideas', 'articles', 'consultations'] },
                    { id: 11, field: 'petitions', type: 'ASC', label: null, valid_for: ['proposals'] },
                    { id: 12, field: 'petitions', type: 'DESC', label: null, valid_for: ['proposals'] },
                    { id: 13, field: 'urgencies', type: 'ASC', label: null, valid_for: ['proposals'] },
                    { id: 14, field: 'urgencies', type: 'DESC', label: null, valid_for: ['proposals'] }
                ],
                activeOptionsSort: [],
                selectedTerms: [],
                taxonomyTerms: [],
                limit: 12,
                offset: 0,
                objectTypes: [
                    { id: 1, value: 'projects', label: null },
                    { id: 2, value: 'ideas', label: null },
                    { id: 3, value: 'articles', label: null },
                    { id: 4, value: 'consultations', label: null },
                    { id: 5, value: 'proposals', label: null },
                    { id: 6, value: 'pages', label: null }
                ],
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
                this.objectTypes.forEach(objectType => {
                    objectType.label = this.$t('search.tipo_objeto.opciones').find(objectTypeTrans => objectTypeTrans.id === objectType.id).label;
                });

                this.optionsSort.forEach(option => {
                    option.label = this.$t('search.orden.opciones').find(optionTrans => optionTrans.id === option.id).label;
                });
                this.activeOptionsSort = this.optionsSort;

                if(this.$route.query.query) {
                    this.query = this.$route.query.query;
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
                    objects_types: this.selectedObjectTypes,
                    terms_id: this.selectedTerms.map(selectedTerm => selectedTerm.id),
                    limit: this.limit,
                    offset: this.offset
                };
                if(this.currentSort) {
                    searchParams.order = this.currentSort.type;
                    searchParams.order_by = this.currentSort.field;
                }

                axios
                    .get('/search', {
                        params: searchParams
                    })
                    .then(res => {
                        this.results = this.results.concat(res.data.results);
                        this.totalResults = res.data.total_results;
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadBtnSearch = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            search() {
                if(this.results.length > 0) {
                    this.results = [];
                    this.totalResults = 0;
                    this.offset = 0;
                }
                this.loadBtnSearch = true;
                this.getResults();
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getResults();
            },
            refreshOptionsSortActives() {
                if(this.selectedObjectTypes.length === 0) {
                    this.activeOptionsSort = this.optionsSort;
                } else {
                    this.activeOptionsSort = [];
                    this.optionsSort.forEach(optionSort => {
                        if(optionSort.valid_for.length === 0) {
                            this.activeOptionsSort.push(optionSort);
                        } else {
                            if(optionSort.valid_for.some(objectTypeValid => !!this.selectedObjectTypes.find(selectedObjectType => selectedObjectType === objectTypeValid))) {
                                this.activeOptionsSort.push(optionSort);
                            }
                        }
                    });
                }
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
            getImgUrl(objectId, objectImage, objectType) {
                if(objectImage) {
                    if(objectType === 'projects') {
                        return (API_URL + '/api/storage/projects/' + objectId + '/' + objectImage);
                    } else if(objectType === 'consultations') {
                        return (API_URL + '/api/storage/consultations/' + objectId + '/' + objectImage);
                    }
                }
                return null;
            },
            clearFilters: function() {
                this.query =  '';
                this.selectedObjectTypes = [];
                this.activeOptionsSort = this.optionsSort;
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
            getIsAvailableVoting(object) {
                let votingStartDate = this.$moment.utc(object.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(object.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();

                if(object._object_type === 'projects') {
                    return object.is_enabled && object.etapa !== 3 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
                } else if(object._object_type === 'consultations') {
                    return object.estado === 1 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
                }
            },
            seeMoreToggle(elementIndex) {
                let dots = document.getElementById("dots-" + elementIndex);
                let moreText = document.getElementById("seemore-" + elementIndex);
                let btnText = document.getElementById("myBtn-" + elementIndex);

                if(dots.style.display === "none") {
                    dots.style.display = "inline";
                    btnText.innerHTML = "Ver más";
                    moreText.style.display = "none";
                } else {
                    dots.style.display = "none";
                    btnText.innerHTML = "Ver menos";
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
</style>