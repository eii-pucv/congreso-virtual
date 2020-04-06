<template>
    <div class="container">
        <div class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h2 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.votos.titulo') }}</h2>
            <div class="mt-20 vld-parent">
                <div v-if="loadVotes" style="height: 300px;">
                    <Loading
                            :active.sync="loadVotes"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadVotes">
                    <div v-for="vote in votes" :key="'vote-' + vote.id" class="list-group list-group-flush">
                        <router-link v-if="vote.project" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + vote.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ vote.project.titulo }}"
                                    </span>
                                    <span class="d-block text-grey">
                                        {{ new Date(toLocalDatetime(vote.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </div>
                            </div>
                        </router-link>
                        <router-link v-else-if="vote.idea" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + vote.idea.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ vote.idea.titulo }}" {{ $t('perfil_usuario.componentes.votos.del_proyecto') }} "{{ vote.idea.project.titulo }}"
                                    </span>
                                    <span class="d-block text-grey">
                                        {{ new Date(toLocalDatetime(vote.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </div>
                            </div>
                        </router-link>
                        <router-link v-else-if="vote.article" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + vote.article.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ vote.article.titulo }}" {{ $t('perfil_usuario.componentes.votos.del_proyecto') }} "{{ vote.article.project.titulo }}"
                                    </span>
                                    <span class="d-block text-grey">
                                        {{ new Date(toLocalDatetime(vote.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </div>
                            </div>
                        </router-link>
                        <router-link v-else-if="vote.public_consultation" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/public_consultation/' + vote.public_consultation_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ vote.public_consultation.titulo }}"
                                    </span>
                                    <span class="d-block text-grey">
                                        {{ new Date(toLocalDatetime(vote.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 text-red"><i class="fas fa-thumbs-down"></i></span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                    <div v-if="totalResults > votes.length">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.votos.cargar') }}
                            <Loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="false"
                                    :height="24"
                            ></Loading>
                        </button>
                    </div>
                    <h6
                            v-if="totalResults === 0 && !loadBtnLoadMore"
                            class="text-center"
                            :style="mode === 'dark' ? 'color: #fff' : ''"
                    >
                        {{ $t('perfil_usuario.componentes.votos.no_hay_votos') }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'UserVotes',
        components: {
            Loading
        },
        props: {
            user_id: Number
        },
        data() {
            return {
                votes: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                loadVotes: true,
                loadBtnLoadMore: false,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted(){
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getVotes();
        },
        methods: {
            getVotes() {
                axios
                    .get('/users/' + this.user_id + '/votes', {
                        params: {
                            has: ['project', 'article', 'idea', 'publicConsultation'],
                            order: 'DESC',
                            order_by: 'updated_at',
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results;
                        this.votes = this.votes.concat(res.data.results);
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadVotes = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getVotes();
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>