<template>
    <div style="min-height: 820px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <div class="container mt-25 mb-10">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.lista_proyectos.titulo') }}</h3>
                        <div class="row px-10">
                            <div class="col-sm">
                                <a role="button" class="btn btn-sm btn-labeled btn-success float-right" href="/admin/project">
                                    <span class="btn-label ml-1"><i class="glyphicon glyphicon-plus"></i></span>{{ $t('anadir') }}
                                </a>
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
                                        v-model="query"
                                        v-on:keyup.enter="getProjects(1)"
                                        type="text"
                                        class="form-control"
                                        :placeholder="$t('administrador.table_list.search_bar.placeholder')"
                                >
                                <div v-if="query !== ''" class="input-group-append">
                                    <button @click="query = ''" class="btn text-white bg-grey"><font-awesome-icon icon="times-circle"/></button>
                                </div>
                                <div class="input-group-append">
                                    <button
                                            @click="getProjects(1)"
                                            class="btn text-white bg-primary"
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
                                                <p v-if="!loadProjects && totalRows === 0">
                                                    {{ $t('administrador.table_list.no_entries') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr v-for="project in data.rows" :key="project.id">
                                            <td v-for="column in data.columns" :key="'project-' + project.id + '-' + column.field">
                                                <p v-if="!column.customizable">{{ project[column.field] }}</p>
                                                <div v-else-if="column.field === 'actions'" class="text-center">
                                                    <button
                                                            class="btn btn-primary btn-sm dropdown-toggle vld-parent"
                                                            type="button"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                    >
                                                        {{ $t('acciones') }}
                                                        <loading
                                                                :active.sync="loadBtnDownloadReports.find(loadBtnDownloadReport => loadBtnDownloadReport.project_id === project.id).value"
                                                                :is-full-page="fullPage"
                                                                :height="24"
                                                                :color="'#ffffff'"
                                                        ></loading>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id }">{{ $t('editar') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/ideas' }">{{ $t('administrador.componentes.lista_proyectos.acciones.ver_ideas') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/articles' }">{{ $t('administrador.componentes.lista_proyectos.acciones.ver_articulos') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/analytic' }">{{ $t('administrador.componentes.lista_proyectos.acciones.analitica') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/send' }">{{ $t('administrador.componentes.lista_proyectos.acciones.notificar') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/delete' }">{{ $t('eliminar') }}</router-link>
                                                        <router-link class="dropdown-item" :to="{ path: '/admin/project/' + project.id + '/documents' }">{{ $t('administrador.componentes.lista_proyectos.acciones.ver_archivos') }}</router-link>
                                                        <button class="dropdown-item" @click="downloadReport(project.id)">{{ $t('administrador.componentes.lista_proyectos.acciones.ver_reporte') }}</button>
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
                                                    @click="getProjects(1)"
                                                    :title="$t('administrador.table_list.pagination_btns.first_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.first_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage - 1 < 1 ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getProjects(currentPage - 1)"
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
                                            <a class="page-link" @click="getProjects(i)">{{ i }}</a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getProjects(currentPage + 1)"
                                                    :title="$t('administrador.table_list.pagination_btns.next_page')"
                                            >
                                                <span aria-hidden="true"><i class="fa fa-angle-right"></i></span>
                                                <span class="sr-only">{{ $t('administrador.table_list.pagination_btns.next_page') }}</span>
                                            </a>
                                        </li>
                                        <li class="page-item" v-bind:class="currentPage + 1 > Math.ceil(totalRows/limit) ? 'disabled' : ''">
                                            <a
                                                    class="page-link"
                                                    @click="getProjects(Math.ceil(totalRows/limit))"
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
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectsList',
        components: {
            Loading
        },
        data() {
            return {
                mode: String,
                projects: [],
                totalRows: 0,
                returnedRows: 0,
                currentPage: 1,
                currentSort: {
                    field: 'boletin',
                    type: 'ASC',
                },
                limit: 10,
                offset: 0,
                query: '',
                data: {
                    columns: [
                        {
                            label: "N° Boletín",
                            field: "boletin",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Título",
                            field: "titulo",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Fecha Inicio",
                            field: "fecha_inicio",
                            isSortable: true,
                            customizable: false
                        },
                        {
                            label: "Fecha Término",
                            field: "fecha_termino",
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
                            label: "Abstenciones",
                            field: "abstencion",
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
                loadProjects: false,
                loadBtnDownloadReports: [],
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

            this.getProjects();
        },
        methods: {
            getProjects(page) {
                if(page) {
                    this.currentPage = page;
                }

                this.offset = 0;
                if(this.currentPage > 1) {
                    this.offset = this.limit * (this.currentPage - 1);
                }

                this.loadProjects = true;
                let loader = this.$loading.show({
                    container: this.fullPage ? null : this.$refs.tableContainer,
                    color: this.color,
                    height: 128
                });

                axios
                    .get('/projects', {
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
                        this.projects = res.data.results;
                        this.data.rows = this.projects;

                        this.loadBtnDownloadReports = this.projects.map(project => {
                            return {
                                project_id: project.id,
                                value: false
                            };
                        });
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.lista_proyectos.mensajes.fallido.carga_proyectos.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_proyectos.mensajes.fallido.carga_proyectos.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadProjects = false;
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
                this.getProjects(1);
            },
            updateLimit(event) {
                this.limit = event.target.value;
                this.getProjects(1);
            },
            async sendNotification(id) {
                try {
                    const res = await axios.get('/projects/' + id + '/notify_email');
                    this.$toastr('success','','Correo enviado');
                } catch (error) {
                    this.$toastr('error', '', 'No se pudo enviar el correo');
                }

            },
            eliminarProyecto(id, boletin) {
                this.$dialog
                .confirm('¿Esta seguro que desea eliminar el proyecto N° ' + boletin + '?')
                .then( async (dialog) => {
                    try {
                        const res = await axios.delete('/projects/' + id);
                        this.$toastr('success','','Se ha eliminado el proyecto');
                    } catch (error) {
                        this.$toastr('error','','Algo ha salido mal, intente nuevamente');
                    }
                });
            },
            downloadReport(projectId) {
                let loadBtnDownloadReport = this.loadBtnDownloadReports.find(loadBtnDownloadReport => loadBtnDownloadReport.project_id === projectId);
                loadBtnDownloadReport.value = true;
                this.$toastr(
                    'info',
                    this.$t('administrador.componentes.lista_proyectos.mensajes.informativo.ver_reporte.generico.cuerpo'),
                    this.$t('administrador.componentes.lista_proyectos.mensajes.informativo.ver_reporte.generico.titulo')
                );
                axios
                    .get('/projects/' + projectId + '/report', {
                        responseType: 'blob'
                    })
                    .then(res => {
                        let type = res.headers['content-type'];
                        let blob = new Blob([res.data], {type: type});
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);

                        let filename = 'Project report.pdf';
                        if(res.headers['x-suggested-filename']) {
                            filename = res.headers['x-suggested-filename'];
                        }
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();

                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.lista_proyectos.mensajes.exito.ver_reporte.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_proyectos.mensajes.exito.ver_reporte.generico.titulo')
                        );
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.lista_proyectos.mensajes.fallido.ver_reporte.generico.cuerpo'),
                            this.$t('administrador.componentes.lista_proyectos.mensajes.fallido.ver_reporte.generico.titulo')
                        );
                    })
                    .finally(() => {
                        loadBtnDownloadReport.value = false;
                    });
            }
        }
    }
</script>

<style scoped>
    @media (max-width: 1400px) {
        .hk-sec-wrapper {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }
    }

    .hk-sec-wrapper {
        background: #fff;
        padding: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: .25rem;
        margin-bottom: 14px;
    }

    .btn {
        margin-top: 0px;
        margin-right: 0px;
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

    .btn-labeled {
        padding-top: 0;
        padding-bottom: 0;
    }
</style>