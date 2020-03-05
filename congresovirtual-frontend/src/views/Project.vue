<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row" id="principalRow">
                <div v-if="loadProject" class="vld-parent">
                    <img
                            src="../assets/img/loader2.gif"
                            style="height:600px; width:1200px"
                            class="pl-0 ma-10 img-fluid col-12"
                    />
                </div>
                <ProjectHeader
                        v-if="!loadProject"
                        :project="project"
                        data-intro="¡Puedes participar en la votación principal del proyecto!"
                        data-step="2"
                ></ProjectHeader>
                <nav v-if="!loadProject" aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item">
                            <a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.breadcumb.inicio') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="/projects" :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.breadcumb.proyectos') }}</a>
                        </li>
                        <li
                                class="breadcrumb-item active"
                                aria-current="page"
                                :style="mode==='dark'?'color: #fff':''"
                        >{{ project.boletin }}</li>
                    </ol>
                </nav>
                <div v-if="!loadProject" class="hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);color: #fff':''">
                    <div class="row px-10">
                        <div class="col-sm-8">
                            <ul
                                    id="myTab"
                                    class="nav nav-light nav-tabs"
                                    role="tablist"
                                    data-intro="¿Deseas seguir realizando actividades en el proyecto?"
                                    data-step="1"
                            >
                                <li
                                        class="nav-item active"
                                        data-intro="O puedes seguir realizando votos de ideas fundamentales o por artículo"
                                        data-step="3"
                                >
                                    <a
                                            @click="changeTab"
                                            id="articulos-tab"
                                            data-toggle="tab"
                                            href="#articulos"
                                            role="tab"
                                            aria-controls="articulos"
                                            aria-selected="true"
                                            class="nav-link active"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >{{ $t('proyecto.contenido.tab.articulos_ideas') }}</a>
                                </li>
                                <li class="nav-item"
                                        data-intro="Entérate de cada detalle del proyecto en la sección de detalles."
                                        data-step="4"
                                >
                                    <a
                                            @click="changeTab"
                                            id="informacion-tab"
                                            data-toggle="tab"
                                            href="#informacion"
                                            role="tab"
                                            aria-controls="informacion"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >{{ $t('proyecto.contenido.tab.detalle') }}</a>
                                </li>
                                <li
                                        class="nav-item"
                                        data-intro="También puedes seguir el avance del proyecto a tiempo real en la sección de 'Seguimiento' "
                                        data-step="6"
                                >
                                    <a
                                            @click="changeTab"
                                            id="traces-tab"
                                            data-toggle="tab"
                                            href="#traces"
                                            role="tab"
                                            aria-controls="comments"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >{{ $t('proyecto.contenido.tab.seguimiento') }}</a>
                                </li>
                                <li class="nav-item"
                                    data-intro="Entérate más a fondo de toda la participación ciudadana en el proyecto"
                                    data-step="7"
                                >
                                    <a
                                            @click="changeTab"
                                            id="analisis-tab"
                                            data-toggle="tab"
                                            href="#analisis"
                                            role="tab"
                                            aria-controls="comments"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >{{ $t('proyecto.contenido.tab.estadistica') }}</a>
                                </li>
                                <li
                                        class="nav-item"
                                        data-intro="O revisa los documentos disponibles para entender mejor los detalles del proyecto"
                                        data-step="8"
                                >
                                    <a
                                            @click="changeTab"
                                            id="documentosAdjuntos-tab"
                                            data-toggle="tab"
                                            href="#documentosAdjuntos"
                                            role="tab"
                                            aria-controls="comments"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >{{ $t('proyecto.contenido.tab.documentos') }}</a>
                                </li>
                            </ul>
                            <div class="tab-content py-25">
                                <div
                                        class="tab-pane fade show active"
                                        id="articulos"
                                        role="tabpanel"
                                        aria-labelledby="articulos-tab"
                                >
                                    <div class="row no-gutters py-5">
                                        <div class="col-12">
                                            <div class="container">
                                                <div class="wrapper my-5">
                                                    <div class="arrow-steps clearfix row no-gutters">
                                                        <div v-if="seguimiento.length > 0" class="col-12 row">
                                                            <div class="col-6 mb-10">
                                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.autoria') }}</h5>
                                                                <h5>
                                                                    <small class :class="mode==='dark'?'':'text-muted '" :style="mode==='dark'?'color: #fff':''">{{ seguimiento[0].AUTORES }}</small>
                                                                </h5>
                                                            </div>
                                                            <div class="col-6">
                                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.iniciativa') }}</h5>
                                                                <h5>
                                                                    <small class :class="mode==='dark'?'':'text-muted '" :style="mode==='dark'?'color: #fff':''">{{ seguimiento[0].INICIATIVA }}</small>
                                                                </h5>
                                                            </div>
                                                            <div class="col-6 mb-10">
                                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.origen') }}</h5>
                                                                <h5>
                                                                    <small class :class="mode==='dark'?'':'text-muted '" :style="mode==='dark'?'color: #fff':''">{{ seguimiento[0].ORIGEN }}</small>
                                                                </h5>
                                                            </div>
                                                            <div class="col-6 mb-10">
                                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.tramitacion') }}</h5>
                                                                <h5>
                                                                    <small class :class="mode==='dark'?'':'text-muted '" :style="mode==='dark'?'color: #fff':''">{{ seguimiento[0].TRAMDESCRIPCION }}</small>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mb-10">
                                                            <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.resumen') }}</h5>
                                                            <h5>
                                                                <small class :class="mode==='dark'?'':'text-muted '" :style="mode==='dark'?'color: #fff':''">{{ project.resumen }}</small>
                                                            </h5>
                                                        </div>
                                                        <div class="step col-sm" v-bind:class="[(project.etapa === 1) ? 'current bg-primary' : '', (project.etapa === 2) ? 'bg-secondary' : '']">
                                                            <h6 class="text-white">{{ $t('proyecto.contenido.votacion_general.titulo') }}</h6>
                                                            <p class="text-white my-5">
                                                                {{ $t('proyecto.contenido.votacion_general.descripcion1') }}
                                                                <v-popover>
                                                                    <strong>{{ $t('proyecto.contenido.votacion_general.strong') }}</strong>
                                                                    <fontAwesomeIcon class="tooltip-target b3 font-16" icon="question-circle"></fontAwesomeIcon>
                                                                    <template slot="popover" style="background-color: #1c7430 !important;">
                                                                        <span class="font-11">
                                                                            {{ $t('proyecto.contenido.votacion_general.popover.primero') }}
                                                                            <strong>{{ $t('proyecto.contenido.votacion_general.popover.strong') }}</strong> {{ $t('proyecto.contenido.votacion_general.popover.segundo') }}
                                                                        </span>
                                                                    </template>
                                                                </v-popover>
                                                                {{ $t('proyecto.contenido.votacion_general.descripcion2') }}
                                                            </p>
                                                            <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#collapseIdeas" aria-expanded="false" aria-controls="collapseIdeas">
                                                                {{ $t('mostrar') }}
                                                            </button>
                                                        </div>
                                                        <div class="step col-sm" v-bind:class="{'current bg-primary': (project.etapa === 2) }" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                                            <h6 class="font-weight-lighter" v-bind:class="{'text-white': (project.etapa === 2) }" :style="mode==='dark'?'color: #fff':''">
                                                                {{ $t('proyecto.contenido.votacion_particular.titulo') }}
                                                            </h6>
                                                            <p class="font-weight-lighter my-5" v-bind:class="{'text-white': (project.etapa === 2) }">
                                                                {{ $t('proyecto.contenido.votacion_particular.descripcion1') }}
                                                                <v-popover>
                                                                    <strong>{{ $t('proyecto.contenido.votacion_particular.strong') }}</strong>
                                                                    <fontAwesomeIcon class="tooltip-target b3 font-16" icon="question-circle"></fontAwesomeIcon>
                                                                    <template slot="popover">
                                                                        <span class="font-11">
                                                                            {{ $t('proyecto.contenido.votacion_particular.popover.primero') }}
                                                                            <strong>{{ $t('proyecto.contenido.votacion_particular.popover.strong') }}</strong> {{ $t('proyecto.contenido.votacion_particular.popover.segundo') }}
                                                                        </span>
                                                                    </template>
                                                                </v-popover>
                                                                {{ $t('proyecto.contenido.votacion_particular.descripcion2') }}
                                                            </p>
                                                            <button v-if="project.etapa !== 1" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#collapseArticles" aria-expanded="false" aria-controls="collapseArticles">
                                                                {{ $t('mostrar') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapse w-100" id="collapseIdeas">
                                            <button class="btn btn-primary btn-sm d-block col-12" @click="createPDF">{{ $t('proyecto.contenido.generar_informe') }}</button>
                                            <ProjectIdeas :project="project" @updateStackedChartVotos="forceRerender"></ProjectIdeas>
                                        </div>
                                        <div class="collapse w-100" id="collapseArticles">
                                            <button class="btn btn-primary btn-sm d-block col-12" @click="createPDF">{{ $t('proyecto.contenido.generar_informe') }}</button>
                                            <ProjectArticles :project="project" @updateStackedChartVotos="forceRerender"></ProjectArticles>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show"
                                     id="informacion"
                                     role="tabpanel"
                                     aria-labelledby="informacion-tab"
                                >
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 px-5" v-html="project.detalle"></div>
                                            <div v-if="project.video" class="col-12 px-5 mt-30">
                                                <h5>{{ $t('proyecto.contenido.video') }}</h5>
                                                <div class="embed-responsive embed-responsive-16by9 mt-20" v-html="project.video"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show"
                                     id="traces"
                                     role="tabpanel"
                                     aria-labelledby="comments-tab"
                                >
                                    <ProjectTraces v-if="project.id" :project_boletin="project.boletin" :project_id="project.id"></ProjectTraces>
                                </div>
                                <div class="tab-pane fade show"
                                     id="analisis"
                                     role="tabpanel"
                                     aria-labelledby="comments-tab"
                                >
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="row mx-0">
                                            <div class="col-md-12 mt-15 mb-10">
                                                <h5 class="hk-sec-title text-center"
                                                    :style="mode==='dark'?'color: #fff':''"
                                                >{{ $t('proyecto.contenido.analisis_votos') }}</h5>
                                            </div>
                                            <div class="col-md-12 mt-15 mb-10">
                                                <h7 class="hk-sec-title"
                                                    :style="mode==='dark'?'color: #fff':''"
                                                >{{ $t('proyecto.contenido.recomendacion') }}</h7>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-15 mb-15">
                                                <div class="col-12">
                                                    <ProjectBarCharts :project="project" :key="keyStackedChartComponent"></ProjectBarCharts>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="row text-center">
                                            <div class="col-6 mt-15 mb-15">
                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.cantidad_comentarios') }}</h5>
                                                <h2 :style="mode==='dark'?'color: #fff':''">
                                                    <b>{{ comments.length }}</b>
                                                </h2>
                                            </div>
                                            <div class="col-6 mt-15 mb-15">
                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.cantidad_participantes') }}</h5>
                                                <h2 :style="mode==='dark'?'color: #fff':''">
                                                    <b>{{ participantes.length }}</b>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="hk-row">
                                            <div class="col-md-6 px-20 py-20">
                                                <h3 class="hk-sec-title text-center"
                                                    :style="mode==='dark'?'color: #fff':''"
                                                >{{ $t('proyecto.contenido.congreso_virtual') }}</h3>
                                                <pie-chart :data="chartData" :options="chartOptions"></pie-chart>
                                            </div>
                                            <div class="col-md-6 px-20 py-20">
                                                <h3 class="hk-sec-title text-center"
                                                    :style="mode==='dark'?'color: #fff':''"
                                                >{{ $t('proyecto.contenido.senado') }}</h3>
                                                <pie-chart :data="chartData" :options="chartOptions"></pie-chart>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="row justify-content-center">
                                            <h3 class="hk-sec-title mt-20"
                                                :style="mode==='dark'?'color: #fff':''"
                                            >{{ $t('proyecto.contenido.nube') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 px-20 py-20">
                                                <div id="app">
                                                    <WordCloud v-if="project.id" :project_id="project.id"></WordCloud>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show"
                                     id="documentosAdjuntos"
                                     role="tabpanel"
                                     aria-labelledby="comments-tab"
                                >
                                    <div>
                                        <div class="row">
                                            <div class="col-12">
                                                <ProjectFiles v-if="project.id" :project_id="project.id"></ProjectFiles>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="mb-20">{{ $t('proyecto.contenido.comentarios') }}</h5>
                            <ProjectComments v-if="!loadProject" :project="project"></ProjectComments>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ProjectHeader from '../components/projects/ProjectHeader';
    import ProjectComments from './Comments';
    import ProjectIdeas from '../components/projects/ProjectIdeas';
    import ProjectArticles from '../components/projects/ProjectArticles';
    import ProjectTraces from '../components/projects/ProjectTraces';
    import ProjectBarCharts from '../components/projects/ProjectBarCharts';
    import ProjectFiles from '../components/projects/ProjectFiles';
    import WordCloud from "../components/projects/WordCloud";

    import axios from "../backend/axios";
    import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
    import PieChart from "../PieChart.js";
    import ChartDataLabels from 'chartjs-plugin-datalabels';
    //import "vue-loading-overlay/dist/vue-loading.css";
    import axioma from "axios";
    import jsPDF from "jspdf";
    import htmlToImage from "html-to-image";
    import "intro.js/minified/introjs.min.css";
    import { bus } from "../main";

    export default {
        name: 'Project',
        components: {
            ProjectHeader,
            ProjectComments,
            ProjectIdeas,
            ProjectArticles,
            ProjectTraces,
            ProjectBarCharts,
            ProjectFiles,
            WordCloud,
            FontAwesomeIcon,
            PieChart,
            ChartDataLabels
        },
        props: {
            project_id: Number,
            mode: String
        },
        data() {
            return {
                project: Object,
                loadProject: true,
                votacionesGeneral: [],
                loadTab: false,
                loadPDF: false,
                fullPage: false,
                color:"#fff",
                ideas: [],
                articles: [],
                comments: [],
                participantes: [],
                seguimiento: [],
                keyStackedChartComponent: 0,
                chartOptions: {
                    hoverBorderWidth: 20
                },
                chartData: {
                    hoverBackgroundColor: "red",
                    hoverBorderWidth: 10,
                    labels: ["Votos a favor", "Votos en contra", "Votos de abstención"],
                    datasets: [
                        {
                            label: "Data One",
                            backgroundColor: ["#41B883", "#E46651", "#3e95cd"],
                            data: []
                        }
                    ]
                }
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getProject();

            bus.$on('tour', function() {
                const introJS = require('intro.js');
                introJS
                    .introJs()
                    .setOption('nextLabel', ' Siguiente ')
                    .setOption('skipLabel', ' Salir ')
                    .setOption('prevLabel', ' Anterior ')
                    .start();
            });

            axios
                .get("/projects/" + this.project_id + "/articles")
                .then(res => {
                    this.articles = res.data.results;
                })
                .catch(() => console.error("FAIL"));
            axios
                .get("/projects/" + this.project_id + "/ideas")
                .then(res => {
                    this.ideas = res.data.results;
                })
                .catch(() => console.error("FAIL"));
            /*axios
              .get('/comments/sorted_by', {
                params:{
                  option: 'TOTAL_ALL_VOTES',
                  order: 'DESC',
                  limit: 10,
                  offset: 0,
                  project_id: this.$route.params.id,
                }
              })
              .then(res => {
                this.comments = res.data;
                console.log(this.comments);
              })
              .catch(() => console.error("FAIL"));*/
        },
        created() {
            Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
                color: "#FFFFFF",
                formatter: function(value, context) {
                    return value + "%";
                }
            });
        },
        methods: {
            getProject() {
                axios
                    .get('/projects/' + this.project_id)
                    .then(res => {
                        this.project = res.data;
                        this.chartData.datasets[0].data.push(
                            Math.trunc(
                                (this.project.votos_a_favor /
                                    (this.project.votos_a_favor +
                                        this.project.votos_en_contra +
                                        this.project.abstencion)) *
                                100
                            )
                        ); //(this.project.votos_a_favor)
                        this.chartData.datasets[0].data.push(
                            Math.trunc(
                                (this.project.votos_en_contra /
                                    (this.project.votos_a_favor +
                                        this.project.votos_en_contra +
                                        this.project.abstencion)) *
                                100
                            )
                        ); //(this.project.votos_en_contra)
                        this.chartData.datasets[0].data.push(
                            Math.trunc(
                                (this.project.abstencion /
                                    (this.project.votos_a_favor +
                                        this.project.votos_en_contra +
                                        this.project.abstencion)) *
                                100
                            )
                        ); //(this.project.abstencion)

                    })
                    .catch(() => {
                        console.error("FAIL");
                    })
                    .finally(() => {
                        this.loadProject = false;
                        axioma
                            .create()
                            .get("https://slr.senado.cl/proyectoInfo/0/" + this.project.boletin)
                            .then(res => {
                                this.seguimiento = res.data;
                            })
                            .catch(e => console.error("FAIL: " + JSON.stringify(e)));
                    });
            },
            async createPDF() {
                this.loadPDF = true;
                var congreso = "Congreso Virtual";
                var cabecera = "Informe correspondiente al detalle del proyecto: ";
                var titulo = '"' + this.project.titulo + '"';
                var resumen = this.project.resumen;
                resumen = resumen.trim().replace(/\s\s+/g, ' ')
                var articulos = "Artículos";
                var ideas = "Ideas fundamentales";
                var comentarios = "Comentarios más votados";

                var doc = new jsPDF();
                var splitcongreso = doc.splitTextToSize(congreso, 180);
                doc.setFontSize(36);
                var y = 20;
                for (var i = 0; i < splitcongreso.length; i++) {
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splitcongreso[i]);
                    y = y + 10;
                }
                var splitcabecera = doc.splitTextToSize(cabecera, 350);
                doc.setFontSize(20);
                for (var i = 0; i < splitcabecera.length; i++) {
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splitcabecera[i]);
                    y = y + 7;
                }
                var splittitulo = doc.splitTextToSize(titulo, 200);
                doc.setFontSize(18);
                for (var i = 0; i < splittitulo.length; i++) {
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splittitulo[i]);
                    y = y + 7;
                }
                doc.setFontSize(11);
                doc.text(20, y, "Boletín: " + this.project.boletin);
                y = y + 5;
                var splitresumen = doc.splitTextToSize(resumen, 175);
                doc.setFontSize(11);
                for (var i = 0; i < splitresumen.length; i++) {
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splitresumen[i]);
                    y = y + 5;
                }
                y = y + 5;
                var splitcomentarios = doc.splitTextToSize(comentarios, 310);
                doc.setFontSize(19);
                for (var i = 0; i < splitcomentarios.length; i++) {
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splitcomentarios[i]);
                    y = y + 8;
                }
                for (var i = 0; i < this.comments.length; i++) {
                    var splitname = String;
                    if (this.comments[i].user) {
                        splitname = doc.splitTextToSize(
                            this.comments[i].user.name + " " + this.comments[i].user.surname,
                            180
                        );
                    } else {
                        splitname = doc.splitTextToSize("Usuario no identificado", 180);
                    }
                    doc.setFontStyle("bold");
                    doc.setFontSize(11);
                    for (var j = 0; j < splitname.length; j++) {
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitname[j]);
                        y = y + 5;
                    }
                    var splitcomments = doc.splitTextToSize(this.comments[i].body, 180);
                    doc.setFontStyle("normal");
                    doc.setFontSize(11);
                    for (var j = 0; j < splitcomments.length; j++) {
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitcomments[j]);
                        y = y + 5;
                    }
                    var splitcommentsafavor = doc.splitTextToSize(
                        "Votos a favor:" + this.comments[i].votos_a_favor,
                        180
                    );
                    var splitcommentsencontra = doc.splitTextToSize(
                        "Votos en contra:" + this.comments[i].votos_en_contra,
                        180
                    );
                    doc.setFontSize(11);
                    doc.setTextColor("green");
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(20, y, splitcommentsafavor);
                    doc.setFontSize(11);
                    doc.setTextColor("red");
                    if (y > 275) {
                        y = 20;
                        doc.addPage();
                    }
                    doc.text(150, y, splitcommentsencontra);
                    doc.setTextColor("normal");
                    y = y + 10;
                }
                if (this.project.etapa === 1) {
                    //Ideas Fundamentales
                    y = y + 3;
                    var splitarideas = doc.splitTextToSize(ideas, 200);
                    doc.setFontSize(18);
                    for (var i = 0; i < splitarideas.length; i++) {
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitarideas[i]);
                        y = y + 7;
                    }
                    var j = y;
                    for (var i = 0; i < this.ideas.length; i++) {
                        var splitidea = doc.splitTextToSize(this.ideas[i].titulo, 290);
                        var ideafavor = doc.splitTextToSize(
                            "votos a favor: " + this.ideas[i].votos_a_favor,
                            290
                        );
                        var ideacontra = doc.splitTextToSize(
                            "votos en contra: " + this.ideas[i].votos_en_contra,
                            290
                        );
                        var ideaabstencion = doc.splitTextToSize(
                            "votos en abstención: " + this.ideas[i].abstencion,
                            290
                        );
                        doc.setFontSize(15);
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitidea);
                        y = y + 5;
                        var splitresumenI = doc.splitTextToSize(this.ideas[i].detalle, 230);
                        doc.setFontSize(11);
                        for (var k = 0; k < splitresumenI.length; k++) {
                            if (y > 275) {
                                y = 20;
                                doc.addPage();
                            }
                            doc.text(20, y, splitresumenI[k]);
                            y = y + 5;
                        }
                        if (y + 50 > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        j = y;
                        y = y + 15;
                        doc.setFontSize(11);
                        doc.setTextColor("green");
                        doc.text(20, y, ideafavor);
                        y = y + 5;
                        doc.setFontSize(11);
                        doc.setTextColor("red");
                        doc.text(20, y, ideacontra);
                        y = y + 5;
                        doc.setFontSize(11);
                        doc.setTextColor(100);
                        doc.text(20, y, ideaabstencion);
                        y = y + 25;
                        doc.setTextColor("black");
                        await htmlToImage
                            .toPng(document.getElementById("canvasI" + this.ideas[i].id))
                            .then(function(dataUrl) {
                                var img = dataUrl;
                                doc.addImage(img, "JPEG", 150, j, 40, 40);
                            })
                            .catch(function(error) {
                                console.error("oops, something went wrong!", error);
                            });
                    }
                }

                if (this.project.etapa === 2) {
                    //Articulos
                    y = y + 3;
                    var splitarticulos = doc.splitTextToSize(articulos, 200);
                    doc.setFontSize(18);
                    for (var i = 0; i < splitarticulos.length; i++) {
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitarticulos[i]);
                        y = y + 7;
                    }
                    j = y;
                    for (var i = 0; i < this.articles.length; i++) {
                        var splitarticle = doc.splitTextToSize(this.articles[i].titulo, 290);
                        var articlefavor = doc.splitTextToSize(
                            "votos a favor: " + this.articles[i].votos_a_favor,
                            290
                        );
                        var articlecontra = doc.splitTextToSize(
                            "votos en contra: " + this.articles[i].votos_en_contra,
                            290
                        );
                        var articleabstencion = doc.splitTextToSize(
                            "votos en abstención: " + this.articles[i].abstencion,
                            290
                        );
                        doc.setFontSize(15);
                        if (y > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        doc.text(20, y, splitarticle);
                        y = y + 5;
                        var splitresumenA = doc.splitTextToSize(
                            this.articles[i].detalle,
                            230
                        );
                        doc.setFontSize(11);
                        for (var k = 0; k < splitresumenA.length; k++) {
                            if (y > 275) {
                                y = 20;
                                doc.addPage();
                            }
                            doc.text(20, y, splitresumenA[k]);
                            y = y + 5;
                        }
                        if (y + 50 > 275) {
                            y = 20;
                            doc.addPage();
                        }
                        j = y;
                        y = y + 15;
                        doc.setFontSize(11);
                        doc.setTextColor("green");
                        doc.text(20, y, articlefavor);
                        y = y + 5;
                        doc.setFontSize(11);
                        doc.setTextColor("red");
                        doc.text(20, y, articlecontra);
                        y = y + 5;
                        doc.setFontSize(11);
                        doc.setTextColor(100);
                        doc.text(20, y, articleabstencion);
                        y = y + 25;
                        doc.setTextColor("black");
                        await htmlToImage
                            .toPng(document.getElementById("canvasA" + this.articles[i].id))
                            .then(function(dataUrl) {
                                var img = dataUrl;
                                doc.addImage(img, "JPEG", 150, j, 40, 40);
                            })
                            .catch(function(error) {
                                console.error("oops, something went wrong!", error);
                            });
                    }
                }
                doc.save("Informe-Proyecto.pdf");
                this.loadPDF = false
            },
            changeTab(e) {
                e.preventDefault();
                $(this).tab("show");
            },
            forceRerender() {
                this.keyStackedChartComponent += 1
            },
        }
    };
