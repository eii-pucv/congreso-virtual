<template>
    <div v-if="projects.length" id="carouselFade" class="carousel slide carousel-fade" ref="carousel">
        <div v-if="loadCarrousel" class="load-carousel vld-parent pa-50 d-flex">
            <loading
                :active.sync="loadCarrousel"
                :is-full-page="fullPage">
            </loading>
        </div>
        <div class="carousel-inner d-flex" v-if="!loadCarrousel">
            <div
                    v-for="(project, index) in projects"
                    :key="project.id"
                    class="carousel-item d-flex pb-50 pt-50"
                    v-bind:style="{ 'background-image': 'url(' + getImgUrl(project.id, project.imagen) + ')' }"
                    v-bind:class="[(index === 0) ? 'active' : '']"
            >
                <div class="container d-flex h-100 px-50 align-items-center justify-content-center">
                    <div class="row w-100">
                        <div class="col-sm-8 justify-content-center my-auto pb-10">
                            <div class="text-justify">
                                <h4 class="text-white font-weight-bold ellipsis mb-20">{{ project.titulo }}</h4>
                                <div class="text-white font-weight-bold font-13 ellipsis">{{ project.resumen }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4 justify-content-center my-auto">
                            <div class="card card-profile-feed" style="background-color: inherit; border:inherit;">
                                <div
                                        v-if="project.etapa === 1 && getIsAvailableVoting(project.id)"
                                        class="bg-white text-primary text-center font-20"
                                >
                                    {{ $t('votacion_general') }}
                                </div>
                                <div
                                        v-else-if="project.etapa === 2 && getIsAvailableVoting(project.id)"
                                        class="bg-white text-success text-center font-20"
                                >
                                    {{ $t('votacion_particular') }}
                                </div>
                                <div
                                        v-else
                                        class="bg-white text-danger text-center font-20"
                                >
                                    {{ $t('votacion_cerrada') }}
                                </div>
                                <div class="row mx-0 text-center text-white my-3">
                                    <div class="col-4 px-0">
                                        <span class="d-block font-24"><i class="fa fa-thumbs-up"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                                        <span class="d-block display-6">{{ project.votos_a_favor }}</span>
                                    </div>
                                    <div class="col-4 px-0">
                                        <span class="d-block font-24"><i class="fa fa-thumbs-down"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                                        <span class="d-block display-6">{{ project.votos_en_contra }}</span>
                                    </div>
                                    <div class="col-4 px-0">
                                        <span class="d-block font-24"><i class="fa fa-minus-circle"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                                        <span class="d-block display-6">{{ project.abstencion }}</span>
                                    </div>
                                </div>
                                <router-link :to="{ path: 'project/' + project.id }" class="btn btn-success text-white">
                                    <i class="fa fa-users"></i> <span class="btn-text"> {{ $t('home.componentes.carrusel_proyectos.participar') }}</span>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-autoplay position-relative text-center">
            <button class="btn-autoplay-projects btn btn-outline-secondary btn-rounded position-absolute" @click="toggle()">
                <span class="text-white" v-if="toggleValue === 'cycle'">▶</span>
                <span class="text-white" v-else>❚❚</span>
            </button>
        </div>
        <a class="carousel-control-prev" href="#carouselFade" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselFade" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import { API_URL } from '../../backend/data_server';
    import { library } from '@fortawesome/fontawesome-svg-core';
    import { faPause } from '@fortawesome/free-solid-svg-icons/faPause';
    import { faPlay } from '@fortawesome/free-solid-svg-icons/faPlay';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    library.add(faPause, faPlay);
    export default {
        name: 'ProjectsCarousel',
        components: { Loading },
        data() {
            return {
                loadCarrousel: true,
                projects: [],
                currentMoment: this.$moment().local(),
                fullPage: false,
                toggleValue: 'cycle',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            axios
                .get('/projects', {
                    params: {
                        'is_public': 1,
                        'is_principal': 1,
                        'is_enabled': 1,
                        'order_by': 'fecha_inicio',
                        'order': 'DESC',
                        'limit': 10
                    }
                })
                .then(res => {
                    this.projects = res.data.results;
                })
                .finally(() => {
                    this.loadCarrousel = false;
                    this.$nextTick(function () {
                        $('#carouselFade').carousel({
                            interval: 4000,
                            pause: 'false'
                        });
                        this.toggle();
                        $('.carousel-control-next').trigger('click');
                    })
                })
        },
        methods:{
            getImgUrl(projectId, projectImage) {
                return API_URL + '/api/storage/projects/' + projectId + '/' + projectImage;
            },
            getIsAvailableVoting(projectId) {
                let project = this.projects.find(project => project.id === projectId);
                let votingStartDate = this.$moment.utc(project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();

                return project.is_enabled && project.etapa !== 3 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
            },
            toggle(){
                $('#carouselFade').carousel(this.toggleValue);
                if(this.toggleValue === 'pause') {
                    this.toggleValue = 'cycle';
                } else {
                    this.toggleValue = 'pause';
                }
            }
        }
    }
</script>

<style scoped>
    .ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 5;
        line-height: 1.4em;
        max-height: 6em;
        -webkit-box-orient: vertical;
        white-space: inherit;
        height: inherit;
        -webkit-line-clamp: 6;
        line-height: 1.4em;
        max-height: 9em;
        white-space: inherit;
        height: inherit;
    }
    .load-carousel {
        background-size: cover;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        background-blend-mode: overlay;
        height: 475px;
    }
    .carousel {
        height: 500px;
    }
    .carousel-item {
        background-size: cover;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        background-blend-mode: overlay;
    }
    .carousel-item > img {
        position: absolute;
        top: 0;
        left: 0;
        min-width: 100%;
        height: 32rem;
    }

    .btn-autoplay-projects {
        width: 50px;
        height: 50px;
        bottom: 30px;
        z-index:1;
    }

    @media (max-width: 575px) {
        .carousel {
            height: 750px;
        }
        .container-autoplay {
            margin-right: 30px;
        }
        .btn-autoplay-projects {
            width: 40px;
            height: 40px;
        }
        .btn-autoplay-projects span{
            font-size: 14px;
        }
    }

    @media (min-width: 40em) {
        .carousel-caption p {
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
            line-height: 1.4;
        }
    }

    @media (min-width: 40em) {
        .carousel-caption p {
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
            line-height: 1.4;
        }
    }
</style>
