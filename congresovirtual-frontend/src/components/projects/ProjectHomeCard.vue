<template>
    <div>
        <div class="card card-profile-feed col-12 px-0" :style="mode==='dark'?'background: #080035; color: #fff; border-color: #fff':''">
            <div
                    v-if="project.etapa === 1 && isAvailableVoting"
                    class="bg-indigo-light-1 card-header d-block font-12 text-center text-white"
            >
                {{ $t('votacion_general') }}
            </div>
            <div
                    v-else-if="project.etapa === 2 && isAvailableVoting"
                    class="bg-green card-header d-block font-12 text-center text-white"
            >
                {{ $t('votacion_particular') }}
            </div>
            <div
                    v-else
                    class="bg-red-dark-3 card-header d-block font-12 text-center text-white"
            >
                {{ $t('votacion_cerrada') }}
            </div>
            <div class="row mx-0 text-center">
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-20"><i class="fas fa-thumbs-up"></i></span>
                    <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                    <span class="d-block display-6">{{ project.votos_a_favor }}</span>
                </div>
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-20"><i class="fas fa-thumbs-down"></i></span>
                    <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                    <span class="d-block display-6">{{ project.votos_en_contra }}</span>
                </div>
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-20"><i class="fas fa-minus-circle"></i></span>
                    <span class="d-block font-14">{{ $t('votos.abstencion') }}</span>
                    <span class="d-block display-6">{{ project.abstencion }}</span>
                </div>
            </div>
            <div class="container embed-responsive embed-responsive-16by9">
                <img
                        class="embed-responsive-item default-project-img"
                        :src="getImgUrl()"
                        style="object-fit: cover;"
                />
                <a class="btn text-white bg-indigo-light-2 top-right mt-5" data-toggle="modal" :data-target="'#myModal' + project.id">
                    <i class="fas fa-share-square"></i>
                </a>
                <div v-if="project.terms.length > 0" class="top-left">
                    <router-link
                            class="badge badge-dark font-12 m-1"
                            :to="{ path: '/projects', query: { 'terms_id[]': project.terms[0].id } }"
                    >
                        {{ project.terms[0].value }}
                    </router-link>
                </div>
            </div>
            <div id="project-name" class="card-body font-16 font-weight-bold overflow-auto px-10 py-10 custom-scrollbar-wk custom-scrollbar-mz text-justify" style="line-height:1.3">{{ project.titulo }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-0 px-2 py-3" :style="mode==='dark'?'background: #080035':''">
                    <span class="d-flex">
                        <i class="far fa-clock font-16 mr-5"></i><span class="font-14" style="line-height:1">{{ $t('tiempo') }}</span>
                        <Countdown v-if="isAvailableVoting" class="font-14" :date="votingEndDate"></Countdown>
                        <Countdown v-else class="text-red font-14" :date="votingEndDate"></Countdown>
                    </span>
                </li>
            </ul>
            <div class="btn-group-vertical btn-block mt-auto">
                <a class="font-1"></a>
                <div class="btn-group">
                    <router-link class="btn btn-success text-white" :to="{ path: 'project/' + project.id }">
                        <i class="fas fa-users"></i> {{ $t('home.componentes.carrusel_proyectos_card.proyectos_card.participar') }}
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Countdown from './ProjectHomeCardCountdown';
    import { API_URL } from '../../backend/data_server';

    export default {
        name: 'ProjectHomeCard',
        components: {
            Countdown
        },
        props: {
            project: Object
        },
        data() {
            return {
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local(),
                isAvailableVoting: false,
                mode: String
            };
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
                this.isAvailableVoting = this.project.is_enabled && this.project.etapa !== 3 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
            },
            getImgUrl() {
                if(this.project.imagen) {
                    return API_URL + '/api/storage/projects/' + this.project.id + '/' + this.project.imagen;
                }
                return null;
            }
        },
    }
</script>

<style scoped>
    .container {
        position: relative;
        text-align: center;
        color: white;
    }

    .top-left {
        position: absolute;
        top: 8px;
        left: 16px;
    }

    .top-right {
        position: absolute;
        top: 8px;
        right: 16px;
    }

    #project-name {
        height: 100px;
    }
</style>
