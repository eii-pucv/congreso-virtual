<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!inEdition" class="hk-sec-title text-center">{{ $t('administrador.componentes.gamificacion.crear_accion.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.gamificacion.crear_accion.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadAction" style="height: 300px;">
                    <Loading
                            :active.sync="loadAction"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadAction">
                    <form @submit.prevent="saveAction">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="type" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_accion.tipo.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.gamificacion.crear_accion.tipo.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="type"
                                        v-model="action.type"
                                        class="form-control custom-select d-block w-100"
                                        :disabled="inEdition"
                                        :required="!inEdition"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option
                                            v-for="type in types"
                                            :key="'type-' + type.id"
                                            :value="type.value"
                                    >
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="subtype" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_accion.subtipo.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.gamificacion.crear_accion.subtipo.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="subtype"
                                        v-model="action.subtype"
                                        class="form-control custom-select d-block w-100"
                                        :disabled="inEdition"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option
                                            v-for="subtype in subtypes"
                                            :key="'subtype-' + subtype.id"
                                            :value="subtype.value"
                                    >
                                        {{ subtype.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="points" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.gamificacion.crear_accion.puntos') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button
                                                @click.prevent="action.points = addOrSubtractPositiveNumber(action.points, -1)"
                                                :disabled="action.points == 0"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input
                                            id="points"
                                            v-model="action.points"
                                            @input="pointsFormat"
                                            type="text"
                                            class="form-control"
                                            required
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <div class="input-group-append">
                                        <button
                                                @click.prevent="action.points = addOrSubtractPositiveNumber(action.points, 1)"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
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
    import axios from '../../../backend/axios';

    export default {
        name: 'CreateOrEditAction',
        components: {
            Loading
        },
        props: {
            action_id: Number
        },
        data() {
            return {
                action: {
                    type: 'COMMENT',
                    subtype: null,
                    points: 0
                },
                types: [
                    { id: 1, value: 'COMMENT', label: null },
                    { id: 2, value: 'VOTE', label: null },
                    { id: 3, value: 'READ', label: null }
                ],
                subtypes: [
                    { id: 1, value: null, label: null },
                    { id: 2, value: 'PROJECT', label: null },
                    { id: 3, value: 'PAGE', label: null },
                    { id: 4, value: 'TERM', label: null }
                ],
                inEdition: false,
                loadAction: true,
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

            this.configComponent();

            if(this.action_id) {
                this.getAction();
                this.inEdition = true;
            } else {
                this.loadAction = false;
            }
        },
        methods: {
            configComponent() {
                this.types.forEach(type => {
                    type.label = this.$t('administrador.componentes.gamificacion.crear_accion.tipo.opciones').find(typeTrans => typeTrans.id === type.id).label;
                });

                this.subtypes.forEach(subtype => {
                    subtype.label = this.$t('administrador.componentes.gamificacion.crear_accion.subtipo.opciones').find(subtypeTrans => subtypeTrans.id === subtype.id).label;
                });
            },
            getAction() {
                axios
                    .get('/actions/' + this.action_id)
                    .then(res => {
                        this.action = res.data;
                    })
                    .finally(() => {
                        this.loadAction = false;
                    });
            },
            saveAction() {
                this.loadBtnSave = true;
                console.log(this.action);
                if(this.action.id) {
                    axios
                        .put('/actions/' + this.action.id, this.action)
                        .then(() => {
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.exito.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.exito.modificado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.fallido.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.fallido.modificado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        })
                } else {
                    axios
                        .post('/actions', this.action)
                        .then(res => {
                            this.action = res.data.data;
                            this.inEdition = true;
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.gamificacion.crear_accion.mensajes.fallido.guardado.generico.titulo')
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
                this.action.points = isNaN(event.target.value) ? 0 : event.target.value;
            },
            back() {
                this.$router.go(-1);
            }
        }
    }
</script>