<template>
    <div class="container my-30" style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <section class="hk-sec-wrapper">
            <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.gamificacion.lista_jugadores.titulo') }}</h3>
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
                            v-on:keyup.enter="getPlayers(1)"
                    >
                    <div v-if="query !== ''" class="input-group-append">
                        <button @click="query = ''" class="btn btn-secondary"><i class="fas fa-times-circle"></i></button>
                    </div>
                    <div class="input-group-append">
                        <button @click="getPlayers(1)" class="btn btn-primary"><i class="fas fa-search"></i></button>
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
                                <p v-if="!loadPlayers && totalRows === 0">
                                    {{ $t('administrador.table_list.no_entries') }}
                                </p>
                            </td>
                        </tr>
                        <tr v-for="player in data.rows" :key="player.user_id">
                            <td v-for="column in data.columns" :key="'player-' + player.user_id + '-' + (column.parentField ? column.parentField + '-' : '') + column.field">
                                <p v-if="!column.customizable && !column.parentField">{{ player[column.field] }}</p>
                                <p v-else-if="!column.customizable && column.parentField">{{ player[column.parentField][column.field] }}</p>
                                <div v-else-if="column.field === 'active_gamification'" class="text-center">
                                    <div v-if="player.active_gamification" class="badge badge-success">SI</div>
                                    <div v-else class="badge badge-grey">NO</div>
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
                                        <router-link class="dropdown-item" :to="{ path: '/admin/user/' + player.user_id }">{{ $t('editar') }}</router-link>
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
                                        @click="getPlayers(1)"
                                        :title="$t('administrador.table_list.pagination_btns.first_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getPlayers(currentPage - 1)"
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
                                <a class="page-link" @click="getPlayers(i)">{{ i }}</a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getPlayers(currentPage + 1)"
                                        :title="$t('administrador.table_list.pagination_btns.next_page')"
                                >
                                    <span aria-hidden="true"><i class="fas fa-angle-right"></i></span>
                                    <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                <a
                                        class="page-link"
                                        @click="getPlayers(Math.ceil(totalRows/limit))"
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
        </section>
    </div>
</template>

<script>
    import axios from '../../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'PlayersList',
        components: {
            Loading
        },
        data() {
            return {
                players: [],
                totalRows: 0,
                returnedRows: 0,
                currentPage: 1,
                currentSort: {
                    field: 'user_id',
                    type: 'ASC',
                },
                limit: 10,
                offset: 0,
                query: '',
                data: {
                    columns: [
                        {
                            label: "ID",
                            field: "user_id",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Nombre",
                            parentField: "user",
                            field: "name",
                            isSortable: false,
                            customizable: false
                        },
                        {
                            label: "Apellido",
                            parentField: "user",
                            field: "surname",
                            isSortable: false,
                            customizable: false
                        },
                        {
                            label: "Puntos",
                            field: "points",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Gamificación Activada",
                            field: "active_gamification",
                            isSortable: true,
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
                loadPlayers: false,
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

            this.getPlayers();
        },
        methods: {
            getPlayers(page) {
                if(page) {
                    this.currentPage = page;
                }

                this.offset = 0;
                if(this.currentPage > 1) {
                    this.offset = this.limit * (this.currentPage - 1);
                }

                this.loadPlayers = true;
                let loader = this.$loading.show({
                    container: this.fullPage ? null : this.$refs.tableContainer,
                    color: this.color,
                    height: 128
                });

                axios
                    .get('/players', {
                        params: {
                            'query': this.query,
                            'order': this.currentSort.type,
                            'order_by': this.currentSort.field,
                            'limit': this.limit,
                            'offset': this.offset
                        }
                    })
                    .then(res => {
                        this.totalRows = res.data.total_results;
                        this.returnedRows = res.data.returned_results;
                        this.players = res.data.results;
                        this.data.rows = this.players;
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se han podido cargar los jugadores', 'Error al cargar');
                    })
                    .finally(() => {
                        this.loadPlayers = false;
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
                this.getPlayers(1);
            },
            updateLimit(event) {
                this.limit = event.target.value;
                this.getPlayers(1);
            }
        }
    }
</script>