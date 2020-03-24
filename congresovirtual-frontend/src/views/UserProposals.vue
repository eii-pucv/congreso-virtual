<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row mt-20">
                <div class="col-md-3">
                    <h2 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas_usuario.contenido.titulo') }}</h2>
                    <div class="form-group">
                        <div class="input-group">
                            <input
                                v-model="query"
                                v-on:keyup.enter="search"
                                type="text"
                                class="form-control"
                                :placeholder="$t('propuestas_usuario.contenido.buscar_placeholder')"
                                :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                        </div>   
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas_usuario.contenido.filtrar') }}</h5>
                        <div class="input-group">
                            <select
                                @change="filterProposals"
                                v-model="filterBy"
                                class="form-control custom-select d-block"
                                id="filter"
                            >
                            <option v-for="(filter, index) in filterOptions" :key="'filtrar_' + index" :value="filter">{{ filter.label }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas_usuario.contenido.ordenar') }}</h5>
                        <div class="input-group">
                            <select
                                v-model="sortBy"
                                class="form-control custom-select d-block"
                                id="sort"
                            >
                            <option v-for="(sort, index) in sortOptions" v-if="sort.type === 0 || sort.type === filterBy.type" :key="'filtrar_' + index" :value="sort">{{ sort.label }}</option>
                            </select>
                        </div>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas_usuario.contenido.resultados') }}: {{ proposals.length }}</span>
                    <div class="btn-group btn-group-toggle btn-block mt-10 mb-20">
                        <button
                            @click="clearFilters"
                            class="btn btn-outline-primary"
                            type="button"
                            :class="mode==='dark'?'btn-outline-light':'btn-outline-primary'"
                            :style="mode==='dark'?'color: #fff':''"
                        >{{ $t('propuestas_usuario.contenido.limpiar') }}</button>
                        <button class="vld-parent btn btn-primary" @click="search">{{ $t('propuestas_usuario.contenido.buscar') }}
                            <loading
                                :active.sync="loadBtnSearch"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div class="btn-group btn-block">
                        <router-link :to="{ path: '/proposals' }" class="btn btn-primary">{{ $t('propuestas_usuario.contenido.boton_propuestas_sugeridas') }}</router-link>
                    </div>
                    <div class="alert alert-info mt-1 text-center">
                        <p>{{ $t('propuestas_usuario.contenido.mensaje_propuestas_sugeridas') }}</p>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="ma-5">
                        <div v-for="proposal in proposals" :key="proposal.id">
                            <div class="card proposal-card" :style="mode==='dark'?'background: #080035; color: #fff; border-color: #fff':''">
                                <div class="card-header">
                                    <p class="card-title text-justify font-weight-bold ellipsis" :class="mode==='dark'?'':'text-primary '">
                                        {{ proposal.titulo }}
                                    </p>
                                    <a v-if="proposal.type === 1" href="#" class="badge badge-primary font-12">{{ $t('propuestas_usuario.contenido.tipo_1') }}</a>
                                    <a v-if="proposal.type === 2" href="#" class="badge badge-warning font-12">{{ $t('propuestas_usuario.contenido.tipo_2') }}</a>
                                </div>
                                <div class="card-body py-0">
                                    <p>
                                        <strong>{{ $t('home.contenido.propuesta.persona') }} </strong> {{ proposal.user.name }} {{ proposal.user.surname }}
                                    </p>
                                    <p>
                                        <strong>{{ $t('home.contenido.propuesta.boletin') }} </strong>{{ proposal.boletin }}
                                    </p>
                                    <p class="ellipsis">
                                        <strong>{{ $t('home.contenido.propuesta.autoria') }} </strong>{{ proposal.autoria }}
                                    </p>
                                    <p>
                                        <strong>{{ $t('home.contenido.propuesta.fecha_ingreso') }} </strong>{{ new Date(toLocalDate(proposal.fecha_ingreso)) | moment($t('componentes.moment.formato_sin_hora')) }}
                                    </p>
                                </div>
                                <div class="px-20 pb-20" v-if="proposal.type === 1">
                                    <p>
                                        <strong>{{ $t('home.contenido.propuesta.apoyos') }} </strong>{{ proposal.petitions }} {{ $t('propuestas_usuario.contenido.de') }} {{ maxPetitions }}
                                    </p>
                                    <div class="progress mt-5">
                                        <div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{ width: (proposal.petitions/maxPetitions * 100) + '%' }"></div>
                                    </div>
                                </div>
                                <div class="px-20 pb-20" v-if="proposal.type === 2">
                                     <p>
                                        <strong>{{ $t('home.contenido.propuesta.apoyos') }} </strong>{{ proposal.urgencies }} {{ $t('propuestas_usuario.contenido.de') }} {{ maxPetitions }}
                                    </p>
                                    <div class="progress mt-5">
                                        <div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{ width: (proposal.urgencies/maxPetitions * 100) + '%' }"></div>
                                    </div>
                                </div>
                                <div class="btn-group-vertical">
                                    <a class="font-1"> </a>
                                    <div class="btn-group mt-auto">
                                        <router-link
                                                :to="{ path: '/proposal/' + proposal.id }"
                                                class="btn btn-primary text-white font-12"
                                        >
                                            <i class="fas fa-eye"></i> {{ $t('propuestas_usuario.contenido.ver') }}
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20" v-if="totalProposals > proposals.length">
                        <button @click="loadMore" class="vld-parent btn btn-secondary btn-block">{{ $t('propuestas_usuario.contenido.cargar') }}
                            <loading
                                :active.sync="loadBtnLoadMore"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div class="py-50 text-center border mb-2" v-if="totalProposals === 0 && !loadBtnSearch">{{ $t('propuestas_usuario.contenido.no_hay_resultados') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axios from '../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'UserProposals',
        components: {
            Loading
        },
        data() {
            return {
                mode: String,
                query: '',
                filterBy: {
                    label: '',
                    name: '',
                    type: ''
                },
                sortBy: {
                    label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_1'),
                    name: 'fecha_ingreso',
                    value: 'DESC'
                },
                proposals: [],
                loadBtnSearch: false,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#ffffff',
                limit: 10,
                offset: 0,
                totalProposals: 0,
                maxPetitions: 100,
                sortOptions: [{
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_1'),
                        name: 'fecha_ingreso',
                        value: 'DESC',
                        type: 0
                    },{
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_2'), 
                        name: 'fecha_ingreso',
                        value:'ASC',
                        type: 0
                    }, {
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_3'), 
                        name: 'petitions',
                        value:'DESC',
                        type: 1
                    }, {
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_4'), 
                        name: 'petitions',
                        value:'ASC',
                        type: 1
                    }, {
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_5'), 
                        name: 'urgencies',
                        value:'DESC',
                        type: 2
                    }, {
                        label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_6'),
                        name: 'urgencies',
                        value:'ASC',
                        type: 2
                    }],
                filterOptions: [{
                        label: this.$t('propuestas_usuario.contenido.opciones_filtrar.opcion_1'),
                        name: 'petitions',
                        type: 1
                    },{
                        label: this.$t('propuestas_usuario.contenido.opciones_filtrar.opcion_2'), 
                        name: 'urgencies',
                        type: 2
                    }],
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            if(this.$route.query['type']) {
                this.filterBy = this.filterOptions.find(option => option.type == this.$route.query['type'])
            }
            this.search()
        },
        methods: {
            computed: {
                isLoggedIn: function () {
                    return this.$store.getters.isLoggedIn;
                },
                userData: function () {
                    return this.$store.getters.userData;
                },
            },
            getProposalsResults() {
                axios
                    .get('/proposals', {
                        params: {
                            query: this.query,
                            is_public: 1,
                            type: this.filterBy.type,
                            limit: this.limit,
                            offset: this.offset,
                            order_by: this.sortBy.name  ? this.sortBy.name : this.filterBy.name,
                            order: this.sortBy.value
                        }
                    })
                    .then(res => {
                        this.proposals = this.proposals.concat(res.data.results);
                        this.totalProposals = res.data.total_results;
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadBtnSearch = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            search() {
                if(this.proposals.length > 0) {
                    this.proposals = [];
                    this.totalProposals = 0;
                    this.offset = 0;
                }
                this.loadBtnSearch = true;
                this.getProposalsResults();
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getProposalsResults();
            },
            clearFilters() {
                this.query = '';
                this.filterBy = {
                    label: '',
                    name: '',
                    type: ''
                };
                this.sortBy = {
                    label: this.$t('propuestas_usuario.contenido.opciones_ordenar.opcion_1'),
                    name: 'fecha_ingreso',
                    value: 'DESC'
                };
                this.limit = 10;
                this.search();
            },
            toLocalDate(date) {
                return this.$moment.utc(date, 'YYYY-MM-DD').local();
            }
        },
    }
</script>
<style>
    .ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        line-height: 16px;
        height: auto;
        max-height: 47px;
    }
    .proposals-introduction {
        height: 18em;
    }
    .proposal-card {
        height: 24em;
    }
    @media (max-width: 1199px) {
        .proposals-introduction {
            height: 22em;
        }
    }
    @media (max-width: 991px) {
        .proposals-introduction {
            height: 28em;
        }
    }
    @media (max-width: 767px) {
        .proposals-introduction {
            height: auto;
        }
        .proposal-card {
            height: auto;
        }
    }
</style>