<template>
    <div class="px-0" style="min-height: 720px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20 px-0">
            <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.breadcumb.faq') }}</li>
            </ol>
        </nav>
        <div class="container mt-5">
            <!-- Row -->
            <div class="row"> 
                <div class="col-xl-4">
                    <div class="card mt-5" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <h6 class="card-header" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.temas') }}</h6>
                        <div class="nav nav-pills faq-nav" id="faq-tabs" role="tablist" aria-orientation="vertical">
                            <a href="#tab1" class="nav-link active" data-toggle="pill" role="tab" aria-controls="tab1" aria-selected="true" :style="mode==='dark'?'color: #fff':''">
                                <i class="mdi mdi-help-circle"></i> {{ $t('faq.contenido.uso_general.titulo1') }}<span class="badge badge-light badge-pill ml-15" :style="mode==='dark'?'color: #fff':''">06</span>
                            </a>
                            <a href="#tab2" class="nav-link" data-toggle="pill" role="tab" aria-controls="tab2" aria-selected="false" :style="mode==='dark'?'color: #fff':''">
                                <i class="mdi mdi-account"></i> {{ $t('faq.contenido.proyectos_ley.titulo1') }}<span class="badge badge-light badge-pill ml-15" :style="mode==='dark'?'color: #fff':''">14</span>
                            </a>
                            <a href="#tab3" class="nav-link" data-toggle="pill" role="tab" aria-controls="tab3" aria-selected="false" :style="mode==='dark'?'color: #fff':''">
                                <i class="mdi mdi-account-settings"></i> {{ $t('faq.contenido.consultas_publicas.titulo1') }}<span class="badge badge-light badge-pill ml-15" :style="mode==='dark'?'color: #fff':''">08</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="tab-content mt-5">
                        <div class="tab-pane show active mt-5" id="tab1" role="tabpanel">
                            <div class="card card-lg" id="consultas_sitio" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <h3 class="card-header border-bottom-0" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('faq.contenido.uso_general.titulo2') }}
                                </h3>
                                <div class="accordion accordion-type-2 accordion-flush" id="accordion_2">
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between activestate">
                                            <a role="button" data-toggle="collapse" href="#q1_generales" aria-expanded="true" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.participar.titulo') }}</a>
                                        </div>
                                        <div id="q1_generales" class="collapse show" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.participar.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q2_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.postular_proyectos.titulo') }}</a>
                                        </div>
                                        <div id="q2_generales" class="collapse" data-parent="#accordion_2">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">
                                                {{ $t('faq.contenido.uso_general.postular_proyectos.descripcion') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q3_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.postular_urgencia.titulo') }}</a>
                                        </div>
                                        <div id="q3_generales" class="collapse" data-parent="#accordion_2">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">
                                            {{ $t('faq.contenido.uso_general.postular_urgencia.descripcion') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q4_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.revisar_proyectos.titulo') }}</a>
                                        </div>
                                        <div id="q4_generales" class="collapse" data-parent="#accordion_2">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.revisar_proyectos.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q5_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.olvidado_contrasena.titulo') }}</a>
                                        </div>
                                        <div id="q5_generales" class="collapse" data-parent="#accordion_2">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.olvidado_contrasena.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q6_generales" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.rut_en_uso.titulo') }}</a>
                                        </div>
                                        <div id="q6_generales" class="collapse" data-parent="#accordion_2">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.uso_general.rut_en_uso.descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab2" role="tabpanel">
                            <div class="card card-lg" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <h3 class="card-header border-bottom-0" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('faq.contenido.proyectos_ley.titulo2') }}
                                </h3>
                                <div class="accordion accordion-type-2 accordion-flush">
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between activestate">
                                            <a role="button" data-toggle="collapse" href="#q1_leyes" aria-expanded="true" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.titulo') }}</a>
                                        </div>
                                        <div id="q1_leyes" class="collapse show" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a  class="collapsed" role="button" data-toggle="collapse" href="#q2_leyes" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_clima.titulo') }}</a>
                                        </div>
                                        <div id="q2_leyes" class="collapse" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q3_leyes" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_goles.titulo') }}</a>
                                        </div>
                                        <div id="q3_leyes" class="collapse" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q4_leyes" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_glaciares.titulo') }}</a>
                                        </div>
                                        <div id="q4_leyes" class="collapse" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q5_leyes" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_organos.titulo') }}</a>
                                        </div>
                                        <div id="q5_leyes" class="collapse" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between">
                                            <a class="collapsed" role="button" data-toggle="collapse" href="#q6_leyes" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_bullying.titulo') }}</a>
                                        </div>
                                        <div id="q6_leyes" class="collapse" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab3" role="tabpanel">
                            <div class="card card-lg" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                <h3 class="card-header border-bottom-0" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('faq.contenido.consultas_publicas.titulo2') }}
                                </h3>
                                <div class="accordion accordion-type-2 accordion-flush">
                                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                        <div class="card-header d-flex justify-content-between activestate">
                                            <a role="button" data-toggle="collapse" href="#q1_consultas" aria-expanded="true" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.consultas_publicas.consulta1.titulo') }}</a>
                                        </div>
                                        <div id="q1_consultas" class="collapse show" data-parent="#accordion_2" role="tabpanel">
                                            <div class="card-body pa-15" :style="mode==='dark'?'color: #fff':''">{{ $t('faq.contenido.proyectos_ley.dudas_uber.descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
        <!-- Container -->
</template>

<script>
    import {bus} from "../main";

    export default {
        name: "Faq",
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

.faq-nav {
    flex-direction: column;
    /* margin: 0 0 32px; */
    /* border-radius: 2px;     */
    /* border: 1px solid #ddd; */
    box-shadow: 0 1px 5px rgba(85, 85, 85, 0.15);
}

.tab-content {
    box-shadow: 0 1px 5px rgba(85, 85, 85, 0.15);
}

.tab-content.card {
    border-radius: 0;
}

.tab-content.card-header {
    padding: 15px 16px;
    border-radius: 0;
    background-color: #f6f6f6;
}

        
      
            

        

/* body { 
  margin-top: 30px;
  background-color: #eee;
}*/





</style>

