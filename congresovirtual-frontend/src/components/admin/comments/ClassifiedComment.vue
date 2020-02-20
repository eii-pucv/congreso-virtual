<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <div class="row">
                <div class="col-sm-12">
                    <section v-if="!loadComment" class="hk-sec-wrapper" :class="mode==='dark'?'dark':'light'">
                        <div class="d-flex flex-row float-left">
                            <span class="mr-10">
                                <i class="fa fa-thumbs-up" :class="comment.votos_a_favor > 0 ? 'text-success' : ''"></i>
                                {{ comment.votos_a_favor }}
                            </span>
                            <span>
                                <i class="fa fa-thumbs-down" :class="comment.votos_en_contra > 0 ? 'text-danger' : ''"></i>
                                {{ comment.votos_en_contra }}
                            </span>
                        </div>
                        <span v-if="comment.state == 0" class="float-right badge badge-pill badge-success font-15 py-2 px-2">
                            {{ $t('administrador.componentes.aporte_clasificado.estado_1') }}
                        </span>
                        <span v-else-if="comment.state == 1" class="float-right badge badge-pill badge-info font-15 py-2 px-2">
                            {{ $t('administrador.componentes.aporte_clasificado.estado_2') }}
                        </span>
                        <span v-else-if="comment.state == 2" class="float-right badge badge-pill badge-danger font-15 py-2 px-2">
                            {{ $t('administrador.componentes.aporte_clasificado.estado_3') }}
                        </span>
                        <span v-else-if="comment.state == 3" class="float-right badge badge-pill badge-danger font-15 py-2 px-2">
                            {{ $t('administrador.componentes.aporte_clasificado.estado_4') }}
                        </span>
                        <span v-else class="float-right badge badge-pill badge-secondary font-15 py-2 px-2">
                            {{ $t('administrador.componentes.aporte_clasificado.estado_5') }}
                        </span>
                        <br />
                        <br />
                        <h4 v-if="comment.user" class="hk-sec-title text-center">
                            {{ $t('administrador.componentes.aporte_clasificado.titulo') }}: {{ fullname }}
                        </h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form class="needs-validation">
                                    <div class="form-row div-align">
                                        <div class="col-md-10 mb-10">
                                            <div class="input-group">
                                                <div v-if="comment.project">
                                                    <span class="font-weight-bold">{{ $t('administrador.componentes.aporte_clasificado.hecho_en') }}</span>:
                                                    <small class="badge badge-pill badge-blue py-2 px-2 mx-2">{{ $t('administrador.componentes.aporte_clasificado.proyecto') }}</small>
                                                    {{ comment.project.titulo }}
                                                </div>
                                                <div v-if="comment.article">
                                                    <span class="font-weight-bold">{{ $t('administrador.componentes.aporte_clasificado.hecho_en') }}</span>:
                                                    <small class="badge badge-pill badge-blue py-2 px-2 mx-2">{{ $t('administrador.componentes.aporte_clasificado.articulo') }}</small>
                                                    {{ comment.article.titulo }}
                                                </div>
                                                <div v-if="comment.idea">
                                                    <span class="font-weight-bold">{{ $t('administrador.componentes.aporte_clasificado.hecho_en') }}</span>:
                                                    <small class="badge badge-pill badge-blue py-2 px-2 mx-2">{{ $t('administrador.componentes.aporte_clasificado.idea_fundamental') }}</small>
                                                    {{ comment.idea.titulo }}
                                                </div>
                                                <div v-if="comment.public_consultation">
                                                    <span class="font-weight-bold">{{ $t('administrador.componentes.aporte_clasificado.hecho_en') }}</span>:
                                                    <small class="badge badge-pill badge-blue py-2 px-2 mx-2">{{ $t('administrador.componentes.aporte_clasificado.consulta_publica') }}</small>
                                                    {{ comment.public_consultation.titulo }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-10 mb-10">
                                            <label class="font-weight-bold" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.aporte_clasificado.comentario') }}:</label>
                                            <div class="media pa-20 border border-2 border-light col-12">
                                                <div class="media-body">
                                                    <div class="row">
                                                        <div class="col-8 col-sm-10">
                                                            <h6 v-if="comment.user" class="mb-2">
                                                                {{ fullname }}
                                                                <small>{{ comment.created_at }}</small>
                                                            </h6>
                                                            <h6 v-else class="mb-2">
                                                                {{ $t('administrador.componentes.aporte_clasificado.no_identificado') }}
                                                                <small>{{ comment.created_at }}</small>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <p>{{ comment.body }}</p>
                                                    <div v-if="comment.files.length" class="row mt-2 mb-3">
                                                        <div
                                                                class="col-sm"
                                                                v-for="file in comment.files"
                                                                :key="file.id"
                                                        >
                                                            <div class="card border">
                                                                <img
                                                                        v-if="(file.extension === 'png') || (file.extension === 'jpg') || (file.extension === 'jpeg') || (file.extension === 'bmp') || (file.extension === 'svg')"
                                                                        class="mx-auto p-2"
                                                                        height="auto"
                                                                        width="200"
                                                                        @click.prevent="downloadFile(file, comment.id)"
                                                                        :src="storageUrl + comment.id + '/' + file.stored_filename"
                                                                />
                                                                <img
                                                                        v-if="file.extension === 'gif'"
                                                                        class="img-fluid"
                                                                        height="auto"
                                                                        width="126"
                                                                        @click.prevent="downloadFile(file, comment.id)"
                                                                        src="../../../assets/img/gif-icon.png"
                                                                />
                                                                <img
                                                                        v-if="file.extension === 'pdf'"
                                                                        class="img-fluid"
                                                                        height="auto"
                                                                        width="116"
                                                                        @click.prevent="downloadFile(file, comment.id)"
                                                                        src="../../../assets/img/pdf-icon.png"
                                                                />
                                                                <img
                                                                        v-if="(file.extension === 'amr') || (file.extension === 'ogg') || (file.extension === 'mp3') || (file.extension === 'm4a') || (file.extension === 'wav') "
                                                                        class="img-fluid"
                                                                        height="auto"
                                                                        width="126"
                                                                        @click.prevent="downloadFile(file, comment.id)"
                                                                        src="../../../assets/img/audio-file-icon.png"
                                                                />
                                                                <div class="card-footer border justify-content-between">
                                                                    <div class="row">
                                                                        <div class="col-12 text-primary">
                                                                            {{ file.original_filename }}
                                                                        </div>
                                                                    </div>
                                                                    <button @click.prevent="downloadFile(file, comment.id)" class="btn btn-sm btn-outline-secondary float-right ml-1">
                                                                        <i class="fa fa-download"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-20">
                                        <a @click="back" class="btn text-white bg-danger">
                                            <font-awesome-icon icon="arrow-circle-left" />
                                            <span class="btn-text">{{ $t('administrador.componentes.aporte_clasificado.atras') }}</span>
                                        </a>
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
    import { API_URL } from '../../../backend/data_server';
    import axios from '../../../backend/axios';

    export default {
        name: 'ClassifiedComment',
        component: { },
        props: {
            comment_id: Number
        },
        data() {
            return {
                mode: String,
                comment: null,
                storageUrl: API_URL + '/api/storage/comments/',
                loadComment: true,
            };
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getComment();
        },
        methods: {
            getComment() {
                axios
                    .get('/comments/' + this.comment_id)
                    .then(res => {
                        this.comment = res.data;
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se ha encontrado el comentario', 'Comentario no encontrado');
                    })
                    .finally(() => {
                        this.loadComment = false;
                    });
            },
            downloadFile(file, commentId) {
                axios
                    .get('/storage/comments/' + commentId + '/' + file.stored_name + '.' + file.extension, {
                        responseType: 'blob'
                    })
                    .then(res => {
                        let type = res.headers['content-type'];
                        let blob = new Blob([res.data], { type: type });
                        let link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = file.original_name + "." + file.extension;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr('error', 'Algo salio mal, no se pudo descargar el archivo', 'Archivo no descargado');
                    });
            },
            back() {
                location.replace(document.referrer);
            }
        },
        computed: {
            fullname() {
                return this.comment.user
                    ? `${this.comment.user.name} ${this.comment.user.surname}`
                    : '';
            }
        },
    };
</script>

<style scoped>
    .seemore {
        display: none;
    }
    .seemore-trigger {
        text-decoration: underline;
        cursor: pointer;
    }
    .div-align {
        align-items: center;
        justify-content: center;
    }
</style>