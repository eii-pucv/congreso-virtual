<template>
    <div>
        <div class="d-md-block" :class="mode==='dark'?'dark':'light'" :style="mode==='dark'?'border-color: #fff':''">
            <div class="mt-auto">
                <div class="card mx-5" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <div class="pa-0 card-body">
                        <div class="row mx-0">
                            <div class="col-sm embed-responsive embed-responsive-4by3 img-consultation">
                                <img
                                        class="embed-responsive-item default-consultation-img"
                                        :src="getImgUrl()"
                                        style="object-fit: cover;"
                                />
                            </div>
                            <div class="col-sm pa-15">
                                <h4 class="my-auto card-title text-center ellipsis" :class="mode==='dark'?'':'text-primary'" >
                                    {{ publicConsultation.titulo }}
                                </h4>
                                <div class="row align-items-center justify-content-center mt-20">
                                    <div class="col-10">
                                        <div class="d-block font-20 text-center text-white px-0">
                                            <div v-if="isAvailableVoting" class="bg-success">
                                                {{ $t('home.componentes.consulta_card.votacion_abierta') }}
                                            </div>
                                            <div v-else class="bg-red-dark-3">
                                                {{ $t('home.componentes.consulta_card.votacion_cerrada') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center my-20">
                                    <div class="offset-1 col-5 px-0">
                                        <span class="d-block font-30 text-green"><i class="fas fa-thumbs-up"></i></span>
                                        <span class="d-block font-20">{{ $t('votos.a_favor') }}</span>
                                        <span class="d-block display-6 font-30 text-green">{{ publicConsultation.votos_a_favor }}</span>
                                    </div>
                                    <div class="col-5 px-0">
                                        <span class="d-block font-30 text-red"><i class="fas fa-thumbs-down"></i></span>
                                        <span class="d-block font-20">{{ $t('votos.en_contra') }}</span>
                                        <span class="d-block display-6 font-30 text-red">{{ publicConsultation.votos_en_contra }}</span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <router-link class="btn btn-success text-white" :to="{ path: 'public_consultation/' + publicConsultation.id }">
                                        <i class="fas fa-eye"></i> {{ $t('ver_consulta') }}
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { API_URL } from '../../backend/data_server';

    export default {
        name: 'PublicConsultationHomeCard',
        components: { },
        props: {
            publicConsultation: Object
        },
        data() {
            return {
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.publicConsultation.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.publicConsultation.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                mode: String,
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.setIsAvailableVoting();
        },
        methods: {
            setIsAvailableVoting() {
                this.isAvailableVoting = this.publicConsultation.estado === 1 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
            },
            getImgUrl() {
                if(this.publicConsultation.imagen) {
                    return (API_URL + '/api/storage/consultations/' + this.publicConsultation.id + '/' + this.publicConsultation.imagen);
                }
                return null;
            }
        },
    }
</script>

<style scoped>
    .ellipsis{
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        line-height: 26px;
        max-height: 100px;
        -webkit-box-orient: vertical;
    }

    @media (max-width: 767px) {
        .img-consultation {
            display: none;
        }
    }
</style>