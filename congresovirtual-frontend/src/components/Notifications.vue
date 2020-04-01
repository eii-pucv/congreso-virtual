<template>
    <v-popover
            @show="getRewards"
            :popover-class="'custom-popover-theme'"
            :popover-inner-class="'tooltip-inner custom-popover-inner custom-scrollbar-wk custom-scrollbar-mz'"
    >
        <button class="btn text-white tooltip-target">
            <i class="fas fa-bell"></i>
        </button>
        <template slot="popover">
            <div>
                <h5 class="mb-10">Notificaciones</h5>
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
                <div v-else-if="!loadRewards && rewards.length > 0">
                    <div v-for="reward in rewards" :key="reward.id">
                        <div class="alert alert-secondary alert-wth-icon alert-dismissible" role="alert">
                            <span class="alert-icon-wrap"><i class="fas fa-vote-yea"></i></span>
                            <p>{{ reward.name }}</p>
                            <small>{{ reward.action.type }} {{ reward.action.subtype }}</small>
                            <p class="alert-link mt-5">{{ reward.points }} puntos de participación</p>
                        </div>
                    </div>
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
                loadRewards: false,
                popoverOptions: {
                    popover: {
                        defaultClass: 'vue-popover-theme',
                        defaultInnerClass: 'tooltip-inner popover-inner vue-popover-inner custom-scrollbar-wk custom-scrollbar-mz',
                    }
                },
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
                this.loadRewards = true;

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
                        this.rewards = [].concat(...events.map(event => event.rewards));
                    })
                    .finally(() => {
                        this.loadRewards = false;
                    });
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