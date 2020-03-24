<template>
    <div class="col-12 px-0">
        <div class="shadow-sm p-3 mb-5 rounded">
            <h2 class="text-center">{{ $t('administrador.componentes.analitica.topic_model.titulo') }}</h2>
            <div class="row mt-20">
                <div class="col-md-9 text-justify">
                    <ul style="list-style: disc inside;">
                        <li>{{ $t('administrador.componentes.analitica.topic_model.parrafos.parrafo1') }}</li>
                        <li>{{ $t('administrador.componentes.analitica.topic_model.parrafos.parrafo2') }}</li>
                        <li>{{ $t('administrador.componentes.analitica.topic_model.parrafos.parrafo3') }}</li>
                        <li>{{ $t('administrador.componentes.analitica.topic_model.parrafos.parrafo4') }}</li>
                        <li>{{ $t('administrador.componentes.analitica.topic_model.parrafos.parrafo5') }}</li>
                    </ul>
                </div>
                <div class="col-md-3 align-self-center mt-10 mt-md-0">
                    <div class="btn-group-vertical btn-block">
                        <button
                                @click.prevent="refreshTopicModel"
                                class="btn btn-primary vld-parent"
                        >
                            <i class="fas fa-sync"></i> {{ $t('administrador.componentes.analitica.topic_model.boton_actualizar') }}
                            <Loading
                                    :active.sync="loadBtnRefresh"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="'#ffffff'"
                            ></Loading>
                        </button>
                        <button
                                @click.prevent="showTopicModelFromCache"
                                class="btn btn-outline-primary vld-parent"
                        >
                            <i class="fas fa-hdd"></i> {{ $t('administrador.componentes.analitica.topic_model.boton_ver_cache') }}
                            <Loading
                                    :active.sync="loadBtnGetFromCache"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="'#ffffff'"
                            ></Loading>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="vld-parent shadow-sm p-3 mb-5 rounded mt-25" style="overflow: auto;">
            <div v-if="loadTopicModel" style="height: 200px;">
                <Loading
                        :active.sync="loadTopicModel"
                        :is-full-page="fullPage"
                        :height="128"
                        :color="'#000000'"
                ></Loading>
            </div>
            <div v-if="!loadTopicModel && !error">
                <p class="text-right text-grey mb-10">
                    {{ $t('administrador.componentes.analitica.topic_model.generado_el') }}: {{ new Date(toLocalDatetime(topicModel.created_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                </p>
                <div v-html="topicModel.value" id="topic_model">
                    {{ topicModel.value }}
                </div>
            </div>
            <div v-else-if="!loadTopicModel && error === 'NOT_GENERATED_DATA_ERROR'">
                <h6 class="text-center my-20">{{ $t('administrador.componentes.analitica.topic_model.errores.no_carga') }}</h6>
            </div>
            <div v-else-if="!loadTopicModel && error === 'NOT_CACHED_DATA_ERROR'">
                <h6 class="text-center my-20">{{ $t('administrador.componentes.analitica.topic_model.errores.no_cache') }}</h6>
            </div>
        </div>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import axios from '../../../backend/axios';

    export default {
        name: 'TopicModel',
        components: {
            Loading
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                topicModel: Object,
                fromCache: true,
                error: false,
                loadTopicModel: false,
                loadBtnRefresh: false,
                loadBtnGetFromCache: false,
                fullPage: false
            }
        },
        mounted() {
            this.getTopicModel();
        },
        methods: {
            getTopicModel() {
                this.loadTopicModel = true;
                axios
                    .get('/topicmodel', {
                        params:{
                            project_id: this.project_id,
                            from_cache: this.fromCache ? 1 : 0
                        }
                    })
                    .then(res => {
                        this.topicModel = res.data;
                        if((!this.topicModel || Object.entries(this.topicModel).length === 0) && this.fromCache) {
                            this.error = 'NOT_CACHED_DATA_ERROR';
                        } else if(!this.topicModel) {
                            this.error = 'NOT_GENERATED_DATA_ERROR';
                        } else {
                            this.error = false;
                        }
                        this.defer();
                    })
                    .catch(() => {
                        this.error = 'NOT_GENERATED_DATA_ERROR';
                    })
                    .finally(() => {
                        this.loadTopicModel = false;
                        this.loadBtnRefresh = false;
                        this.loadBtnGetFromCache = false;
                    });
            },
            defer() {
                if(document.getElementById('topic_model')) {
                    if(document.getElementById('topic_model').getElementsByTagName('script')[0]) {
                        eval(document.getElementById('topic_model').getElementsByTagName('script')[0].innerHTML);
                    } else {
                        setTimeout(() => {
                            this.defer();
                        }, 50);
                    }
                } else {
                    setTimeout(() => {
                        this.defer();
                    }, 50);
                }
            },
            refreshTopicModel() {
                this.loadBtnRefresh = true;
                this.fromCache = false;
                this.getTopicModel();
            },
            showTopicModelFromCache() {
                this.loadBtnGetFromCache = true;
                this.fromCache = true;
                this.getTopicModel();
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>