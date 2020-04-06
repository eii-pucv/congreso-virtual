<template>
    <v-popover
            @show="showRewards"
            :popover-class="'custom-popover-theme'"
            :popover-inner-class="'tooltip-inner custom-popover-inner custom-scrollbar-wk custom-scrollbar-mz'"
    >
        <button class="btn text-white tooltip-target">
            <i class="fas fa-bell"></i>
        </button>
        <template slot="popover">
            <div>
                <h5 class="mb-10">{{ $t('navbar.notificaciones.titulo') }}</h5>
                <hr class="my-10">
                <div v-if="loadRewards" class="vld-parent" style="height: 60vh;">
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
                        <div class="alert alert-secondary alert-wth-icon border border-info">
                            <span v-if="reward.icon" class="alert-icon-wrap"><i :class="'fas fa-' + reward.icon"></i></span>
                            <p>{{ reward.name }}</p>
                            <small>{{ reward.action.type }} {{ reward.action.subtype }}</small>
                            <p class="alert-link mt-5">{{ reward.points }} {{ $t('navbar.notificaciones.puntos') }}</p>
                        </div>
                    </div>
                    <div v-if="totalResults > rewards.length" class="px-2 w-100">
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
    </v-popover>
</template>

<script>
    import axios from '../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Notifications',
        components: {
            Loading
        },
        data: function() {
            return {
                rewards: [],
                totalResults: 0,
                limit: 5,
                offset: 0,
                popoverOptions: {
                    popover: {
                        defaultClass: 'vue-popover-theme',
                        defaultInnerClass: 'tooltip-inner popover-inner vue-popover-inner custom-scrollbar-wk custom-scrollbar-mz'
                    }
                },
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
        },
        methods: {
            getRewards() {
                axios
                    .get('/events', {
                        params: {
                            has_rewards: 1,
                            player_id: JSON.parse(localStorage.user).id,
                            order_by: 'created_at',
                            order: 'DESC',
                            limit: 10,
                            offset: 0
                        }
                    })
                    .then(res => {
                        let events = res.data.results;
                        this.totalResults = res.data.total_results;
                        this.rewards = this.rewards.concat(...events.map(event => event.rewards));
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
            }
        }
    }
</script>

<style>
    .custom-popover-theme {
        width: 400px;
        max-width: 400px !important;
        height: 80vh;
        max-height: 80vh !important;
        padding: 1px 1px 1px 1px;
    }

    .custom-popover-inner {
        width: 396px;
        max-width: 396px !important;
        height: 79vh;
        max-height: 79vh !important;
        overflow-y: auto;
    }
</style>