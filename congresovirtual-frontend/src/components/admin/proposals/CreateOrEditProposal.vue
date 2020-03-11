<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode === 'dark' ? 'background: rgb(12, 1, 80);' : ''">
            <h4 v-if="!proposal_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_propuesta.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_propuesta.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadProposal" style="height: 300px;">
                    <Loading
                            :active.sync="loadProposal"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadProposal">
                    <form @submit.prevent="saveProposal">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-8 mb-10">
                                <label for="titulo" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.titulo') }}</label>
                                <input
                                        id="titulo"
                                        v-model="proposal.titulo"
                                        type="text"
                                        class="form-control"
                                        :readonly="inEdition"
                                        :required="!inEdition"
                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                >
                            </div>
                            <div class="col-md-2 mb-10">
                                <label for="boletin" :style="mode === 'dark' ? 'color: #fff' : ''">N° {{ $t('administrador.componentes.crear_propuesta.boletin') }}</label>
                                <input
                                        id="boletin"
                                        v-model="proposal.boletin"
                                        type="text"
                                        class="form-control"
                                        :readonly="inEdition"
                                        :required="!inEdition"
                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="autoria" :style="mode ===' dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.autoria') }}</label>
                                <input
                                        id="autoria"
                                        v-model="proposal.autoria"
                                        type="text"
                                        class="form-control"
                                        :readonly="inEdition"
                                        :required="!inEdition"
                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="fecha_ingreso" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.fecha_ingreso') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_propuesta.popover_fecha_ingreso') }}</p>
                                    </template>
                                </v-popover>
                                <DatePicker
                                        id="fecha_ingreso"
                                        v-model="fechaIngresoLocal"
                                        :config="dateOptions"
                                        :readonly="inEdition"
                                        :style="mode==='dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                ></DatePicker>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="state" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.estado.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_propuesta.estado.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="state"
                                        v-model="proposal.state"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                >
                                    <option value="0">{{ $t('administrador.componentes.crear_propuesta.estado.opcion1') }}</option>
                                    <option value="1">{{ $t('administrador.componentes.crear_propuesta.estado.opcion2') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="type" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.tipo.titulo') }}</label>
                                <select
                                        id="type"
                                        v-model="proposal.type"
                                        class="form-control custom-select d-block w-100"
                                        required
                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                >
                                    <option value="1">{{ $t('administrador.componentes.crear_propuesta.tipo.opcion1') }}</option>
                                    <option value="2">{{ $t('administrador.componentes.crear_propuesta.tipo.opcion2') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="detalle" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.detalle') }}</label>
                                <textarea
                                        id="detalle"
                                        v-model="proposal.detalle"
                                        class="form-control"
                                        rows="4"
                                        required
                                        :style="mode ==='dark'?'background: rgb(12, 1, 80); color: #fff' : ''"
                                ></textarea>
                            </div>
                        </div>
                        <div v-if="proposal.state == 1">
                            <div class="form-row align-items-center justify-content-center">
                                <div class="col-md-10 mb-10">
                                    <label for="argument" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.argumento') }}</label>
                                    <textarea
                                            id="argument"
                                            v-model="proposal.argument"
                                            class="form-control"
                                            rows="4"
                                            :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="form-row align-items-center justify-content-center">
                                <div class="col-md-4 mb-10">
                                    <label for="video_source" :style="mode==='dark'?'color: #fff':''">{{  $t('administrador.componentes.crear_propuesta.fuente_video.titulo') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_propuesta.fuente_video.popover') }}</p>
                                        </template>
                                    </v-popover>
                                    <select
                                            id="video_source"
                                            v-model="proposal.video_source"
                                            class="form-control custom-select d-block w-100"
                                            :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    >
                                        <option
                                                v-for="videoSource in videoSources"
                                                :key="'video_source-' + videoSource.id"
                                                :value="videoSource.id"
                                        >
                                            {{ videoSource.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-10">
                                    <label for="video_code" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_propuesta.codigo_video.titulo') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_propuesta.codigo_video.popover') }}</p>
                                        </template>
                                    </v-popover>
                                    <div class="input-group">
                                        <div v-if="proposal.video_source" class="input-group-prepend">
                                            <span class="input-group-text">{{ videoCodePrepends.find(videoCodePrepend => videoCodePrepend.id === proposal.video_source).label }}</span>
                                        </div>
                                        <input
                                                id="video_code"
                                                v-model="proposal.video_code"
                                                v-bind:disabled="!proposal.video_source"
                                                type="text"
                                                class="form-control"
                                                :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="is_public"
                                            v-model="proposal.is_public"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="is_public" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_propuesta.publica') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_propuesta.popover_publica') }}</p>
                                        </template>
                                    </v-popover>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" type="submit">
                                <font-awesome-icon icon="save" />
                                <span class="btn-text"> {{ $t('guardar') }}</span>
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click.prevent="back" class="btn btn-danger text-white ml-10">
                                <font-awesome-icon icon="window-close" />
                                <span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('cancelar') }}</span>
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
    import DatePicker from 'vue-bootstrap-datetimepicker';
    import axios from '../../../backend/axios';

    export default {
        name: 'CreateOrEditProposal',
        props: {
            proposal_id: Number
        },
        components: {
            Loading,
            DatePicker
        },
        data() {
            return {
                proposal: {
                    titulo: null,
                    detalle: null,
                    autoria: null,
                    boletin: null,
                    fecha_ingreso: null,
                    state: 0,
                    type: 1,
                    is_public: false,
                    argument: null,
                    video_code: null,
                    video_source: null,
                    user_id: this.$store.getters.userData.id
                },
                fechaIngresoLocal: this.$moment().local(),
                videoSources: this.$t('administrador.componentes.crear_propuesta.fuente_video.opciones'),
                videoCodePrepends: this.$t('administrador.componentes.crear_propuesta.codigo_video.prepends'),
                inEdition: false,
                loadProposal: true,
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

            if(this.proposal_id) {
                this.getProposal();
                this.inEdition = true;
            } else {
                this.loadProposal = false;
            }
        },
        methods: {
            getProposal() {
                axios
                    .get('/proposals/' + this.proposal_id)
                    .then(res => {
                        this.proposal = res.data;
                        this.fechaIngresoLocal = this.proposal.fecha_ingreso ? this.$moment.utc(this.proposal.fecha_ingreso, 'YYYY-MM-DD').local() : null;
                    })
                    .finally(() => {
                        this.loadProposal = false;
                    });
            },
            saveProposal() {
                this.loadBtnSave = true;
                this.proposal.fecha_ingreso = this.fechaIngresoLocal ? this.$moment(this.fechaIngresoLocal, this.$t('componentes.moment.formato_editable_sin_hora')).utc().format('YYYY-MM-DD') : null;
                if(this.proposal.id) {
                    this.validateState()
                        .then(() => {
                            axios
                                .put('/proposals/' + this.proposal.id, this.proposal)
                                .then(() => {
                                    this.$toastr(
                                        'success',
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.exito.modificado.generico.cuerpo'),
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.exito.modificado.generico.titulo')
                                    );
                                })
                                .catch(() => {
                                    this.$toastr(
                                        'error',
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.modificado.generico.cuerpo'),
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.modificado.generico.titulo')
                                    );
                                })
                                .finally(() => {
                                    this.loadBtnSave = false;
                                });
                        })
                        .catch(error => {
                            this.loadBtnSave = false;
                            if(error === false) {
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.modificado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.modificado.generico.titulo')
                                );
                            } else {
                                this.$toastr(
                                    'error',
                                    error.detail,
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.modificado.generico.titulo')
                                );
                            }
                        });
                } else {
                    this.validateState()
                        .then(() => {
                            axios
                                .post('/proposals', this.proposal)
                                .then(res => {
                                    this.proposal = res.data.data;
                                    this.inEdition = true;
                                    this.$toastr(
                                        'success',
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.exito.guardado.generico.cuerpo'),
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.exito.guardado.generico.titulo')
                                    );
                                })
                                .catch(() => {
                                    this.$toastr(
                                        'error',
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.guardado.generico.cuerpo'),
                                        this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.guardado.generico.titulo')
                                    );
                                })
                                .finally(() => {
                                    this.loadBtnSave = false;
                                });
                        })
                        .catch(error => {
                            this.loadBtnSave = false;
                            if(error === false) {
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.guardado.generico.titulo')
                                );
                            } else {
                                this.$toastr(
                                    'error',
                                    error.detail,
                                    this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.guardado.generico.titulo')
                                );
                            }
                        });
                }
            },
            validateState() {
                return new Promise((resolve, reject) => {
                    if(this.proposal.state == 1) {
                        axios
                            .get('/proposals', {
                                params: {
                                    state: 1,
                                    user_id: this.proposal.user_id
                                }
                            })
                            .then(res => {
                                let proposalsUserActives = res.data.results;
                                proposalsUserActives = proposalsUserActives.filter(proposal => proposal.id !== this.proposal.id);
                                if(proposalsUserActives.length > 0) {
                                    return reject({
                                        detail: this.$t('administrador.componentes.crear_propuesta.mensajes.fallido.generico.otra_propuesta_activa.cuerpo')
                                    });
                                }
                                resolve(true);
                            })
                            .catch(() => {
                                reject(false);
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            back() {
                this.$router.go(-1);
            }
        },
        computed: {
            dateOptions() {
                return {
                    format: this.$t('componentes.moment.formato_editable_sin_hora'),
                    locale: this.$moment.locale()
                };
            }
        },
        watch: {
            'proposal.state': function (value) {
                if(!this.loadProposal && value == 0) {
                    this.proposal.argument = null;
                    this.proposal.video_code = null;
                    this.proposal.video_source = null;
                }
            }
        }
    }
</script>