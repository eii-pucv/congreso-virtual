<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper col-xl-6 mx-auto" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.archivos_proyecto.titulo') }}</h4>
            <div class="row justify-content-center">
                <div class="border p-3 m-1">
                    <label>{{ $t('administrador.componentes.archivos_proyecto.archivo') }}:
                        <input type="file" id="file" ref="file" @change="handleFileUpload" />
                    </label>
                    <div class="row justify-content-center">
                        <button
                                class="vld-parent"
                                :disabled="disabledBtn"
                                @click="uploadFile"
                                :class="mode==='dark'?'btn btn-outline-grey px-30':'btn btn-outline-primary px-30'"
                        >
                            {{ $t('administrador.componentes.archivos_proyecto.subir') }}
                            <loading
                                    :active.sync="loadBtnUpload"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center vld-parent">
                <div v-if="loadFiles" style="height: 300px;">
                    <Loading
                            :active.sync="loadFiles"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="'#000000'"
                    ></Loading>
                </div>
                <div v-if="!loadFiles" class="w-100 p-3">
                    <div v-if="files.length === 0" class="text-center">{{ $t('administrador.componentes.archivos_proyecto.no_hay_archivos') }}</div>
                    <ul class="list-unstyled px-20">
                        <li v-for="file in files" :key="file.id" class="media pa-20 border border-2 border-light col-12 mb-5">
                            <div class="media-body" v-if="file">
                                <div class="row mx-0">
                                    <h6 class="text-truncate">{{ file.original_filename }}</h6>
                                </div>
                                <small class="text-grey">{{ new Date(toLocalDatetime(file.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                                <div class="btn-group btn-group-toggle btn-block mt-10">
                                    <button
                                            class="btn-sm vld-parent"
                                            @click="downloadFile(file.id)"
                                            :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-primary'"
                                    >
                                        {{ $t('administrador.componentes.archivos_proyecto.descargar') }}
                                        <loading
                                                :active.sync="loadBtnDownloads.find(loadBtnDownload => loadBtnDownload.file_id === file.id).value"
                                                :is-full-page="fullPage"
                                                :height="24"
                                                :color="color"
                                        ></loading>
                                    </button>
                                    <button
                                            class="btn-sm vld-parent"
                                            @click="deleteFile(file.id)"
                                            :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-danger'"
                                    >
                                        {{ $t('administrador.componentes.archivos_proyecto.eliminar') }}
                                        <loading
                                                :active.sync="loadBtnDeletes.find(loadBtnDelete => loadBtnDelete.file_id === file.id).value"
                                                :is-full-page="fullPage"
                                                :height="24"
                                                :color="color"
                                        ></loading>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from '../../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectFiles',
        components: {
            Loading
        },
        props: {
            project_id: Number,
        },
        data() {
            return {
                files: [],
                file: null,
                disabledBtn: true,
                loadFiles: true,
                loadBtnUpload: false,
                loadBtnDownloads: [],
                loadBtnDeletes: [],
                fullPage: false,
                color: '#ffffff',
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getFiles();
        },
        methods: {
            getFiles() {
                axios
                    .get('/projects/' + this.project_id + '/files')
                    .then(res => {
                        this.files = res.data;
                        this.files.forEach(file => {
                            this.loadBtnDownloads.push({
                                file_id: file.id,
                                value: false
                            });
                            this.loadBtnDeletes.push({
                                file_id: file.id,
                                value: false
                            });
                        });
                    })
                    .finally(() => {
                        this.loadFiles = false;
                    });
            },
            downloadFile(fileId) {
                let file = this.files.find(file => file.id === fileId);
                let loadBtnDownload = this.loadBtnDownloads.find(loadBtnDownload => loadBtnDownload.file_id === fileId);
                loadBtnDownload.value = true;

                axios({
                    method: 'get',
                    url: '/storage/projects/' + this.project_id + '/' + file.stored_name + '.' + file.extension,
                    responseType: 'blob'
                })
                    .then(res => {
                        let type = res.headers['content-type'];
                        let blob = new Blob([res.data], {type: type});
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = file.original_name + '.' + file.extension;
                        document.body.appendChild(link);
                        link.click();

                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.descargado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.descargado.generico.titulo')
                        );
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.descargado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.descargado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        loadBtnDownload.value = false;
                    });
            },
            handleFileUpload() {
                this.file = this.$refs.file.files[0];
                this.disabledBtn = false;
            },
            uploadFile() {
                this.loadBtnUpload = true;
                let projectFileFormData = new FormData();
                projectFileFormData.append('files[]', this.file);
                axios
                    .post('/projects/' + this.project_id + '/files', projectFileFormData)
                    .then(() => {
                        this.getFiles();
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.guardado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.guardado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.guardado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.guardado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.disabledBtn = true;
                        let fileInput = document.getElementById('file');
                        if(fileInput) {
                            document.getElementById('file').value = null;
                        }
                        this.loadBtnUpload = false;
                    });
            },
            deleteFile(fileId) {
                let loadBtnDelete = this.loadBtnDeletes.find(loadBtnDelete => loadBtnDelete.file_id === fileId);
                loadBtnDelete.value = true;
                axios
                    .delete('/projects/' + this.project_id + '/file/' + fileId)
                    .then(() => {
                        this.files = this.files.filter(file => file.id !== fileId);
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.eliminado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.exito.eliminado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.eliminado.generico.cuerpo'),
                            this.$t('administrador.componentes.archivos_proyecto.mensajes.fallido.eliminado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        loadBtnDelete.value = false;
                    });
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>