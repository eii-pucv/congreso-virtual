<template>
    <div id="">
        <div v-if="!loadProject">
            <div class="row">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_general') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_general') }}</p>
                    <div class="barchart-container col-12 px-4">
                        <horizontal-barChart :data="generalVotingChartData" :options="generalVotingChartOptions"></horizontal-barChart>
                    </div>
                </div>
            </div>
            <div class="row mt-30" v-if="showIdeas">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_ideas') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_ideas') }}</p>
                    <div class="barchart-container col-12 px-4">
                        <horizontal-barChart :data="ideasVotingChartData" :options="genericStackedChartOptions"></horizontal-barChart>
                    </div>
                </div>
            </div>
            <div class="row mt-30" v-if="showArticles">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_articulos') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_articulos') }}</p>
                    <div class="barchart-container col-12 px-4">
                        <horizontal-barChart :data="articlesVotingChartData" :options="genericStackedChartOptions"></horizontal-barChart>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div v-if="loadProject">
                <p :style="mode==='dark'?'color: #fff':''"><b>{{ $t('proyecto.componentes.votos_stacked.cargando') }}</b></p>
            </div>
            <div v-else-if="loadProject && !project">
                <p :style="mode==='dark'?'color: #fff':''"><b>{{ $t('proyecto.componentes.votos_stacked.no_carga') }}</b></p>
            </div>
        </div>
    </div>
</template>

<script>
    import horizontalBarChart from '../../horizontalBarChart.js';
    import ChartDataLabels from 'chartjs-plugin-datalabels';
    import axios from '../../backend/axios';

    export default {
        name: 'ProjectStackedChart',
        components: {
            'horizontal-barChart': horizontalBarChart
        },
        props: {
            project: Object,
            project_id: Number
        },
        data() {
            return {
                generalVotingChartData: null,
                ideasVotingChartData: null,
                articlesVotingChartData: null,
                showIdeas: true,
                showArticles: true,
                generalVotingChartOptions: {
                    //maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [
                            {
                                ticks: {
                                    beginAtZero: true
                                },
                            }
                        ]
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                genericStackedChartOptions: {
                    //maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [
                            {
                                stacked: true,
                                ticks: {
                                    beginAtZero: true
                                }
                            }
                        ],
                        yAxes: [
                            {
                                stacked: true,
                                ticks: {
                                    beginAtZero: true
                                }
                            },
                        ]
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontSize: 12,
                        },
                        position: 'top'
                    },
                },
                agreeColor: '#9de19c',
                disagreeColor: '#e19c9c',
                abstentionColor: '#b1b1b1',
                loadProject: true,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            if(!this.project && this.project_id) {
                this.getProject();
            } else {
                this.configComponent();
                this.loadProject = false;
            }
        },
        methods: {
            getProject() {
                axios
                    .get('/projects/' + this.project_id)
                    .then(res => {
                        this.project = res.data;
                        this.configComponent();
                    })
                    .finally(() => {
                        this.loadProject = false;
                    });
            },
            configComponent() {
                this.generateGeneralVotingChartData();
                this.generateIdeasVotingChartData();
                this.generateArticlesVotingChartData();
            },
            generateGeneralVotingChartData() {
                this.generalVotingChartData = {
                    labels: [this.$t('proyecto.contenido.a_favor'), this.$t('proyecto.contenido.en_contra'), this.$t('proyecto.contenido.abstenciones')],
                    datasets: [
                        {
                            backgroundColor: [this.agreeColor, this.disagreeColor, this.abstentionColor],
                            data: [this.project.votos_a_favor, this.project.votos_en_contra, this.project.abstencion],
                        },
                    ]
                };
            },
            generateIdeasVotingChartData() {
                let ideasLabels = [];
                let ideasAgreeVotes = [];
                let ideasDisagreeVotes = [];
                let ideasAbstentionVotes = [];
                let totalVotes = 0;

                this.project.ideas.forEach((idea, index) => {
                    ideasLabels.push('Idea fundamental: ' + (index + 1));
                    ideasAgreeVotes.push(idea.votos_a_favor);
                    ideasDisagreeVotes.push(idea.votos_en_contra);
                    ideasAbstentionVotes.push(idea.abstencion);
                    totalVotes += (idea.votos_a_favor + idea.votos_en_contra + idea.abstencion)
                });

                if(totalVotes > 0) {
                    this.ideasVotingChartData = {
                        labels: ideasLabels,
                        datasets: [
                            {
                                label: this.$t('proyecto.contenido.a_favor'),
                                data: ideasAgreeVotes,
                                backgroundColor: this.agreeColor
                            },
                            {
                                label: this.$t('proyecto.contenido.en_contra'),
                                data: ideasDisagreeVotes,
                                backgroundColor: this.disagreeColor
                            },
                            {
                                label: this.$t('proyecto.contenido.abstenciones'),
                                data: ideasAbstentionVotes,
                                backgroundColor: this.abstentionColor
                            }
                        ]
                    };
                } else {
                    this.showIdeas = false
                }
            },
            generateArticlesVotingChartData() {
                let articlesLabels = [];
                let articlesAgreeVotes = [];
                let articlesDisagreeVotes = [];
                let articlesAbstentionVotes = [];
                let totalVotes = 0;

                this.project.articles.forEach((article, index) => {
                    articlesLabels.push('Artículo: ' + (index + 1));
                    articlesAgreeVotes.push(article.votos_a_favor);
                    articlesDisagreeVotes.push(article.votos_en_contra);
                    articlesAbstentionVotes.push(article.abstencion);
                    totalVotes += (article.votos_a_favor + article.votos_en_contra + article.abstencion)
                });

                if(totalVotes > 0) {
                    this.articlesVotingChartData = {
                        labels: articlesLabels,
                        datasets: [
                            {
                                label: this.$t('proyecto.contenido.a_favor'),
                                data: articlesAgreeVotes,
                                backgroundColor: this.agreeColor
                            },
                            {
                                label: this.$t('proyecto.contenido.en_contra'),
                                data: articlesDisagreeVotes,
                                backgroundColor: this.disagreeColor
                            },
                            {
                                label: this.$t('proyecto.contenido.abstenciones'),
                                data: articlesAbstentionVotes,
                                backgroundColor: this.abstentionColor
                            }
                        ]
                    };
                } else {
                    this.showArticles = false
                }
            }
        }
    }
</script>

<style>
    .barchart-container {
        position: relative;
        margin: auto;
        /*height: 300px;*/
        /*width: 600px;*/
    }
</style>