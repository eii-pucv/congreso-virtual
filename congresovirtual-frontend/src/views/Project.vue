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
                        id="project-header"
                        :data-intro="$t('proyecto.ruta_guiada.pasos.paso_2')"
                        data-step="2"
                        v-if="!loadProject"
                        :project="project"
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
                        >
                            {{ project.boletin }}
                        </li>
                    </ol>
                </nav>
                <div v-if="!loadProject" class="hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);color: #fff':''">
                    <div class="row px-10">
                        <div class="col-sm-8">
                            <ul
                                    id="myTab"
                                    class="nav nav-light nav-tabs"
                                    role="tablist"
                                    :data-intro="$t('proyecto.ruta_guiada.pasos.paso_1')"
                                    data-step="1"
                            >
                                <li
                                        id="list-sections"
                                        class="nav-item active"
                                        :data-intro="$t('proyecto.ruta_guiada.pasos.paso_3')"
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
                                    >
                                        {{ $t('proyecto.contenido.tab.articulos_ideas') }}
                                    </a>
                                </li>
                                <li id="section-info" 
                                    class="nav-item"
                                    :data-intro="$t('proyecto.ruta_guiada.pasos.paso_5')"
                                    data-step="5"
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
                                    >
                                        {{ $t('proyecto.contenido.tab.detalle') }}
                                    </a>
                                </li>
                                <li id="section-traces" class="nav-item"
                                    :data-intro="$t('proyecto.ruta_guiada.pasos.paso_6')"
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
                                    >
                                        {{ $t('proyecto.contenido.tab.seguimiento') }}
                                    </a>
                                </li>
                                <li id="section-analysis" class="nav-item"
                                    :data-intro="$t('proyecto.ruta_guiada.pasos.paso_7')"
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
                                    >
                                        {{ $t('proyecto.contenido.tab.estadistica') }}
                                    </a>
                                </li>
                                <li id="section-documents" 
                                    class="nav-item"
                                    :data-intro="$t('proyecto.ruta_guiada.pasos.paso_8')"
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
                                    >
                                        {{ $t('proyecto.contenido.tab.documentos') }}
                                    </a>
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
                                                        <div id="section-votes" 
                                                            class="step col-sm"
                                                            v-bind:class="[(project.etapa === 1) ? 'current bg-primary' : '', (project.etapa === 2) ? 'bg-secondary' : '']"
                                                        >
                                                            <h6 class="text-white">{{ $t('proyecto.contenido.votacion_general.titulo') }}</h6>
                                                            <p class="text-white my-5">
                                                                {{ $t('proyecto.contenido.votacion_general.descripcion1') }}
                                                                <v-popover>
                                                                    <strong>{{ $t('proyecto.contenido.votacion_general.strong') }} </strong>
                                                                    <span class="tooltip-target b3 font-16"><i class="fas fa-question-circle"></i></span>
                                                                    <template slot="popover" style="background-color: #1c7430 !important;">
                                                                        <span class="font-11">
                                                                            {{ $t('proyecto.contenido.votacion_general.popover.primero') }}
                                                                            <strong>{{ $t('proyecto.contenido.votacion_general.popover.strong') }}</strong> {{ $t('proyecto.contenido.votacion_general.popover.segundo') }}
                                                                        </span>
                                                                    </template>
                                                                </v-popover>
                                                                {{ $t('proyecto.contenido.votacion_general.descripcion2') }}
                                                            </p>
                                                            <button id="btn-ideas" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#collapseIdeas" aria-expanded="false" aria-controls="collapseIdeas">
                                                                {{ $t('mostrar') }}
                                                            </button>
                                                        </div>
                                                        <div class="step col-sm" v-bind:class="{ 'current bg-primary': (project.etapa === 2) }" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                                            <h6 class="font-weight-lighter" v-bind:class="{ 'text-white': (project.etapa === 2) }" :style="mode==='dark'?'color: #fff':''">
                                                                {{ $t('proyecto.contenido.votacion_particular.titulo') }}
                                                            </h6>
                                                            <p class="font-weight-lighter my-5" v-bind:class="{ 'text-white': (project.etapa === 2) }">
                                                                {{ $t('proyecto.contenido.votacion_particular.descripcion1') }}
                                                                <v-popover>
                                                                    <strong>{{ $t('proyecto.contenido.votacion_particular.strong') }} </strong>
                                                                    <span class="tooltip-target b3 font-16"><i class="fas fa-question-circle"></i></span>
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
                                            <ProjectIdeas :project="project" @updateStackedChartVotos="forceRerender"></ProjectIdeas>
                                        </div>
                                        <div class="collapse w-100" id="collapseArticles">
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
                                    <ProjectTraces v-if="project.id" :project="project"></ProjectTraces>
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
                                                <h3 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">
                                                    {{ $t('proyecto.contenido.analisis_votos') }}
                                                </h3>
                                            </div>
                                            <div class="col-md-12 mt-15 mb-10">
                                                <p class="hk-sec-title" :style="mode==='dark'?'color: #fff':''">
                                                    {{ $t('proyecto.contenido.recomendacion') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-15 mb-15">
                                                <div class="col-12">
                                                    <ProjectBarCharts v-if="project.id" :project="project" :key="keyStackedChartComponent"></ProjectBarCharts>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="row mx-0 text-center">
                                            <div class="col-6 py-15">
                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.cantidad_comentarios') }}</h5>
                                                <h2 class="my-15" :style="mode==='dark'?'color: #fff':''">{{ project.total_comments }}</h2>
                                            </div>
                                            <div class="col-6 py-15">
                                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.contenido.cantidad_participantes') }}</h5>
                                                <h2 class="my-15" :style="mode==='dark'?'color: #fff':''">{{ project.total_participants }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <ProjectPieCharts v-if="project.id" :project="project"></ProjectPieCharts>
                                    </div>
                                    <div class="card"
                                         :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
                                         :class="mode==='dark'?'':'bg-light-15'"
                                    >
                                        <div class="row mx-0 justify-content-center">
                                            <h3 class="hk-sec-title mt-20" :style="mode==='dark'?'color: #fff':''">
                                                {{ $t('proyecto.contenido.nube') }}
                                            </h3>
                                        </div>
                                        <div class="pa-20">
                                            <WordCloud v-if="project.id" :project_id="project.id"></WordCloud>
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
                            <ProjectComments v-if="project.id" :project="project"></ProjectComments>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMessage" tabindex="-1" role="dialog" aria-labelledby="modalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Votación Realizada</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Además de votar a favor, en contra o abstención sobre un proyecto, puede hacerlo también con las <strong>Ideas Fundamentales</strong> y <strong>Artículos</strong> del proyecto de ley actual.</p>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</a>
                    <a href="#section-votes" type="button" class="btn btn-primary" @click="focusVotesSection($event)">Aceptar</a>
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
    import ProjectPieCharts from '../components/projects/ProjectPieCharts';
    import ProjectFiles from '../components/projects/ProjectFiles';
    import WordCloud from '../components/projects/WordCloud';
    import axios from '../backend/axios';
    import axioma from 'axios';
    import 'intro.js/minified/introjs.min.css';
    import { bus } from '../main';
    
    export default {
        name: 'Project',
        components: {
            ProjectHeader,
            ProjectComments,
            ProjectIdeas,
            ProjectArticles,
            ProjectTraces,
            ProjectBarCharts,
            ProjectPieCharts,
            ProjectFiles,
            WordCloud
        },
        props: {
            project_id: Number,
            mode: String
        },
        data() {
            return {
                project: Object,
                seguimiento: [],
                keyStackedChartComponent: 0,
                loadProject: true,
                fullPage: false,
                color: '#ffffff'
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getProject();

            let lablesButtons = {
                nextLabel: this.$t('proyecto.ruta_guiada.opciones.boton_siguiente'),
                prevLabel: this.$t('proyecto.ruta_guiada.opciones.boton_anterior'),
                skipLabel: this.$t('proyecto.ruta_guiada.opciones.boton_salir'),
                doneLabel: this.$t('proyecto.ruta_guiada.opciones.boton_finalizar')
            };
            bus.$on('tour', function() {
                const introJS = require('intro.js');     
                introJS
                    .introJs()
                    .setOption('nextLabel', lablesButtons.nextLabel)
                    .setOption('prevLabel', lablesButtons.prevLabel)
                    .setOption('skipLabel', lablesButtons.skipLabel)
                    .setOption('doneLabel', lablesButtons.doneLabel)
                    .setOption('scrollToElement', true)
                    .start()
                    .onchange(function (targetElement) {
                        if($(targetElement).attr("data-step") == 4) {
                            $('#btn-ideas').click();
                        }
                    })
            });
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
            focusVotesSection(e) {
                $('#modalMessage').modal('hide')

                $('html, body').animate({
                    scrollTop: $('#section-votes').offset().top + "px"
                }, 1500);
                $('#btn-ideas').click();
            },
            getProject() {
                axios
                    .get('/projects/' + this.project_id)
                    .then(res => {
                        this.project = res.data;
                    })
                    .finally(() => {
                        axioma
                            .create()
                            .get('https://slr.senado.cl/proyectoInfo/0/' + this.project.boletin)
                            .then(res => {
                                this.seguimiento = res.data;
                            })
                            .finally(() => {
                                this.loadProject = false;
                            });
                    });
            },
            changeTab(e) {
                e.preventDefault();
                $(this).tab("show");
            },
            forceRerender() {
                this.keyStackedChartComponent += 1
            }
        }
    };
</script>

<style>
    #principalRow {
        height: fit-content;
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