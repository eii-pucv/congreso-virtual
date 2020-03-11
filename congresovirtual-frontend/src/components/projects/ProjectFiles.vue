<template>
    <div class="container">
        <p>{{ $t('proyecto.componentes.archivos.texto') }}</p>
        <div v-if="loadFiles" class="vld-parent" style="height: 300px;">
            <Loading
                    :active.sync="loadFiles"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="'#000000'"
            ></Loading>
        </div>
        <div v-if="!loadFiles" class="mt-20">
            <div v-if="files.length === 0" class="text-center">{{ $t('proyecto.componentes.archivos.no_hay_archivos') }}</div>
            <ul v-else class="list-unstyled">
                <li v-for="file in files" :key="file.id" class="media pa-20 border border-2 border-light mb-5">
                    <div class="media-body" v-if="file">
                        <div class="row mx-0">
                            <h6 class="text-truncate">{{ file.original_filename }}</h6>
                        </div>
                        <small class="text-grey">{{ new Date(toLocalDatetime(file.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                        <div class="mt-10">
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
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectFiles',
        components: {
            Loading
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                files: [],
                loadFiles: true,
                loadBtnDownloads: [],
                fullPage: false,
                color: '#ffffff',
                mode: String
            };
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
                            this.$t('proyecto.componentes.archivos.mensajes.exito.descargado.generico.cuerpo'),
                            this.$t('proyecto.componentes.archivos.mensajes.exito.descargado.generico.titulo')
                        );
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('proyecto.componentes.archivos.mensajes.fallido.descargado.generico.cuerpo'),
                            this.$t('proyecto.componentes.archivos.mensajes.fallido.descargado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        loadBtnDownload.value = false;
                    });
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>