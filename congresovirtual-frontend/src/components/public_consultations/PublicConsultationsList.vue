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
                });
        }
    }
</script>