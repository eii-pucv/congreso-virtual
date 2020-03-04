<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!public_consultation_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_consulta.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_consulta.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadPublicConsultation" style="height: 300px;">
                    <Loading
                            :active.sync="loadPublicConsultation"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadPublicConsultation">
                    <form @submit.prevent="savePublicConsultation">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.titulo') }}</label>
                                <input
                                        id="titulo"
                                        v-model="publicConsultation.titulo"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="autor" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.autor') }}</label>
                                <input
                                        id="autor"
                                        v-model="publicConsultation.autor"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="fecha_inicio" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.fecha_inicio') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_consulta.popover_fecha_inicio') }}</p>
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
                                <label for="fecha_termino" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.fecha_termino') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_consulta.popover_fecha_termino') }}</p>
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
                            <div class="col-md-4 mb-10">
                                <label for="estado" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.estado.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_consulta.estado.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="estado"
                                        v-model="publicConsultation.estado"
                                        class="form-control custom-select d-block w-100"
                                        required
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option value="0">{{ $t('administrador.componentes.crear_consulta.estado.opcion1') }}</option>
                                    <option value="1">{{ $t('administrador.componentes.crear_consulta.estado.opcion2') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-10">
                                <label for="terms" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.tema_asociado.titulo') }}</label>
                                <multiselect
                                        id="terms"
                                        v-model="currentPublicConsultationTerms"
                                        group-values="terms"
                                        group-label="value"
                                        track-by="id"
                                        label="value"
                                        :placeholder="$t('administrador.componentes.crear_consulta.tema_asociado.buscar')"
                                        :options="taxonomyTerms"
                                        :multiple="true"
                                        :limit="10"
                                        :limit-text="limitTextTaxonomyTermsMultiselect"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="video_source" :style="mode==='dark'?'color: #fff':''">{{  $t('administrador.componentes.crear_consulta.fuente_video.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_consulta.fuente_video.popover') }}</p>
                                    </template>
                                </v-popover>
                                <select
                                        id="video_source"
                                        v-model="publicConsultation.video_source"
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
                                <label for="video_code" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.codigo_video.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.crear_consulta.codigo_video.popover') }}</p>
                                    </template>
                                </v-popover>
                                <div class="input-group">
                                    <div v-if="publicConsultation.video_source" class="input-group-prepend">
                                        <span class="input-group-text">{{ videoCodePrepends.find(videoCodePrepend => videoCodePrepend.id === publicConsultation.video_source).label }}</span>
                                    </div>
                                    <input
                                            id="video_code"
                                            v-model="publicConsultation.video_code"
                                            v-bind:disabled="!publicConsultation.video_source"
                                            type="text"
                                            class="form-control"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="imagen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.imagen') }}</label>
                                <vue-dropzone
                                        id="imagen"
                                        ref="imagenDropzone"
                                        :options="imagenDropzoneOptions"
                                        @vdropzone-file-added="imagenDropzoneFileAdded"
                                        @vdropzone-removed-file="imagenDropzoneRemovedFile"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></vue-dropzone>
                            </div>
                            <div class="col-md-6 mb-20">
                                <label for="icono" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.icono') }}</label>
                                <vue-icon-picker
                                        id="icono"
                                        v-model="selectedIcon"
                                        height="150px"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></vue-icon-picker>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="resumen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.resumen') }}</label>
                                <textarea
                                        id="resumen"
                                        v-model="publicConsultation.resumen"
                                        class="form-control"
                                        rows="6"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.detalle') }}</label>
                                <editor
                                        id="detalle"
                                        v-model="publicConsultation.detalle"
                                        :init="tinymceInitOptions"
                                />
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
    import VueIconPicker from 'vue-icon-picker';
    import vue2Dropzone from 'vue2-dropzone';
    import Editor from '@tinymce/tinymce-vue';
    import axios from '../../../backend/axios';
    import { API_URL } from '../../../backend/data_server';

    export default {
        name: 'CreateOrEditPublicConsultation',
        components: {
            Loading,
            DatePicker,
            Multiselect,
            VueIconPicker,
            vueDropzone: vue2Dropzone,
            'editor': Editor
        },
        props: {
            public_consultation_id: Number
        },
        data() {
            return {
                publicConsultation: {
                    titulo: null,
                    autor: null,
                    estado: 1,
                    detalle: null,
                    resumen: null,
                    fecha_inicio: null,
                    fecha_termino: null,
                    icono: null,
                    video_code: null,
                    video_source: null,
                    imagen: null
                },
                fechaInicioLocal: this.$moment().local(),
                fechaTerminoLocal: this.$moment().local(),
                currentPublicConsultationTerms: [],
                oldPublicConsultationTerms: [],
                taxonomyTerms: [],
                videoSources: this.$t('administrador.componentes.crear_consulta.fuente_video.opciones'),
                videoCodePrepends: this.$t('administrador.componentes.crear_consulta.codigo_video.prepends'),
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
                selectedIcon: {
                    name: null,
                    type: 'fontawesome'
                },
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
                loadPublicConsultation: true,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();

            if(this.public_consultation_id) {
                this.getPublicConsultation();
            } else {
                this.loadPublicConsultation = false;
            }

            this.getTaxonomyTerms();
        },
        methods: {
            configComponent() {
                this.imagenDropzoneOptions = Object.assign(this.imagenDropzoneOptions, this.$t('componentes.dropzone'));
            },
            getPublicConsultation() {
                axios
                    .get('/consultations/' + this.public_consultation_id)
                    .then(res => {
                        this.publicConsultation = res.data;
                        this.fechaInicioLocal = this.publicConsultation.fecha_inicio ? this.$moment.utc(this.publicConsultation.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local() : null;
                        this.fechaTerminoLocal = this.publicConsultation.fecha_termino ? this.$moment.utc(this.publicConsultation.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local() : null;
                        this.selectedIcon.name = this.publicConsultation.icono;
                        this.currentPublicConsultationTerms = this.publicConsultation.terms;
                        this.oldPublicConsultationTerms = this.currentPublicConsultationTerms;

                        this.loadPublicConsultation = false;
                        this.$nextTick(() => {
                            if(this.publicConsultation.imagen) {
                                let imagen = this.publicConsultation.files.find(file => file.filetype === 'PRIMARY-IMAGE');
                                this.$refs.imagenDropzone.manuallyAddFile({
                                    size: '',
                                    name: imagen.original_filename,
                                    type: 'image/' + imagen.extension
                                }, API_URL + '/api/storage/consultations/' + this.publicConsultation.id + '/' + imagen.stored_filename);
                            }
                        });
                    })
                    .finally(() => {
                        this.loadPublicConsultation = false;
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
            savePublicConsultation() {
                this.loadBtnSave = true;
                this.publicConsultation.fecha_inicio = this.fechaInicioLocal ? this.$moment(this.fechaInicioLocal, this.$t('componentes.moment.formato_editable_con_hora')).utc().format('YYYY-MM-DD HH:mm:ss') : null;
                this.publicConsultation.fecha_termino = this.fechaTerminoLocal ? this.$moment(this.fechaTerminoLocal, this.$t('componentes.moment.formato_editable_con_hora')).utc().format('YYYY-MM-DD HH:mm:ss') : null;
                this.publicConsultation.icono = this.selectedIcon ? this.selectedIcon.name : null;
                if(this.publicConsultation.id) {
                    if(this.publicConsultation.detalle) {
                        axios
                            .put('/consultations/' + this.publicConsultation.id, this.publicConsultation)
                            .then(() => {
                                let promises = [
                                    this.updatePublicConsultationTerms(),
                                    this.updateOrDeleteImagen()
                                ];
                                Promise.all(promises)
                                    .then(() => {
                                        this.$toastr(
                                            'success',
                                            this.$t('administrador.componentes.crear_consulta.mensajes.exito.modificado.generico.cuerpo'),
                                            this.$t('administrador.componentes.crear_consulta.mensajes.exito.modificado.generico.titulo')
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
                                    this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.generico.titulo')
                                );
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.falta_detalle.cuerpo'),
                            this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.falta_detalle.titulo')
                        );
                    }
                } else {
                    if(this.publicConsultation.detalle) {
                        let publicConsultationFormData = this.generatePublicConsultationFormData();
                        axios
                            .post('/consultations', publicConsultationFormData)
                            .then(res => {
                                this.publicConsultation = res.data.data;
                                this.oldPublicConsultationTerms = this.currentPublicConsultationTerms;
                                this.$toastr(
                                    'success',
                                    this.$t('administrador.componentes.crear_consulta.mensajes.exito.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_consulta.mensajes.exito.guardado.generico.titulo')
                                );
                            })
                            .catch(() => {
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_consulta.mensajes.fallido.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_consulta.mensajes.fallido.guardado.generico.titulo')
                                );
                            })
                            .finally(() => {
                                this.loadBtnSave = false;
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_consulta.mensajes.fallido.guardado.falta_detalle.cuerpo'),
                            this.$t('administrador.componentes.crear_consulta.mensajes.fallido.guardado.falta_detalle.titulo')
                        );
                    }
                }
            },
            updatePublicConsultationTerms() {
                return new Promise((resolve, reject) => {
                    if(JSON.stringify(this.currentPublicConsultationTerms) !== JSON.stringify(this.oldPublicConsultationTerms)) {
                        axios
                            .delete('/consultations/' + this.publicConsultation.id + '/terms')
                            .then(() => {
                                let termsId = this.currentPublicConsultationTerms.map(term => term.id);
                                axios
                                    .post('/consultations/' + this.publicConsultation.id + '/terms', {
                                        terms_id: termsId
                                    })
                                    .then(() => {
                                        this.oldPublicConsultationTerms = this.currentPublicConsultationTerms;
                                        resolve(true);
                                    });
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.terminos.titulo'),
                                    detail: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.terminos.cuerpo')
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
                            .post('/consultations/' + this.publicConsultation.id + '/image', newImagenFormData)
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
                                    title: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.imagen_actualizada.titulo'),
                                    detail: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.imagen_actualizada.cuerpo')
                                });
                            });
                    } else if(this.deleteImagen) {
                        axios
                            .delete('/consultations/' + this.publicConsultation.id + '/image')
                            .then(() => {
                                this.deleteImagen = false;
                                resolve(true);
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.imagen_eliminada.titulo'),
                                    detail: this.$t('administrador.componentes.crear_consulta.mensajes.fallido.modificado.imagen_eliminada.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            generatePublicConsultationFormData() {
                let publicConsultationFormData = new FormData();

                if(this.publicConsultation.titulo) publicConsultationFormData.append('titulo', this.publicConsultation.titulo);
                if(this.publicConsultation.autor) publicConsultationFormData.append('autor', this.publicConsultation.autor);
                publicConsultationFormData.append('estado', this.publicConsultation.estado);
                if(this.publicConsultation.detalle) publicConsultationFormData.append('detalle', this.publicConsultation.detalle);
                if(this.publicConsultation.resumen) publicConsultationFormData.append('resumen', this.publicConsultation.resumen);
                if(this.publicConsultation.fecha_inicio) publicConsultationFormData.append('fecha_inicio', this.publicConsultation.fecha_inicio);
                if(this.publicConsultation.fecha_termino) publicConsultationFormData.append('fecha_termino', this.publicConsultation.fecha_termino);
                if(this.publicConsultation.icono) publicConsultationFormData.append('icono', this.publicConsultation.icono);
                if(this.publicConsultation.video_code) publicConsultationFormData.append('video_code', this.publicConsultation.video_code);
                if(this.publicConsultation.video_source) publicConsultationFormData.append('video_source', this.publicConsultation.video_source);

                this.currentPublicConsultationTerms.forEach(term => {
                    publicConsultationFormData.append('terms_id[]', term.id);
                });

                if(this.$refs.imagenDropzone && this.$refs.imagenDropzone.getAcceptedFiles().length > 0) {
                    this.publicConsultation.imagen = this.$refs.imagenDropzone.getAcceptedFiles()[0];
                    publicConsultationFormData.append('imagen', this.publicConsultation.imagen);
                }

                return publicConsultationFormData;
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
            imagenDropzoneFileAdded(file) {
                if(this.publicConsultation.id && !file.manuallyAdded) {
                    this.newImagen = file;
                }
            },
            imagenDropzoneRemovedFile(file) {
                if(this.publicConsultation.id && file.manuallyAdded) {
                    this.deleteImagen = true;
                } else {
                    this.newImagen = null;
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
            'publicConsultation.video_source': function (value) {
                if(!this.loadPublicConsultation && !value) {
                    this.publicConsultation.video_code = null;
                }
            }
        }
    }
</script>