<template>
    <div class="col-12 px-0">
        <div class="container px-0">
            <div class="header-img default-page-img"></div>
            <div class="top-left" style="max-width: 80%;">
                <a
                        v-for="term in page.terms"
                        :key="term.id"
                        :href="'/search?terms_id[]=' + term.id"
                        class="badge badge-pill badge-dark font-12 p-1 m-1"
                >
                    {{ term.value }}
                </a>
            </div>
            <a class="btn text-white bg-indigo-light-2 top-right mr-10 mt-10 font-20" data-toggle="modal" :data-target="'#myModal'+ page.id">
                <span class="btn-text"><font-awesome-icon icon="share-square"/></span>
            </a>
            <h5 class="col-12 hk-sec-title bottom text-white pb-20">
                <p class="col-12">
                    {{ page.title }}
                </p>
                <div class="col-12 mt-15">
                    <p class="text-center"><i class="fa fa-calendar font-18 mr-10"></i>
                        {{ $t('pagina.componentes.header.fecha_publicacion') }}: {{ new Date(page.created_at) | moment("D MMM YYYY") }}
                    </p>
                </div>
            </h5>
        </div>
        <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''">
            <div class="d-block font-20 text-center text-white" style="padding:0px">
                <div class="bg-indigo-light-1">
                    {{ $t('pagina.componentes.header.titulo') }}
                    <span>
                        <v-popover>
                            <fontAwesomeIcon class="tooltip-target b3 font-18" icon="question-circle"></fontAwesomeIcon>
                            <template slot="popover">
                                <p>
                                    {{ $t('pagina.componentes.header.popover') }}
                                </p>
                            </template>
                        </v-popover>
                    </span>
                </div>
            </div>
        </div>
        <div class="modal" :id="'myModal'+ page.id">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('compartir') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span><i class="fa fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <social-sharing :url="APP_URL + '/page/' + page.slug"
                            :title="'Congreso Virtual: ' + page.title"
                            :description="page.title"
                            :quote="page.title"
                            hashtags="congresovirtual"
                            inline-template
                        >
                            <div>
                                <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                    <i class="fa fa-envelope"></i> Email
                                </network>
                                <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                    <span class="fa fa-facebook"></span> Facebook
                                </network>
                                <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                    <i class="fa fa-linkedin"></i> LinkedIn
                                </network>
                                <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                    <i class="fa fa-twitter"></i> Twitter
                                </network>
                                <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </network>
                            </div>
                        </social-sharing>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $t('cerrar') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { APP_URL } from '../../data/globals';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import SocialSharing from 'vue-social-sharing';

export default {
    name: 'PageHeader',
    props: {
        page: Object
    },
    components: {
        FontAwesomeIcon,
        SocialSharing
    },
    data() {
        return {
            mode: String,
            APP_URL
        }
    },
    mounted() {
        if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
            this.mode = 'dark';
        } else {
            this.mode = 'light';
        }
    }
}
</script>