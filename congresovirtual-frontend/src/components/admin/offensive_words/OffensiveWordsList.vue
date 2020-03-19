<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.palabras_ofensivas') }}</h3>
                        <div class="row px-10">
                            <div class="col-sm">
                                <router-link class="btn btn-sm btn-labeled btn-success float-right" :to="{ path: '/admin/offensive-word' }">
                                    <span class="btn-label ml-1"><i class="fa fa-plus"></i></span>{{ $t('anadir') }}
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
                                    v-on:keyup.enter="getOffensiveWords(1)"
                                >
                                <div class="input-group-append" v-if="query !== ''">
                                    <button class="btn text-white bg-grey" @click="query = ''"><font-awesome-icon icon="times-circle"/></button>
                                </div>
                                <div class="input-group-append">
                                    <button
                                        class="btn text-white bg-primary"
                                        @click="getOffensiveWords(1)"
                                    ><font-awesome-icon icon="search"/></button>
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
                                                <p v-if="!loadOffensiveWords && totalRows === 0">
                                                    {{ $t('administrador.table_list.no_entries') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr v-for="word in data.rows" :key="word.id">
                                            <td v-for="column in data.columns" :key="'offensive_word-' + word.id + '-' + column.field">
                                                <p v-if="!column.customizable">{{ word[column.field] }}</p>
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
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/offensive-word/' + word.id }">{{ $t('editar') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/offensive_word/' + word.id + '/delete' }">{{ $t('eliminar') }}</router-link>
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
                                                @click="getOffensiveWords(1)"
                                                :title="$t('administrador.table_list.pagination_btns.first_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                            <a
                                                class="page-link"
                                                @click="getOffensiveWords(currentPage - 1)"
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
                                            <a class="page-link" @click="getOffensiveWords(i)">{{ i }}</a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                class="page-link"
                                                @click="getOffensiveWords(currentPage + 1)"
                                                :title="$t('administrador.table_list.pagination_btns.next_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-right"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                class="page-link"
                                                @click="getOffensiveWords(Math.ceil(totalRows/limit))"
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

import axios from "../../../backend/axios";
import { mdbDatatable, mdbContainer  } from 'mdbvue';

export default {
    
    name: "OffensiveWords",
    components: {
        mdbDatatable,
        mdbContainer
    },
    props: {

    },
    data() {
        return {
            mode: String,
            offensive_words: [],
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
            loadOffensiveWords: false,
            fullPage: false,
            color:"#000000",
            data: {
                columns: [
                    {
                        label: "N°",
                        field: "id",
                        isSortable: true,
                        customizable: false
                    },
                    {
                        label: "Palabra",
                        field: "word",
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
                rows: []
            },
        }
    },
    methods: {
        getOffensiveWords(page) {
            if(page) {
                this.currentPage = page;
            }

            this.offset = 0;
            if(this.currentPage > 1) {
                this.offset = this.limit * (this.currentPage - 1);
            }

            this.loadOffensiveWords = true;
            let loader = this.$loading.show({
                container: this.fullPage ? null : this.$refs.tableContainer,
                color: this.color,
                height: 128
            });

            axios
                .get('/offensive_words', {
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
                    this.offensive_words = res.data.results;
                    this.data.rows = this.offensive_words;
                })
                .catch(() => {
                    this.$toastr('error', 'No se han podido cargar las palabras ofensivas', 'Error al cargar');
                })
                .finally(() => {
                    this.loadOffensiveWords = false;
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
            this.getOffensiveWords(1);
        },
        updateLimit(e) {
            this.limit = e.target.value;
            this.getOffensiveWords(1);
        },
        deleteOffensiveWord(id, value) {
            this.$dialog
            .confirm('¿Esta seguro que desea eliminar la palabra offensiva ' + value + "?")
            .then( async (dialog) => {
                try {
                    const res = await axios.delete('/offensive_words/' + id);
                    this.$toastr('success','','Se ha eliminado la palabra ofensiva');
                } catch (error) {
                    this.$toastr('error','','Algo ha salido mal, intente nuevamente');
                }
            });
        },
    },
    async mounted() {
        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }
        this.getOffensiveWords();
    },
}
</script>