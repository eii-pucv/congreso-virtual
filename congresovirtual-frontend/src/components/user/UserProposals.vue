<template>
    <div class="container">
        <div class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h2 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.propuestas.titulo') }}</h2>
            <div class="mt-20 vld-parent">
                <div v-if="loadProposals" style="height: 300px;">
                    <Loading
                            :active.sync="loadProposals"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadProposals">
                    <div v-for="proposal in proposals" :key="'proposal-' + proposal.id" class="list-group list-group-flush">
                        <div class="list-group-item border border-primary rounded mb-2" :class="mode">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <div class="row mx-0">
                                        <div class="col-md-10 px-0">
                                            <h5 v-if="proposal.titulo" class="mb-20">{{ proposal.titulo }}</h5>
                                            <div class="row mb-20">
                                                <div class="col-md-4">
                                                    <h6>{{ $t('perfil_usuario.componentes.propuestas.boletin') }}:</h6>
                                                    <p>{{ proposal.boletin }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>{{ $t('perfil_usuario.componentes.propuestas.fecha_ingreso') }}:</h6>
                                                    <p>{{ new Date(proposal.fecha_ingreso) | moment($t('componentes.moment.formato_sin_hora')) }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>{{ $t('perfil_usuario.componentes.propuestas.autoria') }}:</h6>
                                                    <p>{{ proposal.autoria }}</p>
                                                </div>
                                            </div>
                                            <div class="row mb-20">
                                                <div class="col-md-4">
                                                    <h6>{{ $t('perfil_usuario.componentes.propuestas.tipo.titulo') }}:</h6>
                                                    <span v-if="proposal.type === 1" class="badge badge-primary font-13">{{ $t('perfil_usuario.componentes.propuestas.tipo.inclusion') }}</span>
                                                    <span v-else-if="proposal.type === 2" class="badge badge-warning font-13">{{ $t('perfil_usuario.componentes.propuestas.tipo.urgencia') }}</span>
                                                </div>
                                                <div v-if="!readOnly" class="col-md-8">
                                                    <h6>{{ $t('perfil_usuario.componentes.propuestas.estado.titulo') }}:</h6>
                                                    <span v-if="proposal.is_public" class="badge badge-success font-13 mr-5">{{ $t('perfil_usuario.componentes.propuestas.estado.publica') }}</span>
                                                    <span v-else class="badge badge-grey font-13 mr-5">{{ $t('perfil_usuario.componentes.propuestas.estado.no_publica') }}</span>
                                                    <span v-if="proposal.state === 0" class="badge badge-secondary font-13 mr-5">{{ $t('perfil_usuario.componentes.propuestas.estado.inactiva') }}</span>
                                                    <span v-else class="badge badge-success font-13 mr-5">{{ $t('perfil_usuario.componentes.propuestas.estado.activa') }}</span>
                                                    <span v-if="proposal.state === 0 || proposal.state === 1" class="badge badge-primary font-13">{{ $t('perfil_usuario.componentes.propuestas.estado.solo_detalle') }}</span>
                                                    <span v-else-if="proposal.state === 2" class="badge badge-primary font-13">{{ $t('perfil_usuario.componentes.propuestas.estado.argumento_video') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center px-0 d-none d-md-block">
                                            <div class="btn-group-vertical btn-block float-md-right">
                                                <router-link class="btn btn-primary" :to="{ path: '/proposal/' + proposal.id }">
                                                    <i class="fas fa-eye"></i> {{ $t('perfil_usuario.componentes.propuestas.ver')}}
                                                </router-link>
                                                <a v-if="!readOnly" class="btn btn-secondary btn-sm" data-toggle="collapse" :href="'#collapse-proposal-' + proposal.id" role="button" aria-expanded="false" aria-controls="collapseControl">
                                                    <i class="fas fa-edit"></i> {{ $t('perfil_usuario.componentes.propuestas.editar')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mb-20 justify-content-center">
                                        <div v-if="proposal.type === 1" class="col-md-10 px-0">
                                            <h6 class="text-center">{{ $t('perfil_usuario.componentes.propuestas.apoyos') }} {{ proposal.petitions }} {{ $t('perfil_usuario.componentes.propuestas.de') }} {{ maxPetitions }}</h6>
                                            <div class="progress my-10">
                                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.petitions/maxPetitions *100) + '%' }"></div>
                                            </div>
                                        </div>
                                        <div v-else-if="proposal.type === 2" class="col-md-10 px-0">
                                            <h6 class="text-center">{{ $t('perfil_usuario.componentes.propuestas.apoyos') }} {{ proposal.urgencies }} {{ $t('perfil_usuario.componentes.propuestas.de') }} {{ maxPetitions }}</h6>
                                            <div class="progress my-10">
                                                <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" :style="{width: (proposal.urgencies/maxPetitions *100) + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mb-20 text-center d-md-none">
                                        <div class="btn-group-vertical btn-block">
                                            <router-link class="btn btn-primary" :to="{ path: '/proposal/' + proposal.id }">
                                                <i class="fas fa-eye"></i> {{ $t('perfil_usuario.componentes.propuestas.ver')}}
                                            </router-link>
                                            <a v-if="!readOnly" class="btn btn-secondary" data-toggle="collapse" :href="'#collapse-proposal-' + proposal.id" role="button" aria-expanded="false" aria-controls="collapseControl">
                                                <i class="fas fa-edit"></i> {{ $t('perfil_usuario.componentes.propuestas.editar')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!readOnly" :id="'collapse-proposal-' + proposal.id" class="collapse">
                                <form @submit.prevent="saveProposal(proposal.id)">
                                    <div class="form-row align-items-center justify-content-center">
                                        <div class="col-md-10 mb-10">
                                            <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.propuestas.detalle') }}</label>
                                            <textarea
                                                    id="detalle"
                                                    v-model="proposal.detalle"
                                                    class="form-control"
                                                    rows="3"
                                                    required
                                                    :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div v-if="proposal.state === 2">
                                        <div class="form-row align-items-center justify-content-center">
                                            <div class="col-md-10 mb-10">
                                                <label for="argument" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.propuestas.argumento') }}</label>
                                                <textarea
                                                        id="argument"
                                                        v-model="proposal.argument"
                                                        class="form-control"
                                                        rows="3"
                                                        required
                                                        :style="mode === 'dark' ? 'background: rgb(12, 1, 80); color: #fff' : ''"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="form-row align-items-center justify-content-center">
                                            <div class="col-md-4 mb-10">
                                                <label for="video_source" :style="mode==='dark'?'color: #fff':''">{{  $t('perfil_usuario.componentes.propuestas.fuente_video.titulo') }}</label>
                                                <v-popover>
                                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                                    <template slot="popover">
                                                        <p>{{ $t('perfil_usuario.componentes.propuestas.fuente_video.popover') }}</p>
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
                                                <label for="video_code" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.propuestas.codigo_video.titulo') }}</label>
                                                <v-popover>
                                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                                    <template slot="popover">
                                                        <p>{{ $t('perfil_usuario.componentes.propuestas.codigo_video.popover') }}</p>
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
                                    <div class="text-center mb-10">
                                        <button class="btn btn-primary vld-parent" type="submit">
                                            <i class="fas fa-save"></i> {{ $t('guardar') }}
                                            <Loading
                                                    :active.sync="loadBtnSave"
                                                    :is-full-page="fullPage"
                                                    :height="24"
                                                    :color="'#ffffff'"
                                            ></Loading>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div v-if="totalResults > proposals.length">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.propuestas.cargar') }}
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
                        {{ $t('perfil_usuario.componentes.propuestas.no_hay_propuestas') }}
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
        name: 'UserProposals',
        components: {
            Loading
        },
        props: {
            user_id: Number,
            readOnly: Boolean
        },
        data() {
            return {
                proposals: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                maxPetitions: 100,
                videoSources: this.$t('perfil_usuario.componentes.propuestas.fuente_video.opciones'),
                videoCodePrepends: this.$t('perfil_usuario.componentes.propuestas.codigo_video.prepends'),
                loadProposals: true,
                loadBtnLoadMore: false,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String,
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getMaxPetitionsSettings();
            this.getProposals();
        },
        methods: {
            getMaxPetitionsSettings() {
                axios
                    .get('/settings', {
                        params: {
                            key: 'max_necessary_petitions'
                        }
                    })
                    .then(res => {
                        if(res.data[0] !== undefined) {
                            this.maxPetitions = JSON.parse(res.data[0].value).number_petitions;
                        }
                    });
            },
            getProposals() {
                axios
                    .get('/users/' + this.user_id + '/proposals', {
                        params: {
                            is_public: 1,
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results;
                        this.proposals = this.proposals.concat(res.data.results);
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadProposals = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getProposals();
            },
            saveProposal(proposalId) {
                this.loadBtnSave = true;
                let proposalIndex = this.proposals.findIndex(proposal => proposal.id == proposalId);
                let proposal = this.proposals[proposalIndex];
                let newProposal = {
                    detalle: proposal.detalle
                };
                if(proposal.state === 2) {
                    newProposal.argument = proposal.argument;
                    newProposal.video_code = proposal.video_code;
                    newProposal.video_source = proposal.video_source;
                }

                axios
                    .put('/proposals/' + proposal.id)
                    .then(res => {
                        this.proposals[proposalIndex] = res.data.data;
                        this.$toastr(
                            'success',
                            this.$t('perfil_usuario.componentes.propuestas.mensajes.exito.modificado.generico.cuerpo'),
                            this.$t('perfil_usuario.componentes.propuestas.mensajes.exito.modificado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('perfil_usuario.componentes.propuestas.mensajes.fallido.modificado.generico.cuerpo'),
                            this.$t('perfil_usuario.componentes.propuestas.mensajes.fallido.modificado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadBtnSave = false;
                    });
            }
        }
    }
</script>