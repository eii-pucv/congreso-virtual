<template>
    <div id="app" >
        <div v-if="!loadProject">
            <div class="row">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_general') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_general') }}</p>
                    <div class="col-12 px-4">
                        <horizontal-barChart :data="generalVotingChartData" :options="generalVotingChartOptions"></horizontal-barChart>
                    </div>
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_ideas') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_ideas') }}</p>
                    <div class="col-12 px-4">
                        <horizontal-barChart :data="ideasVotingChartData" :options="genericStackedChartOptions"></horizontal-barChart>
                    </div>
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-12">
                    <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_articulos') }}</h3>
                    <br/>
                    <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_articulos') }}</p>
                    <div class="col-12 px-4">
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
                generalVotingChartData: Object,
                ideasVotingChartData: Object,
                articlesVotingChartData: Object,
                ideasLabelChartData: [],
                articlesChartData: [],
                generalVotingChartOptions: {
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
                    labels: ['A favor', 'En contra', 'Abstenciones'],
                    datasets: [
                        {
                            backgroundColor: [this.agreeColor, this.disagreeColor, this.abstentionColor],
                            data: [this.project.votos_a_favor, this.project.votos_en_contra, this.project.abstencion],
                        },
                    ],
                };
            },
            generateIdeasVotingChartData() {
                let ideasLabels = [];
                let ideasAgreeVotes = [];
                let ideasDisagreeVotes = [];
                let ideasAbstentionVotes = [];
                this.project.ideas.forEach((idea, index) => {
                    ideasLabels.push('Idea fundamental: ' + (index + 1));
                    ideasAgreeVotes.push(idea.votos_a_favor);
                    ideasDisagreeVotes.push(idea.votos_en_contra);
                    ideasAbstentionVotes.push(idea.abtencion);
                });

                this.ideasVotingChartData = {
                    labels: ideasLabels,
                    datasets: [
                        {
                            label: 'A favor',
                            data: ideasAgreeVotes,
                            backgroundColor: this.agreeColor
                        },
                        {
                            label: 'En contra',
                            data: ideasDisagreeVotes,
                            backgroundColor: this.disagreeColor
                        },
                        {
                            label: 'Abstenciones',
                            data: ideasAbstentionVotes,
                            backgroundColor: this.abstentionColor
                        },
                    ]
                };
            },
            generateArticlesVotingChartData() {
                let articlesLabels = [];
                let articlesAgreeVotes = [];
                let articlesDisagreeVotes = [];
                let articlesAbstentionVotes = [];
                this.project.articles.forEach((article, index) => {
                    articlesLabels.push('Artículo: ' + (index + 1));
                    articlesAgreeVotes.push(article.votos_a_favor);
                    articlesDisagreeVotes.push(article.votos_en_contra);
                    articlesAbstentionVotes.push(article.abtencion);
                });

                this.articlesVotingChartData = {
                    labels: articlesLabels,
                    datasets: [
                        {
                            label: 'A favor',
                            data: articlesAgreeVotes,
                            backgroundColor: this.agreeColor
                        },
                        {
                            label: 'En contra',
                            data: articlesDisagreeVotes,
                            backgroundColor: this.disagreeColor
                        },
                        {
                            label: 'Abstenciones',
                            data: articlesAbstentionVotes,
                            backgroundColor: this.abstentionColor
                        },
                    ]
                };
            }
        }
    }
</script>

<style>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }

    #app {
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
</style>