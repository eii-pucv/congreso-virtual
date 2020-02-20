<template>
    <div class="row row-cols-1 row-cols-md-2 ma-5">
        <PublicConsultationsCards
                v-for="publicConsultation in publicConsultations"
                :key="publicConsultation.id"
                :publicConsultation="publicConsultation"
        ></PublicConsultationsCards>
    </div>
</template>

<script>
    import PublicConsultationsCards from './PublicConsultationsCards';
    import axios from '../../backend/axios';

    export default {
        name: 'PublicConsultationsList',
        components: {
            PublicConsultationsCards
        },
        props: {
            arrayPreferencias: Array,
            terms: Array
        },
        data() {
            return {
                publicConsultations: []
            };
        },
        mounted() {
            axios
                .get('/consultations')
                .then(res => {
                    this.publicConsultations = res.data.results;
                })
                .catch(() => this.loginFailed());
        },
        watch: {
            arrayPreferencias: function(newVal) {
                let i = 0;
                let body = {
                    order_by: String,
                    order: String,
                    limit: Number,
                    terms: Array,
                    titulo: String,
                    detalle: String,
                    resumen: String,
                    postulante: String,
                    votos_a_favor: Number,
                    estado: Number,
                    fecha_inicio: Date,
                    fecha_termino: Date,
                    votos_en_contra: Number
                };

                Object.keys(body).forEach(function(key) {
                    if (newVal[i] !== null || newVal[i] !== undefined) {
                        if (i === 2 || i === 8 || i === 9 || i === 12)
                            body[key] = Number(newVal[i]);
                        else body[key] = newVal[i];
                    }
                    i++;
                });

                axios
                    .post("/consultations/1", body)
                    .then(res => {
                        this.publicConsultations = res.data;
                    })
                    .catch(() => this.loginFailed());
            },
            terms: function() {
                let i = 0;
                let body = {
                    order_by: String, //0
                    order: String, //1
                    limit: Number, //2
                    terms: Array, //3
                    titulo: String, //4
                    detalle: String, //5
                    resumen: String, //6
                    postulante: String, //7
                    votos_a_favor: Number, //8
                    estado: Number, //9
                    fecha_inicio: Date, //10
                    fecha_termino: Date, //11
                    votos_en_contra: Number //12
                };

                Object.keys(body).forEach(key => {
                    if (
                        this.arrayPreferencias[i] !== null ||
                        this.arrayPreferencias[i] !== undefined
                    ) {
                        if (i === 2 || i === 8 || i === 9 || i === 12)
                            body[key] = Number(this.arrayPreferencias[i]);
                        else body[key] = this.arrayPreferencias[i];
                    }
                    i++;
                });

                axios
                    .post("/consultations/1", body)
                    .then(res => {
                        this.publicConsultations = res.data;
                    })
                    .catch(() => this.loginFailed());
            }
        }
    };
</script>

<style scoped></style>
