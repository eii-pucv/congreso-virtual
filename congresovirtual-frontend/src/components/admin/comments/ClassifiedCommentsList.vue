<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.comentarios_clasificados') }}</h3>
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
                                        v-on:keyup.enter="getComments(1)"
                                >
                                <div class="input-group-append" v-if="query !== ''">
                                    <button class="btn text-white bg-grey" @click="query = ''"><font-awesome-icon icon="times-circle"/></button>
                                </div>
                                <div class="input-group-append">
                                    <button
                                            class="btn text-white bg-primary"
                                            @click="getComments(1)"
                                    ><font-awesome-icon icon="search"/></button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-20 mx-10">
                            <div class="table-responsive">
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
                                                    <i v-if="currentSort.field !== column.field" class="fa fa-sort ml-5"></i>
                                                    <i v-else-if="currentSort.type === 'ASC'" class="fa fa-sort-up ml-5"></i>
                                                    <i v-else-if="currentSort.type === 'DESC'" class="fa fa-sort-down ml-5"></i>
                                                </button>
                                                <p v-else>{{ column.label }}</p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="totalRows === 0">
                                            <td :colspan="data.columns.length" class="text-center" style="height: 200px;">
                                                <p v-if="!loadComments && totalRows === 0">
                                                    {{ $t('administrador.table_list.no_entries') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr v-for="comment in data.rows" :key="comment.id">
                                            <td v-for="column in data.columns" :key="'comment-' + comment.id + '-' + column.field">
                                                <p v-if="!column.customizable">{{ comment[column.field] }}</p>
                                                <p v-else-if="column.field === 'body'" class="ellipsis">{{ comment[column.field] }}</p>
                                                <div v-else-if="column.field === 'actions'" class="text-center">
                                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" :href="'/admin/classified/' + comment.id">Ver</a>
                                                        <a class="dropdown-item" :href="'/admin/classified/' + comment.id + '/perception'">Editar percepción</a>
                                                        <a class="dropdown-item" :href="'/admin/comment/' + comment.id + '/delete'">Eliminar</a>
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
                                                    @click="getComments(1)"
                                                    :title="$t('administrador.table_list.pagination_btns.first_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getComments(currentPage - 1)"
                                                    :title="$t('administrador.table_list.pagination_btns.previous_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-left"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.previous_page') }}</span>
                                            </a>
                                        </li>
                                        <li
                                                v-for="i in Math.ceil(totalRows/limit)"
                                                :key="'pag-' + i"
                                                v-bind:class="i === currentPage ? 'active':''"
                                                class="page-item"
                                        >
                                            <a class="page-link" @click="getComments(i)">{{ i }}</a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getComments(currentPage + 1)"
                                                    :title="$t('administrador.table_list.pagination_btns.next_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-right"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getComments(Math.ceil(totalRows/limit))"
                                                    :title="$t('administrador.table_list.pagination_btns.last_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-double-right"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.last_page') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
        name: 'ClassifiedCommentsList',
        components: { },
        data() {
            return {
                mode: String,
                comments: [],
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
                            label: "Resumen",
                            field: "body",
                            isSortable: true,
                            customizable: true
                        },
                        {
                            label: "Fecha Publicación",
                            field: "created_at",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Votos a Favor",
                            field: "votos_a_favor",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Votos en Contra",
                            field: "votos_en_contra",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Percepción",
                            field: "perception",
                            isSortable: true,
                            customizable: false
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
                loadComments: false,
                fullPage: false,
                color: "#000000"
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getComments();
        },
        methods: {
            getComments(page) {
                if(page) {
                    this.currentPage = page;
                }

                this.offset = 0;
                if(this.currentPage > 1) {
                    this.offset = this.limit * (this.currentPage - 1);
                }

                this.loadComments = true;
                let loader = this.$loading.show({
                    container: this.fullPage ? null : this.$refs.tableContainer,
                    color: this.color,
                    height: 128
                });

                axios
                    .get('/comments', {
                        params: {
                            'query': this.query,
                            'state': 0,
                            'order': this.currentSort.type,
                            'order_by': this.currentSort.field,
                            'limit': this.limit,
                            'offset': this.offset
                        }
                    })
                    .then(res => {
                        this.totalRows = res.data.total_results;
                        this.returnedRows = res.data.returned_results;
                        this.comments = res.data.results;
                        this.data.rows = this.comments;
                    })
                    .catch(() => {
                        this.$toastr('error', 'No se han podido cargar los comentarios', 'Error al cargar');
                    })
                    .finally(() => {
                        this.loadComments = false;
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
                this.getComments(1);
            },
            updateLimit(event) {
                this.limit = event.target.value;
                this.getComments(1);
            }
        }
    }
</script>

<style scoped>
    .ellipsis {
        height: auto !important;
        max-height: 200px;
    }
</style>