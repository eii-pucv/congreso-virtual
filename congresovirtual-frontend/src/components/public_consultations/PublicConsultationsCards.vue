<template>
    <div class="col-md-4 mb-4">
        <div class="card h-100" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <img
                    class="card-img-top embed-responsive-item default-consultation-img"
                    :src="getImgUrl()"
                    style="object-fit: cover;"
            />
            <div>
                <a class="btn text-white bg-indigo-light-2 top-right mt-5" data-toggle="modal" :data-target="'#myModal' + publicConsultation.id"><span class="btn-text"><font-awesome-icon icon="share-square"/></span></a>
                <div v-if="publicConsultation.terms.length > 0" class="top-left">
                    <a :href="'/search?terms_id[]=' + publicConsultation.terms[0].id" class="badge badge-pill badge-dark font-12 m-1">{{ publicConsultation.terms[0].value }}</a>
                </div>
            </div>
            <p class="text-center text-justify ellipsis mt-auto ml-5 mr-5">{{ publicConsultation.titulo }}</p>
            <ul class="list-group list-group-flush mt-auto">
                <li class="text-center py-10" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <i class="fa fa-clock font-18"></i>
                    <p>
                        {{ $t('consultas_publicas.inicio') }}: {{ new Date(toLocalDatetime(publicConsultation.fecha_inicio)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                        <br>
                        {{ $t('consultas_publicas.fin') }}: {{ new Date(toLocalDatetime(publicConsultation.fecha_termino)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                    </p>
                </li>
            </ul>
            <div class="btn-group-vertical mt-auto" role="group">
                <a class="font-1"></a>
                <div class="btn-group">
                    <router-link :to="{ path: 'public_consultation/' + publicConsultation.id, query: { mode: this.mode }}" class="btn btn-success text-white">
                        <i class="fa fa-eye"></i><span class="btn-text"> {{ $t('ver_consulta') }}</span>
                    </router-link>
                </div>
            </div>
            <div class="modal" :id="'myModal' + publicConsultation.id">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $t('compartir') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span><i class="fa fa-times"></i></span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <social-sharing :url="APP_URL + '/public_consultation/' + publicConsultation.id"
                                    :title="'Congreso Virtual ' + publicConsultation.titulo"
                                    :description="publicConsultation.titulo"
                                    :quote="publicConsultation.titulo"
                                    hashtags="congresovirtual"
                                    inline-template
                            >
                                <div>
                                    <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                        <i class="fa fa-envelope"></i> Email
                                    </network>
                                    <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                        <span class="fa fa-facebook"></span> Facebook
                                    </network>
                                    <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                        <i class="fa fa-linkedin"></i> LinkedIn
                                    </network>
                                    <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                        <i class="fa fa-twitter"></i> Twitter
                                    </network>
                                    <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                        <i class="fa fa-whatsapp"></i> WhatsApp
                                    </network>
                                </div>
                            </social-sharing>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $t('cerrar') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SocialSharing from 'vue-social-sharing';
    import { API_URL } from '../../backend/data_server';
    import { APP_URL } from '../../data/globals';

    export default {
        name: 'PublicConsultationsCards',
        components: {
            SocialSharing
        },
        props: {
            publicConsultation: Object
        },
        data() {
            return {
                APP_URL,
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }
        },
        methods: {
            getImgUrl() {
                if(this.publicConsultation.imagen) {
                    return (API_URL + '/api/storage/consultations/' + this.publicConsultation.id + '/' + this.publicConsultation.imagen);
                }
                return null;
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>

<style scoped>
    .ellipsis{
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        line-height: 16px;
        max-height: 47px;
        -webkit-box-orient: vertical;
    }

    .top-left {
        position: absolute;
        top: 8px;
        left: 16px;
    }

    .top-right {
        position: absolute;
        top: 8px;
        right: 16px;
    }

    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>
