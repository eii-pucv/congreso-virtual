<template>
    <div style="min-height: 720px;">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <h4 class="hk-sec-title text-center">{{ $t('administrador.componentes.codigo_google.titulo') }}</h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form @submit.prevent="saveCode">
                                    <div class="form-row">
                                        <div class="col-md-12 mb-10">
                                            <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.codigo_google.codigo') }}</label>
                                            <input type="text" class="form-control" v-model="code" placeholder="Escriba el código aquí" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                        </div>
                                    </div>
                                    <div class="button-list">
                                        <button type="submit" class="btn btn-primary">{{ $t('guardar') }}</button>
                                        <button class="btn btn-danger" @click="volver()">{{ $t('cancelar') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../../backend/axios";
import { bus } from "../../main";

export default {
    name: "codeGoogleAnalytic",
    props: {

    },
    components: {

    },

    data() {
        return {
            mode: String,
            code : ""
        }
    },

    methods: {


        async saveCode() {

            let newConfig = {
                settings: []
            }

            newConfig.settings.push({
                key: "code_google_analytics",
                value: this.code
            })

            try {
                const res = await axios.put('/settings',newConfig)
                this.$toastr('success','','Código actualizado')
            } catch (error) {
                console.log(error)
                this.$toastr('error','No se pudo actualizar el código','Error')
            }
        },
        
        volver() {

            window.history.back()
        }
    },

    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }
        
        try {
            const res = await axios.get('/settings?key=code_google_analytics')
            this.code = res.data[0].value
            this.$toastr('success','','Código cargado')
        } catch (error) {
            console.log(error)
            this.$toastr('error','No se pudo cargar el código','Error')
        } 

    },

}

</script>

<style>
    
</style>