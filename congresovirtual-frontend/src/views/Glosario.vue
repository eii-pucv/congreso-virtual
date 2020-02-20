<template>
    <div class="px-0" style="min-height: 720px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20 px-0">
            <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.breadcumb.glosario') }}</li>
            </ol>
        </nav>
        <div class="container-fluid">
        <!-- Row -->
        <div class="row">
            <div class="col-xl-12 pa-0">
                <div class="container mt-20">
                    <div class="row">
                        <div class="card card-lg" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff;':''">
                            <h3 class="card-header border-bottom-0" :style="mode==='dark'?'color: #fff':''">
                                {{ $t('glosario.titulo') }}
                            </h3>
                            <div class="accordion accordion-type-2 accordion-flush" id="accordion_2">
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between activestate">
                                        <a role="button" data-toggle="collapse" href="#collapse_1i" aria-expanded="true" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.iniciativa.titulo') }}</a>
                                    </div>
                                    <div id="collapse_1i" class="collapse show" data-parent="#accordion_2" role="tabpanel">
                                        <div class="card-body pa-15">{{ $t('glosario.contenido.iniciativa.descripcion') }}</div>
                                    </div>
                                </div>
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2i" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.primer_tramite.titulo') }}</a>
                                    </div>
                                    <div id="collapse_2i" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body pa-15">{{ $t('glosario.contenido.primer_tramite.descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_3i" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.segundo_tramite.titulo') }}</a>
                                    </div>
                                    <div id="collapse_3i" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body pa-15">
                                            {{ $t('glosario.contenido.segundo_tramite.descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_4i" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.comisiones_mixtas.titulo') }}</a>
                                    </div>
                                    <div id="collapse_4i" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body pa-15">{{ $t('glosario.contenido.comisiones_mixtas.descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_5i" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.promulgacion.titulo') }}</a>
                                    </div>
                                    <div id="collapse_5i" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body pa-15">
                                            {{ $t('glosario.contenido.promulgacion.descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_6i" aria-expanded="false" :style="mode==='dark'?'color: #fff':''">{{ $t('glosario.contenido.urgencias.titulo') }}</a>
                                    </div>
                                    <div id="collapse_6i" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body pa-15">
                                            {{ $t('glosario.contenido.urgencias.descripcion') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Row -->
    </div>
    </div>
    <!-- Container -->
    </div>
    <!-- /Container -->
</template>

<script>
    import {bus} from "../main";

    export default {
        name: "Glosario",
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
