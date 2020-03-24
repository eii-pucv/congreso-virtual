<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <section class="hk-sec-wrapper">
                <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.configuracion_footer.titulo') }}</h3>
                <div class="row px-10 mb-20">
                    <div class="col-md-6 offset-md-3">
                        <input type="text" class="form-control" :placeholder="$t('administrador.componentes.configuracion_footer.buscar')" v-model="query">
                    </div>
                </div>
                <div class="row px-10 vld-parent">
                    <div v-if="loadFooterSettings" style="height: 550px;">
                        <Loading
                                :active.sync="loadFooterSettings"
                                :is-full-page="fullPage"
                                :height="128"
                        ></Loading>
                    </div>
                    <div v-if="!loadFooterSettings" class="col">
                        <tree :data="treeData" :options="treeOptions" :filter="query">
                            <div slot-scope="{ node }" class="node-container">
                                <span class="tree-text">
                                    <template>
                                        <i :class="node.data.icon"></i>
                                        {{ node.text }}
                                        <div class="node-controls">
                                            <a v-if="node.data.editable" @click.stop="editNode(node)" class="badge badge-primary text-white">
                                                {{ $t('administrador.componentes.configuracion_footer.editar') }}
                                            </a>
                                            <a v-if="node.data.removable" @click.stop="removeNode(node)" class="badge badge-danger text-white ml-5">
                                                {{ $t('administrador.componentes.configuracion_footer.eliminar') }}
                                            </a>
                                            <a v-if="node.data.heritable" @click.stop="addChildNode(node)" class="badge badge-secondary text-white ml-5">
                                                {{ $t('administrador.componentes.configuracion_footer.anadir_hijo') }}
                                            </a>
                                        </div>
                                    </template>
                                </span>
                            </div>
                        </tree>
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" @click="save">
                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <b-modal
                    id="edit-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.configuracion_footer.modal_edicion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="text">{{ $t('administrador.componentes.configuracion_footer.modal_edicion.nombre') }}</label>
                        <input id="text" type="text" class="form-control" v-model="nodeInEdit.text">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <label for="url">{{ $t('administrador.componentes.configuracion_footer.modal_edicion.enlace') }}</label>
                        <v-popover>
                            <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                            <template slot="popover">
                                <p>{{ $t('administrador.componentes.configuracion_footer.modal_edicion.popover_enlace') }}</p>
                            </template>
                        </v-popover>
                        <input id="url" type="text" class="form-control" v-model="nodeInEdit.url">
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                variant="primary"
                                size="sm"
                                @click.prevent="commitEditNode"
                        >
                            <i class="fas fa-save"></i> {{ $t('guardar') }}
                        </b-button>
                        <b-button
                                variant="danger"
                                size="sm"
                                @click.prevent="abortEditNode"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
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
                    <h6 class="text-white">{{ $t('administrador.componentes.configuracion_footer.modal_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.configuracion_footer.modal_eliminacion.pregunta') }}</p>
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
    import { BModal } from 'bootstrap-vue';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'FooterSettings',
        components: {
            'tree': LiquorTree,
            'b-modal': BModal,
            Loading
        },
        data() {
            return {
                footerSettings: Object,
                settingValue: [],
                query: null,
                treeData: [],
                treeOptions: {
                    propertyNames: {
                        text: 'text',
                        children: 'categories'
                    },
                    minFetchDelay: 1000,
                    fetchData: (node) => {
                        return Promise.resolve(data[node.id - 1]);
                    }
                },
                events: [],
                selectedNodeEdit: null,
                nodeInEdit: {
                    text: null,
                    url: null
                },
                selectedNodeRemove: null,
                loadFooterSettings: true,
                loadBtnSave: false,
                fullPage: false,
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getFooterSettings();
        },
        methods: {
            getFooterSettings() {
                axios
                    .get('/settings', {
                        params: {
                            key: 'site_footer'
                        }
                    })
                    .then(res => {
                        this.footerSettings = res.data[0];
                        this.settingValue = JSON.parse(this.footerSettings.value);
                        this.treeData.push({
                            text: res.data[0].label,
                            data: { icon: 'fas fa-bars', editable: false, removable: false, heritable: true, is_root: true },
                            state: { expanded: true },
                            categories: this.getParsedCategories()
                        });
                    })
                    .finally(() => {
                        this.loadFooterSettings = false;
                    });
            },
            getParsedCategories() {
                let categories = this.settingValue.categorias;
                let parsedCategories = [];

                categories.forEach(category => {
                    parsedCategories.push({
                        text: category.text,
                        id: category.id,
                        data: { icon: 'fas fa-folder-open', editable: true, removable: true, heritable: true, url: category.url, is_root: false },
                        state: { expanded: false },
                        categories: this.getParsedSubcategories(category),
                    });
                });
                return parsedCategories;
            },
            getParsedSubcategories(category) {
                let subcategories = category.subcategorias;
                let parsedSubcategories = [];

                subcategories.forEach(subcategory => {
                    parsedSubcategories.push({
                        text: subcategory.text,
                        id: subcategory.id,
                        state: { expanded: false },
                        data: { icon: 'fas fa-folder', editable: true, removable: true, heritable: false, parent_id: category.parent_id, url: subcategory.url, is_root: false }
                    });
                });
                return parsedSubcategories;
            },
            save() {
                this.loadBtnSave = true;
                axios
                    .put('/settings/' + this.footerSettings.id, {
                        value: JSON.stringify(this.settingValue)
                    })
                    .then(() => {
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.configuracion_footer.mensajes.exito.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.configuracion_footer.mensajes.exito.modificado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.configuracion_footer.mensajes.fallido.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.configuracion_footer.mensajes.fallido.modificado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadBtnSave = false;
                    });
            },
            editNode(node) {
                this.selectedNodeEdit = node;
                this.nodeInEdit = {
                    text: node.text,
                    url: node.data.url
                };
                this.$bvModal.show('edit-modal');
            },
            commitEditNode() {
                this.selectedNodeEdit.text = this.nodeInEdit.text;
                this.selectedNodeEdit.data.url = this.nodeInEdit.url;

                this.settingValue.categorias.forEach(category => {
                    if(category.id === this.selectedNodeEdit.id) {
                        category.text = this.nodeInEdit.text;
                        category.url = this.nodeInEdit.url;
                    }

                    category.subcategorias.forEach(subcategory => {
                        if(subcategory.id === this.selectedNodeEdit.id) {
                            subcategory.text = this.nodeInEdit.text;
                            subcategory.url = this.nodeInEdit.url;
                        }
                    });
                });
                this.$bvModal.hide('edit-modal');
            },
            abortEditNode() {
                this.selectedNodeEdit = null;
                this.nodeInEdit = {
                    text: null,
                    url: null
                };
                this.$bvModal.hide('edit-modal');
            },
            removeNode(node) {
                this.selectedNodeRemove = node;
                this.$bvModal.show('delete-modal');
            },
            commitRemoveNode() {
                this.settingValue.categorias.forEach((category, indexCategory) => {
                    if(category.id === this.selectedNodeRemove.id) {
                        this.settingValue.categorias.splice(indexCategory, 1);
                    }
                    category.subcategorias.forEach((subcategory, indexSubcategory) => {
                        if(subcategory.id === this.selectedNodeRemove.id) {
                            category.subcategorias.splice(indexSubcategory, 1);
                        }
                    });
                });
                this.selectedNodeRemove.remove();
                this.$bvModal.hide('delete-modal');
            },
            abortRemoveNode() {
                this.selectedNodeRemove = null;
                this.$bvModal.hide('delete-modal');
            },
            addChildNode(node) {
                if(node.enabled()) {
                    if(node.data.is_root) {
                        node.append({
                            text: this.$t('administrador.componentes.configuracion_footer.nueva_categoria'),
                            children: [],
                            id: this.settingValue.lastCategoriesId + 1,
                            state: { expanded: false },
                            data: { icon: 'fas fa-folder', editable: true, removable: true, heritable: true, parent_id: node.id, url: '#', is_root: false }
                        });
                    } else {
                        node.append({
                            text: this.$t('administrador.componentes.configuracion_footer.nueva_categoria'),
                            children: [],
                            id: this.settingValue.lastCategoriesId + 1,
                            state: { expanded: false },
                            data: { icon: 'fas fa-folder', editable: true, removable: true, heritable: false, parent_id: node.id, url: '#', is_root: false }
                        });
                    }
                }

                if(node.data.is_root) {
                    this.settingValue.categorias.push({
                        id: this.settingValue.lastCategoriesId + 1,
                        text: this.$t('administrador.componentes.configuracion_footer.nueva_categoria'),
                        url: '#',
                        subcategorias: []
                    });

                    this.settingValue.lastCategoriesId++;
                } else {
                    this.settingValue.categorias.forEach(category => {
                        if(category.id === node.id) {
                            category.subcategorias.push({
                                id: this.settingValue.lastCategoriesId + 1,
                                text: this.$t('administrador.componentes.configuracion_footer.nueva_categoria'),
                                parent_id: category.id,
                                url: '#'
                            });
                            this.settingValue.lastCategoriesId++;
                        }
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