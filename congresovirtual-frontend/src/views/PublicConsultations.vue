<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row mt-20">
                <div class="hk-sec-wrapper" :class="mode==='dark'?'dark':'light'">
                    <h5 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">
                        {{ $t('consultas_publicas.contenido.titulo') }}
                    </h5>
                    <div class="row">
                        <p class="card-body text-justify">
                            {{ $t('consultas_publicas.contenido.descripcion.parrafo1') }}
                            <br><br>
                            {{ $t('consultas_publicas.contenido.descripcion.parrafo2') }}
                        </p>
                    </div>
                </div>
                <nav aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.breadcumb.inicio') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.breadcumb.consultas') }}</li>
                    </ol>
                </nav>
                <div class="mb-20">
                    <h5 class="hk-sec-title text-center mb-20" :style="mode==='dark'?'color: #fff':''">{{ $t('consultas_publicas.breadcumb.consultas') }}</h5>
                    <PublicConsultationsList></PublicConsultationsList>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PublicConsultationsList from '../components/public_consultations/PublicConsultationsList';

    export default {
        name: 'PublicConsultations',
        props: {
            mode: String
        },
        components: {
            PublicConsultationsList
        },
        data() {
            return { };
        },
        methods: { },
        mounted() {
            if(this.$store.getters.modo_oscuro === 'dark') {
                this.mode = 'dark';
            } else if (!this.mode) {
                this.mode = 'light';
            }
        },
    };
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