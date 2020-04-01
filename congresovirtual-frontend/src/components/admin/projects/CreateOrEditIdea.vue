<template>
    <div class="container mt-20" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!idea_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_idea.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_idea.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadIdea" style="height: 300px;">
                    <Loading
                            :active.sync="loadIdea"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadIdea">
                    <form @submit.prevent="saveIdea">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_idea.titulo') }}</label>
                                <input
                                        id="titulo"
                                        v-model="idea.titulo"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_idea.detalle') }}</label>
                                <textarea
                                        id="detalle"
                                        v-model="idea.detalle"
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
                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click.prevent="back" class="btn btn-danger ml-10">
                                <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
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
        name: 'CreateOrEditIdea',
        components: {
            Loading
        },
        props: {
            idea_id: Number,
            project_id: Number
        },
        data() {
            return {
                idea: {
                    titulo: null,
                    detalle: null,
                    project_id: null
                },
                loadIdea: true,
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

            if(this.idea_id) {
                this.getIdea();
            } else {
                this.idea.project_id = this.project_id;
                this.loadIdea = false;
            }
        },
        methods: {
            getIdea() {
                axios
                    .get('/ideas/' + this.idea_id)
                    .then(res => {
                        this.idea = res.data;
                    })
                    .finally(() => {
                        this.loadIdea = false;
                    });
            },
            saveIdea() {
                this.loadBtnSave = true;
                if(this.idea.id) {
                    axios
                        .put('/ideas/' + this.idea.id, this.idea)
                        .then(() => {
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_idea.mensajes.exito.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_idea.mensajes.exito.modificado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_idea.mensajes.fallido.modificado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_idea.mensajes.fallido.modificado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadBtnSave = false;
                        });
                } else {
                    axios
                        .post('/ideas', this.idea)
                        .then(res => {
                            this.idea = res.data.data;
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.crear_idea.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_idea.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.crear_idea.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.crear_idea.mensajes.fallido.guardado.generico.titulo')
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