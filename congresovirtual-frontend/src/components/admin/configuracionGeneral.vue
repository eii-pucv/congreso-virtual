<template>
    <div class="container mt-20">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <div class="row">
                        <div class="col-xl-12">
                            <h3 class="hk-sec-title text-center">{{ $t('administrador.componentes.configuracion_general.titulo') }}</h3>
                            <hr class="my-4">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <form @submit.prevent="saveConfiguration">
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.nombre') }}</label>
                                        <input type="text" class="form-control" v-model="site_name" id="nombre" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="row div-align">
                                    <div class="col-md-10 my-10">
                                        <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.propuestas') }}</h5>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.peticiones') }}</label>
                                        <input type="text" class="form-control" v-model="max_petitions.number_petitions" id="max" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="row div-align">
                                    <div class="col-md-10 my-10">
                                        <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes') }}</h5>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-5 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">Facebook</label>
                                        <input type="text" class="form-control" v-model="social_networks.facebook" id="fb" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                    <div class="col-md-5 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">Twitter</label>
                                        <input type="text" class="form-control" v-model="social_networks.twitter" id="twr" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-5 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">Instagram</label>
                                        <input type="text" class="form-control" v-model="social_networks.instagram" id="ig" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                    <div class="col-md-5 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">Youtube</label>
                                        <input type="text" class="form-control" v-model="social_networks.youtube" id="ytb" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="button-list text-center">
                                    <button type="submit" class="btn btn-primary">{{ $t('guardar') }}</button>
                                    <button @click="volver" class="btn btn-danger">{{ $t('cancelar') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../../backend/axios";
import {bus} from "../../main";

export default {
    name: "Site name",
    props: {

    },
    components: {

    },
    data() {
        return {
            mode: String,
            site_name: null,
            max_petitions: {
                number_petitions: null
            },
            social_networks: {
                facebook: "",
                twitter: "",
                instagram: "",
                youtube: ""
            }
        }
    },
    methods: {


        async saveConfiguration () {

            let newConfig = {
                settings: []
            }

            newConfig.settings.push({
                key: "site_name",
                label: "Menú principal",
                value: this.site_name
            })

            newConfig.settings.push({
                key: "max_necessary_petitions",
                label: "Peticiones máximas",
                value: JSON.stringify(this.max_petitions)
            })
            
            newConfig.settings.push({
                key: "social_networks",
                label: "Redes sociales",
                value: JSON.stringify(this.social_networks)
            })

            try {
                const res = await axios.put('/settings', newConfig)
                this.$toastr('success','','Datos actualizados')
            } catch (error) {
                console.log(error)
                this.$toastr('error','No se ha podido guardar los datos','ERROR')
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
            const res_name = await axios.get('/settings?key=site_name')
            this.site_name = res_name.data[0].value

            const res_petitions = await axios.get('/settings?key=max_necessary_petitions')
            this.max_petitions = JSON.parse(res_petitions.data[0].value)

            const res_social = await axios.get('/settings?key=social_networks')
            this.social_networks = JSON.parse(res_social.data[0].value)

            this.$toastr('success','','Datos cargados')
        } catch (error) {
            console.log(error)
            this.$toastr('error','No se han cargado los datos','ERROR')
        }  

    },
    
}
</script>

<style scoped>

.div-align {
    align-items: center;
    justify-content: center;
}
    
</style>