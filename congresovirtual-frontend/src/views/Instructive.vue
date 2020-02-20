<template>
    <div class="px-0" style="min-height: 720px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20 px-0">
            <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.breadcumb.instructivo') }}</li>
            </ol>
        </nav>
        <div class="container card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <div class="tab-content mt-5">
                <div class="tab-pane show active mt-5" id="tab1" role="tabpanel">
                    <div class="card card-lg" id="consultas_sitio" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <h3 class="card-header border-bottom-0" :style="mode==='dark'?'color: #fff':''">
                            {{ $t('instructivo.titulo') }}
                        </h3>
                        <div class="accordion accordion-type-2 accordion-flush" id="accordion_2">
                            <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <div class="card-header d-flex justify-content-between activestate">
                                    <a role="button" data-toggle="collapse" href="#q1_generales" aria-expanded="true" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.contenido.inicio_sesion.titulo') }}</a>
                                </div>
                                <div id="q1_generales" class="collapse show" data-parent="#accordion_2" role="tabpanel">
                                    <div class="card-body pa-15">
                                        {{ $t('instructivo.contenido.inicio_sesion.descripcion.parrafo1') }}
                                        <br>
                                        {{ $t('instructivo.contenido.inicio_sesion.descripcion.parrafo2') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" data-toggle="collapse" href="#q2_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.contenido.postulacion_proyectos.titulo') }}</a>
                                </div>
                                <div id="q2_generales" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body pa-15">
                                        {{ $t('instructivo.contenido.postulacion_proyectos.descripcion') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" data-toggle="collapse" href="#q4_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.contenido.opinion/debatir.titulo') }}</a>
                                </div>
                                <div id="q4_generales" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body pa-15">
                                        {{ $t('instructivo.contenido.opinion/debatir.descripcion') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" data-toggle="collapse" href="#q5_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('instructivo.contenido.olvido_contrasena.titulo') }}</a>
                                </div>
                                <div id="q5_generales" class="collapse" data-parent="#accordion_2">
                                    <div class="card-body pa-15">{{ $t('instructivo.contenido.olvido_contrasena.descripcion') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {bus} from "../main";
    export default {
        name: "Instructive",
        components: {
        
        },
        props: {
            mode: String,
        },
        data() {
            return {
                
            }
        },
        methods: {
           
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

<style>
  .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>