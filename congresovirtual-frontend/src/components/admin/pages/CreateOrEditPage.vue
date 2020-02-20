<template>
    <div class="container mt-20">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <h4 v-if="!page_id" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_pagina.titulo1') }}</h4>
                    <h4 v-else="" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_pagina.titulo2') }}</h4>
                    <div class="row mt-20">
                        <div class="col-xl-12">
                            <form @submit.prevent="saveMicrosite">
                                <div class="form-row div-align">
                                    <div class="col-md-5 mb-10">
                                        <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.nombre') }}</label>
                                        <input type="text" class="form-control" v-model="page.title" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                    <div class="col-md-5 mb-10">
                                        <label for="slug" :style="mode==='dark'?'color: #fff':''">Slug</label>
                                        <input type="text" class="form-control" v-model="this.slugFormat" readonly :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-5 mb-10">
                                        <label for="terminos" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.categorias') }}</label>
                                        <multiselect v-model="categories" tag-placeholder="Añadir" placeholder="Buscar categoria" label="value" track-by="id" :options="categories_options" :multiple="true" :taggable="true" @tag="addCategory"></multiselect>
                                    </div>
                                    <div class="col-md-5 mb-10">
                                        <label for="terminos" :style="mode==='dark'?'color: #fff':''">Tags</label>
                                        <multiselect v-model="tags" tag-placeholder="Añadir" placeholder="Buscar tag" label="value" track-by="id" :options="tags_options" :multiple="true" :taggable="true" @tag="addTag"></multiselect>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="contetn" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.contenido') }}</label>
                                        <tinymce id="edit" :tag="'textarea'" :config="config" v-model="page.content" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></tinymce>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <div class="custom-control custom-checkbox checkbox-primary float-left">
                                            <input type="checkbox" class="custom-control-input" id="customCheck3" v-model="page.is_public" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                            <label class="custom-control-label" for="customCheck3" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_pagina.publica') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="button-list text-center">
                                    <button type="submit" class="btn btn-primary">{{ $t('guardar') }}</button>
                                    <button class="btn btn-danger" @click="volver()">{{ $t('volver') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>

import axios from '../../../backend/axios';
import Multiselect from 'vue-multiselect';

export default {
    name: 'CreateOrEditPage',
    props: {
        page_id: Number
    },
    components: {
        Multiselect
    },
    data() {
        return {
            mode: String,
            page: {
                title: "",
                slug: "",
                content: "",
                is_public: false,
                terms_id: [],
            },
            terms_id: [],
            terminos: [],
            categories: [],
            tags: [],
            categories_options: [],
            tags_options: [],
            config: {
                events: {
                    initialized: function () {
                        console.log('initialized')
                    }
                }
            },
            actual_categories: [],
            actual_tags: []   
        }
    },

    computed: {

        slugFormat () { 
            return this.page.title.split(' ').join('-').toLowerCase()
        }

    },

    methods: {


        async saveMicrosite () {

            let terms = this.categories.concat(this.tags)

            terms.forEach(term => {
                this.terms_id.push(term.id)
            });

            this.page.slug = this.slugFormat

            if (this.page_id) {

                if ((JSON.stringify(this.categories) !== JSON.stringify(this.actual_categories)) || (JSON.stringify(this.tags) !== JSON.stringify(this.actual_tags))) {

                    try {
                        const res = await axios.delete('/pages/' + this.page_id + '/terms')
                        console.log("términos eliminados")
                    } catch (error) {
                        console.log(error)
                    }

                    try {
                        const res = await axios.post('/pages/' + this.page_id + '/terms', {
                            terms_id: this.terms_id
                        })
                        console.log("términos actualizados")
                    } catch (error) {
                        console.log(error)
                    }
                }

                try {

                    let edit_page = {
                        title: this.page.title,
                        slug: this.page.slug,
                        content: this.page.content,
                        is_public: this.page.is_public
                    }

                    const res = await axios.put('/pages/' + this.page_id, edit_page)
                    this.$toastr('success','','Página actualizada')
                    
                } catch (error) {
                    console.log(error)
                    this.$toastr('error','No se ha podido actualizar la página','Página no actualizada')
                }
                


            }
            else {

                this.page.terms_id = this.terms_id

                try {
                    const res = await axios.post('/pages',this.page)
                    this.$toastr('success','','Página creada')
                } catch (error) {
                    console.log(error)
                    this.$toastr('error','No se pudo crear la página','Algo salio mal')
                }
            }
        },
        volver () {
            window.history.back();
        },

        addCategory (newCategory) {

            const tag = {
                value: newCategory,
                id: this.generatedRandom()
            }

            this.categories.push(tag);
            this.categories_options.push(tag);
        },

        addTag(newTag) {

            const tag = {
                value: newTag,
                id: this.generatedRandom()
            }

            this.tags.push(tag);
            this.tags_options.push(tag);
        },
        
        generatedRandom(){
            return window.crypto.getRandomValues();
        }
    },
    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        try {
            const res = await axios.get('/terms', {
                params: {
                    return_all: 1
                }
            });
            let terminos = res.data.results;
            terminos.forEach(term => {
                if (term.taxonomies[0].id == 1) {
                    this.categories_options.push(term)
                }
                if (term.taxonomies[0].id == 2) {
                    this.tags_options.push(term)
                }
            })
        } catch (error) {
            console.log("Algo ha salido mal al obtener las categorias ");
        } 

        if (this.page_id) {

            try {
                const res = await axios.get('/pages/' + this.page_id)
                this.page = res.data
                this.$toastr('success','','Página cargada')
            } catch (error) {
                console.log(error)
                this.$toastr('error','','Página no cargada')
            }

            try {
                const res = await axios.get('/pages/' + this.page_id + '/terms')
                this.terminos = res.data;
                this.terminos.forEach(term => {
                    if (term.taxonomies[0].id == 1) {
                        this.categories.push(term)
                    }
                    if (term.taxonomies[0].id == 2) {
                        this.tags.push(term)
                    }

                    this.actual_categories = this.categories
                    this.actual_tags = this.tags
                })
            } catch (error) {
                console.log(error)
                console.log("error al obtener los términos")
            }
        }

    },
    
}
</script>

<style  scoped>

.div-align {
    align-items: center;
    justify-content: center;
}

</style>