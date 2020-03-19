<template>
    <div class="container mt-20" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!article_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_articulo.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_articulo.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadArticle" style="height: 300px;">
                    <Loading
                            :active.sync="loadArticle"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadArticle">
                    <form @submit.prevent="saveArticle">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_articulo.titulo') }}</label>
                                <input
                                        id="titulo"
                                        v-model="article.titulo"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_articulo.detalle') }}</label>
                                <textarea
                                        id="detalle"
                                        v-model="article.detalle"
                                        type="text"
                                        class="form-control"
                                        rows="3"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
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
                            <button @click.prevent="back" class="btn btn-danger text-white ml-10">
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
    import axios from '../../../backend/axios';

    export default {
        name: 'CreateOrEditArticle',
        components: {
            Loading
        },
        props: {
            article_id: Number,
            project_id: Number
        },
        data() {
            return {
                article: {
                    titulo: null,
                    detalle: null,
                    project_id: null
                },
                loadArticle: true,
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

            if(this.article_id) {
                this.getArticle();
            } else {
                this.article.project_id = this.project_id;
                this.loadArticle = false;
            }
        },
        methods: {
            getArticle() {
                axios
                    .get('/articles/' + this.article_id)
                    .then(res => {
                        this.article = res.data;
                    })
                    .finally(() => {
                        this.loadArticle = false;
                    });
            },
            saveArticle() {
                this.loadBtnSave = true;
                if(this.article.id) {
                    axios
                        .put('/articles/' + this.article.id, this.article)
                        .then(() => {
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_articulo.mensajes.exito.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_articulo.mensajes.exito.modificado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_articulo.mensajes.fallido.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_articulo.mensajes.fallido.modificado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        });
                } else {
                    axios
                        .post('/articles', this.article)
                        .then(res => {
                            this.article = res.data.data;
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_articulo.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_articulo.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_articulo.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_articulo.mensajes.fallido.guardado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        });
                }
            },
            back() {
                this.$router.go(-1);
            }
        }
    }
</script>