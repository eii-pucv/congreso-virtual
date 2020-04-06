<template>
    <div class="container">
        <div class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h2 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.comentarios.titulo') }}</h2>
            <div class="mt-20 vld-parent">
                <div v-if="loadComments" style="height: 300px;">
                    <Loading
                            :active.sync="loadComments"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadComments">
                    <div v-for="comment in comments" :key="'comment-' + comment.id" class="list-group list-group-flush">
                        <router-link v-if="comment.project" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + comment.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ comment.project.titulo }}"
                                    </span>
                                    <blockqoute class="d-block blockquote my-10">
                                        {{ comment.body }}
                                        <footer class="text-grey small text-right">
                                            {{ new Date(toLocalDatetime(comment.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                        </footer>
                                    </blockqoute>
                                </div>
                            </div>
                            <div class="float-right">
                                <span class="text-success mr-15"><i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                                <span class="text-danger"> <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                            </div>
                        </router-link>
                        <router-link v-else-if="comment.idea" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + comment.idea.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ comment.idea.titulo }}" {{ $t('perfil_usuario.componentes.comentarios.del_proyecto') }} "{{ comment.idea.project.titulo }}"
                                    </span>
                                    <blockqoute class="d-block blockquote my-10">
                                        {{ comment.body }}
                                        <footer class="text-grey small text-right">
                                            {{ new Date(toLocalDatetime(comment.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                        </footer>
                                    </blockqoute>
                                </div>
                            </div>
                            <div class="float-right">
                                <span class="text-success mr-15"><i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                                <span class="text-danger"> <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                            </div>
                        </router-link>
                        <router-link v-else-if="comment.article" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/project/' + comment.article.project_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ comment.article.titulo }}" {{ $t('perfil_usuario.componentes.comentarios.del_proyecto') }} "{{ comment.article.project.titulo }}"
                                    </span>
                                    <blockqoute class="d-block blockquote my-10">
                                        {{ comment.body }}
                                        <footer class="text-grey small text-right">
                                            {{ new Date(toLocalDatetime(comment.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                        </footer>
                                    </blockqoute>
                                </div>
                            </div>
                            <div class="float-right">
                                <span class="text-success mr-15"><i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                                <span class="text-danger"> <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                            </div>
                        </router-link>
                        <router-link v-if="comment.public_consultation" class="list-group-item list-group-item-action border border-primary rounded mb-2" :to="{ path: '/public_consultation/' + comment.public_consultation_id }">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block">
                                        "{{ comment.public_consultation.titulo }}"
                                    </span>
                                    <blockqoute class="d-block blockquote my-10">
                                        {{ comment.body }}
                                        <footer class="text-grey small text-right">
                                            {{ new Date(toLocalDatetime(comment.updated_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}
                                        </footer>
                                    </blockqoute>
                                </div>
                            </div>
                            <div class="float-right">
                                <span class="text-success mr-15"><i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                                <span class="text-danger"> <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                            </div>
                        </router-link>
                    </div>
                    <div v-if="totalResults > comments.length">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.comentarios.cargar') }}
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
                        {{ $t('perfil_usuario.componentes.comentarios.no_hay_comentarios') }}
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
        name: 'UserComments',
        components: {
            Loading
        },
        props: {
            user_id: Number
        },
        data() {
            return {
                comments: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                loadComments: true,
                loadBtnLoadMore: false,
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

            this.getComments();
        },
        methods: {
            getComments() {
                axios
                    .get('/users/' + this.user_id + '/comments', {
                        params: {
                            state: 0,
                            has: ['project', 'article', 'idea', 'publicConsultation'],
                            order: 'DESC',
                            order_by: 'updated_at',
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results;
                        this.comments = this.comments.concat(res.data.results);
                        this.offset += res.data.returned_results;
                    })
                    .finally(() => {
                        this.loadComments = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getComments();
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            }
        }
    }
</script>