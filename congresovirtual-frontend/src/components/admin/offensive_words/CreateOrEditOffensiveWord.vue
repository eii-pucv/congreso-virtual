<template>
    <div style="min-height: 720px;">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <h4 v-if="!offensiveword_id" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_palabra_ofensiva.titulo1') }}</h4>
                        <h4 v-else="" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_palabra_ofensiva.titulo2') }}</h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form v-on:submit.prevent="saveOffensiveWord">
                                    <div class="form-group">
                                        <label for="palabra_ofensiva" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_palabra_ofensiva.nombre') }}</label>
                                        <input type="text" class="form-control" v-model="palabra_ofensiva.word" id="palabra_ofensiva" :placeholder="$t('administrador.componentes.crear_palabra_ofensiva.palabra_ofensiva_placeholder')" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                        <div class="button-list">
                                            <button class="btn btn-primary vld-parent" type="submit">
                                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                                <Loading
                                                        :active.sync="loadBtnSave"
                                                        :is-full-page="fullPage"
                                                        :height="24"
                                                        :color="'#ffffff'"
                                                ></Loading>
                                            </button>
                                            <button @click="volver" class="btn btn-danger ml-10">
                                                <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                                            </button>
                                        </div>
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
import axios from '../../../backend/axios';

export default {
    name: 'CreateOrEditTaxonomy',
    props: {
        offensiveword_id: Number,
    },
    data() {
        return {
            mode: String,
            loadBtnSave: false,
            fullPage: false,
            palabra_ofensiva: {
                word: ''
            }
        }
    },
    methods: {
        saveOffensiveWord (){
            this.loadBtnSave = true;
            if(this.offensiveword_id){
                axios
                    .put("/offensive_words/" + this.offensiveword_id, this.palabra_ofensiva)
                    .then(res => {
                        this.$toastr('success', '', 'Palabra ofensiva actualizada')
                    })
                    .catch(() => {
                        this.$toastr('error', '', 'Palabra ofensiva no actualizada')
                    })
                    .finally(() => {
                        this.loadBtnSave = false;
                    });
            } else {
                axios
                    .post("/offensive_words", this.palabra_ofensiva)
                    .then(res => {
                        this.$toastr('success', '', 'Palabra ofensiva creada')
                    })
                    .catch(() => {
                        this.$toastr('error', '', 'Palabra ofensiva no creada')
                    })
                    .finally(() => {
                        this.loadBtnSave = false;
                    });
            }
        },

        volver () {
            location.replace(document.referrer);
        }
    },

    async mounted() {
        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        if (this.offensiveword_id) {
            try {
                const res = await axios.get('/offensive_words/' + this.offensiveword_id)
                this.palabra_ofensiva = res.data
            } catch (err) {
                this.$toastr('error', '', 'No se pudo cargar la palabra ofensiva')
            }
        }
    }
}
</script>