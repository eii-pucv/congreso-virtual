<template>
    <div class="container">
        <div class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h2 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.recompensas_disponibles.titulo') }}</h2>
            <div class="mt-20 vld-parent">
                <div v-if="loadRewards" style="height: 300px;">
                    <Loading
                            :active.sync="loadRewards"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadRewards" class="row mx-0">
                    <div class="row mx-0">
                        <div v-for="reward in rewards" :key="'reward-' + reward.id" class="col-md-4 pb-3 px-2">
                            <div class="alert alert-secondary alert-wth-icon border h-100 mb-0" :class="playerAlreadyHasThisReward(reward.id) ? 'border-info' : 'border-secondary'">
                                <span v-if="reward.icon" class="alert-icon-wrap"><i :class="'fas fa-' + reward.icon"></i></span>
                                <p>{{ reward.name }}</p>
                                <small class="mt-5">{{ getTypeAndSubtypeTrans(reward.action.type, reward.action.subtype) }}</small>
                                <p class="mt-5">{{ reward.points }} {{ $t('perfil_usuario.componentes.recompensas_disponibles.puntos') }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="totalResults > rewards.length" class="px-2 w-100">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.recompensas_disponibles.cargar') }}
                            <Loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="false"
                                    :height="24"
                            ></Loading>
                        </button>
                    </div>
                    <h6
                            v-if="totalResults === 0 && !loadBtnLoadMore"
                            class="text-center"
                            :style="mode === 'dark' ? 'color: #fff' : ''"
                    >
                        {{ $t('perfil_usuario.componentes.recompensas_disponibles.no_hay_recompensas') }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'AvailableRewards',
        components: {
            Loading
        },
        props: {
            user_id: Number
        },
        data() {
            return {
                rewards: [],
                totalResults: 0,
                limit: 12,
                offset: 0,
                types: this.$t('perfil_usuario.componentes.recompensas_disponibles.tipo.opciones'),
                subtypes: this.$t('perfil_usuario.componentes.recompensas_disponibles.subtipo.opciones'),
                playerRewards: [],
                loadRewards: true,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getRewards();
            this.getPlayerRewards();
        },
        methods: {
            getRewards() {
                axios
                    .get('/rewards', {
                        params: {
                            order_by: 'created_at',
                            order: 'ASC',
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results;
                        this.rewards = this.rewards.concat(res.data.results);
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadRewards = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getRewards();
            },
            getPlayerRewards() {
                axios
                    .get('/players/' + this.user_id + '/rewards', {
                        params: {
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.playerRewards = this.playerRewards.concat(res.data.results);
                    });
            },
            getTypeAndSubtypeTrans(type, subtype) {
                let typeTrans = this.types.find(typeTrans => typeTrans.value === type).label;
                let subtypeTans = this.subtypes.find(subtypeTrans => subtypeTrans.value === subtype).label;

                if(typeTrans && subtypeTans) {
                    return (typeTrans) + ' - ' + (subtypeTans);
                } else if(typeTrans) {
                    return typeTrans;
                } else if(subtypeTans) {
                    return subtypeTans;
                } else {
                    return '';
                }
            },
            playerAlreadyHasThisReward(rewardId) {
                return !!(this.playerRewards.find(playerReward => playerReward.id === rewardId));
            }
        }
    }
</script>