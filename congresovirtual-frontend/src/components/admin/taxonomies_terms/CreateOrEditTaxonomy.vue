<template>
    <div class="container mt-20" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!taxonomy_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_taxonomia.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_taxonomia.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadTaxonomy" style="height: 300px;">
                    <Loading
                            :active.sync="loadTaxonomy"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadTaxonomy">
                    <form @submit.prevent="saveTaxonomy">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="value" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_taxonomia.nombre') }}</label>
                                <input
                                        id="value"
                                        v-model="taxonomy.value"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
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
        name: 'CreateOrEditTaxonomy',
        components: {
            Loading
        },
        props: {
            taxonomy_id: Number
        },
        data() {
            return {
                taxonomy: {
                    value: null
                },
                loadTaxonomy: true,
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

            if(this.taxonomy_id) {
                this.getTaxonomy();
            } else {
                this.loadTaxonomy = false;
            }
        },
        methods: {
            getTaxonomy() {
                axios
                    .get('/taxonomies/' + this.taxonomy_id)
                    .then(res => {
                        this.taxonomy = res.data;
                    })
                    .finally(() => {
                        this.loadTaxonomy = false;
                    });
            },
            saveTaxonomy() {
                this.loadBtnSave = true;
                if(this.taxonomy.id) {
                    axios
                        .put('/taxonomies/' + this.taxonomy.id, this.taxonomy)
                        .then(() => {
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.exito.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.exito.modificado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.fallido.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.fallido.modificado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        });
                } else {
                    axios
                        .post('/taxonomies', this.taxonomy)
                        .then(res => {
                            this.taxonomy = res.data.data;
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_taxonomia.mensajes.fallido.guardado.generico.titulo')
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