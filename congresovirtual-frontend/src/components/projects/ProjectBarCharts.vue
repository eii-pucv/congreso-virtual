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
                articles: [],
                ideas: [],
                articlesVotos: [],
                ideasVotos: [],

                article_votos_a_favor: [],
                article_votos_en_contra: [],
                article_abstencion: [],

                ideas_votos_a_favor: [],
                ideas_votos_en_contra: [],
                ideas_abstencion: [],

                labels: [],
                labels2: [],


                generalVotingChartData: Object,
                ideasVotingChartData: Object,
                articlesVotingChartData: Object,
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
                            backgroundColor: ['#9de19c', '#e19c9c', '#b1b1b1'],
                            data: [this.project.votos_a_favor, this.project.votos_en_contra, this.project.abstencion],
                        },
                    ],
                };
            },
            generateIdeasVotingChartData() {
                for(var i = 0; i < this.project.ideas.length; i++){
                    let idea = this.project.ideas[i]

                    this.labels2.push("Idea fundamental: "+(i+1));

                    this.ideas_votos_a_favor.push(idea.votos_a_favor);
                    this.ideas_votos_en_contra.push(idea.votos_en_contra);
                    this.ideas_abstencion.push(idea.abstencion);
                }

                this.ideasVotingChartData = {
                    labels: this.labels2,
                    datasets: [
                        {
                            label: 'A favor',
                            data: this.ideas_votos_a_favor,
                            backgroundColor: '#9de19c'
                        },
                        {
                            label: 'En contra',
                            data: this.ideas_votos_en_contra,
                            backgroundColor: '#e19c9c'
                        },
                        {
                            label: 'Abstenciones',
                            data: this.ideas_abstencion,
                            backgroundColor: '#b1b1b1'
                        },
                    ]
                };
            },
            generateArticlesVotingChartData() {
                for(var i = 0; i < this.project.articles.length; i++){
                    let article = this.project.articles[i];

                    this.labels.push("Articulo: "+(i+1));

                    this.article_votos_a_favor.push(article.votos_a_favor);
                    this.article_votos_en_contra.push(article.votos_en_contra);
                    this.article_abstencion.push(article.abstencion);
                }

                this.articlesVotingChartData = {
                    labels: this.labels,
                    datasets: [
                        {
                            label: 'A favor',
                            data: this.article_votos_a_favor,
                            backgroundColor: '#9de19c'
                        },
                        {
                            label: 'En contra',
                            data: this.article_votos_en_contra,
                            backgroundColor: '#e19c9c'
                        },
                        {
                            label: 'Abstenciones',
                            data: this.article_abstencion,
                            backgroundColor: '#b1b1b1'
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