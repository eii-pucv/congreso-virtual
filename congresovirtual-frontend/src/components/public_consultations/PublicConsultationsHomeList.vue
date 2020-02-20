<template>
    <div>
        <div v-if="loadPublicConsultations" class="vld-parent pa-50 center" style="height: 250px">
            <loading :active.sync="loadPublicConsultations" :is-full-page="fullPage"></loading>
        </div>
        <div v-if="!loadPublicConsultations">
            <carousel
                    :per-page="1"
                    :pagination-enabled="false"
                    :mouse-drag="true"
                    :touch-drag="true"
                    :navigation-enabled="true"
                    navigation-prev-label="<div class='btn bg-indigo-dark-2 text-white font-18'><span><i class='fa fa-angle-left'></i></span></div>"
                    navigation-next-label="<div class='btn bg-indigo-dark-2 text-white font-18'><span><i class='fa fa-angle-right'></i></span><div>"
                    :paginationColor="'#A8A8A8'"
            >
                <slide v-for="publicConsultation in publicConsultations" :key="publicConsultation.id">
                    <PublicConsultationHomeCard :publicConsultation="publicConsultation"></PublicConsultationHomeCard>
                </slide>
            </carousel>
        </div>
        <div v-for="(n, index) in publicConsultations.length" :key="'public_consultations-' + index">
            <div class="modal" :id="'myModal' + publicConsultations[n-1].id">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $t('compartir') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span><i class="fa fa-times"></i></span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <social-sharing
                                    :url="APP_URL + '/public_consultation/' + publicConsultations[n-1].id"
                                    :title="'Congreso Virtual ' + publicConsultations[n-1].titulo"
                                    :description="publicConsultations[n-1].titulo"
                                    :quote="publicConsultations[n-1].titulo"
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
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $t('cerrar') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <router-link :to="{ path: 'public_consultations' }" class="btn btn-primary text-white my-1"><i class="fa fa-eye"></i> <span class="btn-text"> {{ $t('home.contenido.boton_consultas') }}</span></router-link>
        </div>
    </div>
</template>

<script>
    import PublicConsultationHomeCard from './PublicConsultationHomeCard';
    import axios from '../../backend/axios';
    import { APP_URL } from '../../data/globals';
    import SocialSharing from 'vue-social-sharing';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        name: 'PublicConsultationsHomeList',
        components: {
            PublicConsultationHomeCard,
            Loading,
            SocialSharing
        },
        data() {
            return {
                publicConsultations: [],
                loadPublicConsultations: true,
                fullPage: false,
                APP_URL
            };
        },
        mounted() {
            axios
                .get('/consultations', {
                    params: {
                        order_by: 'fecha_inicio',
                        order: 'DESC',
                        limit: 5
                    }
                })
                .then(res => {
                    this.publicConsultations = res.data.results;
                })
                .finally(() => {
                    this.loadPublicConsultations = false;
                });
        }
    };
</script>

<style scoped>
    .VueCarousel-dot--active {
        background-color: #3650ab !important;
    }
    .VueCarousel-navigation-button {
        background: #3650ab !important;
        padding: 0.8rem 1rem !important;
        color: white !important;
    }
</style>
