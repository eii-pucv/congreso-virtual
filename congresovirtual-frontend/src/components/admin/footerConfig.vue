<template>
    <div style="min-height: 720px;">
        <div class="container container-fluid mt-25 mb-10" :style="mode==='dark'?'background: #080035; color: #fff':''">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" v-if="loader">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.footer.titulo') }}</h3>
                                <hr class="my-4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="filtro">  
                            </div>
                        </div>
                        <div class="alert alert-success mt-20" v-if="pagesSelected">
                            <p class="text-center">
                                {{ $t('administrador.componentes.footer.click') }}
                            </p>  
                        </div>
                        <tree :data="treeData" :options="treeOptions" :filter="filtro" ref="arbol">
                            <div slot-scope="{ node }" class="node-container">
                                <span class="tree-text">
                                    <template>
                                        <i :class="node.data.icon"></i>
                                        {{ node.text }}
                                        <div class="node-controls">
                                            <a v-if="node.data.editable" @click.stop="editNode(node)"><span>{{ $t('administrador.componentes.footer.editar') }} </span></a>
                                            <a v-if="node.data.eliminable" @click.stop="removeNode(node)"><span> {{ $t('administrador.componentes.footer.eliminar') }} </span></a>
                                            <a v-if="node.data.heredable" @click.stop="addChildNode(node)"> {{ $t('administrador.componentes.footer.anadir_hijo') }} </a>
                                            <a v-if="node.data.pages && showPagesOption" @click.stop="addPages(node)"><span> {{ $t('administrador.componentes.footer.anadir_paginas') }}</span></a>
                                        </div>
                                    </template>
                                </span>
                            </div>
                        </tree>
                        <div class="button-list text-center">
                            <button class="btn btn-sm btn-success" @click="saveChanges()">{{ $t('administrador.componentes.footer.guardar') }}</button>
                            <button class="btn btn-sm btn-primary" @click="showPages()">{{ $t('administrador.componentes.footer.anadir_paginas') }}</button>
                        </div>
                    </section>
                </div>
            </div>
            <b-modal id="modal-editar" 
                    ref="myModal"  
                    footer-bg-variant="primary"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h4 class="hk-sec-title text-white my-3">{{ $t('administrador.componentes.footer.editar_categoria') }}</h4>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="razon">{{ $t('administrador.componentes.footer.nombre') }}</label>
                        <input type="text" class="form-control" v-model="editcategory.nombre">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="descripcion">URL</label>
                        <input type="text" class="form-control" v-model="editcategory.url">
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <b-button class="btn btn-sm bg-green votable" @click="guardarEdicion()" block><font-awesome-icon icon="envelope"/><span class="btn-text"> {{ $t('guardar') }}</span></b-button>
                    <b-button class="btn btn-sm bg-red votable mb-2" @click="$bvModal.hide('modal-editar'),cleanModal()" block><font-awesome-icon icon="window-close"/><span class="btn-text"> {{ $t('cancelar') }}</span></b-button>
                </template>
            </b-modal>
            <b-modal id="modal-paginas" 
                    ref="myModal"  
                    footer-bg-variant="primary"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h4 class="hk-sec-title text-white my-3">Seleccione las páginas</h4>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="paginas">Páginas</label>
                        <multiselect v-model="pages" tag-placeholder="Añadir" placeholder="Buscar" label="title" :options="pagesOptions" track-by="id" :multiple="true" :taggable="true" @tag="addPage"></multiselect>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <b-button class="btn btn-sm bg-green votable" @click="pagesSelected = true, $bvModal.hide('modal-paginas')" block><font-awesome-icon icon="envelope"/><span class="btn-text"> Guardar</span></b-button>
                    <b-button class="btn btn-sm bg-red votable mb-2" @click="$bvModal.hide('modal-paginas'),cleanModalPaginas()" block><font-awesome-icon icon="window-close"/><span class="btn-text"> Cancelar</span></b-button>
                </template>
            </b-modal>
        </div>
    </div>
</template>

<script>

import axios from "../../backend/axios"
import LiquorTree from 'liquor-tree'
import { BModal } from 'bootstrap-vue';
import Multiselect from 'vue-multiselect';
import { bus } from "../../main";

