<template>
    <div id="project-articles-container">
        <div v-if="loadArticles" class="vld-parent" style="height: 500px;">
            <Loading
                    :active.sync="loadArticles"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="color"
            ></Loading>
        </div>
        <div v-if="!loadArticles">
            <div v-if="articles.length > 0">
                <div v-for="article in articles" :key="article.id" class="shadow-sm p-3 mb-5 rounded my-25" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <h5 :style="mode==='dark'?'color: #fff':''">{{ article.titulo }}</h5>
                    <div class="row no-gutters">
                        <div class="col-sm-7 pr-5">
                            <p class="text-justify">{{ article.detalle }}</p>
                        </div>
                        <div class="col-sm-5">
                            <div v-if="getChartData(article.id)">
                                <pie-chart :id="'canvasI' + article.id" :data="getChartData(article.id)" :key="keyChartComponent"></pie-chart>
                            </div>
                            <div v-else class="row no-gutters">
                                <div class="col pa-5">
                                    <div
                                            class="card border-success shadow ma-5"
                                            :class="mode==='dark'?'dark':'light'"
                                            :id="'canvasI' + article.id"
                                    >
                                        <div class="card-body">
                                            <p class="card-text">{{ $t('proyecto.componentes.articulos.sin_votos') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center my-10">
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 agree"
                                        v-bind:class="{ 'agree-blocked': !isAvailableVoting, 'agree-selected': getUserVotedAgreeValue(article.id) }"
                                        @click="addOrEditVote(0, article.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><i class="fa fa-thumbs-up"></i></span>
                                    <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                                    <span class="d-block display-6">{{ article.votos_a_favor }}</span>
                                </div>
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 disagree"
                                        v-bind:class="{ 'disagree-blocked': !isAvailableVoting, 'disagree-selected': getUserVotedDisagreeValue(article.id) }"
                                        @click="addOrEditVote(1, article.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><i class="fa fa-thumbs-down"></i></span>
                                    <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                                    <span class="d-block display-6">{{ article.votos_en_contra }}</span>
                                </div>
                                <div
                                        class="col-4 px-0 card border-0 rounded-0 abstention"
                                        v-bind:class="{ 'abstention-blocked': !isAvailableVoting, 'abstention-selected': getUserVotedAbstentionValue(article.id) }"
                                        @click="addOrEditVote(2, article.id)"
                                        style="display: inline-block;"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''"
                                >
                                    <span class="d-block font-24"><font-awesome-icon icon="minus-circle"/></span>
                                    <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                                    <span class="d-block display-6">{{ article.abstencion }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ArticleComments v-if="article.id" :project="project" :article_id="article.id"></ArticleComments>
                </div>
                <div class="mb-20" v-if="totalResults > articles.length">
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
                            <p class="card-text">{{ $t('proyecto.contenido.sin_articulos') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import ArticleComments from '../../views/Comments';
    import PieChart from '../../PieChart.js';
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';
    import htmlToImage from 'html-to-image';

    export default {
        name: 'ProjectArticles',
        components: {
            ArticleComments,
            PieChart,
            Loading
        },
        props: {
            project: Object
        },
        data() {
            return {
                articles: [],
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
                loadArticles: true,
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
            this.getArticles();
        },
        methods:{
            setIsAvailableVoting() {
                if(this.$store.getters.userData && this.$store.getters.userData.rol === 'ADMIN') {
                    this.isAvailableVoting = this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                } else {
                    this.isAvailableVoting = this.project.is_enabled && this.project.etapa === 2 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
                }
            },
            getArticles() {
                axios
                    .get('/projects/' + this.project.id + '/articles', {
                        params: {
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.articles = this.articles.concat(res.data.results);
                        this.totalResults = res.data.total_results;
                        this.offset += res.data.returned_results;

                        this.setChartsData();
                        if(this.$store.getters.isLoggedIn) {
                            this.getUserVotes();
                        }
                    })
                    .finally(() => {
                        this.loadArticles = false;
                    });
            },
            getUserVotes() {
                let userId = JSON.parse(localStorage.user).id;
                axios
                    .get('/votes', {
                        params: {
                            user_id: userId,
                            return_all: 1
                        }
                    })
                    .then(res => {
                        let votes = res.data.results;
                        this.articles.forEach(article => {
                            votes.forEach(vote => {
                                if(vote.article_id === article.id) {
                                    this.votes.push(vote);
                                    if(vote.vote === 0) {
                                        this.toggleAgree(article.id);
                                    } else if(vote.vote === 1) {
                                        this.toggleDisagree(article.id);
                                    } else if(vote.vote === 2) {
                                        this.toggleAbstention(article.id);
                                    }
                                }
                            });
                        });
                    });
            },
            toggleAgree(articleId) {
                this.userVotedAgree = this.userVotedAgree.filter(element => element.article_id !== articleId);
                this.userVotedAgree.push({ article_id: articleId, value: true });

                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.article_id !== articleId);
                this.userVotedDisagree.push({ article_id: articleId, value: false });

                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.article_id !== articleId);
                this.userVotedAbstention.push({ article_id: articleId, value: false });
            },
            toggleDisagree(articleId) {
                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.article_id !== articleId);
                this.userVotedDisagree.push({ article_id: articleId, value: true });

                this.userVotedAgree = this.userVotedAgree.filter(element => element.article_id !== articleId);
                this.userVotedAgree.push({ article_id: articleId, value: false });

                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.article_id !== articleId);
                this.userVotedAbstention.push({ article_id: articleId, value: false });
            },
            toggleAbstention(articleId) {
                this.userVotedAbstention = this.userVotedAbstention.filter(element => element.article_id !== articleId);
                this.userVotedAbstention.push({ article_id: articleId, value: true });

                this.userVotedAgree = this.userVotedAgree.filter(element => element.article_id !== articleId);
                this.userVotedAgree.push({ article_id: articleId, value: false });

                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.article_id !== articleId);
                this.userVotedDisagree.push({ article_id: articleId, value: false });
            },
            getUserVotedAgreeValue(articleId) {
                let userVotedAgree = this.userVotedAgree.find(element => element.article_id === articleId);
                if(userVotedAgree) {
                    return userVotedAgree.value;
                }
                return false;
            },
            getUserVotedDisagreeValue(articleId) {
                let userVotedDisagree = this.userVotedDisagree.find(element => element.article_id === articleId);
                if(userVotedDisagree) {
                    return userVotedDisagree.value;
                }
                return false;
            },
            getUserVotedAbstentionValue(articleId) {
                let userVotedAbstention = this.userVotedAbstention.find(element => element.article_id === articleId);
                if(userVotedAbstention) {
                    return userVotedAbstention.value;
                }
                return false;
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getArticles();
            },
            addOrEditVote(voteValue, articleId) {
                this.currentMoment = this.$moment().local();
                this.setIsAvailableVoting();

                let article = this.articles.find(article => article.id === articleId);
                let vote = this.votes.find(vote => vote.article_id === article.id);

                if(this.$store.getters.isLoggedIn) {
                    if(this.isAvailableVoting) {
                        if(vote) {
                            axios
                                .put('/votes/' + vote.id, {
                                    vote: voteValue
                                })
                                .then(() => {
                                    if(vote.vote === 0) {
                                        article.votos_a_favor -= 1;
                                    } else if(vote.vote === 1) {
                                        article.votos_en_contra -= 1;
                                    } else if(vote.vote === 2) {
                                        article.abstencion -= 1;
                                    }

                                    if(voteValue === 0) {
                                        article.votos_a_favor += 1;
                                        this.toggleAgree(article.id);
                                    } else if(voteValue === 1) {
                                        article.votos_en_contra += 1;
                                        this.toggleDisagree(article.id);
                                    } else if(voteValue === 2) {
                                        article.abstencion += 1;
                                        this.toggleAbstention(article.id);
                                    }

                                    vote.vote = voteValue;
                                    this.setChartData(article);
                                    this.forceRerender();
                                    this.$emit('updateStackedChartVotos', vote.id)
                                    this.$toastr('success', 'Tu voto fue cambiado con éxito', 'Voto actualizado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha  podido cambiar tu voto', 'Error');
                                });
                        } else {
                            axios
                                .post('/articles/' + article.id + '/vote', {
                                    vote: voteValue
                                })
                                .then(res => {
                                    this.votes.push(res.data.data);
                                    if(voteValue === 0) {
                                        article.votos_a_favor += 1;
                                        this.toggleAgree(article.id);
                                    } else if(voteValue === 1) {
                                        article.votos_en_contra += 1;
                                        this.toggleDisagree(article.id);
                                    } else if(voteValue === 2) {
                                        article.abstencion += 1;
                                        this.toggleAbstention(article.id);
                                    }

                                    this.setChartData(article);
                                    this.forceRerender();
                                    this.$emit('updateStackedChartVotos', vote.id);
                                    this.$toastr('success', 'Tu voto fue registrado con éxito', 'Voto guardado');
                                })
                                .catch(() => {
                                    this.$toastr('error', 'No se ha podido registrar tu voto', 'Error');
                                });
                        }
                    } else {
                        this.$toastr('warning', 'No se pueden realizar votaciones en este artículo', 'Fuera de plazo, proyecto deshabilitado o fuera de etapa "Votación en Particular"');
                    }
                } else {
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder votar, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            setChartsData() {
                this.articles.forEach(article => {
                    this.setChartData(article);
                });
            },
            setChartData(article) {
                this.chartsData = this.chartsData.filter(element => element.article_id !== article.id);

                if(article.votos_a_favor + article.votos_en_contra + article.abstencion > 0) {
                    this.chartsData.push({
                        article_id: article.id,
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
                                    Math.trunc((article.votos_a_favor / (article.votos_a_favor + article.votos_en_contra + article.abstencion)) * 100),
                                    Math.trunc((article.votos_en_contra / (article.votos_a_favor + article.votos_en_contra + article.abstencion)) * 100),
                                    Math.trunc((article.abstencion / (article.votos_a_favor + article.votos_en_contra + article.abstencion)) * 100)
                                ]
                            }
                        ]
                    });
                }
            },
            getChartData(articleId) {
                let chartData = this.chartsData.find(element => element.article_id === articleId);
                if(chartData) {
                    return chartData;
                }
                return null;
            },
            retornarId(i) {
                htmlToImage.toPng(document.getElementById('canvas'+i))
                    .then(function (dataUrl) {
                        var img = new Image();
                        img.src = dataUrl;
                        document.body.appendChild(img);
                        return img.src;
                    })
                    .catch(function (error) {
                        console.error('oops, something went wrong!', error);
                    });
            },
            forceRerender() {
                this.keyChartComponent += 1
            }
        },
        created() {
            Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
                color: '#FFFFFF',
                formatter: function(value, context) {
                    return value + '%';
                }
            });
        }
    }
</script>

<style>
.Button {
    font-size:13px;
    font-weight: bold;
    background:white;
    color: black
}
</style>
