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
                    navigation-prev-label="<div class='btn bg-indigo-dark-2 text-white font-18'><span><i class='fas fa-angle-left'></i></span></div>"
                    navigation-next-label="<div class='btn bg-indigo-dark-2 text-white font-18'><span><i class='fas fa-angle-right'></i></span><div>"
                    :paginationColor="'#A8A8A8'"
            >
                <slide v-for="publicConsultation in publicConsultations" :key="publicConsultation.id">
                    <PublicConsultationHomeCard :publicConsultation="publicConsultation"></PublicConsultationHomeCard>
                </slide>
            </carousel>
        </div>
        <div class="text-center">
            <router-link class="btn btn-primary text-white my-1" :to="{ path: 'public_consultations' }">
                <i class="fas fa-eye"></i> {{ $t('home.contenido.boton_consultas') }}
            </router-link>
        </div>
    </div>
</template>

<script>
    import PublicConsultationHomeCard from './PublicConsultationHomeCard';
    import Loading from 'vue-loading-overlay';
    import axios from '../../backend/axios';
    import { APP_URL } from '../../data/globals';

    export default {
        name: 'PublicConsultationsHomeList',
        components: {
            PublicConsultationHomeCard,
            Loading
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
