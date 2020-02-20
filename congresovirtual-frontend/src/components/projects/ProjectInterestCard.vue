<template>
    <div class="ma-5 col-lg-13">
        <div class="card" :class="mode==='dark'?'dark':'light'">
            <div class="d-sm-none">
                <div v-if="project.etapa === 1 && project.fecha_termino > this.today" class="bg-indigo-light-1 card-header d-block font-12 text-center text-white">{{ $t('votacion_general') }}</div>
                <div v-if="project.etapa === 2 && project.fecha_termino > this.today" class="bg-green card-header d-block font-12 text-center text-white">{{ $t('votacion_particular') }}</div>
                <div v-if="project.etapa === 3 || project.fecha_termino <= this.today" class="bg-red-dark-3 card-header d-block font-12 text-center text-white">{{ $t('votacion_general') }}</div>
            </div>
            <div class="btn-group-vertical mt-auto">
                <div class="embed-responsive card-body">
                    <div class="row d-flex embed-responsive " style="margin-left: 0%">
                        <div class="col-sm-4 col-12 px-0 embed-responsive embed-responsive-4by3">
                            <img
                                    class="embed-responsive-item default-project-img"
                                    :src="getImgUrl()"
                                    style="object-fit: cover;"
                            />
                        </div>
                        <div class="col-12 col-sm-8 ma-0 pa-0">
                            <div class="d-none d-sm-block">
                                <div class=" btn-group btn-group-toggle d-block">
                                    <div class="font-1"></div>
                                    <div v-if="project.etapa === 2 && project.fecha_termino > this.today" class="bg-green card-header d-block font-12 text-center text-white">{{ $t('votacion_particular') }}</div>
                                </div>
                                <div class=" btn-group btn-group-toggle d-block">
                                    <div class="font-1"></div>
                                    <div v-if="project.etapa === 3 || project.fecha_termino <= this.today" class="bg-red-dark-3 card-header d-block font-12 text-center text-white">{{ $t('votacion_general') }}</div>
                                </div>
                                <div class=" btn-group btn-group-toggle d-block">
                                    <div class="font-1"></div>
                                    <div v-if="project.etapa === 1 && project.fecha_termino > this.today" class="bg-indigo-light-1 card-header d-block font-12 text-center text-white">{{ $t('votacion_general') }}</div>
                                </div>
                            </div>
                            <div class="row mt-2 mx-0">
                                <div class="col-11 col-md-10" style="max-width: 80%;">
                                    <a v-for="term in project.terms.slice(0, 5)" :href="'/projects?terms_id[]=' + term.id" :key="term.id" class="badge badge-light font-12 p-1 m-1">{{ term.value }}</a>
                                </div>
                                <div class="col-1 col-md-2 text-right px-0">
                                    <a
                                            class="btn ma-5 text-white bg-indigo-light-2"
                                            data-toggle="modal"
                                            :data-target="'#myModal' + project.id"
                                    >
                                        <span class="btn-text"><font-awesome-icon icon="share-square"/></span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 pt-15">
                                <p class="card-title text-center font-weight-bold font-15">
                                    {{ project.titulo }}
                                </p>
                                <div class="text-center mb-2">
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24 text-green"><i class="fa fa-thumbs-up"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                                        <span class="d-block display-6 text-green">{{ project.votos_a_favor }}</span>
                                    </div>
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24 text-red"><i class="fa fa-thumbs-down"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                                        <span class="d-block display-6 text-red">{{ project.votos_en_contra }}</span>
                                    </div>
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24"><font-awesome-icon icon="minus-circle"/></span>
                                        <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                                        <span class="d-block display-6">{{ project.abstencion }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group btn-group-toggle d-block btn-group-vertical">
                                <a class="font-1 "></a>
                                <router-link :to="{ path: 'project/' + project.id, query: { mode: this.mode }}" class="btn btn-success text-white d-block ma-0"><i class="fa fa-users"></i> <span class="btn-text"> {{ $t('participar') }}</span></router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal" :id="'myModal' + project.id">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $t('compartir') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span><i class="fa fa-times"></i></span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <social-sharing :url="APP_URL + '/project/' + project.id"
                                            :title="'Congreso Virtual ' + project.titulo"
                                            :description="project.titulo"
                                            :quote="project.titulo"
                                            hashtags="congresovirtual"
                                            inline-template
                            >
                                <div>
                                    <network
                                            class="btn btn-block btn-social btn-email bg-red-light-2 text-white"
                                            network="email"
                                    >
                                        <i class="fa fa-envelope"></i> Email
                                    </network>
                                    <network
                                            class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white"
                                            network="facebook"
                                    >
                                        <span class="fa fa-facebook"></span> Facebook
                                    </network>
                                    <network
                                            class="btn btn-block btn-social bg-blue-dark-2 text text-white"
                                            network="linkedin"
                                    >
                                        <i class="fa fa-linkedin"></i> LinkedIn
                                    </network>
                                    <network
                                            class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white"
                                            network="twitter"
                                    >
                                        <i class="fa fa-twitter"></i> Twitter
                                    </network>
                                    <network
                                            class="btn btn-block btn-social bg-green-light-1 text text-white"
                                            network="whatsapp"
                                    >
                                        <i class="fa fa-whatsapp"></i> WhatsApp
                                    </network>
                                </div>
                            </social-sharing>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                {{ $t('cerrar') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SocialSharing from 'vue-social-sharing';
    import { API_URL } from '../../backend/data_server';
    import { APP_URL } from '../../data/globals';
    require("select2/dist/js/select2.full.min");
    require("../../../src/assets/js/select2-data");

    export default {
        name: 'ProjectInterestCard',
        components: { SocialSharing },
        props: {
            project: Object,
        },
        data() {
            return {
                chartData: {
                    labels: ["A favor", "En contra", "Abstención"],
                    datasets: [
                        {
                            label: "Proyecto",
                            backgroundColor: ["#41B883", "#E46651", "#00D8FF"],
                            data: [
                                this.project.votos_a_favor,
                                this.project.votos_en_contra,
                                this.project.abstencion
                            ]
                        }
                    ]
                },
                today: null,
                votes: [],
                vote: {
                    project_id: null,
                    user_id: null,
                    vote: null
                },
                project_id2: this.project_id,
                APP_URL,
                mode: String
            };
        },
        methods: {
            getImgUrl() {
                if(this.project.imagen) {
                    return API_URL + '/api/storage/projects/' + this.project.id + '/' + this.project.imagen;
                }
                return null;
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy +'-'+ mm + '-' + dd ;
            this.today = today;
        }
    };
</script>

<style>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
        border-color: #fff;
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>
