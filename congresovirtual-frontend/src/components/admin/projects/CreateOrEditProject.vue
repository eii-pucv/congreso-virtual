<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!project_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_proyecto.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_proyecto.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadProject" style="height: 300px;">
                    <Loading
                            :active.sync="loadProject"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadProject">
                    <form @submit.prevent="saveProject">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-8 mb-10">
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.titulo') }}</label>
                                <input
                                        id="titulo"
                                        v-model="project.titulo"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-2 mb-10">
                                <label for="boletin" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.boletin') }}</label>
                                <input
                                        id="boletin"
                                        v-model="project.boletin"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="postulante" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.postulante') }}</label>
                                <input
                                        id="postulante"
                                        v-model="project.postulante"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="fecha_inicio" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.fecha_inicio') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_proyecto.popover_fecha_inicio') }}</p>
                                    </template>
                                </v-popover>
                                <DatePicker
                                        id="fecha_inicio"
                                        v-model="fechaInicioLocal"
                                        :config="dateOptions"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></DatePicker>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="fecha_termino" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.fecha_termino') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_proyecto.popover_fecha_termino') }}</p>
                                    </template>
                                </v-popover>
                                <DatePicker
                                        id="fecha_termino"
                                        v-model="fechaTerminoLocal"
                                        :config="dateOptions"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></DatePicker>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-6 mb-10">
                                <label for="estado" :style="mode==='dark'?'color: #fff':''">{{  $t('administrador.componentes.crear_proyecto.estado') }}</label>
                                <input
                                        id="estado"
                                        v-model="project.estado"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="etapa" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.etapa.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_proyecto.etapa.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="etapa"
                                        v-model="project.etapa"
                                        class="form-control custom-select d-block w-100"
                                        required
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option value="1">{{ $t('administrador.componentes.crear_proyecto.etapa.opcion1') }}</option>
                                    <option value="2">{{ $t('administrador.componentes.crear_proyecto.etapa.opcion2') }}</option>
                                    <option value="3">{{ $t('administrador.componentes.crear_proyecto.etapa.opcion3') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="video_source" :style="mode==='dark'?'color: #fff':''">{{  $t('administrador.componentes.crear_proyecto.fuente_video.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_proyecto.fuente_video.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="video_source"
                                        v-model="project.video_source"
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
                                <label for="video_code" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.codigo_video.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_proyecto.codigo_video.popover') }}</p>
                                    </template>
                                </v-popover>
                                <div class="input-group">
                                    <div v-if="project.video_source" class="input-group-prepend">
                                        <span class="input-group-text">{{ videoCodePrepends.find(videoCodePrepend => videoCodePrepend.id === project.video_source).label }}</span>
                                    </div>
                                    <input
                                            id="video_code"
                                            v-model="project.video_code"
                                            v-bind:disabled="!project.video_source"
                                            type="text"
                                            class="form-control"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="terms" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.tema_asociado.titulo') }}</label>
                                <multiselect
                                        id="terms"
                                        v-model="currentProjectTerms"
                                        group-values="terms"
                                        group-label="value"
                                        track-by="id"
                                        label="value"
                                        :placeholder="$t('administrador.componentes.crear_proyecto.tema_asociado.buscar')"
                                        :options="taxonomyTerms"
                                        :multiple="true"
                                        :limit="10"
                                        :limit-text="limitTextTaxonomyTermsMultiselect"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="stopwords" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.stopword.titulo') }}</label>
                                <multiselect
                                        id="stopwords"
                                        v-model="currentProjectStopwords"
                                        group-values="stopwords"
                                        group-label="select_all"
                                        track-by="value"
                                        label="value"
                                        :tag-placeholder="$t('administrador.componentes.crear_proyecto.stopword.agregar')"
                                        :placeholder="$t('administrador.componentes.crear_proyecto.stopword.buscar')"
                                        :options="stopwords"
                                        :multiple="true"
                                        :taggable="true"
                                        @tag="addStopword"
                                        :limit="20"
                                        :limit-text="limitTextStopwordsMultiselect"
                                        :group-select="true"
                                        :style="mode==='dark'?' color: #fff':''"
                                        @remove="unselectAllStopwords"
                                ></multiselect>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="imagen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.imagen') }}</label>
                                <vue-dropzone
                                        id="imagen"
                                        ref="imagenDropzone"
                                        :options="imagenDropzoneOptions"
                                        @vdropzone-file-added="imagenDropzoneFileAdded"
                                        @vdropzone-removed-file="imagenDropzoneRemovedFile"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></vue-dropzone>
                            </div>
                            <div class="col-md-6 mb-10">
                                <label for="files" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.archivos') }}</label>
                                <vue-dropzone
                                        id="files"
                                        ref="filesDropzone"
                                        :options="filesDropzoneOptions"
                                        @vdropzone-file-added="filesDropzoneFileAdded"
                                        @vdropzone-removed-file="filesDropzoneRemovedFile"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></vue-dropzone>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="resumen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.resumen') }}</label>
                                <textarea
                                        id="resumen"
                                        v-model="project.resumen"
                                        class="form-control"
                                        rows="6"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.detalle') }}</label>
                                <editor
                                        id="detalle"
                                        v-model="project.detalle"
                                        :init="tinymceInitOptions"
                                />
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="notificar_correo"
                                            v-model="project.notificar_correo"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="notificar_correo" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.suscritos') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="is_public"
                                            v-model="project.is_public"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="is_public" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.publico') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_proyecto.popover_publico') }}</p>
                                        </template>
                                    </v-popover>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="is_principal"
                                            v-model="project.is_principal"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="is_principal" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.principal') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_proyecto.popover_principal') }}</p>
                                        </template>
                                    </v-popover>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="is_enabled"
                                            v-model="project.is_enabled"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="is_enabled" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.habilitado') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_proyecto.popover_habilitado') }}</p>
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
                            <a v-if="project.id" class="btn btn-outline-primary ml-10" :href="'/admin/project/' + project.id + '/idea'">
                                <font-awesome-icon icon="pencil-alt" />
                                <span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('administrador.componentes.crear_proyecto.agregar_idea') }}</span>
                            </a>
                            <a v-if="project.id" class="btn btn-outline-primary ml-10" :href="'/admin/project/' + project.id + '/article'">
                                <font-awesome-icon icon="book" />
                                <span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('administrador.componentes.crear_proyecto.agregar_articulo') }}</span>
                            </a>
                            <button @click="back" class="btn btn-danger text-white ml-10">
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
    import Multiselect from 'vue-multiselect';
    import vue2Dropzone from 'vue2-dropzone';
    import Editor from '@tinymce/tinymce-vue';
    import axios from '../../../backend/axios';
    import { API_URL } from '../../../backend/data_server';

    export default {
        name: 'CreateOrEditProject',
        components: {
            Loading,
            DatePicker,
            Multiselect,
            vueDropzone: vue2Dropzone,
            'editor': Editor
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                project: {
                    titulo: null,
                    postulante: null,
                    estado: null,
                    etapa: 1,
                    detalle: null,
                    resumen: null,
                    fecha_inicio: null,
                    fecha_termino: null,
                    boletin: null,
                    is_principal: false,
                    is_public: false,
                    is_enabled: true,
                    video_code: null,
                    video_source: null,
                    imagen: null,
                    files: [],
                    notificar_correo: false
                },
                fechaInicioLocal: this.$moment().local(),
                fechaTerminoLocal: this.$moment().local(),
                videoSources: this.$t('administrador.componentes.crear_proyecto.fuente_video.opciones'),
                videoCodePrepends: this.$t('administrador.componentes.crear_proyecto.codigo_video.prepends'),
                currentProjectTerms: [],
                oldProjectTerms: [],
                taxonomyTerms: [],
                currentProjectStopwords: [],
                oldProjectStopwords: [],
                stopwords: [
                    {
                        select_all: 'Seleccionar todos',
                        stopwords: []
                    }
                ],
                imagenDropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 200,
                    maxFiles: 1,
                    maxFilesize: 20,
                    addRemoveLinks: true,
                    acceptedFiles: '.png, .jpeg, .jpg, .bmp, .gif, .svg'
                },
                newImagen: null,
                deleteImagen: false,
                filesDropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 200,
                    maxFiles: 5,
                    maxFilesize: 20,
                    addRemoveLinks: true,
                    acceptedFiles: '.png, .jpeg, .jpg, .bmp, .gif, .svg, .amr, .ogg, .mp3, .m4a, .wav, .pdf, .doc, .docx, .odt, .ppt, .pptx, .pps, .ppsx, .odp'
                },
                newFiles: [],
                deleteFiles: [],
                tinymceInitOptions: {
                    height: 300,
                    language: this.$i18n.locale,
                    language_url: '/tinymce/langs/es.js',
                    skin_url: '/tinymce/skins/lightgray',
                    plugins: [
                        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        'searchreplace wordcount visualblocks visualchars code fullscreen',
                        'insertdatetime media nonbreaking save table contextmenu directionality',
                        'template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample'

                    ],
                    toolbar:
                        'insertfile undo redo | formatselect | styleselect | bold italic strikethrough forecolor backcolor | \
                        alignleft aligncenter alignright alignjustify | \
                        bullist numlist outdent indent | link image | removeformat'
                },
                loadProject: true,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String,
                API_URL
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();

            if(this.project_id) {
                this.getProject();
            } else {
                this.loadProject = false;
            }

            this.getTaxonomyTerms();
            this.getStopwords();
        },
        methods: {
            configComponent() {
                this.imagenDropzoneOptions = Object.assign(this.imagenDropzoneOptions, this.$t('componentes.dropzone'));
                this.filesDropzoneOptions = Object.assign(this.filesDropzoneOptions, this.$t('componentes.dropzone'));
            },
            getProject() {
                axios
                    .get('/projects/' + this.project_id)
                    .then(res => {
                        this.project = res.data;
                        this.fechaInicioLocal = this.project.fecha_inicio ? this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local() : null;
                        this.fechaTerminoLocal = this.project.fecha_termino ? this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local() : null;
                        this.currentProjectTerms = this.project.terms;
                        this.oldProjectTerms = this.currentProjectTerms;
                        this.getProjectStopwords();

                        this.loadProject = false;
                        this.$nextTick(() => {
                            if(this.project.imagen) {
                                let imagen = this.project.files.find(file => file.filetype === 'PRIMARY-IMAGE');
                                this.$refs.imagenDropzone.manuallyAddFile({
                                    size: '',
                                    name: imagen.original_filename,
                                    type: 'image/' + imagen.extension
                                }, API_URL + '/api/storage/projects/' + this.project.id + '/' + imagen.stored_filename);
                            }

                            this.project.files.forEach(file => {
                                if(file.filetype !== 'PRIMARY-IMAGE') {
                                    this.$refs.filesDropzone.manuallyAddFile({
                                        size: '',
                                        id: file.id,
                                        name: file.original_filename
                                    }, API_URL + '/api/storage/projects/' + this.project.id + '/' + file.stored_filename);
                                }
                            });
                        });
                    })
                    .finally(() => {
                        this.loadProject = false;
                    });
            },
            getProjectStopwords() {
                axios
                    .get('/projects/' + this.project_id + '/stopwords', {
                        params: {
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.currentProjectStopwords = res.data.results.map(stopword => {
                            return { value: stopword.value };
                        });
                        this.oldProjectStopwords = this.currentProjectStopwords;
                    });
            },
            getTaxonomyTerms() {
                axios
                    .get('/taxonomies', {
                        params: {
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.taxonomyTerms = res.data.results;
                    });
            },
            getStopwords() {
                axios
                    .get('/stopwords', {
                        params: {
                            distinct_value: 1,
                            return_all: 1
                        }
                    })
                    .then(res => {
                        this.stopwords[0].stopwords = this.stopwords[0].stopwords.concat(res.data.results);
                    });
            },
            saveProject() {
                this.loadBtnSave = true;
                this.project.fecha_inicio = this.fechaInicioLocal ? this.$moment(this.fechaInicioLocal, this.$t('componentes.moment.formato_editable_con_hora')).utc().format('YYYY-MM-DD HH:mm:ss') : null;
                this.project.fecha_termino = this.fechaTerminoLocal ? this.$moment(this.fechaTerminoLocal, this.$t('componentes.moment.formato_editable_con_hora')).utc().format('YYYY-MM-DD HH:mm:ss') : null;
                if(this.project.id) {
                    if(this.project.detalle) {
                        axios
                            .put('/projects/' + this.project.id, this.project)
                            .then(() => {
                                let promises = [
                                    this.updateProjectTerms(),
                                    this.updateProjectStopwords(),
                                    this.updateOrDeleteImagen(),
                                    this.addNewFiles(),
                                    this.deleteOldFiles()
                                ];
                                Promise.all(promises)
                                    .then(() => {
                                        this.$toastr(
                                            'success',
                                            this.$t('administrador.componentes.crear_proyecto.mensajes.exito.modificado.generico.cuerpo'),
                                            this.$t('administrador.componentes.crear_proyecto.mensajes.exito.modificado.generico.titulo')
                                        );
                                    })
                                    .catch(error => {
                                        this.$toastr('error', error.detail, error.title);
                                    })
                                    .finally(() => {
                                        this.loadBtnSave = false;
                                    });
                            })
                            .catch(() => {
                                this.loadBtnSave = false;
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.generico.titulo')
                                );
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.falta_detalle.cuerpo'),
                            this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.falta_detalle.titulo')
                        );
                    }
                } else {
                    if(this.project.detalle) {
                        let projectFormData = this.generateProjectFormData();
                        axios
                            .post('/projects', projectFormData)
                            .then(res => {
                                this.project = res.data.data;
                                this.oldProjectTerms = this.currentProjectTerms;

                                if(this.currentProjectStopwords.length > 0) {
                                    axios
                                        .post('/projects/' + this.project.id + '/stopwords', {
                                            stopwords: this.currentProjectStopwords
                                        })
                                        .then(() => {
                                            this.oldProjectStopwords = this.currentProjectStopwords;
                                            this.$toastr(
                                                'success',
                                                this.$t('administrador.componentes.crear_proyecto.mensajes.exito.guardado.generico.cuerpo'),
                                                this.$t('administrador.componentes.crear_proyecto.mensajes.exito.guardado.generico.titulo')
                                            );
                                        })
                                        .catch(() => {
                                            this.$toastr(
                                                'error',
                                                this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.stopwords.cuerpo'),
                                                this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.stopwords.titulo')
                                            );
                                        });
                                } else {
                                    this.$toastr(
                                        'success',
                                        this.$t('administrador.componentes.crear_proyecto.mensajes.exito.guardado.generico.cuerpo'),
                                        this.$t('administrador.componentes.crear_proyecto.mensajes.exito.guardado.generico.titulo')
                                    );
                                }
                            })
                            .catch(() => {
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.generico.titulo')
                                );
                            })
                            .finally(() => {
                                this.loadBtnSave = false;
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.falta_detalle.cuerpo'),
                            this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.guardado.falta_detalle.titulo')
                        );
                    }
                }
            },
            updateProjectTerms() {
                return new Promise((resolve, reject) => {
                    if(JSON.stringify(this.currentProjectTerms) !== JSON.stringify(this.oldProjectTerms)) {
                        axios
                            .delete('/projects/' + this.project.id + '/terms')
                            .then(() => {
                                let termsId = this.currentProjectTerms.map(term => term.id);
                                axios
                                    .post('/projects/' + this.project.id + '/terms', {
                                        terms_id: termsId
                                    })
                                    .then(() => {
                                        this.oldProjectTerms = this.currentProjectTerms;
                                        resolve(true);
                                    });
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.terminos.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.terminos.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            updateProjectStopwords() {
                return new Promise((resolve, reject) => {
                    if(JSON.stringify(this.currentProjectStopwords) !== JSON.stringify(this.oldProjectStopwords)) {
                        axios
                            .delete('/projects/' + this.project.id + '/stopwords')
                            .then(() => {
                                axios
                                    .post('/projects/' + this.project.id + '/stopwords', {
                                        stopwords: this.currentProjectStopwords
                                    })
                                    .then(() => {
                                        this.oldProjectStopwords = this.currentProjectStopwords;
                                        resolve(true);
                                    });
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.stopwords.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.stopwords.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            updateOrDeleteImagen() {
                return new Promise((resolve, reject) => {
                    if(this.newImagen && this.newImagen.accepted) {
                        let newImagenFormData = new FormData();
                        newImagenFormData.append('imagen', this.newImagen);
                        axios
                            .post('/projects/' + this.project.id + '/image', newImagenFormData)
                            .then(() => {
                                this.newImagen = null;
                                resolve(true);
                            })
                            .catch(() => {
                                if(this.$refs.imagenDropzone) {
                                    this.$refs.imagenDropzone.removeAllFiles();
                                }
                                this.newImagen = null;
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.imagen_actualizada.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.imagen_actualizada.cuerpo')
                                });
                            });
                    } else if(this.deleteImagen) {
                        axios
                            .delete('/projects/' + this.project.id + '/image')
                            .then(() => {
                                this.deleteImagen = false;
                                resolve(true);
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.imagen_eliminada.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.imagen_eliminada.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            addNewFiles() {
                return new Promise((resolve, reject) => {
                    if(this.newFiles.length > 0) {
                        let newFilesFormData = new FormData();
                        let filesAccepted = this.newFiles.filter(file => file.accepted);
                        filesAccepted.forEach(file => {
                            newFilesFormData.append('files[]', file);
                        });
                        axios
                            .post('/projects/' + this.project.id + '/files', newFilesFormData)
                            .then(() => {
                                this.newFiles = [];
                                resolve(true);
                            })
                            .catch(() => {
                                if(this.$refs.filesDropzone) {
                                    this.$refs.filesDropzone.forEach(file => {
                                        if(!file.manuallyAdded) {
                                            this.$refs.filesDropzone.removeFile(file);
                                        }
                                    });
                                }
                                this.newFiles = [];
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.archivos_agregados.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.archivos_agregados.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            deleteOldFiles() {
                return new Promise((resolve, reject) => {
                    if(this.deleteFiles.length > 0) {
                        axios
                            .delete('/projects/' + this.project.id + '/files', {
                                params: {
                                    files_id: this.deleteFiles
                                }
                            })
                            .then(() => {
                                this.deleteFiles = [];
                                resolve(true);
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.archivos_eliminados.titulo'),
                                    detail: this.$t('administrador.componentes.crear_proyecto.mensajes.fallido.modificado.archivos_eliminados.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            generateProjectFormData() {
                let projectFormData = new FormData();

                if(this.project.titulo) projectFormData.append('titulo', this.project.titulo);
                if(this.project.postulante) projectFormData.append('postulante', this.project.postulante);
                if(this.project.estado) projectFormData.append('estado', this.project.estado);
                projectFormData.append('etapa', this.project.etapa);
                if(this.project.detalle) projectFormData.append('detalle', this.project.detalle);
                if(this.project.resumen) projectFormData.append('resumen', this.project.resumen);
                if(this.project.fecha_inicio) projectFormData.append('fecha_inicio', this.project.fecha_inicio);
                if(this.project.fecha_termino) projectFormData.append('fecha_termino', this.project.fecha_termino);
                if(this.project.boletin) projectFormData.append('boletin', this.project.boletin);
                projectFormData.append('is_principal', this.project.is_principal ? 1 : 0);
                projectFormData.append('is_public', this.project.is_public ? 1 : 0);
                projectFormData.append('is_enabled', this.project.is_enabled ? 1 : 0);
                if(this.project.video_code) projectFormData.append('video_code', this.project.video_code);
                if(this.project.video_source) projectFormData.append('video_source', this.project.video_source);
                projectFormData.append('notificar_correo', this.project.notificar_correo ? 1 : 0);

                this.currentProjectTerms.forEach(term => {
                    projectFormData.append('terms_id[]', term.id);
                });

                if(this.$refs.imagenDropzone && this.$refs.imagenDropzone.getAcceptedFiles().length > 0) {
                    this.project.imagen = this.$refs.imagenDropzone.getAcceptedFiles()[0];
                    projectFormData.append('imagen', this.project.imagen);
                }

                if(this.$refs.filesDropzone) {
                    this.project.files = this.$refs.filesDropzone.getAcceptedFiles();
                }
                this.project.files.forEach(file => {
                    projectFormData.append('files[]', file);
                });

                return projectFormData;
            },
            unselectAllStopwords(removeOption) {
                if(Array.isArray(removeOption)) {
                    this.currentProjectStopwords.splice(0, this.currentProjectStopwords.length);
                }
            },
            addStopword(newTag) {
                const tag = { value: newTag };
                this.stopwords[0].stopwords.push(tag);
                this.currentProjectStopwords.push(tag);
            },
            limitTextTaxonomyTermsMultiselect(count) {
                if(this.$i18n.locale === 'es') {
                    if(count === 1) {
                        return `y 1 tema más`;
                    } else {
                        return `y ${count} temas más`;
                    }
                } else if(count === 1) {
                    return `and 1 more topic`;
                } else {
                    return `and ${count} more topics`;
                }
            },
            limitTextStopwordsMultiselect(count) {
                if(this.$i18n.locale === 'es') {
                    if(count === 1) {
                        return `y 1 palabra más`;
                    } else {
                        return `y ${count} palabras más`;
                    }
                } else if(count === 1) {
                    return `and 1 more word`;
                } else {
                    return `and ${count} more words`;
                }
            },
            imagenDropzoneFileAdded(file) {
                if(this.project.id && !file.manuallyAdded) {
                    this.newImagen = file;
                }
            },
            imagenDropzoneRemovedFile(file) {
                if(this.project.id && file.manuallyAdded) {
                    this.deleteImagen = true;
                } else {
                    this.newImagen = null;
                }
            },
            filesDropzoneFileAdded(file) {
                if(this.project.id && !file.manuallyAdded) {
                    this.newFiles.push(file);
                }
            },
            filesDropzoneRemovedFile(file) {
                if(this.project.id && file.manuallyAdded) {
                    this.deleteFiles.push(file.id);
                } else {
                    this.newFiles = this.newFiles.filter(newFile => newFile !== file);
                }
            },
            back() {
                location.replace(document.referrer);
            }
        },
        computed: {
            dateOptions() {
                return {
                    format: this.$t('componentes.moment.formato_editable_con_hora'),
                    locale: this.$moment.locale()
                };
            }
        },
        watch: {
            'project.video_source': function (value) {
                if(!this.loadProject && !value) {
                    this.project.video_code = null;
                }
            }
        }
    }
</script>