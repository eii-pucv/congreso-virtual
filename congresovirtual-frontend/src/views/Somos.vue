<template>
    <div class="px-0" style="min-height: 720px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
        <nav aria-label="breadcrumb" class="container pt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <li class="breadcrumb-item" ><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('somos.breadcumb.inicio') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('somos.breadcumb.somos') }}</li>
            </ol>
        </nav>
        <div class="container mt-3">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''">
                        <div class="card-body mb-30 text-justify" :style="mode==='dark'?'color: #fff':''">
                            <h2 class="hk-sec-title text-center mb-40" :style="mode==='dark'?'color: #fff':''">{{ $t('somos.titulo') }}</h2>
                            {{ $t('somos.contenido.parrafo1') }}<br><br>
                            {{ $t('somos.contenido.parrafo2') }}
                            <a class="font-weight-bold" href="https://www.camara.cl/camara/media/bicameral/bicameral.html" target="_blank">{{ $t('somos.contenido.link_parrafo2') }}</a>{{ $t('somos.contenido.parrafo2_2') }}
                            <br><br>
                            {{ $t('somos.contenido.parrafo3') }}<br><br>
                            {{ $t('somos.contenido.parrafo4') }}<br><br>
                            {{ $t('somos.contenido.parrafo5') }}<a class="font-weight-bold" href="https://www.camara.cl/camara/camara_diputados.aspx" target="_blank">{{ $t('somos.contenido.link_parrafo5') }}</a>{{ $t('somos.contenido.y') }}<a class="font-weight-bold" href="https://www.senado.cl/funciones-del-senado/senado/2012-11-07/100615.html" target="_blank">{{ $t('somos.contenido.link2_parrafo5') }}</a>.<br><br>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { bus } from "../main"
    
    export default {
        name: "Somos",
        props: {
            mode: String,
        },
        data(){
            return{
                
            };
        },
        methods:{
            
        },
        mounted() {

            if (this.$store.getters.modo_oscuro == "dark") {
                this.mode = "dark"
            } else if (!this.mode) {
                this.mode = "light"
            }

        }
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

.jumbotron {
    position: relative;
    padding: 4.3rem 0;
    margin: 0;
    border-radius: 0;
    color: #fff;
    text-align: center;
}

.jumbotron, .jumbotron.jumbotron-pro, .theme-pro .jumbotron {
    background-color: #092C78;
}

.jumbotron .h1, .jumbotron h1 {
    margin-top: 0;
    color: white;
}

</style>
