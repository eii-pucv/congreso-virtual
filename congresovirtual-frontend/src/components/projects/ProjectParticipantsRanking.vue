<template>
    <div class="container">
        <h3 class="text-center">{{ $t('proyecto.componentes.ranking_participantes.titulo') }}</h3>
        <div v-if="loadParticipants" class="vld-parent" style="height: 300px;">
            <Loading
                    :active.sync="loadParticipants"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="'#000000'"
            ></Loading>
        </div>
        <div v-if="!loadParticipants" class="mt-20">
            <div v-if="participants.length === 0" class="text-center">{{ $t('proyecto.componentes.ranking_participantes.no_hay_participantes') }}</div>
            <div v-else class="list-group">
                <div v-for="(participant, index) in participants" :key="participant.user_id" class="list-group-item list-group-item-action">
                    <div class="row ma-0 pa-0">
                        <div class="col-sm-2 text-center align-self-center pa-10">
                            <span v-if="index === 0" class="font-25" style="color: #efb810;"><i class="fas fa-trophy fa-lg"></i></span>
                            <span v-else-if="index === 1" class="font-25" style="color: #e3e4e5;"><i class="fas fa-trophy fa-lg"></i></span>
                            <span v-else-if="index === 2" class="font-25" style="color: #cd7f32;"><i class="fas fa-trophy fa-lg"></i></span>
                            <span v-else class="font-25"><i class="fas fa-medal fa-lg"></i></span>
                        </div>
                        <div class="col-sm-10 align-self-center pa-10">
                            <router-link class="h6" :to="{ path: '/user', query: { 'user_id': participant.user_id } }">
                                {{ participant.name }} {{ participant.surname }}
                            </router-link>
                            <p>{{ participant.project_total_points }} {{ $t('proyecto.componentes.ranking_participantes.puntos') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import { API_URL } from '../../backend/data_server';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectParticipantsRanking',
        components: {
            Loading
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                participants: [],
                loadParticipants: true,
                fullPage: false,
                color: '#ffffff',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getParticipants();
        },
        methods: {
            getParticipants() {
                axios
                    .get('/gamification/ranking_project_players_rewards_actions', {
                        params: {
                            project_id: this.project_id,
                            order_by: 'project_total_points',
                            order: 'DESC',
                            limit: 10,
                            offset: 0
                        }
                    })
                    .then(res => {
                        this.participants = res.data.results;
                    })
                    .finally(() => {
                        this.loadParticipants = false;
                    });
            },
            getParticipantAvatarImgUrl(userId, userAvatar) {
                if(userAvatar) {
                    return (API_URL + '/api/storage/avatars/' + userId + '/' + userAvatar);
                }
                return null;
            },
        }
    }
</script>