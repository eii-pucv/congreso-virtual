<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!page_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_pagina.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_pagina.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadPage" style="height: 300px;">
                    <Loading
                            :active.sync="loadPage"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadPage">
                    <form @submit.prevent="savePage">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-5 mb-10">
                                <label for="title" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.titulo') }}</label>
                                <input
                                        id="title"
                                        v-model="page.title"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-md-5 mb-10">
                                <label for="slug" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.slug') }}</label>
                                <input
                                        id="slug"
                                        v-model="page.slug"
                                        @input="slugFormat"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="terms" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.tema_asociado.titulo') }}</label>
                                <multiselect
                                        id="terms"
                                        v-model="currentPageTerms"
                                        group-values="terms"
                                        group-label="value"
                                        track-by="id"
                                        label="value"
                                        :placeholder="$t('administrador.componentes.crear_pagina.tema_asociado.buscar')"
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
                                <label for="content" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.contenido') }}</label>
                                <editor
                                        id="content"
                                        v-model="page.content"
                                        :init="tinymceInitOptions"
                                />
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="is_public"
                                            v-model="page.is_public"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <label for="is_public" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.publica') }}</label>
                                    <v-popover>
                                        <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
                                        <template slot="popover">
                                            <p>{{ $t('administrador.componentes.crear_pagina.popover_publica') }}</p>
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
    import axios from '../../../backend/axios';
    import Loading from 'vue-loading-overlay';
    import Multiselect from 'vue-multiselect';
    import Editor from '@tinymce/tinymce-vue';

    export default {
        name: 'CreateOrEditPage',
        components: {
            Loading,
            Multiselect,
            'editor': Editor
        },
        props: {
            page_id: Number
        },
        data() {
            return {
                page: {
                    title: null,
                    slug: null,
                    content: null,
                    is_public: false,
                    terms_id: []
                },
                currentPageTerms: [],
                oldPageTerms: [],
                taxonomyTerms: [],
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
                loadPage: true,
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

            if(this.page_id) {
                this.getPage();
            } else {
                this.loadPage = false;
            }

            this.getTaxonomyTerms();
        },
        methods: {
            getPage() {
                axios
                    .get('/pages/' + this.page_id)
                    .then(res => {
                        this.page = res.data;
                        this.currentPageTerms = this.page.terms;
                        this.oldPageTerms = this.currentPageTerms;
                    })
                    .finally(() => {
                        this.loadPage = false;
                    })
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
            savePage() {
                this.loadBtnSave = true;
                if(this.page.id) {
                    if(this.page.content) {
                        axios
                            .put('/pages/' + this.page.id, this.page)
                            .then(() => {
                                this.updatePageTerms()
                                    .then(() => {
                                        this.$toastr(
                                            'success',
                                            this.$t('administrador.componentes.crear_pagina.mensajes.exito.modificado.generico.cuerpo'),
                                            this.$t('administrador.componentes.crear_pagina.mensajes.exito.modificado.generico.titulo')
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
                                    this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.generico.titulo')
                                );
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.falta_contenido.cuerpo'),
                            this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.falta_contenido.titulo')
                        );
                    }
                } else {
                    if(this.page.content) {
                        this.page.terms_id = this.currentPageTerms.map(term => term.id);
                        axios
                            .post('/pages', this.page)
                            .then(res => {
                                this.page = res.data.data;
                                this.oldPageTerms = this.currentPageTerms;
                                this.$toastr(
                                    'success',
                                    this.$t('administrador.componentes.crear_pagina.mensajes.exito.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_pagina.mensajes.exito.guardado.generico.titulo')
                                );
                            })
                            .catch(() => {
                                this.$toastr(
                                    'error',
                                    this.$t('administrador.componentes.crear_pagina.mensajes.fallido.guardado.generico.cuerpo'),
                                    this.$t('administrador.componentes.crear_pagina.mensajes.fallido.guardado.generico.titulo')
                                );
                            })
                            .finally(() => {
                                this.loadBtnSave = false;
                            });
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.crear_pagina.mensajes.fallido.guardado.falta_contenido.cuerpo'),
                            this.$t('administrador.componentes.crear_pagina.mensajes.fallido.guardado.falta_contenido.titulo')
                        );
                    }
                }
            },
            updatePageTerms() {
                return new Promise((resolve, reject) => {
                    if(JSON.stringify(this.currentPageTerms) !== JSON.stringify(this.oldPageTerms)) {
                        axios
                            .delete('/pages/' + this.page.id + '/terms')
                            .then(() => {
                                let termsId = this.currentPageTerms.map(term => term.id);
                                axios
                                    .post('/pages/' + this.page.id + '/terms', {
                                        terms_id: termsId
                                    })
                                    .then(() => {
                                        this.oldPageTerms = this.currentPageTerms;
                                        resolve(true);
                                    });
                            })
                            .catch(() => {
                                reject({
                                    title: this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.terminos.titulo'),
                                    detail: this.$t('administrador.componentes.crear_pagina.mensajes.fallido.modificado.terminos.cuerpo')
                                });
                            });
                    } else {
                        resolve(true);
                    }
                });
            },
            limitTextTaxonomyTermsMultiselect(count) {
                if (this.$i18n.locale === 'es') {
                    if (count === 1) {
                        return `y 1 tema más`;
                    } else {
                        return `y ${count} temas más`;
                    }
                } else if (count === 1) {
                    return `and 1 more topic`;
                } else {
                    return `and ${count} more topics`;
                }
            },
            slugFormat(event) {
                this.page.slug = event.target.value.replace(' ', '-').toLowerCase();
            },
            back() {
                location.replace(document.referrer);
            },
        }
    }
</script>