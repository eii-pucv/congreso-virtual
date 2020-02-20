<template>
    <div class="row">
        <div class="col-md-6 py-4">
            <div class="row">
                <div class="col-12 proposals-introduction vld-parent">
                    <h3 class="mb-4 text-center mx-5" :style="mode==='dark'?'color: #fff':''">{{ $t('home.contenido.incorporacion_proyectos.titulo') }}</h3>
                    <p class="lead mb-4 text-justify" :style="mode==='dark'?'color: #fff':''">
                        {{ $t('home.contenido.incorporacion_proyectos.descripcion') }}
                    </p>
                    <div v-if="loadPetitionProposals" style="height: 100px;">
                        <loading
                                :active.sync="loadPetitionProposals"
                                :is-full-page="fullPage"
                                :color="color"
                        ></loading>
                    </div>
                </div>
                <div v-if="!loadPetitionProposals" class="col-12">
                    <h5 class="text-center mb-20" v-if="this.petitionProposals.length === 0">{{ $t('home.contenido.sin_datos') }}</h5>
                    <div v-for="proposal in petitionProposals" :key="proposal.id">
                        <div class="card proposal-card" :style="mode==='dark'?'background: #080035; color: #fff; border-color: #fff':''">
                            <div class="card-header">
                                <p class="card-title text-justify font-weight-bold ellipsis" :class="mode==='dark'?'':'text-primary '">
                                    {{ proposal.titulo }}
                                </p>
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
                            <div class="px-20 pb-20">
                                <p>
                                    <strong>{{ $t('home.contenido.propuesta.apoyos') }} </strong>{{ proposal.petitions }} de {{ maxPetitions }}
                                </p>
                                <div class="progress mt-5">
                                    <div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.petitions/maxPetitions * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="btn-group-vertical">
                                <a class="font-1"> </a>
                                <div class="btn-group mt-auto">
                                    <button @click="voteProposal(proposal)" class="btn btn-primary text-white font-12"><font-awesome-icon icon="vote-yea"/> <span class="btn-text"> {{ $t('home.contenido.incorporacion_proyectos.boton_apoyo') }}</span></button>
                                    <a role="button" class="btn btn-secondary text-white font-12" :href="'/proposal/' + proposal.id"><font-awesome-icon icon="eye"/> <span class="btn-text"> {{ $t('home.contenido.incorporacion_proyectos.ver') }}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a :href="'/proposals'" class="btn btn-primary btn-block mb-15" style="text-transform: inherit">{{ $t('home.contenido.boton_proyectos') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 py-4">
            <div class="row">
                <div class="col-12 proposals-introduction vld-parent">
                    <h3 class="mb-4 text-center mx-5" :style="mode==='dark'?'color: #fff':''">{{ $t('home.contenido.urgencias_proyectos.titulo') }}</h3>
                    <p class="lead mb-4 text-justify" :style="mode==='dark'?'color: #fff':''">
                        {{ $t('home.contenido.urgencias_proyectos.descripcion') }}
                    </p>
                    <div v-if="loadUrgencyProposals" style="height: 100px;">
                        <loading
                                :active.sync="loadUrgencyProposals"
                                :is-full-page="fullPage"
                                :color="color"
                        ></loading>
                    </div>
                </div>
                <div v-if="!loadUrgencyProposals" class="col-12">
                    <h5 class="text-center mb-20" v-if="this.urgencyProposals.length === 0">{{ $t('home.contenido.sin_datos') }}</h5>
                    <div v-for="proposal in this.urgencyProposals" :key="proposal.id">
                        <div class="card proposal-card" :style="mode==='dark'?'background: #080035; color: #fff; border-color: #fff':''">
                            <div class="card-header">
                                <p class="card-title text-justify font-weight-bold ellipsis" :class="mode==='dark'?'':'text-primary '">
                                    {{ proposal.titulo }}
                                </p>
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
                            <div class="px-20 pb-20">
                                <p>
                                    <strong>{{ $t('home.contenido.propuesta.apoyos') }} </strong>{{ proposal.urgencies }} de {{ maxPetitions }}
                                </p>
                                <div class="progress mt-5">
                                    <div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{ width: (proposal.urgencies/maxPetitions * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="btn-group-vertical">
                                <a class="font-1"> </a>
                                <div class="btn-group mt-auto">
                                    <button @click="voteProposal(proposal)" class="btn btn-warning text-primary font-12">
                                        <i class="fa fa-warning"></i> <span class="btn-text"> {{ $t('home.contenido.urgencias_proyectos.boton_apoyo') }}</span>
                                    </button>
                                    <a :href="'/proposal/' + proposal.id" class="btn btn-primary text-white font-12"><i class="fas fa-eye"></i><span class="btn-text"> {{ $t('home.contenido.urgencias_proyectos.ver') }}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a :href="'/proposals'" class="btn btn-warning btn-block mb-15 text-primary" style="text-transform: inherit">{{ $t('home.contenido.boton_proyectos') }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProposalsHomeList',
        components: {
            Loading
        },
        data: function () {
            return {
                petitionProposals: [],
                urgencyProposals: [],
                maxPetitions: 100,
                loadPetitionProposals: true,
                loadUrgencyProposals: true,
                fullPage: false,
                color: "#000000",
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getPetitionProposals();
            this.getUrgencyProposals();

            axios
                .get('/settings?key=max_necessary_petitions')
                .then(res => {
                    if(res.data[0] !== undefined) {
                        this.maxPetitions = JSON.parse(res.data[0].value).number_petitions;
                    }
                });
        },
        methods: {
            getPetitionProposals() {
                axios
                    .get('/proposals', {
                        params: {
                            'is_public': 1,
                            'type': 1,
                            'order_by': 'petitions',
                            'order': 'DESC',
                            'limit': 3
                        }
                    })
                    .then(res => {
                        this.petitionProposals = res.data.results;
                    })
                    .finally(() => {
                        this.loadPetitionProposals = false;
                    });
            },
            getUrgencyProposals() {
                axios
                    .get('/proposals', {
                        params: {
                            'is_public': 1,
                            'type': 2,
                            'order_by': 'urgencies',
                            'order': 'DESC',
                            'limit': 3
                        }
                    })
                    .then(res => {
                        this.urgencyProposals = res.data.results;
                    })
                    .finally(() => {
                        this.loadUrgencyProposals = false;
                    });
            },
            voteProposal(proposal) {
                if(this.isLoggedIn){
                    axios
                        .post("/urgencies", {
                            urgency: proposal.type,
                            proposal_id: proposal.id,
                            user_id: this.userData.id
                        })
                        .then(res => {
                            if(proposal.type === 1) {
                                proposal.petitions++;
                            } else {
                                proposal.urgencies++;
                            }
                            this.$toastr("success", "Su solicitud fue considerada", "Apoyo enviado");
                        })
                        .catch(() => {
                            // 1: inclusión a Congreso Virtual - 2: petición de Urgencia
                            let type = proposal.type === 1 ? 'inclusión a Congreso Virtual' : 'Urgencia'
                            this.$toastr("warning", `Ya existe su voto para apoyar la ${type} de esta propuesta`, "Ya votaste esta urgencia");
                        });
                } else {
                    this.$toastr("warning", "", "Debes iniciar sesión");
                }
            },
            toLocalDate(date) {
                return this.$moment.utc(date, 'YYYY-MM-DD').local();
            }
        },
        computed: {
            isLoggedIn: function () {
                return this.$store.getters.isLoggedIn;
            },
            userData: function () {
                return this.$store.getters.userData;
            },
        }
    };
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