<template>
    <div style="min-height: 820px;" class="vld-parent" :class="mode==='dark'?'dark':'light'">
        <div v-if="loadConfirmation" style="height: 300px;">
            <Loading
                    :active.sync="loadConfirmation"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="color"
            ></Loading>
        </div>
        <section v-if="!loadConfirmation">
            <div v-if="confirmation" class="ma-80 text-center">
                <img src="../assets/img/success-icon.png" height="200" width="200">
                <h1 class="display-3">{{ $t('confirmacion.contenido.usuario') }}</h1>
                <p class="lead mt-20" :class="mode==='dark'?'text-white':'text-primary '">
                    {{ $t('confirmacion.contenido.ahora') }} <a href="/login"><strong>{{ $t('confirmacion.contenido.enlace') }}</strong></a>
                </p>
                <router-link :to="{ path: '/' }" class="btn btn-primary btn-sm text-uppercase mt-30">{{ $t('confirmacion.contenido.inicio') }}</router-link>
            </div>
            <div v-else class="ma-80 text-center">
                <img src="../assets/img/fail-icon.png" height="200" width="200">
                <h1 class="display-3">{{ $t('confirmacion.contenido.error') }}</h1>
                <p class="lead mt-20" :class="mode==='dark'?'text-white':'text-primary '">
                    <strong>{{ $t('confirmacion.contenido.pasos') }}</strong> {{ $t('confirmacion.contenido.intentar') }}
                </p>
                <router-link :to="{ path: '/' }" class="btn btn-primary btn-sm text-uppercase mt-30">{{ $t('confirmacion.contenido.inicio') }}</router-link>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from '../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'UserConfirmation',
        components: {
            Loading
        },
        data() {
            return {
                confirmation: null,
                loadConfirmation: true,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            axios
                .get('/auth/signup/activate/' + this.$route.query.token)
                .then(() => {
                    this.confirmation = true;
                })
                .catch(() => {
                    this.confirmation = false;
                })
                .finally(() => {
                    this.loadConfirmation = false;
                });
        }
    };
</script>