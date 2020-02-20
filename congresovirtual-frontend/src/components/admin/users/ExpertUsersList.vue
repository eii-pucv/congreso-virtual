<template>
    <div style="min-height: 720px;">
        <div class="container container-fluid mt-25 mb-10" :style="mode==='dark'?'background: #080035; color: #fff':''">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.navbar.usuarios.lista_expertos') }}</h3>
                        <div class="row">
                            <div class="col-sm" hidden>
                                <a role="button" class="btn btn-sm btn-labeled btn-success float-right" href="/admin/user">
                                <span class="btn-label ml-1"><i class="glyphicon glyphicon-plus"></i></span>{{ $t('anadir') }}</a>
                            </div>
                        </div>
                        <div class="row mt-20">
                            <div class="col-md">
                                <mdb-container>
                                    <mdb-datatable
                                        :data="data"
                                        bordered
                                        arrows
                                        responsive
                                        autoWidth
                                        :searching="true"
                                        headerColor="elegant-color"
                                        headerWhite
                                        :sorting="true"
                                    />
                                </mdb-container>
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
import {API_URL} from "../../../backend/data_server";
import {bus} from "../../../main";

export default {
    name: 'ExpertUsersList',
    props: {

    },
    components: {
        mdbDatatable,
        mdbContainer
    },
    data() {
        return {
            mode: String,
            experts_users: [],
            data: {
                columns: [  
                    {
                        label: "Avatar",
                        field: "avatarimg"
                    },
                    {
                        label: "Nombre",
                        field: "name",
                        sort: "asc"
                    },
                    {
                        label: "Apellido",
                        field: "surname",
                        sort: "asc"
                    },
                    {
                        label: "Titulo profesional",
                        field: "carrera",
                        sort: "asc"
                    },
                    {
                        label: "Áreas desempeño",
                        field: "areas",
                    },
                    {
                        label: "Temas trabajo",
                        field: "temas"
                    },
                    {
                        label: "Acciones",
                        field: "botones",
                    }
                ],
                rows: [],
            },
        }
    },
    methods: {
        
        
    },
    async mounted() {
        if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
            this.mode = 'dark';
        } else {
            this.mode = 'light';
        }

        try {
            const res = await axios.get('/users?es_experto=1')
            this.experts_users = res.data
            console.log(JSON.stringify(this.experts_users))
            this.loader = true
            this.$toastr('success','','Usuarios cargados')
        } catch (error) {
            console.log(error)
            this.$toastr('error','','Usuarios no cargados')
        } 

        if (this.experts_users) {

            this.experts_users.forEach( experto => {

                let avatarURL =  API_URL + '/api/storage/avatars/'+ experto.id + '/' + experto.avatar
                experto.avatarimg = '<img class="img-fluid rounded-circle" style="max-width: 80px;" src=" ' + avatarURL + ' ">'

                if (experto.titulo_profesional) {
                    experto.carrera = '<h6><span class="badge badge-secondary py-10">' + experto.titulo_profesional + '</span></h6>'
                }

                if (experto.areas_desempenio) {
                    experto.areas = '<h6><span class="badge badge-success py-10">' + experto.areas_desempenio + '</span></h6>'
                }

                if (experto.temas_trabajo) {
                    experto.temas = '<h6><span class="badge badge-info py-10">' + experto.temas_trabajo + '</span></h6>'
                }

                experto.botones = `<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/user/` + experto.id + `">Editar</a>
                                        <a class="dropdown-item" href="/admin/user/` + experto.id + `/delete">Eliminar</a>
                                    </div>`

                this.data.rows.push(experto)
            })



        }


    },
    
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

.btn, .btn-icon{
    margin-top: 0px;
    margin-right: 1px;
}

.btn.btn-icon {
    height: 25px;
    width: 25px;
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