<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row">
                <div v-if="loadPublicConsultation" class="vld-parent">
                    <img src="../assets/img/loader2.gif" style="height:600px;width:1200px" class="pl-0 ma-10 img-fluid col-12">
                </div>
                <PublicConsultationHeader v-if="!loadPublicConsultation" :publicConsultation="publicConsultation"></PublicConsultationHeader>
                <nav aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item"><a href="/" :style="mode==='dark'?'color: #fff':''">{{ $t('consulta.breadcumb.inicio') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="/public_consultations" :style="mode==='dark'?'color: #fff':''">{{ $t('consulta.breadcumb.proyectos') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('consulta.breadcumb.consulta') }} {{ publicConsultation.id }}</li>
                    </ol>
                </nav>
                <div class="col-sm-12 hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);color: #fff':''">
                    <ul class="nav nav-light nav-tabs" role="tablist">
                        <li class="nav-item active">
                            <a
                                id="detail-tab"
                                data-toggle="tab"
                                href="#detail"
                                role="tab"
                                aria-controls="detail"
                                aria-selected="true"
                                class="nav-link active"
                                :style="mode==='dark'?'color: #fff':''"
                            >
                                {{ $t('consulta.contenido.detalle') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                    id="comments-tab"
                                    data-toggle="tab"
                                    href="#comments"
                                    role="tab"
                                    aria-controls="comments"
                                    aria-selected="true"
                                    class="nav-link"
                                    :style="mode==='dark'?'color: #fff':''"
                            >
                                {{ $t('consulta.contenido.comentarios') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-25">
                        <div
                                id="detail"
                                class="tab-pane fade show active"
                                role="tabpanel"
                                aria-labelledby="detail-tab"
                        >
                            <div class="container">
                                <div class="row">
                                    <a class="col-12 px-5">{{ publicConsultation.resumen }}</a>
                                    <div class="col-12 px-5 my-10" v-html="publicConsultation.detalle"></div>
                                    <div v-if="publicConsultation.video" class="col-12 px-5 mt-30">
                                        <h5>{{ $t('consulta.contenido.video') }}</h5>
                                        <div class="embed-responsive embed-responsive-16by9 mt-20" v-html="publicConsultation.video"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                                id="comments"
                                class="tab-pane fade show"
                                role="tabpanel"
                                aria-labelledby="comments-tab"
                        >
                            <PublicConsultationComments v-if="publicConsultation.id" :publicConsultation="publicConsultation"></PublicConsultationComments>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PublicConsultationHeader from '../components/public_consultations/PublicConsultationHeader';
    import PublicConsultationComments from './Comments';
    import axios from '../backend/axios';

    export default {
        name: 'PublicConsultation',
        components: {
            PublicConsultationComments,
            PublicConsultationHeader
        },
        props: {
            public_consultation_id: Number
        },
        data() {
            return {
                publicConsultation: Object,
                loadPublicConsultation: true,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getPublicConsultation();
        },
        methods: {
            getPublicConsultation() {
                axios
                    .get('/consultations/' + this.public_consultation_id)
                    .then(res => {
                        this.publicConsultation = res.data;
                    })
                    .finally(() => {
                        this.loadPublicConsultation = false;
                    });
            }
        }
    }
</script>