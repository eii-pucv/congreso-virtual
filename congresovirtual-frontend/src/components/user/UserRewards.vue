<template>
    <div class="container">
        <div class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h2 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.recompensas.titulo') }}</h2>
            <div class="mt-20 vld-parent">
                <div v-if="loadRewards" style="height: 300px;">
                    <Loading
                            :active.sync="loadRewards"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-else-if="!loadRewards">
                    <div class="row mx-0">
                        <div v-for="reward in rewards" :key="'reward-' + reward.id + 'event-' + reward.event_id" class="col-md-4 pb-3 px-2">
                            <div class="alert alert-secondary alert-wth-icon border h-100 mb-0" :class="reward.last_rewards ? 'border-info' : 'border-secondary'">
                                <span v-if="reward.icon" class="alert-icon-wrap"><i :class="'fas fa-' + reward.icon"></i></span>
                                <div class="row h-100 ml-0">
                                    <div class="col-10 px-0">
                                        <p>{{ reward.name }}</p>
                                        <small class="mt-5">{{ getTypeAndSubtypeTrans(reward.action.type, reward.action.subtype) }}</small>
                                        <p class="mt-5">{{ reward.points }} {{ $t('navbar.notificaciones.puntos') }}</p>
                                    </div>
                                    <div class="col-2 text-center d-flex flex-column px-0">
                                        <div>
                                            <div class="badge" :class="reward.last_rewards ? 'badge-info' : 'badge-secondary'">x {{ reward.quantity }}</div>
                                        </div>
                                        <div v-if="!readOnly" class="mt-auto">
                                            <a @click.prevent="showRewardDetailModal(reward.id, reward.event_id)" class="badge badge-primary text-white">
                                                <i class="fas fa-eye font-12"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="totalResults > rewards.length" class="px-2 w-100">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.recompensas.cargar') }}
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
                        {{ $t('perfil_usuario.componentes.recompensas.no_hay_recompensas') }}
                    </h6>
                </div>
            </div>
            <b-modal
                    id="reward-detail-modal"
                    v-if="!readOnly && eventDataModal"
                    size="lg"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('perfil_usuario.componentes.recompensas.detalle.titulo') }}</h6>
                </template>
                <div class="row mb-20">
                    <div class="col-2 text-center">
                        <span v-if="eventDataModal.rewards[0].icon" class="alert-icon-wrap font-30"><i :class="'fas fa-' + eventDataModal.rewards[0].icon"></i></span>
                    </div>
                    <div class="col-10 my-auto">
                        <h6>{{ eventDataModal.rewards[0].name }}</h6>
                    </div>
                </div>
                <div class="row mb-20">
                    <div class="col-md-4">
                        <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.motivo') }}:</strong>
                        <p>{{ getTypeAndSubtypeTrans(eventDataModal.rewards[0].action.type, eventDataModal.rewards[0].action.subtype) }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.puntos_obtenidos') }}:</strong>
                        <p>{{ eventDataModal.rewards[0].points * eventDataModal.rewards.length }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.obtenida_el') }}:</strong>
                        <p>{{ new Date(toLocalDatetime(eventDataModal.created_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</p>
                    </div>
                </div>
                <div class="row mb-20">
                    <div class="col-12">
                        <div v-if="eventDataModal.rewards[0].action.subtype === 'PROJECT'">
                            <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.proyecto_relacionado') }}:<br></strong>
                            <router-link :to="{ path: '/project/' + eventDataModal.project_id }">
                                {{ eventDataModal.project.titulo }}
                            </router-link>
                        </div>
                        <div v-else-if="eventDataModal.rewards[0].action.subtype === 'PAGE'">
                            <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.pagina_relacionada') }}:<br></strong>
                            <router-link :to="{ path: '/page/' + eventDataModal.page.slug }">
                                {{ eventDataModal.page.title }}
                            </router-link>
                        </div>
                        <div v-else-if="eventDataModal.rewards[0].action.subtype === 'TERM'">
                            <strong>{{ $t('perfil_usuario.componentes.recompensas.detalle.terminos_relacionados') }}:<br></strong>
                            <router-link
                                    v-for="term in eventDataModal.terms"
                                    :key="'term-' + term.id"
                                    class="badge badge-primary mr-5 mt-5"
                                    :to="{ path: '/search', query: { 'terms_id[]': term.id } }"
                            >
                                {{ term.value }}
                            </router-link>
                        </div>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <b-button
                            variant="danger"
                            size="sm"
                            @click.prevent="$bvModal.hide('reward-detail-modal')"
                    >
                        <i class="fas fa-window-close"></i> {{ $t('cerrar') }}
                    </b-button>
                </template>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import { BModal } from 'bootstrap-vue';
    import axios from '../../backend/axios';

    export default {
        name: 'UserRewards',
        components: {
            Loading,
            BModal
        },
        props: {
            user_id: Number,
            readOnly: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                rewards: [],
                events: [],
                totalResults: 0,
                limit: 12,
                offset: 0,
                types: this.$t('perfil_usuario.componentes.recompensas.tipo.opciones'),
                subtypes: this.$t('perfil_usuario.componentes.recompensas.subtipo.opciones'),
                eventDataModal: null,
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
        },
        methods: {
            getRewards() {
                axios
                    .get('/events', {
                        params: {
                            has_rewards: 1,
                            player_id: this.user_id,
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
                                    auxReward.event_id = event.id;
                                    eventCompressedRewards.push(auxReward);
                                } else {
                                    compressedReward.quantity++;
                                }
                            });
                            globalCompressedRewards.push(eventCompressedRewards);
                        });

                        this.totalResults = res.data.total_results;
                        this.rewards = this.rewards.concat(...globalCompressedRewards);
                        this.events = this.events.concat(events);
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
            showRewardDetailModal(rewardId, eventId) {
                let eventDataModal = JSON.parse(JSON.stringify(this.events.find(event => event.id === eventId))); // Clone object
                eventDataModal.rewards = eventDataModal.rewards.filter(reward => reward.id === rewardId);
                this.eventDataModal = eventDataModal;

                setTimeout(() => {
                    this.$bvModal.show('reward-detail-modal');
                }, 100);
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>