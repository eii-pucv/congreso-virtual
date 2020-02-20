<template>
    <div class="col-12 px-0">
        <div class="container px-0">
            <div class="header-img default-proposal-img"></div>
            <div v-if="proposal.type === 1" class="top-left ml-10 mt-10" style="max-width: 80%;">
                <a @click="voteProjectProposal(proposal)" class="btn btn-success text-white font-20"><span class="btn-text"><font-awesome-icon icon="thumbs-up"/></span></a>
                <label class="text-white align-text-middle ml-15 my-0">{{ $t('propuesta.componentes.header.apoyar1') }}</label>
            </div>
            <div v-if="proposal.type === 2" class="top-left ml-10 mt-10" style="max-width: 80%;">
                <a @click="voteProjectProposal(proposal)" class="btn text-white btn-warning font-20"><span class="btn-text"><font-awesome-icon icon="thumbs-up"/></span></a>
                <label class="text-white align-text-middle ml-15 my-0">{{ $t('propuesta.componentes.header.apoyar2') }}</label>
            </div>
            <a class="btn text-white bg-indigo-light-2 top-right mr-10 mt-10 font-20" data-toggle="modal" :data-target="'#myModal'+ proposal.id"><span class="btn-text"><font-awesome-icon icon="share-square"/></span></a>
            <h5 class="col-12 hk-sec-title bottom text-white pb-20">
                <div class="row mx-0">
                    <div class="col-12">
                        <small>
                            {{ $t('propuesta.componentes.header.datos.boletin') }}: {{ proposal.boletin }}
                            <v-popover>
                                <fontAwesomeIcon class="tooltip-target" icon="info-circle"></fontAwesomeIcon>
                                <template slot="popover">
                                    <p>{{ $t('propuesta.componentes.header.datos.popover_boletin') }}</p>
                                </template>
                            </v-popover>
                        </small>
                    </div>
                    <p class="col-12 mt-15">
                        {{ proposal.titulo }}
                    </p>
                </div>
                <div class="row mx-0 mt-40">
                    <div class="col-12 mb-10">
                        <small class="pt-15" style="margin:0px; font-size: 16px;">
                            <i class="fas fa-user"></i>
                            {{ $t('propuesta.componentes.header.datos.persona') }}: {{ proposal.user.name }} {{ proposal.user.surname }}
                        </small>
                    </div>
                </div>
                <div class="row mx-0">
                    <div class="col-8 mb-5">
                        <small class="pt-15" style="margin:0px; font-size: 16px;">
                            <i class="fas fa-landmark"></i>
                            {{ $t('propuesta.componentes.header.datos.autoria') }}: {{ proposal.autoria }} 
                        </small>
                    </div>
                    <div class="col-4 mb-5 text-right">
                        <small class="pt-15" style="margin:0px; font-size: 16px;">
                            <i class="fas fa-calendar-check"></i>
                            {{ $t('propuesta.componentes.header.datos.inicio') }}: {{ new Date(toLocalDate(proposal.fecha_ingreso)) | moment($t('componentes.moment.formato_sin_hora')) }}
                        </small>
                    </div>
                </div>
            </h5>
        </div>
        <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"   >
            <div v-if="proposal.type === 1" class="d-block font-20 text-center text-white" style="padding:0px">
                <div class="bg-indigo-light-1">
                    {{ $t('propuesta.componentes.header.proyecto_ley.titulo') }}
                    <span>
                        <v-popover>
                            <fontAwesomeIcon class="tooltip-target b3 font-18" icon="question-circle"></fontAwesomeIcon>
                            <template slot="popover">
                                <p>
                                    {{ $t('propuesta.componentes.header.proyecto_ley.popover') }}
                                </p>
                            </template>
                        </v-popover>
                    </span>
                </div>
                <div class="row mx-10 align-items-center justify-content-center">
                    <div class="col-10">
                        <p class="my-10" :class="mode==='dark'?'':'text-primary '">
                            <strong>{{ $t('propuesta.componentes.header.proyecto_ley.strong') }} </strong>{{ proposal.petitions }} {{ $t('propuesta.componentes.header.proyecto_ley.de') }} 100
                        </p>
                        <div class="progress mb-10">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.petitions/100*100) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="proposal.type === 2" class="d-block font-20 text-center text-white" style="padding:0px">
                <div class="bg-yellow-dark-1">
                    {{ $t('propuesta.componentes.header.urgencia.titulo') }}
                    <span>
                        <v-popover>
                            <fontAwesomeIcon class="tooltip-target b3 font-18" icon="question-circle"></fontAwesomeIcon>
                            <template slot="popover">
                                <p>
                                    {{ $t('propuesta.componentes.header.urgencia.popover') }}
                                </p>
                            </template>
                        </v-popover>
                    </span>
                </div>
                <div class="row align-items-center justify-content-center">
                    <div class="col-10">
                        <p class="text-primary my-10">
                            <strong>{{ $t('propuesta.componentes.header.urgencia.strong') }} </strong>{{ proposal.urgencies }} {{ $t('propuesta.componentes.header.urgencia.de') }} {{ maxPetitions }}
                        </p>
                        <div class="progress mb-10">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.urgencies/maxPetitions * 100) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" :id="'myModal'+ proposal.id">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('compartir') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span><i class="fa fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <social-sharing
                                :url="APP_URL + '/proposal/' + proposal.id"
                                :title="'Congreso Virtual: '+ proposal.titulo"
                                :description="proposal.titulo"
                                :quote="proposal.titulo"
                                hashtags="congresovirtual"
                                inline-template
                        >
                            <div>
                                <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                    <i class="fa fa-envelope"></i> Email
                                </network>
                                <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                    <span class="fa fa-facebook"></span> Facebook
                                </network>
                                <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                    <i class="fa fa-linkedin"></i> LinkedIn
                                </network>
                                <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                    <i class="fa fa-twitter"></i> Twitter
                                </network>
                                <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </network>
                            </div>
                        </social-sharing>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $t('cerrar') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import SocialSharing from 'vue-social-sharing';
import { APP_URL } from '../../data/globals';
import axios from "../../backend/axios";

export default {
    name: 'ProposalHeader',
    props: {
        proposal: Object
    },
    components: {
        FontAwesomeIcon,
        SocialSharing
    },
    data() {
        return {
            maxPetitions: 100,
            mode: String,
            APP_URL
        }
    },
    async mounted() {
        if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
            this.mode = 'dark';
        } else {
            this.mode = 'light';
        }

        axios
            .get('/settings?key=max_necessary_petitions')
            .then(res => {
                if(res.data[0] !== undefined) {
                    this.maxPetitions = JSON.parse(res.data[0].value).number_petitions;
                }
            });
    },
    methods: {
        voteProjectProposal(proposal) {
            if(this.isLoggedIn){
            axios
                .post('/urgencies', {
                    urgency: proposal.type,
                    proposal_id: proposal.id,
                    user_id: this.userData.id
                })
                .then(res => {
                    if (proposal.type == 1) {
                        proposal.petitions++;
                    }
                    else {
                        proposal.urgencies++;
                    }
                    this.$toastr("success", "Su solicitud fue considerada", "Apoyo enviado");
                })
                .catch(() => {
                    this.$toastr("warning", "Existe guardado un voto de urgencia tuyo para esta propuesta", "Ya votaste esta urgencia");
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
        }
    }
}
</script>