</script>

<style>
    #principalRow {
        height: fit-content;
    }

    #app {
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 2px;
        width: 100%;
        height: 100%;
    }

    .arrow-steps .step {
        font-size: 14px;
        cursor: default;
        padding: 10px 10px 10px 30px;
        float: left;
        position: relative;
        background-color: #d9e3f7;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        transition: background-color 0.2s ease;
    }

    .arrow-steps .step:after,
    .arrow-steps .step:before {
        content: " ";
        position: absolute;
        top: 0;
        right: -16px;
        width: 0;
        height: 0;
        border-top: 25px solid transparent;
        border-bottom: 25px solid transparent;
        border-left: 17px solid #9e9e9e !important;
        z-index: 2;
        transition: border-color 0.2s ease;
    }

    .arrow-steps .step:before {
        right: auto;
        left: 0;
        border-left: 17px solid #fff;
        z-index: 0;
    }

    .arrow-steps .step:first-child:before {
        border: none;
    }

    .arrow-steps .step:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .arrow-steps .step span {
        position: relative;
    }

    .arrow-steps .step span:before {
        opacity: 0;
        content: "✔";
        position: absolute;
        top: -2px;
        left: -20px;
    }

    .arrow-steps .step.done span:before {
        opacity: 1;
        -webkit-transition: opacity 0.3s ease 0.5s;
        -moz-transition: opacity 0.3s ease 0.5s;
        -ms-transition: opacity 0.3s ease 0.5s;
        transition: opacity 0.3s ease 0.5s;
    }

    .arrow-steps .step.current {
        color: #fff;
        background-color: green !important;
    }

    .arrow-steps .step.current:after {
        border-left: 17px solid green !important;
    }

    .tab-content {
        -webkit-box-shadow: inherit !important;
        box-shadow: inherit !important;
    }
</style>