export default {
    name: "footerConfig",
    props: {

    },
    components: {
        'tree': LiquorTree,
        'b-modal': BModal,
        Multiselect
    },
    data() {
        return {
            mode: String,
            settingValue: [],
            categorias: [],
            filtro: "",
            config: {},
            treeData: [],
            treeOptions: {
                propertyNames: {
                    text: 'nombre',
                    children: 'categorias'
                },
                minFetchDelay: 1000,
                fetchData: (node) => {
                    return Promise.resolve(data[node.id - 1])
                },
            },
            selected: {},
            events: [],
            loader: false,
            editcategory: {
                nombre: "",
                url: ""
            },
            showPagesOption: false,
            pagesSelected: false,
            pagesOptions: [],
            pages: []
        }
    },

    computed: {
        eventsList() {
            return this.events.concat().reverse()
        },
    },

    methods: {

        async saveChanges() {

            let newConfig = {
                settings: []
            }

            newConfig.settings.push({
                key: "site_footer",
                label: "Pié de página",
                value: JSON.stringify(this.settingValue)
            })

            try {
                const res = await axios.put('/settings',newConfig)
                this.$toastr('success','','Menu actualizado')
            } catch (error) {
                console.log(error)
                this.$toastr('error','','Algo salió mal')
            }
        },

        getTreeData() {

            this.categorias = this.settingValue.categorias
            let tree = []

            this.categorias.forEach(categoria => {

                let treeFormat = {
                    nombre: categoria.text,
                    id: categoria.id,
                    data: {icon: 'fa fa-folder-open', editable: true, eliminable: true, heredable: true, url: categoria.url, padreDePadres: false, pages: true },
                    state: { expanded: false },
                    categorias: this.getSubCategorias(categoria),
                }

                tree.push(treeFormat)
                  
            })

            this.loader = true

            return tree
        },

        getSubCategorias(categoria) {

            let subcategorias = categoria.subcategorias
            let treeSubcategories = []

            subcategorias.forEach(subcategoria => {

                let treeSubcategoria = {
                    nombre: subcategoria.text,
                    id: subcategoria.id,
                    state: { expanded: false },
                    data: { icon: 'fa fa-folder', editable: true, eliminable: true, heredable: false, parent_id: categoria.parent_id, url: subcategoria.url, padreDePadres: false, pages: false }
                }

                treeSubcategories.push(treeSubcategoria)
            })

            return treeSubcategories
        },

        editNode(node, e) {
            this.selected = node
            this.editcategory.nombre = node.text
            this.editcategory.url = node.data.url
            this.$bvModal.show('modal-editar')
        },

        guardarEdicion() {
            this.selected.text = this.editcategory.nombre
            this.selected.data.url = this.editcategory.url

            this.settingValue.categorias.forEach( categoria => {

                if (categoria.id == this.selected.id) {
                    categoria.text = this.editcategory.nombre
                    categoria.url = this.editcategory.url
                }

                categoria.subcategorias.forEach( subcategoria => {

                    if (subcategoria.id == this.selected.id) {
                        subcategoria.text = this.editcategory.nombre
                        subcategoria.url = this.editcategory.url
                    }
                })
            })

            this.$bvModal.hide('modal-editar')

            // console.log(JSON.stringify(this.settingValue))
        },

        removeNode(node) {
            
            if (confirm('Estás seguro?')) {

                let indexCategories = 0
                let indexSubcategories = 0


                this.settingValue.categorias.forEach( categoria => {

                    if (categoria.id == node.id) {
                        this.settingValue.categorias.splice(indexCategories,1)
                    }
                    else {
                        indexCategories++
                    }

                    categoria.subcategorias.forEach( subcategoria => {

                        if (subcategoria.id == node.id) {
                            categoria.subcategorias.splice(indexSubcategories,1)
                        }
                        else {
                            indexSubcategories++
                        }
                    })

                    indexSubcategories = 0
                })

                node.remove()

                // console.log(JSON.stringify(this.settingValue));
            }
        },

        addChildNode(node) {
            if (node.enabled()) {

                if (node.data.padreDePadres) {

                    node.append({ 
                        text: 'Nueva categoria',
                        children: [],
                        id: this.settingValue.lastCategoriesId + 1,
                        state: { expanded: false },
                        data: { icon: 'fa fa-folder', editable: true, eliminable: true, heredable: true, parent_id: node.id, url: '#', padreDePadres: false, pages: true }
                    })

                } else {

                    node.append({ 
                        text: 'Nueva categoria',
                        children: [],
                        id: this.settingValue.lastCategoriesId + 1,
                        state: { expanded: false },
                        data: { icon: 'fa fa-folder', editable: true, eliminable: true, heredable: false, parent_id: node.id, url: '#', padreDePadres: false, pages: false }
                    })
                }
            }

            if (node.data.padreDePadres) {

                this.settingValue.categorias.push({
                    id: this.settingValue.lastCategoriesId + 1,
                    text: 'Nueva categoria',
                    url: '#',
                    subcategorias: []
                })

                this.settingValue.lastCategoriesId++
            }
            else {

                this.settingValue.categorias.forEach( categoria => {

                    if (categoria.id == node.id) {

                        categoria.subcategorias.push({
                            id: this.settingValue.lastCategoriesId + 1,
                            text: 'Nueva categoria',
                            parent_id: categoria.id,
                            url: '#'
                        })

                        this.settingValue.lastCategoriesId++
                    }
                })
            }

            // console.log(JSON.stringify(this.settingValue));
        },

        addPages(node) {

            try {

                this.settingValue.categorias.forEach( categoria => {

                    if (categoria.id == node.id) {

                        this.pages.forEach( pagina => {

                            categoria.subcategorias.push({
                                id: this.settingValue.lastCategoriesId + 1,
                                text: pagina.title,
                                url: '/page/' + pagina.slug
                            })

                            node.append({ 
                                text: pagina.title,
                                children: [],
                                id: this.settingValue.lastCategoriesId + 1,
                                state: { expanded: false },
                                data: { icon: 'fa fa-folder', editable: true, eliminable: true, heredable: false, url: '/page/' + pagina.slug, padreDePadres: false, pages: false }
                            })

                            this.settingValue.lastCategoriesId++
                        })
                    }
                })

                this.pagesSelected = false
                this.$toastr('success','Las páginas seleccionadas han sido agregadas','Páginas añadidas')
                
            } catch (error) {
                this.$toastr('error','No se ha podido agregar las páginas','ERROR')
            }
        },

        async showPages(node) {

            this.showPagesOption = true

            if (!this.pagesOptions.length) {

                try {
                    const res = await axios.get('/pages?order_by=id')
                    this.pagesOptions = res.data
                    this.$toastr('success','','Páginas cargadas')
                } catch (error) {
                    console.log(error)
                    this.$toastr('error','No se ha podido obtener las páginas','Error')
                }
            }

            this.$bvModal.show('modal-paginas')
        },

        addPage(newPage) {

            const page = {
                value: newPage,
                id: this.generatedRandom()
            }

            this.pages.push(page);
            this.pagesOptions.push(page);
        },

        cleanModal() {

            this.editcategory.nombre = "",
            this.editcategory.url =  ""
        },

        initEventViewer(event) {
            const events = this.events
						debugger;
            return function (node, newNode) {
              let nodeText = '-'
              let targetNode = newNode && newNode.text ? newNode : node

              if (targetNode && targetNode.text) {
                nodeText = targetNode.text
              }

              events.push(
                Object.assign(
                  {},
                  event,
                  { time: new Date, nodeText, id: key++ }
                )
              )

            //   console.log(event, arguments)
            }
        }

    },

    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        try {
            const res = await axios.get('/settings?key=site_footer')
            this.settingValue = JSON.parse(res.data[0].value)
            this.treeData.push({
                nombre: res.data[0].label,
                data: { icon: 'fa fa-bars', editable: false, eliminable: false, heredable: true, padreDePadres: true },
                state: { expanded: true },
                categorias: this.getTreeData(),
            })

        } catch (error) {
            this.$toastr('error','No se ha podido obtener el footer','Error')
            console.log(error)
        }  
    
    },
    
}
</script>

<style scoped>
    
</style>