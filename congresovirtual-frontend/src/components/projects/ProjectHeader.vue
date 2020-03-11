<template>
    <div class="col-12 px-0">
        <div class="container px-0">
            <img
                    class="header-img embed-responsive-item default-project-img"
                    :src="getImgUrl()"
            />
            <div class="top-left" style="max-width: 80%;">
                <a
                        v-for="term in project.terms"
                        :key="term.id"
                        :href="'/projects?terms_id[]=' + term.id"
                        class="badge badge-dark font-12 p-1 m-1"
                >
                    {{ term.value }}
                </a>
            </div>
            <a
                    class="btn text-white bg-indigo-light-2 top-right mr-10 mt-10 font-20"
                    data-toggle="modal"
                    :data-target="'#myModal' + project.id"
            >
                <span class="btn-text"><font-awesome-icon icon="share-square"/></span>
            </a>
            <h5 class="col-12 hk-sec-title bottom text-white pb-20">
                <p class="col-12">
                    {{ project.titulo }}
                </p>
                <div class="row mx-0 mt-15">
                    <small class="col-md-6 my-10">
                        {{ $t('proyecto.componentes.header.boletin') }}: {{ project.boletin }}
                        <v-popover>
                            <fontAwesomeIcon class="tooltip-target" icon="info-circle"></fontAwesomeIcon>
                            <template slot="popover">
                                <p>{{ $t('proyecto.componentes.header.popover_boletin') }}</p>
                            </template>
                        </v-popover>
                    </small>
                    <small class="col-md-4 offset-md-2 my-10">
                        <a class="row justify-content-center"><font-awesome-icon icon="clock" class="mr-5"></font-awesome-icon> {{ $t('tiempo') }}</a>
                        <Countdown v-if="isAvailableVoting" class="row pt-15 text-center" :date="votingEndDate"></Countdown>
                        <Countdown v-else class="row pt-15 text-center text-red" :date="votingEndDate"></Countdown>
                    </small>
                </div>
            </h5>
        </div>
        <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''">
            <div class="d-block font-20 text-center text-white">
                <div v-if="project.etapa === 1 && isAvailableVoting" class="bg-indigo-light-1">
                    {{ $t('votacion_general') }}
                    <v-popover>
                        <fontAwesomeIcon class="tooltip-target font-18" icon="question-circle"></fontAwesomeIcon>
                        <template slot="popover">
                            <p>{{ $t('proyecto.componentes.header.popover_general') }}</p>
                        </template>
                    </v-popover>
                </div>
                <div v-else-if="project.etapa === 2 && isAvailableVoting" class="bg-green">
                    {{ $t('votacion_particular') }}
                    <v-popover>
                        <fontAwesomeIcon class="tooltip-target font-18" icon="question-circle"></fontAwesomeIcon>
                        <template slot="popover">
                            <p>{{ $t('proyecto.componentes.header.popover_particular') }}</p>
                        </template>
                    </v-popover>
                </div>
                <div v-else class="bg-red-dark-3">
                    {{ $t('votacion_cerrada') }}
                    <v-popover>
                        <fontAwesomeIcon class="tooltip-target font-18" icon="question-circle"></fontAwesomeIcon>
                        <template slot="popover">
                            <p>{{ $t('proyecto.componentes.header.popover_cerrada') }}</p>
                        </template>
                    </v-popover>
                </div>
            </div>
            <div class="text-center ma-5">
                <div
                        class="col-4 col-sm-2 card mx-sm-50 pa-5 my-0 agree"
                        v-bind:class="{ 'agree-blocked': !isAvailableVoting, 'agree-selected': userVotedAgree }"
                        @click="addOrEditVote(0)"
                        style="display: inline-block;"
                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                >
                    <span class="d-block">{{ $t('votos.a_favor') }}</span>
                    <span class="col-6 font-30"><i class="fa fa-thumbs-up"></i></span>
                    <span class="col-6 display-6">{{ project.votos_a_favor }}</span>
                </div>
                <div
                        class="col-4 col-sm-2 card mx-sm-50 pa-5 my-0 disagree"
                        v-bind:class="{ 'disagree-blocked': !isAvailableVoting, 'disagree-selected': userVotedDisagree }"
                        @click="addOrEditVote(1)"
                        style="display: inline-block;"
                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                >
                    <span class="d-block">{{ $t('votos.en_contra') }}</span>
                    <span class="col-6 font-30"><i class="fa fa-thumbs-down"></i></span>
                    <span class="col-6 display-6">{{ project.votos_en_contra }}</span>
                </div>
                <div
                        class="col-4 col-sm-2 card mx-sm-50 pa-5 my-0 abstention"
                        v-bind:class="{ 'abstention-blocked': !isAvailableVoting, 'abstention-selected': userVotedAbstention }"
                        @click="addOrEditVote(2)"
                        style="display: inline-block;"
                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                >
                    <span class="d-block">{{ $t('votos.abstencion') }}</span>
                    <span class="col-6 font-30"><fontAwesomeIcon icon="minus-circle"/></span>
                    <span class="col-6 display-6">{{ project.abstencion }}</span>
                </div>
            </div>
        </div>
        <div class="modal" :id="'myModal' + project.id">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('compartir') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span><i class="fa fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <social-sharing :url="APP_URL + '/project/' + project.id"
                            :title="'Congreso Virtual: '+ project.titulo"
                            :description="project.titulo"
                            :quote="project.titulo"
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
    import Countdown from "./Countdown"
    import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
    import { library } from "@fortawesome/fontawesome-svg-core";
    import SocialSharing from 'vue-social-sharing';
    import { faInfoCircle } from "@fortawesome/free-solid-svg-icons/faInfoCircle";
    import { faShareSquare } from "@fortawesome/free-solid-svg-icons/faShareSquare";
    import { API_URL } from "../../backend/data_server";
    import { APP_URL } from '../../data/globals';
    import { faMinusCircle } from "@fortawesome/free-solid-svg-icons/faMinusCircle";
    import { bus } from '../../main';
    import axios from "../../backend/axios";

    library.add(faInfoCircle, faShareSquare, faMinusCircle);
    export default {
        components: {
            SocialSharing,
            FontAwesomeIcon,
            Countdown
        },
        name: 'ProjectHeader',
        props: {
            project: Object
        },
        data() {
            return {
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                userHasVoted: false,
                userVotedAgree: false,
                userVotedDisagree: false,
                userVotedAbstention: false,
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
                this.isFirstVote();
            }
        },
        methods: {
            isFirstVote() {
                axios
                    .get('/users/' + JSON.parse(localStorage.user).id + '/votes')
                    .then(res => {
                        let votes = res.data.results;
                        if(votes.length > 0) {
                            this.userHasVoted = true;
                        }
                    });
            },
            setIsAvailableVoting() {
                if(this.$store.getters.userData && this.$store.getters.userData.rol === 'ADMIN') {
                    this.isAvailableVoting = this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                } else {
                    this.isAvailableVoting = this.project.is_enabled && this.project.etapa !== 3 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                }
            },
            async getUserVote() {
                let userId = JSON.parse(localStorage.user).id;
                try {
                    const res = await axios.get(`/projects/${this.project.id}/vote`, {
                        params: {
                            user_id: userId
                        }
                    });
                    this.vote = res.data;

                    if(this.vote.vote === 0) {
                        this.toggleAgree();
                    } else if(this.vote.vote === 1) {
                        this.toggleDisagree();
                    } else if(this.vote.vote === 2) {
                        this.toggleAbstention();
                    }
                } catch (err) {
                    // this.$toastr('warning', '', 'No se ha encontradon votos del usuario relacionados con el proyecto');
                }
            },
            toggleAgree() {
                this.userVotedAgree = true;
                this.userVotedDisagree = false;
                this.userVotedAbstention = false;
            },
            toggleDisagree() {
                this.userVotedDisagree = true;
                this.userVotedAgree = false;
                this.userVotedAbstention = false;
            },
            toggleAbstention() {
                this.userVotedAbstention = true;
                this.userVotedAgree = false;
                this.userVotedDisagree = false;
            },
            addOrEditVote(voteValue) {
                this.currentMoment = this.$moment().local();
                this.setIsAvailableVoting();

                if(this.$store.getters.isLoggedIn) {
                    if(this.isAvailableVoting) {
                        if(this.vote) {
                            axios
                                .put('/votes/' + this.vote.id, {
                                    vote: voteValue
                                })
                                .then(() => {
                                    if(this.vote.vote === 0) {
                                        this.project.votos_a_favor -= 1;
                                    } else if(this.vote.vote === 1) {
                                        this.project.votos_en_contra -= 1;
                                    } else if(this.vote.vote === 2) {
                                        this.project.abstencion -= 1;
                                    }

                                    if(voteValue === 0) {
                                        this.project.votos_a_favor += 1;
                                        this.toggleAgree();
                                    } else if(voteValue === 1) {
                                        this.project.votos_en_contra += 1;
                                        this.toggleDisagree();
                                    } else if(voteValue === 2) {
                                        this.project.abstencion += 1;
                                        this.toggleAbstention();
                                    }
                                    this.vote.vote = voteValue;
                                    this.$toastr('success', 'Tu voto fue cambiado con éxito', 'Voto actualizado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido cambiar tu voto', 'Error');
                                });
                        } else {
                            axios
                                .post('/projects/' + this.project.id + '/vote', {
                                    vote: voteValue
                                })
                                .then(res => {
                                    this.vote = res.data.data;
                                    if(voteValue === 0) {
                                        this.project.votos_a_favor += 1;
                                        this.toggleAgree();
                                    } else if(voteValue === 1) {
                                        this.project.votos_en_contra += 1;
                                        this.toggleDisagree();
                                    } else if(voteValue === 2) {
                                        this.project.abstencion += 1;
                                        this.toggleAbstention();
                                    }

                                    if(!this.userHasVoted) {
                                        bus.$emit('tour');
                                        // $('#modalMessage').modal('show');
                                    }
                                    this.$toastr('success', 'Tu voto fue registrado con éxito', 'Voto guardado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido registrar tu voto', 'Error');
                                });
                        }
                    } else {
                        this.$toastr('warning', 'No se pueden realizar votaciones en este proyecto de ley', 'Fuera de plazo, proyecto deshabilitado o en etapa de cierre de votación');
                    }    
                } else {
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder votar, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            getImgUrl() {
                if(this.project.imagen) {
                    return API_URL + '/api/storage/projects/' + this.project.id + '/' + this.project.imagen;
                }
                return null;
            }
        }
    }
</script>
