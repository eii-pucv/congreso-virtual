<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.terminos.titulo') }}</h3>
                        <div class="row px-10 mb-20">
                            <div class="col-md-6 offset-md-3">
                                <input type="text" class="form-control" :placeholder="$t('administrador.componentes.terminos.buscar')" v-model="query">
                            </div>
                        </div>
                        <div class="row px-10 vld-parent">
                            <div v-if="loadTaxonomy" class="" style="height: 550px;">
                                <loading
                                        :active.sync="loadTaxonomy"
                                        :is-full-page="fullPage"
                                        :height="128"
                                        :color="color"
                                ></loading>
                            </div>
                            <div v-if="!loadTaxonomy">
                                <div v-if="loadAction">
                                    <loading
                                            :active.sync="loadAction"
                                            :height="128"
                                            :color="color"
                                    ></loading>
                                </div>
                                <tree :data="treeData" :options="treeOptions" :filter="query" ref="arbol" v-model="selected">
                                    <div slot-scope="{ node }" class="node-container">
                            <span class="tree-text">
                                <template>
                                    <i :class="node.data.icon"></i>
                                    {{ node.text }}
                                    <div class="node-controls">
                                        <a v-if="node.data.editable" @click.stop="editNode(node)"><span>{{ $t('administrador.componentes.terminos.editar') }} </span></a>
                                        <a v-if="node.data.eliminable" @click.stop="removeNode(node)"><span> {{ $t('administrador.componentes.terminos.eliminar') }} </span></a>
                                        <a @click.stop="addChildNode(node)"> {{ $t('administrador.componentes.terminos.anadir') }}</a>
                                    </div>
                                </template>
                            </span>
                                    </div>
                                </tree>
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
    import LiquorTree from 'liquor-tree';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

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
                mode: String,
                taxonomy: null,
                terms: [],
                events: [],
                nodosExistentes: [],
                treeData: [],
                query: '',
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
                newTerm: {},
                loadTaxonomy: false,
                loadAction: false,
                fullPage: false,
                color:"#000000"
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
            }
        },
        methods: {
            getTaxonomy() {
                this.loadTaxonomy = true;
                axios
                    .get('/taxonomies/' + this.taxonomy_id)
                    .then(res => {
                        this.taxonomy = res.data;
                        this.treeData.push({
                            nombre: this.taxonomy.value,
                            id: this.taxonomy.id,
                            data: {
                                icon: 'fa fa-book',
                                editable: false,
                                eliminable: false,
                                padreDePadres: true
                            },
                            state: { expanded: true },
                            categorias: this.getTreeData()
                        });

                        setTimeout(() => {
                            this.$refs.arbol.$on('node:editing:start', (node) => {
                                console.log('Start editing: ' + node.text);
                            });

                            this.$refs.arbol.$on('node:editing:stop', (node, isTextChanged) => {
                                this.sendEditNode(node);
                            });
                        }, 1000);
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se han podido cargar los datos de la taxonomía', 'Error al cargar');
                    })
                    .finally(() => {
                        this.loadTaxonomy = false;
                    });
            },
            getTreeData() {
                this.terms = this.taxonomy.terms;
                let tree = [];

                this.terms.forEach(term => {
                    let treeFormat = {
                        nombre: term.value,
                        id: term.id,
                        data: {
                            icon: 'fa fa-folder-open',
                            editable: true,
                            eliminable: false,
                            parent_id: term.parent_id,
                            padreDePadres: false
                        },
                        state: { expanded: false },
                        categorias: []
                    };

                    if(!this.verificarExistencia(term)) {
                        treeFormat.categorias = this.getSubCategorias(term);
                        this.nodosExistentes.push(term);
                        if(!term.parent_id) {
                            tree.push(treeFormat);
                        }
                    }
                });
                return tree;
            },
            getSubCategorias(termino) {
                let children = [];
                this.terms.forEach(term => {
                    if(term.parent_id === termino.id) {
                        let sonFormat = {
                            nombre: term.value,
                            id: term.id,
                            state: { expanded: false },
                            data: {
                                icon: 'fa fa-folder',
                                editable: true,
                                eliminable: true,
                                parent_id: termino.parent_id,
                                padreDePadres: false
                            },
                            categorias: []
                        };

                        if(!this.verificarExistencia(term)) {
                            sonFormat.categorias = this.getSubCategorias(term);
                            this.nodosExistentes.push(term);
                            children.push(sonFormat);
                        }
                    }
                });
                return children;
            },
            verificarExistencia(currentNode) {
                this.nodosExistentes.forEach(node => {
                    if(node.id === currentNode.id) {
                        return true;
                    }
                });
                return false;
            },
            editNode(node) {
                node.startEditing();
            },
            sendEditNode(node) {
                this.loadAction = true;
                axios
                    .put('/terms/' + node.id, {
                        value: node.data.text
                    })
                    .then(() => {
                        this.$toastr('success','','Término actualizado');
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se pudo actualizar el término',' Actualización fallida');
                    })
                    .finally(() => {
                        this.loadAction = false;
                    });
            },
            removeNode(node) {
                if(confirm('¿Estás seguro?')) {
                    this.loadAction = true;
                    axios
                        .delete('/terms/' + node.id)
                        .then(() => {
                            this.$toastr('success', '', 'Término eliminado');
                            node.remove();
                        })
                        .catch(() => {
                            this.$toastr('error', '', 'Término no eliminado');
                        })
                        .finally(() => {
                            this.loadAction = false;
                        });
                }
            },
            addChildNode(node) {
                if(node.data.padreDePadres) {
                    this.loadAction = true;
                    axios
                        .post('/terms', {
                            value: 'Nuevo término',
                            parent_id: null,
                            taxonomies_id: [this.taxonomy_id]
                        })
                        .then(res => {
                            this.newNode = res.data;
                            if(node.enabled()) {
                                node.append({
                                    text: 'Nuevo término',
                                    children: [],
                                    id: this.newNode.data.id,
                                    state: { expanded: false },
                                    data: {
                                        icon: 'fa fa-folder',
                                        editable: true,
                                        eliminable: true,
                                        parent_id: null,
                                        padreDePadres: false
                                    }
                                });
                            }
                            this.$toastr('success', '', 'Término creado');
                        })
                        .catch(() => {
                            this.$toastr('error', '', 'Término no creado');
                        })
                        .finally(() => {
                            this.loadAction = false;
                        });
                } else {
                    this.loadAction = true;
                    axios
                        .post('/terms', {
                            value: 'Nuevo término',
                            parent_id: node.id,
                            taxonomies_id: [this.taxonomy_id]
                        })
                        .then(res => {
                            this.newNode = res.data;
                            if(node.enabled()) {
                                node.append({
                                    text: 'Nuevo término',
                                    children: [],
                                    id: this.newNode.data.id,
                                    state: { expanded: false },
                                    data: {
                                        icon: 'fa fa-folder',
                                        editable: true,
                                        eliminable: true,
                                        parent_id: node.id,
                                        padreDePadres: false
                                    }
                                });
                            }
                            this.$toastr('success', '', 'Término creado');
                        })
                        .catch(() => {
                            this.$toastr('error', '', 'Término no creado');
                        })
                        .finally(() => {
                            this.loadAction = false;
                        });
                }
            },
            initEventViewer(event) {
                const events = this.events;
                debugger;
                return function (node, newNode) {
                    let nodeText = '-';
                    let targetNode = newNode && newNode.text ? newNode : node;

                    if (targetNode && targetNode.text) {
                        nodeText = targetNode.text;
                    }

                    events.push(
                        Object.assign(
                            {},
                            event,
                            {
                                time: new Date,
                                nodeText,
                                id: key++
                            }
                        )
                    );
                }
            }
        },
        computed: {
            eventsList() {
                return this.events.concat().reverse()
            }
        }
    }
</script>

<style scoped>
    .hk-sec-wrapper {
        background: #fff;
        padding: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: .25rem;
        margin-bottom: 14px;
    }
    .btn, .btn-icon{
        margin-top: 0px;
        margin-right: 2px;
    }
    .btn.btn-icon {
        height: 30px;
        width: 30px;
        padding: 0;
    }
    .btn-label {
        position: relative;
        left: -12px;
        display: inline-block;
        padding: 6px 12px;
        background: rgba(0,0,0,0.15);
        border-radius: 3px 0 0 3px;
    }

    @media (max-width: 1400px) {
        .hk-sec-wrapper {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }
    }
</style>