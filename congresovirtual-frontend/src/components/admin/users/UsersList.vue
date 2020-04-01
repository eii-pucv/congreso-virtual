<template>
    <div class="container my-30" style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <section class="hk-sec-wrapper">
            <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.navbar.usuarios.lista_usuarios') }}</h3>
            <h4 v-if="isTrashList" class="text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.eliminar.eliminados_temporalmente.eliminados') }}</h4>
            <div v-if="!isTrashList" class="row justify-content-between px-10">
                <div class="col-sm">
                    <router-link class="btn btn-sm btn-labeled btn-danger float-left" :to="{ path: '/admin/users', query: { 'only_trashed': 1 } }">
                        <span class="btn-label ml-1"><i class="fas fa-trash"></i></span>{{ $t('papelera') }}
                    </router-link>
                </div>
                <div class="col-sm">
                    <router-link class="btn btn-sm btn-labeled btn-success float-right" :to="{ path: '/admin/user' }">
                        <span class="btn-label ml-1"><i class="fas fa-plus"></i></span>{{ $t('anadir') }}
                    </router-link>
                </div>
            </div>
            <div class="row justify-content-between mt-20 px-10">
                <div class="input-group col-md-6 col-xl-4 my-5">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ $t('administrador.table_list.entries_select.show') }}</span>
                    </div>
                    <select @change="updateLimit" v-model="limit" class="custom-select form-control">
                        <option :selected="this.value === limit" :value="10">10</option>
                        <option :selected="this.value === limit" :value="25">25</option>
                        <option :selected="this.value === limit" :value="50">50</option>
                        <option :selected="this.value === limit" :value="100">100</option>
                    </select>
                    <div class="input-group-append">
                        <span class="input-group-text">{{ $t('administrador.table_list.entries_select.entries') }}</span>
                    </div>
                </div>
                <div class="input-group col-md-6 col-xl-4 my-5">
                    <input
                            type="text"
                            class="form-control"
                            :placeholder="$t('administrador.table_list.search_bar.placeholder')"
                            v-model="query"
                            v-on:keyup.enter="getUsers(1)"
                    >
                    <div v-if="query !== ''" class="input-group-append">
                        <button @click="query = ''" class="btn btn-secondary"><i class="fas fa-times-circle"></i></button>
                    </div>
                    <div class="input-group-append">
                        <button @click="getUsers(1)" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="row mt-20 mx-10">
                <div class="table-responsive" style="min-height: 300px;">
                    <table class="table table-sm table-hover table-bordered vld-parent" ref="tableContainer">
                        <thead>
                        <tr>
                            <th
                                    v-for="column in data.columns"
                                    :key="column.field"
                                    class="th-sm bg-primary text-white text-center px-0 py-0"
                            >
                                <button
                                        v-if="column.isSortable === true"
                                        v-on:click="sort(column.field)"
                                        class="btn btn-sm text-white btn-block"
                                >
                                    {{ column.label }}
                                    <i v-if="currentSort.field !== column.field" class="fas fa-sort ml-5"></i>
                                    <i v-else-if="currentSort.type === 'ASC'" class="fas fa-sort-up ml-5"></i>
                                    <i v-else-if="currentSort.type === 'DESC'" class="fas fa-sort-down ml-5"></i>
                                </button>
                                <p v-else>{{ column.label }}</p>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="totalRows === 0">
                            <td :colspan="data.columns.length" class="text-center" style="height: 200px;">
                                <p v-if="!loadUsers && totalRows === 0">
                                    {{ $t('administrador.table_list.no_entries') }}
                                </p>
                            </td>
                        </tr>
                        <tr v-for="user in data.rows" :key="user.id">
                            <td v-for="column in data.columns" :key="'user-' + user.id + '-' + column.field">
                                <p v-if="!column.customizable">{{ user[column.field] }}</p>
                                <div v-else-if="column.field === 'active'" class="text-center">
                                    <div v-if="user.active" class="badge badge-success">SI</div>
                                    <div v-else class="badge badge-grey">NO</div>
                                </div>
                                <div v-else-if="column.field === 'social_networks'" class="button-list text-center">
                                    <button v-if="user.has_facebook_token" class="btn btn-icon btn-icon-style-1 btn-primary" title="Facebook" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-facebook"></i></span>
                                    </button>
                                    <button v-if="!user.has_facebook_token" class="btn btn-icon btn-icon-style-1 btn-grey" title="Facebook" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-facebook"></i></span>
                                    </button>
                                    <button v-if="user.has_google_token" class="btn btn-icon btn-icon-style-1 btn-danger" title="Google" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-google-plus"></i></span>
                                    </button>
                                    <button v-if="!user.has_google_token" class="btn btn-icon btn-icon-style-1 btn-grey" title="Google" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-google-plus"></i></span>
                                    </button>
                                    <button v-if="user.has_twitter_token" class="btn btn-icon btn-icon-style-1 btn-info" title="Twitter" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-twitter"></i></span>
                                    </button>
                                    <button v-if="!user.has_twitter_token" class="btn btn-icon btn-icon-style-1 btn-grey" title="Twitter" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-twitter"></i></span>
                                    </button>
                                    <button v-if="user.has_github_token" class="btn btn-icon btn-icon-style-1 btn-dark" title="GitHub" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-github"></i></span>
                                    </button>
                                    <button v-if="!user.has_github_token" class="btn btn-icon btn-icon-style-1 btn-grey" title="GitHub" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-github"></i></span>
                                    </button>
                                    <button v-if="user.has_clave_unica_token" class="btn btn-icon btn-icon-style-1 btn-green" title="Clave Única" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-key"></i></span>
                                    </button>
                                    <button v-if="!user.has_clave_unica_token" class="btn btn-icon btn-icon-style-1 btn-grey" title="Clave Única" style="height: 22px; width: 22px; margin-top: 0; margin-right: 1px;">
                                        <span class="btn-icon-wrap"><i class="fa fa-key"></i></span>
                                    </button>
                                </div>
                                <div v-else-if="column.field === 'actions'" class="text-center">
                                    <button
                                            class="btn btn-primary btn-sm dropdown-toggle"
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                    >
                                        {{ $t('acciones') }}
                                    </button>
                                    <div class="dropdown-menu">
                                        <router-link v-if="!isTrashList" class="dropdown-item" :to="{ path: '/admin/user/' + user.id }">{{ $t('editar') }}</router-link>
                                        <button v-if="!isTrashList" @click="showDeleteUserModal(user.id)" class="dropdown-item">{{ $t('eliminar') }}</button>
                                        <button v-if="isTrashList" @click="showForceDeleteUserModal(user.id)" class="dropdown-item">{{ $t('eliminar_permanente') }}</button>
                                        <button v-if="isTrashList" @click="showUndeleteUserModal(user.id)" class="dropdown-item">{{ $t('restaurar') }}</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row justify-content-between mt-10 ml-10 mr-10">
                <div class="col-md-5 my-5">
                    <p v-if="totalRows > 0">
                        {{ $t('administrador.table_list.info_text.showing') + ' '
                        + (offset + 1) + ' '
                        + $t('administrador.table_list.info_text.to') + ' '
                        + (offset + returnedRows) + ' '
                        + $t('administrador.table_list.info_text.of') + ' '
                        + totalRows + ' '
                        + $t('administrador.table_list.info_text.entries') }}
                    </p>
                </div>
                <div class="col-md-7 my-5">
                    <div class="row justify-content-end">
                        <ul v-if="totalRows > 0" class="pagination" style="overflow: auto;">
                            <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getUsers(1)"
                                        :title="$t('administrador.table_list.pagination_btns.first_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getUsers(currentPage - 1)"
                                        :title="$t('administrador.table_list.pagination_btns.previous_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-left"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.previous_page') }}</span>
                                </a>
                            </li>
                            <li
                                    v-for="i in Math.ceil(totalRows/limit)"
                                    :key="'pag-' + i"
                                    v-bind:class="i === currentPage ? 'active':''"
                                    class="page-item"
                            >
                                <a class="page-link" @click="getUsers(i)">{{ i }}</a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getUsers(currentPage + 1)"
                                        :title="$t('administrador.table_list.pagination_btns.next_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-right"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getUsers(Math.ceil(totalRows/limit))"
                                        :title="$t('administrador.table_list.pagination_btns.last_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-double-right"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.last_page') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <b-modal
                    id="delete-user-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.eliminar_usuario.modal_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.eliminar_usuario.modal_eliminacion.pregunta') }}</p>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                class="vld-parent"
                                variant="danger"
                                size="sm"
                                @click.prevent="deleteUser(false)"
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
                                @click.prevent="$bvModal.hide('delete-user-modal')"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
            <b-modal
                    id="force-delete-user-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.eliminar_usuario.modal_forzar_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.eliminar_usuario.modal_forzar_eliminacion.pregunta') }}</p>
                        <div class="alert alert-warning row ma-0 mt-10 pa-0">
                            <div class="col-sm-2 text-center align-self-center pa-10">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="col-sm-10 pa-10">
                                {{ $t('administrador.componentes.eliminar.precaucion') }}
                            </div>
                        </div>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                class="vld-parent"
                                variant="danger"
                                size="sm"
                                @click.prevent="deleteUser(true)"
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
                                @click.prevent="$bvModal.hide('force-delete-user-modal')"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
            <b-modal
                    id="undelete-user-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.eliminar_usuario.modal_restaurar.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.eliminar_usuario.modal_restaurar.pregunta') }}</p>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                class="vld-parent"
                                variant="primary"
                                size="sm"
                                @click.prevent="undeleteUser"
                        >
                            <i class="fas fa-trash-restore"></i> {{ $t('si') }}
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
                                @click.prevent="$bvModal.hide('undelete-user-modal')"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
        </section>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import { BModal } from 'bootstrap-vue';
    import axios from '../../../backend/axios';

    export default {
        name: 'UsersList',
        components: {
            Loading,
            BModal
        },
        data() {
            return {
                isTrashList: false,
                users: [],
                totalRows: 0,
                returnedRows: 0,
                currentPage: 1,
                currentSort: {
                    field: 'id',
                    type: 'ASC',
                },
                limit: 10,
                offset: 0,
                query: '',
                data: {
                    columns: [
                        {
                            label: "ID",
                            field: "id",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Nombre",
                            field: "name",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Apellido",
                            field: "surname",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Correo Electrónico",
                            field: "email",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Activo",
                            field: "active",
                            isSortable: true,
                            customizable: true
                        },
                        {
                            label: "Redes Sociales",
                            field: "social_networks",
                            isSortable: false,
                            customizable: true
                        },
                        {
                            label: "Acciones",
                            field: "actions",
                            isSortable: false,
                            customizable: true
                        }
                    ],
                    rows: []
                },
                userIdDelete: null,
                userIdUndelete: null,
                loadUsers: false,
                loadModalBtn: false,
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

            if(this.$route.query.only_trashed == '1') {
                this.isTrashList = true;
            }

            this.getUsers();
        },
        methods: {
            getUsers(page) {
                if(page) {
                    this.currentPage = page;
                }

                this.offset = 0;
                if(this.currentPage > 1) {
                    this.offset = this.limit * (this.currentPage - 1);
                }

                this.loadUsers = true;
                let loader = this.$loading.show({
                    container: this.fullPage ? null : this.$refs.tableContainer,
                    color: this.color,
                    height: 128
                });

                axios
                    .get('/users', {
                        params: {
                            'query': this.query,
                            'only_trashed': this.isTrashList ? 1 : 0,
                            'order': this.currentSort.type,
                            'order_by': this.currentSort.field,
                            'limit': this.limit,
                            'offset': this.offset
                        }
                    })
                    .then(res => {
                        this.totalRows = res.data.total_results;
                        this.returnedRows = res.data.returned_results;
                        this.users = res.data.results;
                        this.data.rows = this.users;
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se han podido cargar los usuarios', 'Error al cargar');
                    })
                    .finally(() => {
                        this.loadUsers = false;
                        loader.hide();
                    });
            },
            sort(field) {
                if(field === this.currentSort.field) {
                    if(this.currentSort.type === 'ASC') {
                        this.currentSort.type = 'DESC';
                    } else {
                        this.currentSort.type = 'ASC';
                    }
                } else {
                    this.currentSort.field = field;
                    this.currentSort.type = 'ASC';
                }
                this.getUsers(1);
            },
            updateLimit(event) {
                this.limit = event.target.value;
                this.getUsers(1);
            },
            showDeleteUserModal(userId) {
                this.userIdDelete = userId;
                this.$bvModal.show('delete-user-modal');
            },
            showForceDeleteUserModal(userId) {
                this.userIdDelete = userId;
                this.$bvModal.show('force-delete-user-modal');
            },
            showUndeleteUserModal(userId) {
                this.userIdUndelete = userId;
                this.$bvModal.show('undelete-user-modal');
            },
            deleteUser(force) {
                this.loadModalBtn = true;
                if(!force) {
                    axios
                        .delete('/users/' + this.userIdDelete, {
                            params: {
                                force_delete: 0
                            }
                        })
                        .then(() => {
                            this.getUsers();
                            this.$toastr('success', 'Se ha eliminado temporalmente el usuario', 'Eliminación exitosa');
                        })
                        .catch(() => {
                            this.$toastr('error', 'No se ha podido eliminar el usuario', 'Eliminación fallida');
                        })
                        .finally(() => {
                            this.userIdDelete = null;
                            this.loadModalBtn = false;
                            this.$bvModal.hide('delete-user-modal');
                        });
                } else {
                    axios
                        .delete('/users/' + this.userIdDelete, {
                            params: {
                                force_delete: 1
                            }
                        })
                        .then(() => {
                            this.getUsers();
                            this.$toastr('success', 'Se ha eliminado permanentemente el usuario', 'Eliminación exitosa');
                        })
                        .catch(() => {
                            this.$toastr('error', 'No se ha podido eliminar el usuario', 'Eliminación fallida');
                        })
                        .finally(() => {
                            this.userIdDelete = null;
                            this.loadModalBtn = false;
                            this.$bvModal.hide('force-delete-user-modal');
                        });
                }
            },
            undeleteUser() {
                this.loadModalBtn = true;
                axios
                    .patch('/users/' + this.userIdUndelete + '/undelete')
                        .then(() => {
                            this.getUsers();
                            this.$toastr('success', 'Se ha restaurado el usuario', 'Restauración exitosa');
                        })
                        .catch(() => {
                            this.$toastr('error', 'No se ha podido restaurar el usuario', 'Restauración fallida');
                        })
                        .finally(() => {
                            this.userIdUndelete = null;
                            this.loadModalBtn = false;
                            this.$bvModal.hide('undelete-user-modal');
                        });
            }
        }
    }
</script>