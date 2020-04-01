<template>
    <div class="card card-profile-feed" :style="mode === 'dark' ? 'background: #080035; color: #fff' : ''">
        <div class="card-header card-header-action">
            <h2 :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.propuestas.titulo') }}</h2>
        </div>
        <div v-if="loaded">
            <p class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.propuestas.cantidad') }}: {{ totalResults }}</p> 
            <br/>
            <ul v-if="proposals.length > 0" class="list-group list-group-flush">
                <li v-for="(proposal, index) in proposals" :key="'comentario_' + index" class="mb-2 mx-10 list-group-item border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                    <div class="media align-items-center">
                        <div class="media-body mb-2">
                            <h5 v-if="proposal.titulo" class="text-justify mb-20" :style="mode==='dark'?'color: #fff':''">{{ proposal.titulo }}</h5>
                            <div class="row align-items-center justify-content-center">
                                <div class="col-xl-3 align-mobile col-md-3 col-sm-10 mb-10">
                                    <h5>{{ $t('perfil_usuario.componentes.propuestas.boletin') }} N°</h5>
                                    <h5><small>{{ proposal.boletin }}</small></h5>
                                </div>
                                <div class="col-xl-3 align-mobile col-md-3 col-sm-10 mb-10">
                                    <h5>{{ $t('perfil_usuario.componentes.propuestas.fecha') }}</h5>
                                    <h5><small>{{ proposal.fecha_ingreso }}</small></h5>
                                </div>
                                <div class="col-xl-4 align-mobile col-md-4 col-sm-10 mb-10">
                                    <h5>{{ $t('perfil_usuario.componentes.propuestas.estado.titulo') }}</h5>
                                    <span v-if="proposal.is_public" class="badge bagde-pill badge-success font-13 py-8 px-10 mx-10">{{ $t('perfil_usuario.componentes.propuestas.estado.publica') }}</span>
                                    <span v-else class="badge bagde-pill badge-grey font-13 py-8 px-10 mx-10">{{ $t('perfil_usuario.componentes.propuestas.estado.no_publica') }}</span>
                                    <span v-if="proposal.state" class="badge bagde-pill badge-success font-13 py-8 px-10">{{ $t('perfil_usuario.componentes.propuestas.estado.activa') }}</span>
                                    <span v-else class="badge bagde-pill badge-grey font-13 py-8 px-10">{{ $t('perfil_usuario.componentes.propuestas.estado.no_activa') }}</span>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center">
                                <div class="col-10 mb-10">
                                    <h5>{{ $t('perfil_usuario.componentes.propuestas.autoria') }}</h5>
                                    <h5><small>{{ proposal.autoria }}</small></h5>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-warning" data-toggle="collapse" :href="'#collapse-propuesta-' + index" role="button" aria-expanded="false" aria-controls="collapseControl">
                            {{ $t('perfil_usuario.componentes.propuestas.editar') }}
                        </a>
                    </div>
                    <div :id="'collapse-propuesta-' + index" class="collapse">
                        <hr>
                        <form @submit.prevent="saveChanges(proposal)" class="needs-validation">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-10 mb-10">
                                    <h5 class="mb-5">{{ $t('perfil_usuario.componentes.propuestas.detalle') }}</h5>
                                    <textarea class="form-control" rows="3" v-model="proposal.detalle" ></textarea>
                                </div>
                            </div>
                            <div v-if="proposal.is_public" class="row align-items-center justify-content-center">
                                <div v-if="proposal.urgencies" class="col-10">
                                    <h5><small>{{ proposal.urgencies }} {{ $t('perfil_usuario.componentes.propuestas.de') }} {{ maxPetitions }}</small></h5>
                                    <div class="progress mb-10 p-0">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.urgencies/100*100) + '%' }"></div>
                                    </div>
                                </div>
                                <div v-else class="col-10">
                                    <h5 ><small>{{ proposal.petitions }} {{ $t('perfil_usuario.componentes.propuestas.de') }} {{ maxPetitions }}</small></h5>
                                    <div class="progress mb-10 p-0">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.petitions/100*100) + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="proposal.state" class="form-row align-items-center justify-content-center">
                                <div class="col-10 mb-10 p-0">
                                    <h5 class="mb-5">{{ $t('perfil_usuario.componentes.propuestas.argumento') }}</h5>
                                    <textarea class="form-control" rows="3" v-model="proposal.argument" ></textarea>
                                </div>
                            </div>
                            <div v-if="proposal.state" class="form-row align-items-center justify-content-center">
                                <div class="col-10 mb-10 p-0">
                                    <h5 class="mb-5">{{ $t('perfil_usuario.componentes.propuestas.video_url') }}</h5>
                                    <input type="text" class="form-control" v-model="proposal.video_url">
                                </div>
                            </div>
                            <div v-if="proposal.state" class="form-row align-items-center justify-content-center">
                                <div class="col-10 mb-10 p-0">
                                    <h5 class="mb-5">{{ $t('perfil_usuario.componentes.propuestas.fuente') }}</h5>
                                    <input type="text" class="form-control" v-model="proposal.video_source">
                                </div>
                            </div>
                            <div class="text-center mb-10 p-0">
                                <button type="submit" class="btn btn-sm btn-primary">{{ $t('guardar') }}</button>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
            <div class="mb-20" v-if="totalResults > proposals.length">
                <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.propuestas.cargar') }}
                    <loading
                        :active.sync="loadBtnLoadMore"
                        :is-full-page="fullPage"
                        :height="24"
                        :color="color"
                    ></loading>
                </button>
            </div>
            <div v-if="proposals.length == 0">
                <h6 class="px-4 mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.propuestas.error1') }}</h6>
            </div>
        </div>
        <div v-else>
            <h6 class="px-4 mb-2" :style="mode==='dark'?'color: #fff':''"> {{ $t('perfil_usuario.componentes.propuestas.cargando') }}</h6>
        </div>
        <div v-if="failed">
            <h6 class="px-4 mb-2" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.propuestas.error2') }}</h6>
        </div>
    </div>   
