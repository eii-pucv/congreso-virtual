<template>
    <div :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="jumbotron text-center" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <div class="container">
                <h1 class="jumbotron-heading" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.titulo') }}</h1>
                <br>
                <p class="lead mb-0" :class="mode==='dark'?'':'text-muted'" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.parrafo1') }}
                </p>
                <p class="lead mb-0" :class="mode==='dark'?'':'text-muted'" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.parrafo2') }}
                </p>
            </div>
        </div>
        <nav class="container px-0" aria-label="breadcrumb">
            <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <li class="breadcrumb-item">
                    <a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.breadcumb.inicio') }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.breadcumb.propuestas') }}
                </li>
            </ol>
        </nav>
        <div class="container hk-sec-wrapper" :style="mode === 'dark' ? 'background: rgb(12, 1, 80);' : ''">
            <div class="row mx-0">
                <div class="col-md-4">
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.buscar') }}</h5>
                        <input
                                v-model="query"
                                v-on:keyup.enter="search"
                                type="text"
                                class="form-control"
                                :placeholder="$t('propuestas.contenido.busqueda')"
                                :style="mode==='dark'?'background: #080035; color: #fff':''"
                        />
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.fecha') }}</h5>
                        <DatePicker
                                v-model="fechaIngreso"
                                :config="dateOptions"
                                :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                        ></DatePicker>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.autores') }}</h5>
                        <input
                                v-model="autores"
                                type="text"
                                class="form-control"
                                :placeholder="$t('propuestas.contenido.autores_placeholder')"
                                :style="mode==='dark'?'background: #080035; color: #fff':''"
                        />
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.origen') }}</h5>
                        <select
                                v-model="origen"
                                class="form-control custom-select d-block"
                        >
                            <option v-for="(source, index) in sourceProposals" :key="'origen_tipo_' + index" :value="source">{{ source }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h5 class="mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.ordenar') }}</h5>
                        <select
                                @change="sort"
                                v-model="ordenar"
                                class="form-control custom-select d-block"
                        >
                            <option v-for="(sort, index) in sortOptions" :key="'ordenar_' + index" :value="sort.value">{{ sort.name }}</option>
                        </select>
                    </div>
                    <span class="mt-10" :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.resultados') }}: {{ proposals.length }}</span>
                    <div class="btn-group btn-block mt-10 mb-20">
                        <button
                                @click="clearFilters"
                                class="btn btn-outline-primary"
                                type="button"
                                :class="mode==='dark'?'btn-light':'btn-primary'"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('propuestas.contenido.limpiar') }}
                        </button>
                        <button @click="search" class="vld-parent btn btn-primary">{{ $t('propuestas.contenido.buscar') }}
                            <Loading
                                    :active.sync="loadBtnSearch"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></Loading>
                        </button>
                    </div>
                </div>
                <div class="col-md-8" v-if="!loadBtnSearch && proposals.length > 0">
                    <div v-for="(proposal, index) in proposals.slice(0, limit)" :key="'proposal-' + index">
                        <div class="card" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); border-color: #fff;' : ''">
                            <div class="card-header">
                                <p class="card-title text-justify font-weight-bold" :class="mode === 'dark' ? '' : 'text-primary'">
                                    {{ proposal.PROYSUMA }}
                                </p>
                            </div>
                            <div class="card-body pt-0">
                                <p>
                                    <strong>{{ $t('propuestas.contenido.boletin') }}</strong>
                                    {{ proposal.PROYNUMEROBOLETIN }}
                                </p>
                                <p>
                                    <strong>{{ $t('propuestas.contenido.autoria') }}</strong>
                                    {{ proposal.AUTORES }}
                                </p>
                                <p>
                                    <strong>{{ $t('propuestas.contenido.fecha') }}</strong>
                                    {{ proposal.FECHAINGRESO }}
                                </p>
                            </div>
                            <div class="btn-group-vertical">
                                <a class="font-1"></a>
                                <div class="btn-group mt-auto">
                                    <button
                                            @click="createUserProposal(proposal, 1)"
                                            class="btn btn-primary font-12"
                                    >
                                        <i class="fas fa-vote-yea"></i> {{ $t('propuestas.contenido.incluir') }}
                                    </button>
                                    <button
                                            @click="createUserProposal(proposal, 2)"
                                            class="btn btn-warning font-12"
                                    >
                                        <i class="fas fa-warning"></i> {{ $t('propuestas.contenido.pedir') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button
                            class="btn btn-secondary btn-block"
                            v-if="limit < proposals.length"
                            @click="loadMore"
                    >
                        {{ $t('propuestas.contenido.cargar') }}
                    </button>
                </div>
                <div v-if="!loadBtnSearch && proposals.length === 0" class="col-md-8 my-30 border d-flex align-self-start">
                    <p class="text-center w-100 h-100 my-50">
                        {{ $t('propuestas.contenido.no_hay_resultados') }}
                    </p>
                </div>
            </div>
        </div>
        <b-modal
                id="modal-new-user-proposal"
                header-bg-variant="primary"
                body-bg-variant="light"
                footer-bg-variant="light"
                header-class="justify-content-center"
                hide-header-close
                no-close-on-backdrop
                centered
        >
            <template v-slot:modal-header>
                <h5 class="text-white">{{ $t('propuestas.contenido.modal.detalle') }}</h5>
            </template>
            <form @submit.prevent="commitCreateUserProposal" id="form-new-proposal">
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="detalle">{{ $t('propuestas.contenido.modal.descripcion') }}</label>
                        <textarea
                                id="detalle"
                                v-model="newUserProposal.detalle"
                                class="form-control"
                                rows="3"
                                required
                        ></textarea>
                    </div>
                </div>
            </form>
            <template v-slot:modal-footer>
                <div class="btn-group w-100">
                    <b-button
                            variant="success"
                            size="sm"
                            type="submit"
                            form="form-new-proposal"
                    >
                        <i class="fas fa-envelope"></i> {{ $t('propuestas.contenido.modal.enviar') }}
                        <Loading
                                :active.sync="loadModalBtn"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="'#ffffff'"
                        ></Loading>
                    </b-button>
                    <b-button
                            variant="danger"
                            size="sm"
                            @click.prevent="abortCreateUserProposal"
                    >
                        <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import { BModal } from 'bootstrap-vue';
    import DatePicker from 'vue-bootstrap-datetimepicker';
    import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
    import axios from '../backend/axios';
    import axioma from 'axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Proposals',
        components: {
            BModal,
            DatePicker,
            Loading
        },
        data: function() {
            return {
                proposals: [],
                auxProposals: [],
                startProposals: [],
                boletines: [],
                query: '',
                fechaIngreso: '',
                autores: '',
                origen: '',
                sourceProposals: [],
                ordenar: 'DESC',
                sortOptions: [
                    {
                        name: this.$t('propuestas.contenido.opciones_ordenar.opcion_1'),
                        value: 'DESC'
                    },
                    {
                        name: this.$t('propuestas.contenido.opciones_ordenar.opcion_2'),
                        value: 'ASC'
                    }
                ],
                limit: 10,
                newUserProposal: {
                    titulo: null,
                    detalle: null,
                    autoria: null,
                    boletin: null,
                    fecha_ingreso: null,
                    type: null
                },
                loadBtnSearch: true,
                loadModalBtn: false,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getAllProposals();
        },
        methods: {
            getAllProposals() {
                axios
                    .get('settings', {
                        params: {
                            key: 'external_api'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            let proposalsUrlList = JSON.parse(res.data[0].value).proposals_url_list;
                            if(proposalsUrlList.length > 0) {
                                let promises = [];
                                proposalsUrlList.forEach(url => {
                                    promises.push(this.getProposalsFromUrl(url));
                                });

                                Promise.all(promises)
                                    .then(() => {
                                        this.proposals = this.auxProposals;
                                        this.removeExistingUserProposals()
                                            .finally(() => {
                                                this.loadBtnSearch = false;
                                            });
                                    });
                            } else {
                                this.loadBtnSearch = false;
                            }
                        } else {
                            this.loadBtnSearch = false;
                        }
                    })
                    .catch(() => {
                        this.loadBtnSearch = false;
                    });
            },
            getProposalsFromUrl(url) {
                return new Promise(resolve => {
                    axioma
                        .create()
                        .get(url)
                        .then(res => {
                            this.auxProposals = this.auxProposals.concat(res.data);
                        })
                        .finally(() => {
                            resolve(true);
                        });
                });
            },
            removeExistingUserProposals() {
                return new Promise(resolve => {
                    axios
                        .get('/proposals', {
                            params: {
                                return_all: 1
                            }
                        })
                        .then(res => {
                            let userProposals = res.data.results;
                            this.boletines = userProposals.map(userProposal => userProposal.boletin);
                            this.proposals = this.proposals.filter(proposal => !this.boletines.includes(proposal.PROYNUMEROBOLETIN));

                            this.sourceProposals = [...new Set(this.proposals.map(proposal => proposal.ORIGEN))];
                            this.startProposals.push(...this.proposals);
                        })
                        .finally(() => {
                            resolve(true);
                        });
                });
            },
            search() {
                let proposalsList = [];
                this.proposals = [...this.startProposals];
                proposalsList = this.proposals.filter(proposal => {
                    if(proposal.PROYSUMA !== undefined
                        && proposal.AUTORES !== undefined
                        && proposal.FECHAINGRESO !== undefined
                        && proposal.ORIGEN !== undefined) {
                        return proposal.PROYSUMA.toLowerCase().includes(this.query.toLowerCase())
                            && proposal.AUTORES.toLowerCase().includes(this.autores.toLowerCase())
                            && proposal.FECHAINGRESO.includes(this.fechaIngreso)
                            && proposal.ORIGEN.includes(this.origen);
                    } else {
                        return false;
                    }
                });
                this.proposals = [...proposalsList];
                this.sort();
            },
            loadMore() {
                let proposalsLength = this.proposals.length;
                if(this.limit < proposalsLength) {
                    if(proposalsLength - this.limit < 10) {
                        this.limit += proposalsLength - this.limit;
                    } else {
                        this.limit += 10;
                    }
                }
            },
            sort() {
                if(this.ordenar === 'DESC') {
                    this.proposals = this.proposals.sort((a, b) => {
                        return new Date(b.PROYFECHAINGRESO) - new Date(a.PROYFECHAINGRESO);
                    });
                } else {
                    this.proposals = this.proposals.sort((a, b) => {
                        return new Date(a.PROYFECHAINGRESO) - new Date(b.PROYFECHAINGRESO);
                    });
                }
            },
            clearFilters() {
                this.query = '';
                this.fechaIngreso = '';
                this.autores = '';
                this.origen = '';
                this.limit = 10;
                this.proposals = [...this.startProposals];
            },
            createUserProposal(proposal, type) {
                this.newUserProposal.titulo = proposal.PROYSUMA;
                this.newUserProposal.autoria = proposal.AUTORES;
                this.newUserProposal.boletin = proposal.PROYNUMEROBOLETIN;
                this.newUserProposal.fecha_ingreso = proposal.FECHAINGRESO.split('/').reverse().join('-');
                this.newUserProposal.type = type;
                this.$bvModal.show('modal-new-user-proposal');
            },
            commitCreateUserProposal() {
                this.loadModalBtn = true;
                if(this.isLoggedIn) {
                    axios
                        .post('/proposals', this.newUserProposal)
                        .then(() => {
                            this.$toastr('success', 'Debes esperar a que ésta sea revisada y aprobada por un administrador antes de que se publique', 'Propuesta creada correctamente');
                        })
                        .catch(() => {
                            this.$toastr('warning', '', 'Alguien ya hizo esta propuesta antes');
                        })
                        .finally(() => {
                            this.clearNewUserProposal();
                            this.loadModalBtn = false;
                            this.$bvModal.hide('modal-new-user-proposal');
                        });
                } else {
                    this.clearNewUserProposal();
                    this.loadModalBtn = false;
                    this.$toastr('warning', '', 'Debes iniciar sesión para poder crear una propuesta');
                }
            },
            abortCreateUserProposal() {
                this.clearNewUserProposal();
                this.$bvModal.hide('modal-new-user-proposal');
            },
            clearNewUserProposal() {
                this.newUserProposal = {
                    titulo: null,
                    detalle: null,
                    autoria: null,
                    boletin: null,
                    fecha_ingreso: null,
                    type: null
                };
            }
        },
        computed: {
            dateOptions() {
                return {
                    //format: this.$t('componentes.moment.formato_editable_sin_hora'),
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                    locale: this.$moment.locale()
                };
            },
            isLoggedIn() {
                return this.$store.getters.isLoggedIn;
            }
        }
    }
</script>
