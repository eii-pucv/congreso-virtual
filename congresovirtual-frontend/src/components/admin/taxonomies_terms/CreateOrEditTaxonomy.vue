<template>
    <div style="min-height: 720px;">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <h4 v-if="!taxonomy_id" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_taxonomia.titulo1') }}</h4>
                        <h4 v-else="" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_taxonomia.titulo2') }}</h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form v-on:submit.prevent="saveTaxonomie">
                                    <div class="form-group">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_taxonomia.nombre') }}</label>
                                        <input type="text" class="form-control" v-model="taxonomia.value" id="titulo" placeholder="Ingrese el nombre" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                        <div class="button-list">
                                            <a type="submit" @click="saveTaxonomie" class="btn text-white bg-green votable"><font-awesome-icon icon="envelope"/><span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('enviar') }}</span></a>
                                            <a @click="volver()" class="btn text-white bg-red ml-10 votable"><font-awesome-icon icon="window-close"/><span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('cancelar') }}</span></a>
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
        taxonomy_id: Number,
    },
    data() {
        return {
            mode: String,
            taxonomia: {
                value: ''
            }
        }
    },
    methods: {
        saveTaxonomie (){
                if(this.taxonomy_id){
                    axios
                        .put("/taxonomies/" + this.taxonomy_id, this.taxonomia)
                        .then(res => {
                            //console.log("datos de proyecto" + JSON.stringify(res.data));
                            console.log("TAXONOMIA ACTUALIZADO")
                            this.$toastr('success', '', 'Taxonomía actualizado')
                        })
                        .catch(() => {
                            console.log("FALLO")
                            this.$toastr('error', '', 'Taxonomía no actualizado')
                        });
                } else {
                    axios
                        .post("/taxonomies", this.taxonomia)
                        .then(res => {
                            //console.log("datos de proyecto" + JSON.stringify(res.data));
                            console.log("TAXONOMIA GUARDADO")
                            this.$toastr('success', '', 'Taxonomía creada')
                        })
                        .catch(() => {
                            console.log("FALLO")
                            this.$toastr('error', '', 'Taxonomía no creada')
                        });
                }
            },

        volver () {
            window.history.back();
        }
        
    },

    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        if (this.taxonomy_id) {
            console.log(this.taxonomy_id)
            try {
                const res = await axios.get('/taxonomies/' + this.taxonomy_id)
                this.taxonomia = res.data
                console.log(JSON.stringify(res.data))
            } catch (error) {
                console.log("Algo ha salido mal");
            }
        }

            try {
                const res = await axios.get('/terms');
                let terminos = res.data.results;
                /*terminos.forEach(term => {
                    if (term.taxonomies[0].id == 1) {
                        this.categoryOptions.push(term)
                    }
                    if (term.taxonomies[0].id == 2) {
                        this.tagsOptions.push(term)
                    }
                })*/
            } catch (error) {
                console.log("Algo ha salido mal al obtener las categorias ");
            }
    }
}
</script>