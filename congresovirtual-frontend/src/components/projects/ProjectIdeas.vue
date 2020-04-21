<template>
    <div>
        <div v-if="loadIdeas" class="vld-parent" style="height: 500px;">
            <Loading
                    :active.sync="loadIdeas"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="color"
            ></Loading>
        </div>
        <div v-if="!loadIdeas" 
            id="project-ideas-container"
            :data-intro="$t('proyecto.ruta_guiada.pasos.paso_4')" 
            data-step="4"
            data-position="right"
            data-scrollTo="tooltip"
        >
            <div v-if="ideas.length > 0">
                <div v-for="idea in ideas" :key="idea.id" class="shadow-sm p-3 mb-5 rounded my-25" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <h5 :style="mode==='dark'?'color: #fff':''">{{ idea.titulo }}</h5>
                    <div class="row no-gutters">
                        <div class="col-sm-7 pr-5">
                            <p class="text-justify">{{ idea.detalle }}</p>
                        </div>
                        <div class="col-sm-5">
                            <div v-if="getChartData(idea.id)">
                                <pie-chart :id="'canvasI' + idea.id" :data="getChartData(idea.id)" :key="keyChartComponent"></pie-chart>
                            </div>
                            <div v-else class="row no-gutters">
                                <div class="col pa-5">
                                    <div
                                            class="card border-success shadow ma-5"
                                            :class="mode==='dark'?'dark':'light'"
                                            :id="'canvasI' + idea.id"
                                    >
                                        <div class="card-body">
                                            <p class="card-text">{{ $t('proyecto.componentes.ideas.sin_votos') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center my-10">
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 agree"
                                        v-bind:class="{ 'agree-blocked': !isAvailableVoting, 'agree-selected': getUserVotedAgreeValue(idea.id) }"
                                        @click="addOrEditVote(0, idea.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><i class="fas fa-thumbs-up"></i></span>
                                    <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                                    <span class="d-block display-6">{{ idea.votos_a_favor }}</span>
                                </div>
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 disagree"
                                        v-bind:class="{ 'disagree-blocked': !isAvailableVoting, 'disagree-selected': getUserVotedDisagreeValue(idea.id) }"
                                        @click="addOrEditVote(1, idea.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><i class="fas fa-thumbs-down"></i></span>
                                    <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                                    <span class="d-block display-6">{{ idea.votos_en_contra }}</span>
                                </div>
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 abstention"
                                        v-bind:class="{ 'abstention-blocked': !isAvailableVoting, 'abstention-selected': getUserVotedAbstentionValue(idea.id) }"
                                        @click="addOrEditVote(2, idea.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><i class="fas fa-minus-circle"></i></span>
                                    <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                                    <span class="d-block display-6">{{ idea.abstencion }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <IdeaComments v-if="idea.id" :project="project" :idea_id="idea.id"></IdeaComments>
                </div>
                <div class="mb-20" v-if="totalResults > ideas.length">
                    <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('proyecto.contenido.cargar') }}
                        <loading
                                :active.sync="loadBtnLoadMore"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="color"
                        ></loading>
                    </button>
                </div>
            </div>
            <div v-else class="row no-gutters">
                <div class="col-12">
                    <div class="card border-success shadow ma-5" :class="mode==='dark'?'dark':'light'">
                        <div class="card-body">
                            <p class="card-text">{{ $t('proyecto.contenido.sin_ideas') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import IdeaComments from '../../views/Comments';
    import PieChart from '../../PieChart.js';
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectIdeas',
        components: {
            IdeaComments,
            PieChart,
            Loading
        },
        props: {
            project: Object
        },
        data() {
            return {
                ideas: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                userVotedAgree: [],
                userVotedDisagree: [],
                userVotedAbstention: [],
                votes: [],
                chartsData: [],
                loadIdeas: true,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#000000',
                mode: String,
                keyChartComponent: 0
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.setIsAvailableVoting();
            this.getIdeas();
        },
        methods: {
            setIsAvailableVoting() {
                if(this.$store.getters.userData && this.$store.getters.userData.rol === 'ADMIN') {
                    this.isAvailableVoting = this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                } else {
                    this.isAvailableVoting = this.project.is_enabled && this.project.etapa !== 3 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                }
            },
            getIdeas() {
                axios
                    .get('/projects/' + this.project.id + '/ideas', {
                        params: {
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.ideas = this.ideas.concat(res.data.results);
                        this.totalResults = res.data.total_results;
                        this.offset += res.data.returned_results;

                        this.setChartsData();
                        if(this.$store.getters.isLoggedIn) {
                            this.getUserVotes();
                        }
                    })
                    .finally(() => {
                        this.loadIdeas = false;
                    });
            },
            getUserVotes() {
                let userId = this.$store.getters.userData.id;
                axios
                    .get('/votes', {
                        params: {
                            user_id: userId,
                            return_all: 1
                        }
                    })
                    .then(res => {
                        let votes = res.data.results;
                        this.ideas.forEach(idea => {
                            votes.forEach(vote => {
                                if(vote.idea_id === idea.id) {
                                    this.votes.push(vote);
                                    if(vote.vote === 0) {
                                        this.toggleAgree(idea.id);
                                    } else if(vote.vote === 1) {
                                        this.toggleDisagree(idea.id);
                                    } else if(vote.vote === 2) {
                                        this.toggleAbstention(idea.id);
                                    }
                                }
                            });
                        });
                    });
            },
            toggleAgree(ideaId) {
                this.userVotedAgree = this.userVotedAgree.filter(element => element.idea_id !== ideaId);
                this.userVotedAgree.push({ idea_id: ideaId, value: true });

                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.idea_id !== ideaId);
                this.userVotedDisagree.push({ idea_id: ideaId, value: false });

                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.idea_id !== ideaId);
                this.userVotedAbstention.push({ idea_id: ideaId, value: false });
            },
            toggleDisagree(ideaId) {
                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.idea_id !== ideaId);
                this.userVotedDisagree.push({ idea_id: ideaId, value: true });

                this.userVotedAgree = this.userVotedAgree.filter(element => element.idea_id !== ideaId);
                this.userVotedAgree.push({ idea_id: ideaId, value: false });

                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.idea_id !== ideaId);
                this.userVotedAbstention.push({ idea_id: ideaId, value: false });
            },
            toggleAbstention(ideaId) {
                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.idea_id !== ideaId);
                this.userVotedAbstention.push({ idea_id: ideaId, value: true });

                this.userVotedAgree = this.userVotedAgree.filter(element => element.idea_id !== ideaId);
                this.userVotedAgree.push({ idea_id: ideaId, value: false });

                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.idea_id !== ideaId);
                this.userVotedDisagree.push({ idea_id: ideaId, value: false });
            },
            getUserVotedAgreeValue(ideaId) {
                let userVotedAgree = this.userVotedAgree.find(element => element.idea_id === ideaId);
                if(userVotedAgree) {
                    return userVotedAgree.value;
                }
                return false;
            },
            getUserVotedDisagreeValue(ideaId) {
                let userVotedDisagree = this.userVotedDisagree.find(element => element.idea_id === ideaId);
                if(userVotedDisagree) {
                    return userVotedDisagree.value;
                }
                return false;
            },
            getUserVotedAbstentionValue(ideaId) {
                let userVotedAbstention = this.userVotedAbstention.find(element => element.idea_id === ideaId);
                if(userVotedAbstention) {
                    return userVotedAbstention.value;
                }
                return false;
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getIdeas();
            },
            addOrEditVote(voteValue, ideaId) {
                this.currentMoment = this.$moment().local();
                this.setIsAvailableVoting();

                let idea = this.ideas.find(idea => idea.id === ideaId);
                let vote = this.votes.find(vote => vote.idea_id === idea.id);

                if(this.$store.getters.isLoggedIn) {
                    if(this.isAvailableVoting) {
                        if(vote) {
                            axios
                                .put('/votes/' + vote.id, {
                                    vote: voteValue
                                })
                                .then(() => {
                                    if(vote.vote === 0) {
                                        idea.votos_a_favor -= 1;
                                    } else if(vote.vote === 1) {
                                        idea.votos_en_contra -= 1;
                                    } else if(vote.vote === 2) {
                                        idea.abstencion -= 1;
                                    }

                                    if(voteValue === 0) {
                                        idea.votos_a_favor += 1;
                                        this.toggleAgree(idea.id);
                                    } else if(voteValue === 1) {
                                        idea.votos_en_contra += 1;
                                        this.toggleDisagree(idea.id);
                                    } else if(voteValue === 2) {
                                        idea.abstencion += 1;
                                        this.toggleAbstention(idea.id);
                                    }

                                    vote.vote = voteValue;
                                    this.setChartData(idea);
                                    this.forceRerender();
                                    this.$emit('updateStackedChartVotos', vote.id);
                                    this.$toastr('success', 'Tu voto fue cambiado con éxito', 'Voto actualizado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido cambiar tu voto', 'Error');
                                });
                        } else {
                            axios
                                .post('/ideas/' + idea.id + '/vote', {
                                    vote: voteValue
                                })
                                .then(res => {
                                    vote = res.data.data;
                                    this.votes.push(vote);
                                    if(voteValue === 0) {
                                        idea.votos_a_favor += 1;
                                        this.toggleAgree(idea.id);
                                    } else if(voteValue === 1) {
                                        idea.votos_en_contra += 1;
                                        this.toggleDisagree(idea.id);
                                    } else if(voteValue === 2) {
                                        idea.abstencion += 1;
                                        this.toggleAbstention(idea.id);
                                    }

                                    if(vote.gamification_result && vote.gamification_result.rewards.length > 0) {
                                        this.$store.dispatch('hasNewNotifications', true);
                                        this.$toastr('success', this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.cuerpo'), this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.titulo'));
                                    }

                                    this.setChartData(idea);
                                    this.forceRerender();
                                    this.$emit('updateStackedChartVotos', vote.id);
                                    this.$toastr('success', 'Tu voto fue registrado con éxito', 'Voto guardado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido registrar tu voto', 'Error');
                                });
                        }
                    } else {
                        this.$toastr('warning', 'No se pueden realizar votaciones en esta idea fundamental', 'Fuera de plazo, proyecto deshabilitado o en etapa "Votación Cerrada"');
                    }
                } else {
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder votar, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            setChartsData() {
                this.ideas.forEach(idea => {
                    this.setChartData(idea);
                });
            },
            setChartData(idea) {
                this.chartsData = this.chartsData.filter(element => element.idea_id !== idea.id);

                if(idea.votos_a_favor + idea.votos_en_contra + idea.abstencion > 0) {
                    this.chartsData.push({
                        idea_id: idea.id,
                        labels: [
                            this.$t('votos.a_favor'),
                            this.$t('votos.en_contra'),
                            this.$t('votos.abstencion')
                        ],
                        datasets: [
                            {
                                label: 'Proyecto de Ley',
                                backgroundColor: ['#9de19c', '#e19c9c', '#b1b1b1'],
                                data: [
                                    Math.trunc((idea.votos_a_favor / (idea.votos_a_favor + idea.votos_en_contra + idea.abstencion)) * 100),
                                    Math.trunc((idea.votos_en_contra / (idea.votos_a_favor + idea.votos_en_contra + idea.abstencion)) * 100),
                                    Math.trunc((idea.abstencion / (idea.votos_a_favor + idea.votos_en_contra + idea.abstencion)) * 100)
                                ]
                            }
                        ]
                    });
                }
            },
            getChartData(ideaId) {
                let chartData = this.chartsData.find(element => element.idea_id === ideaId);
                if(chartData) {
                    return chartData;
                }
                return null;
            },
            forceRerender() {
                this.keyChartComponent += 1
            }
        },
        created() {
            Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
                color: '#ffffff',
                formatter: function(value, context) {
                    return value + '%';
                }
            });
        }
    }
</script>
