<template>
<div style="min-height: 520px;"> 
    <div class="container">
        <section>
            <div class="ma-80 text-center">
                <img src="../../../assets/img/email-icono.png" height="200" width="200">
                <h1 class="display-4">{{ $t('administrador.componentes.notificacion.seguro') }}</h1>
                <br>
                <p class="lead">{{ $t('administrador.componentes.notificacion.aceptar') }}</p>
                <div class="button-list text-center">
                    <button class="btn text-white bg-green votable" :disabled="disabledBtn" @click="ProjectSendNotification()"><font-awesome-icon icon="envelope"/><span class="btn-text"> {{ $t('enviar') }}</span></button>
                    <button class="btn text-white bg-red ml-10 votable" @click="volver()"><font-awesome-icon icon="window-close"/><span class="btn-text"> {{ $t('cancelar') }}</span></button>
                </div>
            </div>
        </section>
        <section class="hk-sec-wrapper" v-if="users.length">
            <h2 class="hk-sec-title text-center">{{ $t('administrador.componentes.notificacion.usuarios_suscritos') }}</h2>
            <mdb-container> 
                <mdb-datatable
                    :data="data"
                    striped
                    bordered
                    arrows
                    responsive
                    :searching="true"
                    headerColor="elegant-color"
                    headerWhite
                    :sorting="true"
                />
            </mdb-container>
        </section>
    </div>
</div>
</template>

<script>

import axios from "../../../backend/axios";
import { mdbDatatable, mdbContainer } from 'mdbvue';

export default {

    name: 'ProjectSendNotification',
    components: {
        mdbDatatable,
        mdbContainer
    },
    props: {
        project_id: Number
    },
    data() {
        return {
            users: [],
            data: {
                columns: [  
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
                        label: "Nombre usuario",
                        field: "username",
                        sort: "asc"
                    },
                    {
                        label: "Correo electrónico",
                        field: "email",
                        sort: "asc"
                    },
                ],
                rows: [],
            },
            disabledBtn: false
        }
    },
    methods: {

        async sendNotification() {
            this.disabledBtn = true;
            try {
                const res = await axios.get('/projects/' + this.project_id + '/notify_email')
                this.disabledBtn = false;
                this.$toastr('success','','Correo enviado')
                this.users = res.data.users_to_notify
                this.setDatatable()
            } catch (error) {
                console.log(error)
                this.disabledBtn = false;
                this.$toastr('error','','No se pudo enviar el correo')
            }
        },

        setDatatable () {

            if (this.users) {
                this.users.forEach(usuario => {
                    this.data.rows.push(usuario) 
                });
            } 
        },

        volver() {
            window.history.back()
        }
        
    },
    mounted() {
        
    },


    
}

</script>

<style>
    
</style>