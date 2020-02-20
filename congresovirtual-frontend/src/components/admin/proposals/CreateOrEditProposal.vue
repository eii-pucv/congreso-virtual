<template>
    <div style="min-height: 720px;">
        <div class="container mt-20">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :style="mode === 'dark' ? 'background: rgb(12, 1, 80);' : ''">
                        <h4 v-if="!proposal_id" class="text-center" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.titulo1') }}</h4>
                        <h4 v-else="" class="text-center" :style="mode ==='dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.titulo2') }}</h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form>
                                    <div class="form-row alineado">
                                        <div class="col-md-2 mb-10">
                                            <label for="boletin" :style="mode === 'dark' ? 'color: #fff' : ''">N° {{ $t('administrador.componentes.crear_propuesta.boletin') }}</label>
                                            <input type="text" class="form-control" v-model="proposal.boletin" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''" :disabled="edit">
                                        </div>
                                        <div class="col-md-8 mb-10">
                                            <label for="titulo" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.titulo') }}</label>
                                            <input type="text" class="form-control" v-model="proposal.titulo" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''" :disabled="edit">
                                        </div>
                                    </div>
                                    <div class="form-row alineado">
                                        <div class="col-md-4 mb-10">
                                            <label for="autoria" :style="mode ===' dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.autoria') }}</label>
                                            <input type="text" class="form-control" v-model="proposal.autoria" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''" :disabled="edit">
                                        </div>
                                        <div class="col-md-3 mb-10">
                                            <label for="fecha" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.fecha_ingreso') }}</label>
                                            <date-picker v-model="proposal.fecha_ingreso" :config="dateOptions" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''" :disabled="edit"></date-picker>
                                        </div>
                                        <div class="col-md-3 mb-10">
                                            <label for="tipo" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.tipo') }}</label>
                                            <select v-model="proposal.type" class="form-control custom-select d-block w-100" id="activityBox" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''">
                                                <option value="1">{{ $t('administrador.componentes.crear_propuesta.propuesta') }}</option>
                                                <option value="2" >{{ $t('administrador.componentes.crear_propuesta.urgencia') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row alineado">
                                        <div class="col-md-10 mb-10">
                                            <label for="detalle" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.detalle') }}</label>
                                            <textarea class="form-control" rows="5" v-model="proposal.detalle" :style="mode ==='dark'?'background: rgb(12, 1, 80); color: #fff' : ''"></textarea>
                                        </div>
                                    </div>
                                    <div v-if="proposal.state" class="form-row alineado">
                                        <div class="col-md-10 mb-10">
                                            <label for="argument" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.argumento') }}</label>
                                            <textarea class="form-control" rows="3" v-model="proposal.argument" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"></textarea>
                                        </div>
                                    </div>
                                    <div v-if="proposal.state" class="form-row alineado">
                                        <div class="col-md-10 mb-10">
                                            <label for="url" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.video_url') }}</label>
                                            <input type="text" class="form-control" v-model="proposal.video_url" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''">
                                        </div>
                                    </div>
                                    <div v-if="proposal.state" class="form-row alineado">
                                        <div class="col-md-10 mb-10">
                                            <label for="url" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.fuente_video') }}</label>
                                            <input type="text" class="form-control" v-model="proposal.video_source" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''">
                                        </div>
                                    </div>
                                    <div class="form-row alineado">
                                        <div class="col-md-10 mb-10">
                                            <div class="custom-control custom-checkbox checkbox-primary float-left">
                                                <input type="checkbox" class="custom-control-input" id="customCheck" v-model="proposal.is_public" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''">
                                                <label class="custom-control-label" for="customCheck" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('administrador.componentes.crear_propuesta.publica') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button-list text-center">
                                        <a @click="saveProposal()" role="button" class="btn text-white bg-success votable"><font-awesome-icon icon="envelope"/><span class="btn-text" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('enviar') }}</span></a>
                                        <a v-if="!proposal.state && proposal_id" @click="cambiarEstado(1)" class="btn text-white ml-10 bg-primary votable"><font-awesome-icon icon="check-circle"/><span class="btn-text" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('administrador.componentes.crear_propuesta.activar') }}</span></a>
                                        <a v-if="proposal.state && proposal_id" @click="cambiarEstado(0)" class="btn text-white ml-10 bg-secondary votable"><font-awesome-icon icon="ban"/><span class="btn-text" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('administrador.componentes.crear_propuesta.desactivar') }}</span></a>
                                        <a @click="volver()" class="btn text-white bg-danger ml-10 votable"><font-awesome-icon icon="window-close"/><span class="btn-text" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('cancelar') }}</span></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../../../backend/axios";
