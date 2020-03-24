<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container-fluid mt-25 mb-10">
            <div class="row px-10">
                <section class="hk-sec-wrapper col-12" :class="mode==='dark'?'dark':'light'">
                    <div class="row">
                        <div class="col-12">
                            <ul id="myTab" class="nav nav-light nav-tabs" role="tablist">
                                <li class="nav-item active">
                                    <a @click="changeTab" id="nGramaBarChart-tab" data-toggle="tab" href="#nGramaBarChart" role="tab" aria-controls="infromacion" aria-selected="true" class="nav-link active" :style="mode==='dark'?'color: #fff':''">
                                        N-{{ $t('administrador.componentes.analitica.n-grama') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a @click="changeTab" id="nubePalabras-tab" data-toggle="tab" href="#nubePalabras" role="tab" aria-controls="informacion" aria-selected="true" class="nav-link" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('administrador.componentes.analitica.nube') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a @click="changeTab" id="usuarioPorRangoDeEdadBarChart-tab" data-toggle="tab" href="#usuarioPorRangoDeEdadBarChart" role="tab" aria-controls="informacion" aria-selected="true" class="nav-link" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('administrador.componentes.analitica.analisis_usuario') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a @click="changeTab" id="datosPorVotacionBarChart-tab" data-toggle="tab" href="#datosPorVotacion" role="tab" aria-controls="informacion" aria-selected="true" class="nav-link" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('administrador.componentes.analitica.datos_votacion') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a @click="changeTab" id="topicmodel-tab" data-toggle="tab" href="#topicmodel" role="tab" aria-controls="topicmodel" aria-selected="true" class="nav-link" :style="mode==='dark'?'color: #fff':''">
                                        Topic Model
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-12">
                            <div class="tab-content">
                                <div id="nGramaBarChart" class="tab-pane fade show active" role="tabpanel">
                                    <div class="row px-20">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <h5 :style="mode==='dark'?'Color: #fff;':''">{{ $t('administrador.componentes.analitica.descripcion_n-grama') }}:</h5>
                                                <div class="form-group mt-10">
                                                    <label for="quantityMinWordsSelect" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.analitica.seleccione') }}:</label>
                                                    <select id="quantityMinWordsSelect" @change="updateNgram" v-model="minWordsNgram" class="custom-select form-control">
                                                        <option :selected="this.value === minWordsNgram" :value="2">2</option>
                                                        <option :selected="this.value === minWordsNgram" :value="3">3</option>
                                                        <option :selected="this.value === minWordsNgram" :value="4">4</option>
                                                        <option :selected="this.value === minWordsNgram" :value="5">5</option>
                                                        <option :selected="this.value === minWordsNgram" :value="6">6</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row vld-parent">
                                                <div v-if="loadNgram" style="height: 500px;">
                                                    <loading
                                                            :active.sync="loadNgram"
                                                            :is-full-page="fullPage"
                                                            :height="128"
                                                            :color="'#000000'"
                                                    ></loading>
                                                </div>
                                                <div v-else-if="!loadNgram && !ngramError" id="barChar" class="mb-25">
                                                    <BarChart :data="chartData" :options="chartOptions"></BarChart>
                                                </div>
                                                <div v-else-if="ngramError" class="mt-20">
                                                    <h6>{{ $t('administrador.componentes.analitica.no_carga_n-grama') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="text-center">{{ $t('administrador.componentes.analitica.comentarios') }}:</h5>
                                            <h2 class="text-center">{{ totalComments }}</h2>
                                            <h5 class="text-center mt-20">{{ $t('administrador.componentes.analitica.comentarios_recientes') }}</h5>
                                            <div class="row justify-content-center mt-10 vld-parent">
                                                <div v-if="loadComments" style="height: 600px;">
                                                    <loading
                                                            :active.sync="loadComments"
                                                            :is-full-page="fullPage"
                                                            :height="128"
                                                            :color="'#000000'"
                                                    ></loading>
                                                </div>
                                                <p v-else-if="!loadComments && comments.length === 0">
                                                    {{ $t('administrador.componentes.analitica.sin_comentarios') }}
                                                </p>
                                                <ul v-else-if="!loadComments && comments.length > 0" class="list-unstyled pa-20 col-12 custom-scrollbar-wk custom-scrollbar-mz" style="max-height: 600px; overflow: auto;">
                                                    <li v-for="comment in comments" :key="comment.id" class="media pa-20 mb-5 border border-2 border-light col-12">
                                                        <div class="media-body" v-if="comment.user">
                                                            <div class="row">
                                                                <div class="col-9 mb-2">
                                                                    <h6 v-if="comment.user && comment.user.username">{{ comment.user.username }}</h6>
                                                                    <h6 v-else-if="comment.user && !comment.user.username">{{ comment.user.name }} {{ comment.user.surname }}</h6>
                                                                    <h6 v-else>{{ $t('componentes.comentarios.no_identificado') }}</h6>
                                                                    <small class="text-grey">{{ new Date(toLocalDatetime(comment.created_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                                                                </div>
                                                            </div>
                                                            <p class="comment-body custom-scrollbar-wk custom-scrollbar-mz">{{ comment.body }}</p>
                                                        </div>
                                                    </li>
                                                    <div v-if="totalComments > comments.length" class="mt-10 mb-20">
                                                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMoreComments">{{ $t('administrador.componentes.analitica.ver_mas') + ' (' + limit + ')' }}
                                                            <loading
                                                                    :active.sync="loadBtnLoadMoreComments"
                                                                    :is-full-page="fullPage"
                                                                    :height="24"
                                                                    :color="'#ffffff'"
                                                            ></loading>
                                                        </button>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="nubePalabras" class="tab-pane fade show" role="tabpanel">
                                    <div class="row px-20">
                                        <h2>{{ $t('administrador.componentes.analitica.nube') }}:</h2>
                                        <p class="text-justify">{{ $t('administrador.componentes.analitica.descripcion_nube') }}</p>
                                        <WordCloud v-if="project_id" :project_id="project_id"></WordCloud>
                                    </div>
                                </div>
                                <div id="usuarioPorRangoDeEdadBarChart" class="tab-pane fade show" role="tabpanel">
                                    <div class="row px-20">
                                        <div class="row">
                                            <h2>{{ $t('administrador.componentes.analitica.usuarios') }}:</h2>
                                            <p class="text-justify">{{ $t('administrador.componentes.analitica.grafico') }}</p>
                                            <TreemapUsuariosPorRegion  v-bind:project_id="project_id"></TreemapUsuariosPorRegion>
                                        </div>
                                        <div class="row">
                                            <h2>{{ $t('administrador.componentes.analitica.genero_creacion') }}:</h2>
                                            <p class="text-justify">{{ $t('administrador.componentes.analitica.siguiente') }}</p>
                                            <HorizontalUserGenderBarChart></HorizontalUserGenderBarChart>
                                            <HorizontalUserAgesBarChart></HorizontalUserAgesBarChart>
                                        </div>
                                    </div>
                                </div>
                                <div id="datosPorVotacion" class="tab-pane fade show" role="tabpanel">
                                    <div class="row px-20">
                                        <h2>{{ $t('administrador.componentes.analitica.datos') }}:</h2>
                                        <p class="text-justify">{{ $t('administrador.componentes.analitica.seccion') }}</p>
                                        <div class="col-md-12 mt-15 mb-15">
                                            <ProjectBarCharts :project_id="project_id"></ProjectBarCharts>
                                        </div>
                                    </div>
                                </div>
                                <div id="topicmodel" class="tab-pane fade show" role="tabpanel">
                                    <div class="row px-20" style="overflow: auto;">
                                        <TopicModel :project_id="project_id"></TopicModel>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    import BarChart from '../../../BarChart.js';
    import WordCloud from '../../projects/WordCloud';
    import TreemapUsuariosPorRegion from './TreeMapUsuariosPorRegion';
    import HorizontalUserGenderBarChart from '../../projects/AnalitycUsersGenderChart';
    import HorizontalUserAgesBarChart from '../../projects/AnalitycUsersAgesChart';
    import ProjectBarCharts from '../../projects/ProjectBarCharts';
    import TopicModel from './TopicModel';
    import axios from '../../../backend/axios';
    import HighlightText from 'vue-highlight-text';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectAnalytic',
        components: {
            BarChart,
            WordCloud,
            TreemapUsuariosPorRegion,
            HorizontalUserGenderBarChart,
            HorizontalUserAgesBarChart,
            ProjectBarCharts,
            TopicModel,
            HighlightText,
            Loading
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                mode: String,
                comments: [],
                totalComments: 0,
                limit: 10,
                offset: 0,
                loadComments: true,
                loadBtnLoadMoreComments: false,
                chartData: {
                    labels: [],
                    datasets: [{
                        label: this.$t('administrador.componentes.analitica.frecuencia'),
                        backgroundColor: 'rgba(80, 80, 240, 0.5)',
                        data: []
                    }],
                },
                chartOptions: {
                    elements: {
                        rectangle : {
                            borderWidth: 1,
                            borderColor: 'rgb(0, 0, 250)',
                            borderskipped: 'bottom'
                        },
                        responsive: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                },
                minWordsNgram: 2,
                loadNgram: true,
                ngramError: false,
                fullPage: false,
                color: "#000000"
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getComments();
            this.getNgram();
        },
        methods: {
            getComments() {
                axios
                    .get('/comments', {
                        params: {
                            'project_id': this.project_id,
                            'include_all_comments': 1,
                            'order_by': 'created_at',
                            'order': 'DESC',
                            'limit': this.limit,
                            'offset': this.offset
                        }
                    })
                    .then(res => {
                        this.comments = this.comments.concat(res.data.results);
                        this.totalComments = res.data.total_results;
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadComments = false;
                        this.loadBtnLoadMoreComments = false;
                    });
            },
            loadMoreComments() {
                this.loadBtnLoadMoreComments = true;
                this.getComments();
            },
            getNgram() {
                this.loadNgram = true;
                axios
                    .get('/ngram', {
                        params: {
                            'project_id': this.project_id,
                            'min_words': this.minWordsNgram
                        }
                    })
                    .then(res => {
                        let matrixX = res.data.names;
                        let matrixY = res.data.values;
                        this.chartData.labels = matrixX.slice(0, 10);
                        this.chartData.datasets[0].data = matrixY.slice(0, 10);
                    })
                    .catch(() => {
                        this.ngramError = true;
                    })
                    .finally(() => {
                        this.loadNgram = false;
                    });
            },
            updateNgram(event) {
                this.minWordsNgram = event.target.value;
                this.getNgram();
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            },
            changeTab(e) {
                e.preventDefault();
                $(this).tab("show");
            },
        }
    }
</script>

<style scoped>
    #barChar {
        height: 500px;
        width: 500px;
        overflow: auto;
    }
</style>