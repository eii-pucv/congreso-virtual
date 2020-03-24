<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <section class="hk-sec-wrapper">
                <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.lista_terminos_taxonomia.titulo') }}</h3>
                <div class="row px-10 mb-20">
                    <div class="col-md-6 offset-md-3">
                        <input type="text" class="form-control" :placeholder="$t('administrador.componentes.lista_terminos_taxonomia.buscar')" v-model="query">
                    </div>
                </div>
                <div class="row px-10 vld-parent">
                    <div v-if="loadTaxonomy" style="height: 550px;">
                        <Loading
                                :active.sync="loadTaxonomy"
                                :is-full-page="fullPage"
                                :height="128"
                                :color="color"
                        ></Loading>
                    </div>
                    <div v-if="!loadTaxonomy" class="col">
                        <div v-if="loadAction">
                            <loading
                                    :active.sync="loadAction"
                                    :height="128"
                                    :color="color"
                            ></loading>
                        </div>
                        <tree :data="treeData" :options="treeOptions" :filter="query" ref="arbol">
                            <div slot-scope="{ node }" class="node-container">
                                <span class="tree-text">
                                    <template>
                                        <i :class="node.data.icon"></i>
                                        {{ node.text }}
                                        <div class="node-controls">
                                            <a v-if="node.data.editable" @click.stop="editNode(node)" class="badge badge-primary text-white">
                                                {{ $t('administrador.componentes.lista_terminos_taxonomia.editar') }}
                                            </a>
                                            <a v-if="node.data.removable" @click.stop="removeNode(node)" class="badge badge-danger text-white ml-5">
                                                {{ $t('administrador.componentes.lista_terminos_taxonomia.eliminar') }}
                                            </a>
                                            <a @click.stop="addChildNode(node)" class="badge badge-secondary text-white ml-5">
                                                {{ $t('administrador.componentes.lista_terminos_taxonomia.anadir_hijo') }}
                                            </a>
                                        </div>
                                    </template>
                                </span>
                            </div>
                        </tree>
                    </div>
                </div>
            </section>
            <b-modal
                    id="delete-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.lista_terminos_taxonomia.modal_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.lista_terminos_taxonomia.modal_eliminacion.pregunta') }}</p>
                        <div v-if="selectedNodeRemove && selectedNodeRemove.children.length > 0" class="alert alert-warning row ma-0 mt-10 pa-0">
                            <div class="col-sm-2 text-center align-self-center pa-10">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="col-sm-10 pa-10">
                                {{ $t('administrador.componentes.lista_terminos_taxonomia.modal_eliminacion.precaucion') }}
                            </div>
                        </div>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                variant="danger"
                                size="sm"
                                @click.prevent="commitRemoveNode"
                        >
                            <i class="fas fa-trash"></i> {{ $t('si') }}
                            <Loading
                                    :active.sync="loadModalBtn"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="'#ffffff'"
                            ></Loading>
                        </b-button>
                        <b-button
                                variant="secondary"
                                size="sm"
                                @click.prevent="abortRemoveNode"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import axios from '../../../backend/axios';
    import LiquorTree from 'liquor-tree';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'TaxonomyTermsList',
        components: {
            'tree': LiquorTree,
            Loading
        },
        props: {
            taxonomy_id: Number
        },
        data() {
            return {
                taxonomy: null,
                nodesAlreadyExist: [],
                treeData: [],
                query: null,
                treeOptions: {
                    propertyNames: {
                        text: 'value',
                        children: 'categories'
                    },
                    minFetchDelay: 1000,
                    fetchData: (node) => {
                        return Promise.resolve(data[node.id - 1]);
                    }
                },
                events: [],
                selectedNodeRemove: null,
                loadTaxonomy: true,
                loadModalBtn: false,
                loadAction: false,
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

            this.getTaxonomy();
        },
        methods: {
            getTaxonomy() {
                axios
                    .get('/taxonomies/' + this.taxonomy_id)
                    .then(res => {
                        this.taxonomy = res.data;
                        this.treeData.push({
                            value: this.taxonomy.value,
                            id: this.taxonomy.id,
                            data: { icon: 'fas fa-book', editable: false, removable: false, is_root: true },
                            state: { expanded: true },
                            categories: this.getParsedCategories()
                        });

                        setTimeout(() => {
                            this.$refs.arbol.$on('node:editing:stop', (node) => {
                                this.commitEditNode(node);
                            });
                        }, 1000);
                    })
                    .finally(() => {
                        this.loadTaxonomy = false;
                    });
            },
            getParsedCategories() {
                let categories = this.taxonomy.terms;
                let parsedCategories = [];
                categories.forEach(category => {
                    let parsedCategory = {
                        value: category.value,
                        id: category.id,
                        data: { icon: 'fas fa-folder-open', editable: true, removable: true, parent_id: category.parent_id, is_root: false },
                        state: { expanded: false },
                        categories: []
                    };

                    if(!this.checkAlreadyExists(category)) {
                        parsedCategory.categories = this.getParsedSubcategories(category);
                        this.nodesAlreadyExist.push(category);
                        if(!category.parent_id) {
                            parsedCategories.push(parsedCategory);
                        }
                    }
                });
                return parsedCategories;
            },
            getParsedSubcategories(categoryData) {
                let categories = this.taxonomy.terms;
                let parsedSubcategories = [];
                categories.forEach(category => {
                    if(category.parent_id === categoryData.id) {
                        let parsedSubcategory = {
                            value: category.value,
                            id: category.id,
                            state: { expanded: false },
                            data: { icon: 'fas fa-folder', editable: true, removable: true, parent_id: category.parent_id, is_root: false },
                            categories: []
                        };

                        if(!this.checkAlreadyExists(category)) {
                            parsedSubcategory.categories = this.getParsedSubcategories(category);
                            this.nodesAlreadyExist.push(category);
                            parsedSubcategories.push(parsedSubcategory);
                        }
                    }
                });
                return parsedSubcategories;
            },
            checkAlreadyExists(searchedNode) {
                return !!this.nodesAlreadyExist.find(node => node.id === searchedNode.id);
            },
            editNode(node) {
                node.startEditing();
            },
            commitEditNode(node) {
                this.loadAction = true;
                axios
                    .put('/terms/' + node.id, {
                        value: node.data.text
                    })
                    .then(() => {
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.modificado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.modificado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadAction = false;
                    });
            },
            removeNode(node) {
                this.selectedNodeRemove = node;
                this.$bvModal.show('delete-modal');
            },
            commitRemoveNode() {
                this.loadModalBtn = true;
                axios
                    .delete('/terms/' + this.selectedNodeRemove.id, {
                        params: {
                            force_delete: 1
                        }
                    })
                    .then(() => {
                        this.selectedNodeRemove.remove();
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.eliminado.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.eliminado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.eliminado.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.eliminado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadModalBtn = false;
                        this.$bvModal.hide('delete-modal');
                    });
            },
            abortRemoveNode() {
                this.selectedNodeRemove = null;
                this.$bvModal.hide('delete-modal');
            },
            addChildNode(node) {
                if(node.data.is_root) {
                    this.loadAction = true;
                    axios
                        .post('/terms', {
                            value: this.$t('administrador.componentes.lista_terminos_taxonomia.nuevo_termino'),
                            parent_id: null,
                            taxonomies_id: [this.taxonomy_id]
                        })
                        .then(res => {
                            this.newNode = res.data;
                            if(node.enabled()) {
                                node.append({
                                    text: this.$t('administrador.componentes.lista_terminos_taxonomia.nuevo_termino'),
                                    children: [],
                                    id: this.newNode.data.id,
                                    state: { expanded: false },
                                    data: { icon: 'fas fa-folder', editable: true, removable: true, parent_id: null, is_root: false }
                                });
                            }
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.guardado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadAction = false;
                        });
                } else {
                    this.loadAction = true;
                    axios
                        .post('/terms', {
                            value: this.$t('administrador.componentes.lista_terminos_taxonomia.nuevo_termino'),
                            parent_id: node.id,
                            taxonomies_id: [this.taxonomy_id]
                        })
                        .then(res => {
                            this.newNode = res.data;
                            if(node.enabled()) {
                                node.append({
                                    text: this.$t('administrador.componentes.lista_terminos_taxonomia.nuevo_termino'),
                                    children: [],
                                    id: this.newNode.data.id,
                                    state: { expanded: false },
                                    data: { icon: 'fas fa-folder', editable: true, removable: true, parent_id: node.id, is_root: false }
                                });
                            }
                            this.$toastr(
                                'success',
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.exito.guardado.generico.titulo')
                            );
                        })
                        .catch(() => {
                            this.$toastr(
                                'error',
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.guardado.generico.cuerpo'),
                                this.$t('administrador.componentes.lista_terminos_taxonomia.mensajes.fallido.guardado.generico.titulo')
                            );
                        })
                        .finally(() => {
                            this.loadAction = false;
                        });
                }
            }
        },
        computed: {
            eventsList() {
                return this.events.concat().reverse();
            }
        }
    }
</script>