import { bus } from "../../../main";
//import 'bootstrap/dist/css/bootstrap.css';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

export default {
    name: 'CreateOrEditProposal',
    props: {
        proposal_id: Number
    },
    components: {
        datePicker
    },
    data() {
        return {
            mode: String,
            proposal: {},
            dateOptions: {
                format: 'YYYY-MM-DD',
                useCurrent: true,
            }, 
            config: {
                events: {
                    initialized: function () {
                        console.log('initialized')
                    }
                }
            },
            edit: false
        }
    },
    methods: {
        async saveProposal() {
            this.proposal.user_id = JSON.parse(localStorage.user).id
            if (this.proposal_id) {
                if (!this.proposal.state) {
                    try {
                        const res = await axios.put('/proposals/' + this.proposal_id, {
                            type: this.proposal.type,
                            is_public: this.proposal.is_public,
                            detalle: this.proposal.detalle
                        })
                        this.$toastr('success','','Propuesta actualizada')
                    } catch (error) {
                        this.$toastr('error','','Propuesta no actualizada')
                    }
                }
                else {
                    try {
                        const res = await axios.put('/proposals/' + this.proposal_id, this.proposal)
                        this.$toastr('success','','Propuesta actualizada')
                    } catch (error) {
                        this.$toastr('error','','Propuesta no actualizada')
                    }
                }
            }
            else {
                try {
                    const res = await axios.post('/proposals',this.proposal)
                    this.$toastr('success','','Propuesta creada')
                } catch (error) {
                    this.$toastr('error','No se pudo crear la propuesta','Propuesta no creada')
                }
            }
        },
        async cambiarPublicacion(estado) { 
            try {
                const res = await axios.put('/proposals/' + this.proposal_id, {
                    is_public: estado
                })
                this.$toastr('success','','Estado de publicación actualizado')
            } catch (error) {
    
                this.$toastr('error','No se ha podido cambiar la disponibilidad del proyecto','Algo ha salido mal')
            }
        },
        async cambiarEstado (estado) {
            try {
                const resultsSameState = await axios.get('/proposals?state=1&&user_id=' + this.proposal.user.id)
                if((resultsSameState.data.results.length > 0 && resultsSameState.data.results[0].id == this.proposal_id) || resultsSameState.data.results.length == 0) {
                    const res = await axios.put('/proposals/' + this.proposal_id, {
                        state: estado
                    })
                    this.proposal.state = estado
                    this.$toastr('success','','Estado de activación actualizado')
                } else {
                    this.$toastr('warning','',`Ya existe una propuesta activa del usuario "${this.proposal.user.name} ${this.proposal.user.surname}"`)     
                }
            } catch (error) {
                this.$toastr('error','No se ha podido cambiar la disponibilidad del proyecto','Algo ha salido mal')
            }
        },
        volver() {
            window.history.back()
        }
    },
    async mounted() {
        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }
        if(this.proposal_id) {
            try {
                const res = await axios.get('/proposals/' + this.proposal_id);
                this.proposal = res.data;
                this.$toastr('success','','Propuesta cargada');
                this.edit = true;
            } catch (error) {
                this.$toastr('error','No se pudo cargar la propuesta','Carga fallida');
            }
        }    
    },
}
</script>

<style scoped>

.alineado {
    align-items: center;
    justify-content: center;
}

    
</style>