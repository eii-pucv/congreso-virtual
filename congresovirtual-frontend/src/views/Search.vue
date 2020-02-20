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
                        <label class="font-weight-bold">{{ $t('search.filtrar') }}</label>
                        <div v-for="objectType in objectTypes" class="input-group" :key="objectType.id">
                            <div class="custom-control custom-checkbox">
                                <input
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
                                >{{ objectType.label }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ $t('search.ordenar') }}</label>
                        <select
                                @change="verFiltro"
                                v-model="orderBy"
                                class="form-control custom-select d-block"
                                id="filterCommentBy"
                        >
                            <option value="1">{{ $t('search.tipo_objeto.titulo') }}</option>
                            <option value="2">{{ $t('search.fecha') }}</option>
                            <option value="3">{{ $t('search.votos') }}</option>
                        </select>
                    </div>
                    <div class="form-group" v-for="(taxonomy, index) in taxonomies" :key="taxonomy.id">
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
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('search.resultados') }}: {{ totalResults }}</span>
                    <div class="btn-group btn-group-toggle btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn"
                                type="button"
                                :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >{{ $t('search.limpiar') }}</button>
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
                    <div class="row ma-5">
                        <div class="card-columns">
                            <div v-for="(object, index) in results" :key="'resultado-' + index">
                                <div
                                        v-if="object._object_type === 'projects'"
                                        class="card border-primary"
                                        :class="mode==='dark'?'border-white':''"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <img
                                            class="card-img-top embed-responsive-item default-project-img"
                                            :src="getImgUrl(object.id, object.imagen, object._object_type)"
                                            style="object-fit: cover;"
                                    />
                                    <div class="card-header card-header-action">
                                        {{ $t('search.proyecto') }}
                                        <div class="d-flex align-items-center card-action-wrap">
                                            <a href="#" class="inline-block refresh mr-15">
                                                <i class="fa icon-like"></i>
                                                {{ object.votos_a_favor }}
                                            </a>
                                            <a href="#" class="inline-block refresh">
                                                <i class="fa icon-dislike"></i>
                                                {{ object.votos_en_contra }}
                                            </a>
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
                                        <router-link :to="'/project/' + object.id" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                    </div>
                                </div>
                                <div
                                        v-if="object._object_type === 'ideas'"
                                        class="card"
                                        :class="mode==='dark'?'border-white':''"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <div class="card-header">{{ $t('search.idea') }}</div>
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
                                        <router-link
                                                :to="'/project/' + object.project_id"
                                                class="btn btn-primary"
                                        >{{ $t('participar') }}</router-link>
                                    </div>
                                </div>
                                <div
                                        v-if="object._object_type === 'articles'"
                                        class="card"
                                        :class="mode==='dark'?'border-white':''"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <div class="card-header card-header-action" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('search.articulo') }}
                                        <div class="d-flex align-items-center card-action-wrap">
                                            <a href="#" class="inline-block refresh mr-15">
                                                <i class="fa icon-like"></i>
                                                {{ object.votos_a_favor }}
                                            </a>
                                            <a href="#" class="inline-block refresh">
                                                <i class="fa icon-dislike"></i>
                                                {{ object.votos_en_contra }}
                                            </a>
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
                                            >{{ object.detalle.substring( seeMoreLimitText) }}</span>
                                            <span
                                                    v-if="object.detalle.length > seeMoreLimitText"
                                                    class="seemore-trigger"
                                                    href
                                                    @click="seeMoreToggle(index)"
                                                    :id="'myBtn-' + index"
                                                    :class="mode==='dark'?'':'text-primary '"
                                            >{{ $t('search.ver_mas') }}</span>
                                        </p>
                                        <router-link :to="'/project/' + object.project_id" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                    </div>
                                </div>
                                <div
                                        v-if="object._object_type === 'consultations'"
                                        class="card"
                                        :class="mode==='dark'?'border-white':''"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <img
                                            class="card-img-top embed-responsive-item default-consultation-img"
                                            :src="getImgUrl(object.id, object.imagen, object._object_type)"
                                            style="object-fit: cover;"
                                    />
                                    <div class="card-header card-header-actions">
                                        {{ $t('search.consulta') }}
                                        <div class="d-flex align-items-center card-action-wrap">
                                            <a href="#" class="inline-block refresh mr-15">
                                                <i class="fa icon-like"></i>
                                                {{ object.votos_a_favor }}
                                            </a>
                                            <a href="#" class="inline-block refresh">
                                                <i class="fa icon-dislike"></i>
                                                {{ object.votos_en_contra }}
                                            </a>
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
                                        <router-link :to="'/public_consultation/' + object.id " class="btn btn-primary">{{ $t('participar') }}</router-link>
                                    </div>
                                </div>
                                <div
                                        v-if="object._object_type === 'proposals'"
                                        class="card"
                                        :class="mode==='dark'?'border-white':''"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <img class="card-img-top embed-responsive-item default-proposal-img" />
                                    <div class="card-header">{{ $t('search.propuesta') }}</div>
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
                                        <router-link :to="'/proposal/' + object.id" class="btn btn-primary">{{ $t('participar') }}</router-link>
                                    </div>
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
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Search',
        components: {
            Loading
        },
        data: function() {
            return {
                mode: String,
                query: '',
                selectedObjectTypes: [],
                selectedTerms: [],
                orderBy: Number,
                results: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                taxonomies: [],
                objectTypes: [
                    { id: 1, value: 'projects', label: null },
                    { id: 2, value: 'ideas', label: null },
                    { id: 3, value: 'articles', label: null },
                    { id: 4, value: 'consultations', label: null },
                    { id: 5, value: 'proposals', label: null }
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
                this.objectTypes.forEach(objectType => {
                    objectType.label = this.$t('search.tipo_objeto.opciones').find(objectTypeTrans => objectTypeTrans.id === objectType.id).label;
                });

                if(this.$route.query.query) {
                    this.query = this.$route.query.query;
                }

                if(this.$route.query['terms_id[]']) {
                    this.selectedTermsList.push(this.$route.query['terms_id[]']);
                }
            },
            getResults() {
                axios
                    .get('/search', {
                        params: {
                            query: this.query,
                            objects_types: this.selectedObjectTypes,
                            terms_id: this.selectedTermsList,
                            limit: this.limit,
                            offset: this.offset
                        }
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
            getTaxonomies() {
                axios
                    .get('/taxonomies')
                    .then(res => {
                        for(let i = 0; i < res.data.length; i++) {
                            let taxo = res.data.results[i];
                            if(taxo.value !== 'Categorías') {
                                this.taxonomies.push(taxo);
                            }
                        }
                    });
            },
            addSelectedTerms(terms) {
                this.selectedTerms.push.apply(this.selectedTerms, terms);
                this.selectedTerms = this.selectedTerms.filter(this.onlyUnique);
            },
            onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            },
            seeMoreToggle: function(elementIndex) {
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
            },
            clearFilters: function() {
                this.query =  '';
                this.selectedObjectTypes = [];
                this.selectedTerms = [];
                this.limit = 10;
            },
            filterByType() {
                return (this.results = this.results.sort((a, b) => {
                    let comp = 0;
                    if(a._object_type > b._object_type) {
                        comp = 1;
                    } else if(a._object_type < b._object_type) {
                        comp = -1;
                    }
                    return comp;
                }));
            },
            filterByDate() {
                return (this.results = this.results.sort((a, b) => {
                    let fake_date = new Date("January 1, 1970 00:00:00");
                    let res_a = new Date();
                    let res_b = new Date();
                    let comp = 0;

                    if(a.fecha_inicio) {
                        res_a = new Date(a.fecha_inicio);
                    } else {
                        res_a = fake_date;
                    }
                    if(b.fecha_inicio) {
                        res_b = new Date(b.fecha_inicio);
                    } else {
                        res_b = fake_date;
                    }

                    if(res_a > res_b) {
                        comp = 1;
                    } else if(res_a < res_b) {
                        comp = -1;
                    }
                    return comp;
                }));
            },
            filterDesc() {
                return (this.results = this.results.sort((a, b) => {
                    let res_a = a.abstencion + a.votos_a_favor + a.votos_en_contra || 0;
                    let res_b = b.abstencion + b.votos_a_favor + b.votos_en_contra || 0;
                    let comp = 0;
                    if(res_a < res_b) {
                        comp = 1;
                    } else if(res_a > res_b) {
                        comp = -1;
                    }
                    return comp;
                }));
            }
        },
        computed: {
            selectedTermsList() {
                let processedList = [];
                this.selectedTerms.forEach(item => {
                    processedList = processedList.concat(item);
                });
                return processedList;
            },
            verFiltro() {
                if(this.orderBy === 1) {
                    this.filterByType();
                } else if(this.orderBy === 2) {
                    this.filterByDate();
                } else if(this.orderBy === 3) {
                    this.filterDesc();
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

    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>