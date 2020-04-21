<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <div class="row">
                <div v-if="loadHeader" class="vld-parent">
                    <img src="../assets/img/loader2.gif" style="height: 600px; width: 1200px;" class="pl-0 ma-10 img-fluid col-12">
                </div>
                <ProposalHeader v-if="!loadHeader" :proposal="proposal"></ProposalHeader>
                <nav aria-label="breadcrumb" class="container px-0">
                    <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('propuesta.breadcumb.inicio') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="/proposals" :style="mode==='dark'?'color: #fff':''">{{ $t('propuesta.breadcumb.propuesta') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ proposal.boletin }}</li>
                    </ol>
                </nav>
                <div class="col-sm-12 hk-sec-wrapper hk-gallery-wrap" :style="mode==='dark'?'background: rgb(12, 1, 80);color: #fff':''">
                    <div class="container py-25">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 px-5">
                                <p class="text-justify">{{ proposal.detalle }}</p>
                            </div>
                            <div v-if="proposal.state" class="col-12 px-5">
                                <br>
                                <p class="text-justify">{{ proposal.argument }}</p>
                            </div>
                            <div v-if="proposal.state && proposal.video" class="col-12 px-5 mt-30">
                                <h5>{{ $t('propuesta.contenido.video') }}</h5>
                                <div class="embed-responsive embed-responsive-16by9 mt-20" v-html="proposal.video"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import axios from '../backend/axios';
    import ProposalHeader from '../components/proposals/ProposalHeader';

    export default {
        name: 'Proposal',
        components: {
            ProposalHeader,
        },
        props: {
            mode: String
        },
        data() {
            return {
                proposal: Object,
                loadHeader: true
            }
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            axios
                .get('/proposals/' + this.$route.params.id)
                .then(res => {
                    this.proposal = res.data;
                })
                .finally(() => {
                    this.loadHeader = false;
                });
        }
    }
</script>