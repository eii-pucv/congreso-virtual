<template>
    <div :class="mode==='dark'?'dark':'light'">
        <section>
            <ProjectsCarousel></ProjectsCarousel>
        </section>
        <nav aria-label="breadcrumb" class="container mt-20">
            <ol class="breadcrumb" :style="mode==='dark'?'background: #0c0050; color: #fff':''">
                <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('home.breadcumb') }}</li>
            </ol>
        </nav>
        <section class="container mt-30">
            <h2 class="pb-4 text-center mx-5" :style="mode==='dark'?'color: #fff':''" :class="mode==='dark'?'':'text-primary '">{{ $t('home.contenido.proyectos_ley') }}</h2>
            <ProjectsHomeList></ProjectsHomeList>
        </section>
        <section class="container mt-30">
            <h2 class="pb-4 text-center mx-5" :style="mode==='dark'?'color: #fff':''" :class="mode==='dark'?'':'text-primary '">{{ $t('home.contenido.consultas_publicas') }}</h2>
            <div class="col-12">
                <div class="px-20">
                    <PublicConsultationsHomeList></PublicConsultationsHomeList>
                </div>
            </div>
        </section>
        <section class="container mt-30">
            <ProposalsHomeList></ProposalsHomeList>
        </section>
    </div>
</template>

<script>
    import ProjectsCarousel from '../components/projects/ProjectsCarousel';
    import ProjectsHomeList from '../components/projects/ProjectsHomeList';
    import PublicConsultationsHomeList from '../components/public_consultations/PublicConsultationsHomeList';
    import ProposalsHomeList from '../components/proposals/ProposalsHomeList';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'Home',
        components: {
            ProjectsCarousel,
            ProjectsHomeList,
            PublicConsultationsHomeList,
            ProposalsHomeList,
            Loading
        },
        data() {
            return {
                mode: String
            };
        },
        mounted() {
            if ((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            if(this.$route.query.access_token) {
                let accessToken = this.$route.query.access_token;
                this.$store.dispatch('loginRrss', accessToken)
                    .then(() => {
                        this.$toastr('success', this.$t('login.contenido.mensajes.exito.generico.cuerpo'), this.$t('login.contenido.mensajes.exito.generico.titulo'));
                    })
                    .catch(() => {
                        this.$toastr('error', this.$t('login.contenido.mensajes.fallido.generico_rrss.cuerpo'), this.$t('login.contenido.mensajes.fallido.generico_rrss.titulo'));
                    });
            }
        }
    }
</script>

<style>
    .marketing .col-lg-4 {
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .marketing h2 {
        font-weight: normal;
    }
    .marketing .col-lg-4 p {
        margin-right: .75rem;
        margin-left: .75rem;
    }
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }
    .light {
        color: #000;
        background: #fff;
    }
</style>
