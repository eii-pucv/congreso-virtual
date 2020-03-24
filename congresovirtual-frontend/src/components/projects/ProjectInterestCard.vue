<template>
    <div class="ma-5 col-lg-13">
        <div class="card" :class="mode==='dark'?'dark':'light'">
            <div class="d-sm-none">
                <div
                        v-if="project.etapa === 1 && isAvailableVoting"
                        class="bg-indigo-light-1 card-header d-block font-12 text-center text-white"
                >
                    {{ $t('votacion_general') }}
                </div>
                <div
                        v-else-if="project.etapa === 2 && isAvailableVoting"
                        class="bg-green card-header d-block font-12 text-center text-white"
                >
                    {{ $t('votacion_particular') }}
                </div>
                <div
                        v-else
                        class="bg-red-dark-3 card-header d-block font-12 text-center text-white"
                >
                    {{ $t('votacion_general') }}
                </div>
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
                                    <router-link
                                            v-for="term in project.terms.slice(0, 5)"
                                            :key="term.id"
                                            class="badge badge-light font-12 p-1 m-1"
                                            :to="{ path: '/projects', query: { 'terms_id[]': term.id } }"
                                    >
                                        {{ term.value }}
                                    </router-link>
                                </div>
                                <div class="col-1 col-md-2 text-right px-0">
                                    <a
                                            class="btn ma-5 text-white bg-indigo-light-2"
                                            data-toggle="modal"
                                            :data-target="'#myModal' + project.id"
                                    >
                                        <i class="fas fa-share-square"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 pt-15">
                                <p class="card-title text-center font-weight-bold font-15">
                                    {{ project.titulo }}
                                </p>
                                <div class="text-center mb-2">
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24 text-green"><i class="fas fa-thumbs-up"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                                        <span class="d-block display-6 text-green">{{ project.votos_a_favor }}</span>
                                    </div>
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24 text-red"><i class="fas fa-thumbs-down"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                                        <span class="d-block display-6 text-red">{{ project.votos_en_contra }}</span>
                                    </div>
                                    <div class="col-4 px-0" style="display: inline-block;">
                                        <span class="d-block font-24"><i class="fas fa-minus-circle"></i></span>
                                        <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                                        <span class="d-block display-6">{{ project.abstencion }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group btn-group-toggle d-block btn-group-vertical">
                                <a class="font-1"></a>
                                <router-link
                                        class="btn btn-success text-white d-block ma-0"
                                        :to="{ path: 'project/' + project.id }"
                                >
                                    <i class="fas fa-users"></i> {{ $t('participar') }}
                                </router-link>
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
                                <i class="fas fa-times"></i>
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
                                    <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                        <i class="fas fa-envelope"></i> Email
                                    </network>
                                    <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                        <span class="fab fa-facebook-square"></span> Facebook
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

    export default {
        name: 'ProjectInterestCard',
        components: {
            SocialSharing
        },
        props: {
            project: Object,
        },
        data() {
            return {
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                APP_URL,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.setIsAvailableVoting();
        },
        methods: {
            setIsAvailableVoting() {
                this.isAvailableVoting = this.project.is_enabled && this.project.etapa !== 3 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
            },
            getImgUrl() {
                if(this.project.imagen) {
                    return API_URL + '/api/storage/projects/' + this.project.id + '/' + this.project.imagen;
                }
                return null;
            }
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
