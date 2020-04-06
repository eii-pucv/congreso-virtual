<template>
    <div class="col-12 px-0">
        <div class="container px-0">
            <img
                    class="header-img embed-responsive-item default-consultation-img"
                    :src="getImgUrl()"
            />
            <div class="top-left" style="max-width: 80%;">
                <router-link
                        v-for="term in publicConsultation.terms"
                        :key="term.id"
                        :to="{ path: '/public_consultations', query: { 'terms_id[]': term.id } }"
                        class="badge badge-dark font-12 p-1 m-1"
                >
                    {{ term.value }}
                </router-link>
            </div>
            <a
                    class="btn text-white bg-indigo-light-2 top-right mr-10 mt-10 font-20"
                    data-toggle="modal"
                    :data-target="'#myModal' + publicConsultation.id"
            >
                <i class="fas fa-share-square"></i>
            </a>
            <h5 class="col-12 hk-sec-title col-12 bottom text-white pb-20">
                <p class="col-12">
                    {{ publicConsultation.titulo }}
                </p>
                <div class="row mx-0 mt-15">
                    <p class="col-md-6 my-10">
                        <i class="fas fa-calendar-check"></i>
                        {{ $t('consulta.componentes.header.inicio') }}: {{ new Date(votingStartDate) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                    </p>
                    <p class="col-md-6 my-10">
                        <i class="fas fa-calendar-times"></i>
                        {{ $t('consulta.componentes.header.fin') }}: {{ new Date(votingEndDate) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                    </p>
                </div>
            </h5>
        </div>
        <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''">
            <div class="d-block font-20 text-center text-white">
                <div v-if="publicConsultation.estado === 1 && isAvailableVoting" class="bg-indigo-light-1">
                    {{ $t('consulta.componentes.header.votacion_abierta') }}
                    <v-popover>
                        <span class="tooltip-target font-18"><i class="fas fa-question-circle"></i></span>
                        <template slot="popover">
                            <p>{{ $t('consulta.componentes.header.popover') }}</p>
                        </template>
                    </v-popover>
                </div>
                <div v-else class="bg-red-dark-3">
                    {{ $t('consulta.componentes.header.votacion_cerrada') }}
                    <v-popover>
                        <span class="tooltip-target font-18"><i class="fas fa-question-circle"></i></span>
                        <template slot="popover">
                            <p>{{ $t('consulta.componentes.header.popover') }}</p>
                        </template>
                    </v-popover>
                </div>
            </div>
            <div class="text-center ma-5">
                <div
                        class="col-4 col-md-2 card mx-md-50 pa-5 my-0 agree"
                        v-bind:class="{ 'agree-blocked': !isAvailableVoting, 'agree-selected': userVotedAgree }"
                        @click="addOrEditVote(0)"
                        style="display: inline-block;"
                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                >
                    <span class="d-block">{{ $t('votos.a_favor') }}</span>
                    <span class="col-6 font-30"><i class="fas fa-thumbs-up"></i></span>
                    <span class="col-6 display-6">{{ publicConsultation.votos_a_favor }}</span>
                </div>
                <div
                        class="col-4 col-md-2 card mx-md-50 pa-5 my-0 disagree"
                        v-bind:class="{ 'disagree-blocked': !isAvailableVoting, 'disagree-selected': userVotedDisagree }"
                        @click="addOrEditVote(1)"
                        style="display: inline-block;"
                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                >
                    <span class="d-block">{{ $t('votos.en_contra') }}</span>
                    <span class="col-6 font-30"><i class="fas fa-thumbs-down"></i></span>
                    <span class="col-6 display-6">{{ publicConsultation.votos_en_contra }}</span>
                </div>
            </div>
        </div>
        <div class="modal" :id="'myModal' + publicConsultation.id">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('compartir') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <social-sharing
                                :url="APP_URL + '/public_consultation/' + publicConsultation.id"
                                :title="'Congreso Virtual: '+ publicConsultation.titulo"
                                :description="publicConsultation.titulo"
                                :quote="publicConsultation.titulo"
                                hashtags="congresovirtual"
                                inline-template
                        >
                            <div>
                                <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                    <i class="fas fa-envelope"></i> Email
                                </network>
                                <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                    <i class="fab fa-facebook-square"></i> Facebook
                                </network>
                                <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                    <i class="fab fa-linkedin"></i> LinkedIn
                                </network>
                                <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                    <i class="fab fa-twitter"></i> Twitter
                                </network>
                                <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
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
    import SocialSharing from 'vue-social-sharing';
    import { API_URL } from '../../backend/data_server';
    import { APP_URL } from '../../data/globals';
    import axios from '../../backend/axios';

    export default {
        components: {
            SocialSharing
        },
        name: 'PublicConsultationHeader',
        props: {
            publicConsultation: Object,
        },
        data() {
            return {
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.publicConsultation.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.publicConsultation.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                userVotedAgree: false,
                userVotedDisagree: false,
                vote: null,
                mode: String,
                APP_URL
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.setIsAvailableVoting();

            if(this.$store.getters.isLoggedIn) {
                this.getUserVote();
            }
        },
        methods: {
            setIsAvailableVoting() {
                if(this.$store.getters.userData && this.$store.getters.userData.rol === 'ADMIN') {
                    this.isAvailableVoting = this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                } else {
                    this.isAvailableVoting = this.publicConsultation.estado === 1 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                }
            },
            getUserVote() {
                let userId = JSON.parse(localStorage.user).id;
                axios
                    .get('/consultations/' + this.publicConsultation.id + '/vote', {
                        params: {
                            user_id: userId
                        }
                    })
                    .then(res => {
                        this.vote = res.data;
                        if(this.vote.vote === 0) {
                            this.toggleAgree();
                        } else if(this.vote.vote === 1) {
                            this.toggleDisagree();
                        }
                    });
            },
            toggleAgree() {
                this.userVotedAgree = true;
                this.userVotedDisagree = false;
            },
            toggleDisagree() {
                this.userVotedDisagree = true;
                this.userVotedAgree = false;
            },
            addOrEditVote(voteValue) {
                this.currentMoment = this.$moment().local();
                this.setIsAvailableVoting();

                if(this.$store.getters.isLoggedIn) {
                    if(this.isAvailableVoting) {
                        if(this.vote) {
                            axios
                                .put('/votes/' + this.vote.id, {
                                    'vote': voteValue
                                })
                                .then(() => {
                                    if(this.vote.vote === 0) {
                                        this.publicConsultation.votos_a_favor -= 1;
                                    } else if(this.vote.vote === 1) {
                                        this.publicConsultation.votos_en_contra -= 1;
                                    }

                                    if(voteValue === 0) {
                                        this.publicConsultation.votos_a_favor += 1;
                                        this.toggleAgree();
                                    } else if(voteValue === 1) {
                                        this.publicConsultation.votos_en_contra += 1;
                                        this.toggleDisagree();
                                    }

                                    this.vote.vote = voteValue;
                                    this.$toastr('success', 'Tu voto fue cambiado con éxito', 'Voto actualizado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido cambiar tu voto', 'Error');
                                });
                        } else {
                            axios
                                .post('/consultations/' + this.publicConsultation.id + '/vote', {
                                    'vote': voteValue
                                })
                                .then(res => {
                                    this.vote = res.data.data;
                                    if(voteValue === 0) {
                                        this.publicConsultation.votos_a_favor += 1;
                                        this.toggleAgree();
                                    } else if(voteValue === 1) {
                                        this.publicConsultation.votos_en_contra += 1;
                                        this.toggleDisagree();
                                    }

                                    this.$toastr('success', 'Tu voto fue registrado con éxito', 'Voto guardado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido registrar tu voto', 'Error');
                                });
                        }
                    } else {
                        this.$toastr('warning', 'No se pueden realizar votaciones en esta consulta pública', 'Fuera de plazo o consulta inactiva');
                    }
                } else {
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder votar, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            getImgUrl() {
                if(this.publicConsultation.imagen) {
                    return (API_URL + '/api/storage/consultations/' + this.publicConsultation.id + '/' + this.publicConsultation.imagen);
                }
                return null;
            }
        }
    };
</script>
