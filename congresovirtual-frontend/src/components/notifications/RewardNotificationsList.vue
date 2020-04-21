<template>
    <div>
        <div v-if="loadRewards" class="vld-parent" style="height: 300px;">
            <Loading
                    :active.sync="loadRewards"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="color"
                    :background-color="backgroundColor"
            ></Loading>
        </div>
        <div v-else-if="!loadRewards">
            <div v-for="reward in rewards" :key="reward.id">
                <div class="alert alert-secondary alert-wth-icon border" :class="reward.last_rewards ? 'border-info' : 'border-secondary'">
                    <span v-if="reward.icon" class="alert-icon-wrap"><i :class="'fas fa-' + reward.icon"></i></span>
                    <div class="row ml-0">
                        <div class="col-10 px-0">
                            <p>{{ reward.name }}</p>
                            <small class="mt-5">{{ getTypeAndSubtypeTrans(reward.action.type, reward.action.subtype) }}</small>
                            <p class="mt-5">{{ reward.points }} {{ $t('navbar.notificaciones.puntos') }}</p>
                        </div>
                        <div class="col-2 text-center px-0">
                            <div class="badge" :class="reward.last_rewards ? 'badge-info' : 'badge-secondary'">x {{ reward.quantity }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="totalResults > rewards.length" class="w-100 pb-2">
                <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('navbar.notificaciones.cargar') }}
                    <Loading
                            :active.sync="loadBtnLoadMore"
                            :is-full-page="false"
                            :height="24"
                    ></Loading>
                </button>
            </div>
            <h6 v-if="totalResults === 0 && !loadBtnLoadMore" class="text-center">
                {{ $t('navbar.notificaciones.no_hay_recompensas') }}
            </h6>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'RewardNotificationsList',
        components: {
            Loading
        },
        data() {
            return {
                rewards: [],
                totalResults: 0,
                limit: 5,
                offset: 0,
                types: [],
                subtypes: [],
                loadRewards: false,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#000000',
                backgroundColor: 'transparent',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
            this.showRewards();
        },
        methods: {
            configComponent() {
                this.types = this.$t('navbar.notificaciones.tipo.opciones');
                this.subtypes = this.$t('navbar.notificaciones.subtipo.opciones');
            },
            getRewards() {
                axios
                    .get('/events', {
                        params: {
                            has_rewards: 1,
                            player_id: this.$store.getters.userData.id,
                            order_by: 'created_at',
                            order: 'DESC',
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        let events = res.data.results;
                        let globalCompressedRewards = [];
                        events.forEach((event, eventIndex) => {
                            let eventCompressedRewards = [];
                            event.rewards.forEach(reward => {
                                let compressedReward = eventCompressedRewards.find(eventCompressedReward => eventCompressedReward.id === reward.id);
                                if(!compressedReward) {
                                    let auxReward = reward;
                                    auxReward.quantity = 1;
                                    auxReward.last_rewards = eventIndex === 0 && this.offset === 0;
                                    eventCompressedRewards.push(auxReward);
                                } else {
                                    compressedReward.quantity++;
                                }
                            });
                            globalCompressedRewards.push(eventCompressedRewards);
                        });

                        this.totalResults = res.data.total_results;
                        this.rewards = this.rewards.concat(...globalCompressedRewards);
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadRewards = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            showRewards() {
                this.loadRewards = true;
                this.getRewards();
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getRewards();
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
            }
        }
    }
</script>