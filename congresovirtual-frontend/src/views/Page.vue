<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row">
                <div v-if="loadPage" class="vld-parent">
                    <img src="../assets/img/loader2.gif" style="height: 600px; width: 1200px;" class="pl-0 ma-10 img-fluid col-12">
                </div>
                <PageHeader v-if="!loadPage" :page="page"></PageHeader>
                <nav aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('pagina.breadcumb.inicio') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="/pages" :style="mode==='dark'?'color: #fff':''">{{ $t('pagina.breadcumb.paginas') }}</a></li>
                        <li v-if="page" class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('pagina.breadcumb.pagina') }} {{ page.id }}</li>
                    </ol>
                </nav>
                <div v-if="!loadPage" class="col-sm-12 hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);color: #fff':''">
                    <div class="container py-25">
                        <div class="row">
                            <div v-if="page" class="col-12 px-5" v-html="page.content">
                            </div>
                            <div v-else class="col-12 text-center">
                                <h1 class="mb-3">{{ $t('pagina.contenido.no_encontrada.mensaje1') }}</h1>
                                <p class="font-weight-light">{{ $t('pagina.contenido.no_encontrada.mensaje2') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PageHeader from "../components/pages/PageHeader";
    import axios from "../backend/axios";

    export default {
        name: 'Page',
        components: {
            PageHeader,
        },
        props: {
            slug: String,
            mode: String
        },
        data() {
            return {
                page: Object,
                loadPage: true
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getPage();
        },
        methods: {
            getPage() {
                axios
                    .get('pages', {
                        params: {
                            slug: this.slug
                        }
                    })
                    .then(res => {
                        this.page = res.data.results[0];
                    })
                    .finally(() => {
                        this.loadPage = false;
                    });
            }
        }
    }
</script>

<style scoped>
    .arrow-steps .step {
        font-size: 14px;
        cursor: default;
        padding: 10px 10px 10px 30px;
        float: left;
        position: relative;
        background-color: #d9e3f7;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        transition: background-color 0.2s ease;
    }

    .arrow-steps .step:after,
    .arrow-steps .step:before {
        content: " ";
        position: absolute;
        top: 0;
        right: -16px;
        width: 0;
        height: 0;
        border-top: 25px solid transparent;
        border-bottom: 25px solid transparent;
        border-left: 17px solid #9e9e9e!important;
        z-index: 2;
        transition: border-color 0.2s ease;
    }

    .arrow-steps .step:before {
        right: auto;
        left: 0;
        border-left: 17px solid #fff;
        z-index: 0;
    }

    .arrow-steps .step:first-child:before {
        border: none;
    }

    .arrow-steps .step:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .arrow-steps .step span {
        position: relative;
    }

    .arrow-steps .step span:before {
        opacity: 0;
        content: "✔";
        position: absolute;
        top: -2px;
        left: -20px;
    }

    .arrow-steps .step.done span:before {
        opacity: 1;
        -webkit-transition: opacity 0.3s ease 0.5s;
        -moz-transition: opacity 0.3s ease 0.5s;
        -ms-transition: opacity 0.3s ease 0.5s;
        transition: opacity 0.3s ease 0.5s;
    }

    .arrow-steps .step.current {
        color: #fff;
        background-color: green !important;
    }

    .arrow-steps .step.current:after {
        border-left: 17px solid  green !important;
    }

    .tab-content {
        -webkit-box-shadow: inherit !important;
        box-shadow: inherit !important;
    }
</style>