</template>

<script>
    import axios from "../../backend/axios";

    export default {
        name: "userProposals",
        props: {
            maxPetitions: Number
        },
        data() {
            return {
                totalResults: 0,
                proposals: [],
                limit: 10,
                offset: 0,
                loadBtnLoadMore: false,
                loaded: false,
                failed: false,
                mode: String,
            };
        },
        methods: {
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getProposals();
            },
            getProposals() {
                axios
                    .get("/users/"+ JSON.parse(localStorage.user).id+"/proposals", {
                        params: {
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results
                        this.proposals = this.proposals.concat(res.data.results)
                        this.offset += res.data.returned_results
                    })
                    .catch(() => {
                        console.log("FALLO");
                        this.failed = true
                    })
                    .finally(() => {
                        this.loaded = true
                    })
            },
            async saveChanges(proposal) {
                if (proposal.state) {
                    let embedURL = proposal.video_url.split('watch?v=').join('embed/')
                    try {
                        const res = await axios.put('/proposals/' + proposal.id, {
                            detalle: proposal.detalle,
                            argument: proposal.argument,
                            video_url: embedURL,
                            video_source: proposal.video_source
                        })
                        this.$toastr('success','','Datos guardados')
                    } catch (error) {
                        console.log(error)
                        this.$toastr('error','No se ha podido guardar los datos','ERROR')
                    }
                } else {
                    try {
                        const res = await axios.put('/proposals/' + proposal.id, {
                            detalle: proposal.detalle
                        })
                        this.$toastr('success','','Datos guardados')
                    } catch (error) {
                        console.log(error)
                        this.$toastr('error','No se ha podido guardar los datos','ERROR')
                    }
                }
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getProposals();
        }
    }
</script>

<style>
    @media (max-width: 767px) { 
        .align-mobile {
            margin-left: 23px; 
        }
    }

    @media (min-width: 576px) { 
        .align-mobile {
            margin-left: 0px; 
        }
    }
</style>