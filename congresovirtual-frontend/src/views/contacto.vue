<template>
    <div :class="mode==='dark'?'dark':'light'">
        <section class="jumbotron text-center" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <div class="container">
                <h1 class="jumbotron-heading" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.titulo') }}</h1>
                <br>
                <p class="lead mb-0" :class="mode==='dark'?'':'text-muted'" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('contacto.contenido.texto') }}
                </p>
            </div>
        </section>
        <div class="px-100 text-center" :style="mode==='dark'?'background: #080035;':''">
            <div class="row">
                <div class="col">
                    <nav class="container px-0" aria-label="breadcrumb">
                        <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                            <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.breadcumb.inicio') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="/contactanos" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.breadcumb.contacto') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container text-center" :style="mode==='dark'?'background: #080035;':''">
            <div class="row">
                <div class="col">
                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <div class="card-header bg-primary text-white"><i class="fa fa-envelope"></i> {{ $t('contacto.breadcumb.contacto') }}
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.nombre') }}</label>
                                    <input type="text" class="form-control" placeholder="Ingresa tu nombre completo" v-model="contacto.nombre" required :style="mode==='dark'?'background: #080035; color: #fff':''">
                                </div>
                                <div class="form-group">
                                    <label for="email" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.correo') }}</label>
                                    <input type="email" class="form-control" placeholder="Ingresa tu email" v-model="contacto.email" required :style="mode==='dark'?'background: #080035; color: #fff':''">
                                </div>
                                <div class="form-group">
                                    <label for="message" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.mensaje') }}</label>
                                    <textarea type="text" class="form-control" rows="6" v-model="contacto.mensaje" required :style="mode==='dark'?'background: #080035; color: #fff':''"></textarea>
                                </div>
                                <div class="mx-auto text-center">
                                    <a role="button" class="btn text-white bg-green votable"><font-awesome-icon icon="envelope"/><span class="btn-text" @click="createMail"> {{ $t('enviar') }}</span></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card text-center bg-light mb-3">
                        <div class="card-header bg-success text-white text-uppercase"><i class="fa fa-home"></i> {{ $t('contacto.contenido.info') }}</div>
                        <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                            <ul class="list-group list-group-flush w-100 align-items-stretch">
                                <li><i class="fa fa-map-marker fa-2x"></i>
                                    <br>
                                    <p>Avenida Pedro Montt s/n Valparaíso.</p>
                                </li>
                                <li><i class="fa fa-phone mt-4 fa-2x"></i>
                                    <br>
                                    <p>(56-32) 250 5000 - (56-2) 2674 7800</p>
                                </li>
                                <li><i class="fa fa-envelope mt-4 fa-2x"></i>
                                    <br>
                                    <p>admin@congresovirtual.cl</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../backend/axios"
import VueRecaptcha from 'vue-recaptcha';
import {bus} from "../main";

export default {
    name: "contacto",
    props: {
        mode: String,
    },
    components: {
        VueRecaptcha
    },
    data() {
        return {
            contacto: {
                nombre: "",
                email: "",
                mensaje: "",
            },
            
        }
    },
    methods: {
        createMail() {
            window.location.href = `mailto:admin@congresovirtual.informaticapucv.cl?subject=${this.contacto.nombre}&body=${this.contacto.mensaje}`
        }
    },
    mounted() {
        if (this.$store.getters.modo_oscuro == "dark") {
            this.mode = "dark"
        } else if (!this.mode) {
            this.mode = "light"
        }
    },
}
</script>

<style scoped>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>