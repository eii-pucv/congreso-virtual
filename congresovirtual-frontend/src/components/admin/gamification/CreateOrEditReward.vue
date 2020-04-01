<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!inEdition" class="hk-sec-title text-center">{{ $t('administrador.componentes.gamificacion.crear_recompensa.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.gamificacion.crear_recompensa.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadReward" style="height: 300px;">
                    <Loading
                            :active.sync="loadReward"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadReward">
                    <form @submit.prevent="saveReward">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_recompensa.nombre') }}</label>
                                <input
                                        id="name"
                                        v-model="reward.name"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="points" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_recompensa.puntos') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button
                                                @click.prevent="reward.points = addOrSubtractPositiveNumber(reward.points, -1)"
                                                :disabled="reward.points == 0"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input
                                            id="points"
                                            v-model="reward.points"
                                            @input="pointsFormat"
                                            type="text"
                                            class="form-control"
                                            required
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <div class="input-group-append">
                                        <button
                                                @click.prevent="reward.points = addOrSubtractPositiveNumber(reward.points, 1)"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="actions_needed" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_recompensa.acciones_necesarias.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.gamificacion.crear_recompensa.acciones_necesarias.popover') }}</p>
                                    </template>
                                </v-popover>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button
                                                @click.prevent="reward.actions_needed = addOrSubtractPositiveNumber(reward.actions_needed, -1)"
                                                :disabled="inEdition || reward.actions_needed == 0"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input
                                            id="actions_needed"
                                            v-model="reward.actions_needed"
                                            @input="actionsNeededFormat"
                                            type="text"
                                            class="form-control"
                                            :readonly="inEdition"
                                            :required="!inEdition"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <div class="input-group-append">
                                        <button
                                                @click.prevent="reward.actions_needed = addOrSubtractPositiveNumber(reward.actions_needed, 1)"
                                                :disabled="inEdition"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="actions" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_recompensa.accion.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.gamificacion.crear_recompensa.accion.popover') }}</p>
                                    </template>
                                </v-popover>
                                <multiselect
                                        id="actions"
                                        v-model="selectedAction"
                                        track-by="id"
                                        :custom-label="actionCustomLabel"
                                        :placeholder="$t('administrador.componentes.gamificacion.crear_recompensa.accion.seleccionar')"
                                        :options="actions"
                                        :loading="loadActions"
                                        :allow-empty="false"
                                        :searchable="false"
                                        :showLabels="false"
                                        :disabled="inEdition"
                                        :required="!inEdition"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-20">
                                <label for="icon" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_recompensa.icono') }}</label>
                                <vue-icon-picker
                                        id="icon"
                                        v-model="selectedIcon"
                                        height="150px"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></vue-icon-picker>
                            </div>
                        </div>
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" type="submit">
                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click.prevent="back" class="btn btn-danger ml-10">
                                <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import Multiselect from 'vue-multiselect';
    import VueIconPicker from 'vue-icon-picker';
    import axios from '../../../backend/axios';

    export default {
        name: 'CreateOrEditReward',
        components: {
            Loading,
            Multiselect,
            VueIconPicker
        },
        props: {
            reward_id: Number
        },
        data() {
            return {
                reward: {
                    name: null,
                    points: 0,
                    actions_needed: 0,
                    icon: null,
                    action_id: null
                },
                actions: [],
                selectedAction: null,
                selectedIcon: {
                    name: null,
                    type: 'fontawesome'
                },
                inEdition: false,
                loadReward: true,
                loadActions: true,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            if(this.reward_id) {
                this.getReward();
                this.inEdition = true;
            } else {
                this.loadReward = false;
            }

            this.getActions();
        },
        methods: {
            getReward() {
                axios
                    .get('/rewards/' + this.reward_id)
                    .then(res => {
                        this.reward = res.data;
                        this.selectedAction = this.reward.action;
                        this.selectedIcon.name = this.reward.icon;
                    })
                    .finally(() => {
                        this.loadReward = false;
                    });
            },
            getActions() {
                axios
                    .get('/actions', {
                        params: {
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.actions = res.data.results;
                        this.selectedAction = this.actions[0];
                    })
                    .finally(() => {
                        this.loadActions = false;
                    });
            },
            saveReward() {
                this.loadBtnSave = true;
                this.reward.action_id = this.selectedAction ? this.selectedAction.id : null;
                this.reward.icon = this.selectedIcon ? this.selectedIcon.name : null;
                if(this.reward.id) {
                    axios
                        .put('/rewards/' + this.reward.id, this.reward)
                        .then(() => {
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.exito.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.exito.modificado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.fallido.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.fallido.modificado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        })
                } else {
                    axios
                        .post('/rewards', this.reward)
                        .then(res => {
                            this.reward = res.data.data;
                            this.inEdition = true;
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_recompensa.mensajes.fallido.guardado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        });
                }
            },
            addOrSubtractPositiveNumber(firstValue, secondValue) {
                firstValue = parseInt(firstValue);
                secondValue = parseInt(secondValue);

                if(!isNaN(firstValue) && !isNaN(secondValue)) {
                    if(firstValue + secondValue >= 0) {
                        return firstValue + secondValue;
                    }
                }
                return 0;
            },
            pointsFormat(event) {
                this.reward.points = isNaN(event.target.value) ? 0 : event.target.value;
            },
            actionsNeededFormat(event) {
                this.reward.actions_needed = isNaN(event.target.value) ? 0 : event.target.value;
            },
            actionCustomLabel(action) {
                if(action) {
                    return action.type + (action.subtype ? ' - ' + action.subtype : '') + ' (' + action.points + ' pts.)';
                }
                return '';
            },
            back() {
                this.$router.go(-1);
            }
        }
    }
</script>