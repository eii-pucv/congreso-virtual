<template>
    <div  style="min-height: 720px;" :class="mode==='dark'?'dark':'light'">
        <section>
            <div class="ma-80 text-center" v-if="this.confirmation == 1">
                <img src="../../assets/img/success-icon.png" height="200" width="200">
                <h1 class="display-3">{{ $t('confirmacion.contenido.usuario') }}</h1>
                <p class="lead" :class="mode==='dark'?'text-white':'text-primary '">{{ $t('confirmacion.contenido.ahora') }} <a href="/login"><strong>{{ $t('confirmacion.contenido.enlace') }}</strong></a></p>
                <hr>
                <p class="lead">
                    <a class="btn btn-primary btn-sm text-uppercase" href="/" role="button">{{ $t('confirmacion.contenido.inicio') }}</a>
                </p>
            </div>

            <div class="ma-80 text-center" v-if="this.confirmation == 0">
                <img src="../../assets/img/fail-icon.png" height="200" width="200">
                <h1 class="display-3">{{ $t('confirmacion.contenido.error') }}</h1>
                <p class="lead" :class="mode==='dark'?'text-white':'text-primary '"><strong>{{ $t('confirmacion.contenido.pasos') }}</strong> {{ $t('confirmacion.contenido.intentar') }}</p>
                <hr>
                <p class="lead">
                    <a class="btn btn-primary btn-sm text-uppercase" href="/" role="button">{{ $t('confirmacion.contenido.inicio') }}</a>
                </p>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from "../../backend/axios";
    export default {
        name: "confirmation",
        props: {
            mode: String
        },
        methods: $("#myTab a").on("click", function(e) {
            e.preventDefault();
            $(this).tab("show");
        }),
        data() {
            return {
                confirmation: null
            };
        },
        mounted() {

            if (this.$store.getters.modo_oscuro == "dark") {
                this.mode = "dark"
            } else if (!this.mode) {
                this.mode = "light"
            }

            console.log(this.$route.query.token);
            axios
                .get("/auth/signup/activate/" + this.$route.query.token)
                .then(res => {
                    console.log("RESULTADO " + JSON.stringify(res.data, null, 4));
                    this.confirmation = 1;
                })
                .catch(() => {
                    console.error("FAIL")
                    this.confirmation = 0;
                });
        }
    };
</script>