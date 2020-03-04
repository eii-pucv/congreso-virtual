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
            <div class="row mx-0 text-center my-5">
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-24 text-green"><i class="fa fa-thumbs-up"></i></span>
                    <span class="d-block font-14">{{ $t('votos.a_favor') }}</span>
                    <span class="d-block display-6 text-green">{{ project.votos_a_favor }}</span>
                </div>
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-24 text-red"><i class="fa fa-thumbs-down"></i></span>
                    <span class="d-block font-14">{{ $t('votos.en_contra') }}</span>
                    <span class="d-block display-6 text-red">{{ project.votos_en_contra }}</span>
                </div>
                <div class="col-4 px-0" style="display: inline-block;">
                    <span class="d-block font-24"><font-awesome-icon icon="minus-circle"/></span>
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
                    <span class="btn-text"><font-awesome-icon icon="share-square"/></span>
                </a>
                <div v-if="project.terms.length > 0" class="top-left">
                    <a :href="'/projects?terms_id[]=' + project.terms[0].id" class="badge badge-pill badge-dark font-12 m-1">{{ project.terms[0].value }}</a>
                </div>
            </div>
            <div id="project-name" class="card-body font-16 font-weight-bold overflow-auto px-10 py-10 custom-scrollbar-wk custom-scrollbar-mz">{{ project.titulo }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-center" :style="mode==='dark'?'background: #080035':''">
                    <span><i class="fa fa-clock font-20 mr-10"></i><span class="font-18">{{ $t('tiempo') }}</span></span><span class="text-right"></span>
                    <Countdown v-if="isAvailableVoting" class="pt-10 font-18" :date="votingEndDate"></Countdown>
                    <Countdown v-else class="pt-10 text-red font-18" :date="votingEndDate"></Countdown>
                </li>
            </ul>
            <div class="btn-group-vertical btn-block mt-auto">
                <a class="font-1"></a>
                <div class="btn-group">
                    <router-link :to="{ path: 'project/' + project.id }" class="btn btn-success text-white">
                        <i class="fa fa-users"></i><span class="btn-text"> {{ $t('home.componentes.carrusel_proyectos_card.proyectos_card.participar') }}</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Countdown from './Countdown';
    import { API_URL } from '../../backend/data_server';
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    import { library } from '@fortawesome/fontawesome-svg-core';
    import { faShareSquare } from '@fortawesome/free-solid-svg-icons/faShareSquare';
    import { faMinusCircle } from '@fortawesome/free-solid-svg-icons/faMinusCircle';

    library.add(faShareSquare,faMinusCircle);
    export default {
        name: 'ProjectHomeCard',
        components: {
            FontAwesomeIcon,
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
