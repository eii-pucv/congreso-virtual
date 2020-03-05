<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper col-xl-6 mx-auto" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.documentos.titulo') }}</h4>
            <div class="row justify-content-center">
                <div class="border p-3 m-1">
                    <label>{{ $t('administrador.componentes.documentos.archivo') }}:
                        <input type="file" id="file" ref="file" @change="handleFileUpload"/>
                    </label>
                    <div class="row justify-content-center">
                        <button :disabled="disabledBtn" @click="submitFile" :class="mode==='dark'?'btn btn-outline-grey px-30':'btn btn-outline-primary px-30'">{{ $t('administrador.componentes.documentos.subir') }}</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center">
                <div class="w-100 p-3 m-1" v-if="!loadFiles">
                    <ul v-if="files.length > 0" class="list-unstyled px-20 col-12">
                        <li v-for="(file, index) in files" :key="file.id" class="media pa-20 border border-2 border-light col-12 mb-5">
                            <div class="media-body" v-if="file">
                                <div class="row mx-0">
                                    <h6 class="text-truncate">{{ file.original_filename }}</h6>
                                </div>
                                <small class="text-grey">{{ new Date(toLocalDatetime(file.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                                <div class="btn-group btn-group-toggle btn-block mt-10">
                                    <button class="vld-parent" @click="download(file.id)" :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-primary'">{{ $t('administrador.componentes.documentos.descargar') }}
                                        <loading
                                                :active.sync="loadBtnDownloads.find(loadBtnDownload => loadBtnDownload.file_id === file.id).value"
                                                :is-full-page="fullPage"
                                                :height="24"
                                                :color="color"
                                        ></loading>
                                    </button>
                                    <button @click="eliminarFile(file, index)" :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-danger'">{{ $t('administrador.componentes.documentos.eliminar') }}</button>
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
                file: '',
                disabledBtn: true,
                loadFiles: true,
                loadBtnDownloads: [],
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
                        });
                    })
                    .finally(() => {
                        this.loadFiles = false;
                    });
            },
            download(fileId) {
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
                        this.$toastr('success', '', 'Archivo listo para descargar');
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr('error', '', 'No se pudo descargar el archivo');
                    })
                    .finally(() => {
                        loadBtnDownload.value = false;
                    });
            },

            submitFile() {
                let formData = new FormData();
                formData.append('files[]', this.file);
                axios.post('/projects/' + this.project_id + '/files',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(() => {
                    console.log('EXITO!!');
                    this.disabledBtn = true;
                    let fileInput = document.getElementById("file")
                    if (fileInput) {
                        document.getElementById("file").value = "";
                    }
                    this.getFiles()
                    this.$toastr('success', '', 'Archivo subido');
                })
                    .catch(() => {
                        console.log('ERROR!!');
                        this.disabledBtn = true;
                        let fileInput = document.getElementById("file")
                        if (fileInput) {
                            document.getElementById("file").value = "";
                        }
                        this.$toastr('error', '', 'No se pudo subir el archivo, quizás superó el tamaño máximo del archivo permitido');
                    });
            },

            /*
                Maneja un cambio en el archivo subido
            */
            handleFileUpload() {
                this.file = this.$refs.file.files[0];
                this.disabledBtn = false;
            },

            eliminarFile(file, index) {
                axios
                    .delete("/projects/" + this.project_id + "/file/" + file.id)
                    .then(res => {
                        this.files.splice(index, 1);
                        this.$toastr('success', '', 'Archivo eliminado');
                        console.log("Éxito al eliminar el archivo");
                    })
                    .catch(() => {
                        console.error("FAIL");
                        this.$toastr('error', '', 'No se pudo eliminar el archivo');
                    });
            }
        }
    }
</script>