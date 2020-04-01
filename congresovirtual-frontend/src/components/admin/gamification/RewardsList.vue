<template>
    <div class="container my-30" style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <section class="hk-sec-wrapper">
            <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.gamificacion.lista_recompensas.titulo') }}</h3>
            <h4 v-if="isTrashList" class="text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.eliminar.eliminados_temporalmente.eliminadas') }}</h4>
            <div v-if="!isTrashList" class="row justify-content-between px-10">
                <div class="col-sm">
                    <router-link class="btn btn-sm btn-labeled btn-danger float-left" :to="{ path: '/admin/gamification/rewards', query: { 'only_trashed': 1 } }">
                        <span class="btn-label ml-1"><i class="fas fa-trash"></i></span>{{ $t('papelera') }}
                    </router-link>
                </div>
                <div class="col-sm">
                    <router-link class="btn btn-sm btn-labeled btn-success float-right" :to="{ path: '/admin/gamification/reward' }">
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
                            v-on:keyup.enter="getRewards(1)"
                    >
                    <div v-if="query !== ''" class="input-group-append">
                        <button @click="query = ''" class="btn btn-secondary"><i class="fas fa-times-circle"></i></button>
                    </div>
                    <div class="input-group-append">
                        <button @click="getRewards(1)" class="btn btn-primary"><i class="fas fa-search"></i></button>
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
                                <p v-if="!loadRewards && totalRows === 0">
                                    {{ $t('administrador.table_list.no_entries') }}
                                </p>
                            </td>
                        </tr>
                        <tr v-for="reward in data.rows" :key="reward.id">
                            <td v-for="column in data.columns" :key="'reward-' + reward.id + '-' + column.field">
                                <p v-if="!column.customizable && !column.parentField">{{ reward[column.field] }}</p>
                                <p v-else-if="!column.customizable && column.parentField">{{ reward[column.parentField][column.field] }}</p>
                                <div v-else-if="column.customizable && column.field === 'type'" class="text-center">
                                    <div class="badge badge-warning">{{ reward.action.type }}</div>
                                </div>
                                <div v-else-if="column.customizable && column.field === 'subtype'" class="text-center">
                                    <div class="badge badge-info">{{ reward.action.subtype }}</div>
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
                                        <router-link v-if="!isTrashList" class="dropdown-item" :to="{ path: '/admin/gamification/reward/' + reward.id }">{{ $t('editar') }}</router-link>
                                        <button v-if="!isTrashList" @click="showDeleteRewardModal(reward.id)" class="dropdown-item">{{ $t('eliminar') }}</button>
                                        <button v-if="isTrashList" @click="showForceDeleteRewardModal(reward.id)" class="dropdown-item">{{ $t('eliminar_permanente') }}</button>
                                        <button v-if="isTrashList" @click="showUndeleteRewardModal(reward.id)" class="dropdown-item">{{ $t('restaurar') }}</button>
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
                                        @click="getRewards(1)"
                                        :title="$t('administrador.table_list.pagination_btns.first_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getRewards(currentPage - 1)"
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
                                <a class="page-link" @click="getRewards(i)">{{ i }}</a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getRewards(currentPage + 1)"
                                        :title="$t('administrador.table_list.pagination_btns.next_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-right"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getRewards(Math.ceil(totalRows/limit))"
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
                    id="delete-reward-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_eliminacion.pregunta') }}</p>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                class="vld-parent"
                                variant="danger"
                                size="sm"
                                @click.prevent="deleteReward(false)"
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
                                @click.prevent="$bvModal.hide('delete-reward-modal')"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
            <b-modal
                    id="force-delete-reward-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_forzar_eliminacion.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_forzar_eliminacion.pregunta') }}</p>
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
                                @click.prevent="deleteReward(true)"
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
                                @click.prevent="$bvModal.hide('force-delete-reward-modal')"
                        >
                            <i class="fas fa-window-close"></i> {{ $t('no') }}
                        </b-button>
                    </div>
                </template>
            </b-modal>
            <b-modal
                    id="undelete-reward-modal"
                    header-bg-variant="primary"
                    body-bg-variant="light"
                    footer-bg-variant="light"
                    header-class="justify-content-center"
                    hide-header-close
                    no-close-on-backdrop
                    centered
            >
                <template v-slot:modal-header>
                    <h6 class="text-white">{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_restaurar.titulo') }}</h6>
                </template>
                <div class="form-row">
                    <div class="col-md-12 mb-10">
                        <p>{{ $t('administrador.componentes.gamificacion.eliminar_recompensa.modal_restaurar.pregunta') }}</p>
                    </div>
                </div>
                <template v-slot:modal-footer>
                    <div class="btn-group w-100">
                        <b-button
                                class="vld-parent"
                                variant="primary"
                                size="sm"
                                @click.prevent="undeleteReward"
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
                                @click.prevent="$bvModal.hide('undelete-reward-modal')"
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
        name: 'PagesList',
        components: {
            Loading,
            BModal
        },
        data() {
            return {
                isTrashList: false,
                rewards: [],
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
                            label: "Puntos",
                            field: "points",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Acciones Necesarias",
                            field: "actions_needed",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Tipo de Acción",
                            parentField: "action",
                            field: "type",
                            isSortable: false,
                            customizable: true
                        },
                        {
                            label: "Subtipo de Acción",
                            parentField: "action",
                            field: "subtype",
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
                    rows: [],
                },
                rewardIdDelete: null,
                rewardIdUndelete: null,
                loadRewards: false,
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

            this.getRewards();
        },
        methods: {
            getRewards(page) {
                if(page) {
                    this.currentPage = page;
                }

                this.offset = 0;
                if(this.currentPage > 1) {
                    this.offset = this.limit * (this.currentPage - 1);
                }

                this.loadRewards = true;
                let loader = this.$loading.show({
                    container: this.fullPage ? null : this.$refs.tableContainer,
                    color: this.color,
                    height: 128
                });

                axios
                    .get('/rewards', {
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
                        this.rewards = res.data.results;
                        this.data.rows = this.rewards;
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se han podido cargar las recompensas', 'Error al cargar');
                    })
                    .finally(() => {
                        this.loadRewards = false;
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
                this.getRewards(1);
            },
            updateLimit(event) {
                this.limit = event.target.value;
                this.getRewards(1);
            },
            showDeleteRewardModal(rewardId) {
                this.rewardIdDelete = rewardId;
                this.$bvModal.show('delete-reward-modal');
            },
            showForceDeleteRewardModal(rewardId) {
                this.rewardIdDelete = rewardId;
                this.$bvModal.show('force-delete-reward-modal');
            },
            showUndeleteRewardModal(rewardId) {
                this.rewardIdUndelete = rewardId;
                this.$bvModal.show('undelete-reward-modal');
            },
            deleteReward(force) {
                this.loadModalBtn = true;
                if(!force) {
                    axios
                        .delete('/rewards/' + this.rewardIdDelete, {
                            params: {
                                force_delete: 0
                            }
                        })
                        .then(() => {
                            this.getRewards();
                            this.$toastr('success', 'Se ha eliminado temporalmente la recompensa', 'Eliminación exitosa');
                        })
                        .catch(() => {
                            this.$toastr('error', 'No se ha podido eliminar la recompensa', 'Eliminación fallida');
                        })
                        .finally(() => {
                            this.rewardIdDelete = null;
                            this.loadModalBtn = false;
                            this.$bvModal.hide('delete-reward-modal');
                        });
                } else {
                    axios
                        .delete('/rewards/' + this.rewardIdDelete, {
                            params: {
                                force_delete: 1
                            }
                        })
                        .then(() => {
                            this.getRewards();
                            this.$toastr('success', 'Se ha eliminado permanentemente la recompensa', 'Eliminación exitosa');
                        })
                        .catch(() => {
                            this.$toastr('error', 'No se ha podido eliminar la recompensa', 'Eliminación fallida');
                        })
                        .finally(() => {
                            this.rewardIdDelete = null;
                            this.loadModalBtn = false;
                            this.$bvModal.hide('force-delete-reward-modal');
                        });
                }
            },
            undeleteReward() {
                this.loadModalBtn = true;
                axios
                    .patch('/rewards/' + this.rewardIdUndelete + '/undelete')
                    .then(() => {
                        this.getRewards();
                        this.$toastr('success', 'Se ha restaurado la recompensa', 'Restauración exitosa');
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se ha podido restaurar la recompensa', 'Restauración fallida');
                    })
                    .finally(() => {
                        this.rewardIdUndelete = null;
                        this.loadModalBtn = false;
                        this.$bvModal.hide('undelete-reward-modal');
                    });
            }
        }
    }
</script>