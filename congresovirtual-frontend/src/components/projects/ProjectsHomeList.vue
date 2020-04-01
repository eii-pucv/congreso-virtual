<template>
    <div>
        <div v-if="loadProjects" class="vld-parent pa-50" style="height: 550px;">
            <loading
                :active.sync="loadProjects"
                :is-full-page="fullPage"
            ></loading>
        </div>
        <div v-if="!loadProjects" class="container my-0">
            <div class="slider-container">
                <ul class="controls d-flex" id="customize-controls" tabindex="0">
                    <li class="prev btn bg-indigo-dark-2 text-white font-18 left-control" data-controls="prev" aria-controls="customize" tabindex="-1">
                        <i class="fas fa-angle-left"></i>
                    </li>
                    <li class="next btn bg-indigo-dark-2 text-white font-18 right-control" data-controls="next" aria-controls="customize" tabindex="-1">
                        <i class="fas fa-angle-right"></i>
                    </li>
                    <li id="btn-autoplay" class="btn btn-autoplay bg-indigo-dark-2 text-white font-18 mx-auto mb-2 d-none" data-controls="stop" tabindex="-1" style="width:43px;height:43px;"></li>
                </ul>
                <tiny-slider v-bind="tinySliderOptions" ref="tinySlider">
                    <ProjectHomeCard v-for="project in projects" :key="project.id" :project="project"></ProjectHomeCard>
                </tiny-slider>
            </div>
        </div>
        <div v-for="project in projects" :key="project.id">
            <div class="modal" :id="'myModal' + project.id">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $t('compartir') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <social-sharing :url="APP_URL + '/project/' + project.id"
                                :title="'Congreso Virtual '+ project.titulo"
                                :description="project.titulo"
                                :quote="project.titulo"
                                hashtags="congresovirtual"
                                inline-template
                            >
                                <div>
                                    <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                        <i class="fas fa-envelope"></i> Email
                                    </network>
                                    <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                        <i class="fab fa-facebook-square"></i> Facebook
                                    </network>
                                    <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                        <i class="fab fa-linkedin"></i> LinkedIn
                                    </network>
                                    <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                        <i class="fab fa-twitter"></i> Twitter
                                    </network>
                                    <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
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
    </div>
</template>

<script>
    import ProjectHomeCard from "./ProjectHomeCard";
    import axios from '../../backend/axios';
    import { APP_URL } from '../../data/globals';
    import Loading from 'vue-loading-overlay';
    import VueTinySlider from 'vue-tiny-slider';
    import SocialSharing from "vue-social-sharing";

    export default {
        name: 'ProjectsHomeList',
        components: {
            ProjectHomeCard,
            Loading,
            'tiny-slider': VueTinySlider,
            SocialSharing
        },
        data() {
            return {
                projects: [],
                currentMoment: this.$moment().local(),
                tinySliderOptions: {
                    nav: false,
                    mouseDrag: true,
                    touchDrag: true,
                    gutter: 20,
                    items: 1,
                    autoplay: false,
                    autoplayHoverPause: false,
                    autoplayButton: "#btn-autoplay",
                    autoplayTimeout: 4000,
                    autoplayText: [
                        "▶",
                        "❚❚"
                    ],
                    swipeAngle: false,
                    controlsContainer: '#customize-controls',
                    responsive: {
                        992: { items: 3 },
                        768: { items: 2 }
                    }
                },
                loadProjects: true,
                fullPage: false,
                APP_URL,
                breakpoints: {
                    600: {
                        perView: 1
                    },
                    800: {
                        perView: 2
                    }
                }
            };
        },
        mounted() {
            axios
            .get('/projects', {
                    params: {
                        'is_public': 1,
                        'order_by': 'fecha_inicio',
                        'order': 'DESC',
                        'limit': 10
                    }
                }
            )
            .then(res => {
                let auxProjects = res.data.results;
                this.projects = auxProjects.filter(project => this.getIsAvailableVoting(project));
                this.projects = this.projects.concat(auxProjects.filter(project => !this.getIsAvailableVoting(project)));
            })
            .finally(() => {
                this.loadProjects = false;
            });
        },
        methods: {
            getIsAvailableVoting(project) {
                let votingStartDate = this.$moment.utc(project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();
                return project.is_enabled && project.etapa !== 3 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
            }
        }
    }
</script>

<style scoped>
    .left-control, .right-control {
        position: absolute;
        z-index: 3;
        margin-top: 240px;
    }

    .left-control {
        left: -3%;
    }

    .right-control {
        right: -3%;
    }
</style>
