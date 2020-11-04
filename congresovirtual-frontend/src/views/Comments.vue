<template>
    <div>
        <div class="row">
            <div class="col-12 p-0">
                <div class="form-row align-content-center justify-content-center">
                    <div class="col-11">
                        <label class="font-weight-bold" :style="mode==='dark'?'color: #fff':''">{{ $t('componentes.comentarios.buscar') }}</label>
                        <div class="input-group has-feedback">
                            <input
                                    v-model="query"
                                    v-on:keyup.enter="search"
                                    type="text"
                                    class="form-control"
                                    :placeholder="$t('componentes.comentarios.buscar_placeholder')"
                            >
                            <div v-if="query !== ''" class="input-group-append">
                                <button @click="query = ''" class="btn btn-secondary"><i class="fas fa-times-circle"></i></button>
                            </div>
                            <div class="input-group-append"> 
                                <button @click="search" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row align-content-center justify-content-center mt-3">
                    <div class="col-11">
                        <label class="font-weight-bold" :style="mode==='dark'?'color: #fff':''">{{ $t('componentes.comentarios.orden.titulo') }}</label>
                        <select @change="sort" v-model="selectedSortId" class="form-control custom-select d-block">
                            <option
                                    v-for="optionSort in optionsSort"
                                    :key="'option-sort-' + optionsSort.id"
                                    :value="optionSort.id"
                            >
                                {{ optionSort.label }}
                            </option>
                        </select>
                    </div>
                </div>
                <div v-if="loadComments" class="vld-parent pa-50" style="height: 500px;">
                    <loading
                        :active.sync="loadComments"
                        :is-full-page="fullPage"
                        :background-color="mode==='dark'?'#0c0050':''"
                        :color="mode==='dark'?'#fff':''"
                        :loader="loaderType"
                    ></loading>
                </div>
                <div v-else-if="comments.length === 0 && !loadComments" class="text-center my-20">{{ $t('componentes.comentarios.no_hay_comentarios') }}</div>
                <ul v-else class="col-12 list-unstyled my-20 custom-scrollbar-wk custom-scrollbar-mz" style="max-height: 1000px; overflow: auto;">
                    <li
                            v-for="comment in comments"
                            :key="comment.id"
                            class="media pa-20 border border-2 border-light col-12 mb-5 mt-10 custom-scrollbar-wk custom-scrollbar-mz"
                            style="overflow: auto;"
                    >
                        <div class="media-body">
                            <div class="row">
                                <div class="col-9 mb-2">
                                    <router-link v-if="comment.user && comment.user.username" class="h6" :to="{ path: '/user', query: { 'username': comment.user.username } }">
                                        {{ comment.user.username }}<br>
                                    </router-link>
                                    <router-link v-else-if="comment.user && !comment.user.username" class="h6" :to="{ path: '/user', query: { 'user_id': comment.user.id } }">
                                        {{ comment.user.name }} {{ comment.user.surname }}<br>
                                    </router-link>
                                    <h6 v-else>{{ $t('componentes.comentarios.no_identificado') }}</h6>
                                    <small class="text-grey">{{ new Date(toLocalDatetime(comment.created_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                                </div>
                                <div v-if="comment.user && comment.user.id === userId" class="col-3">
                                    <div class="btn-group float-right">
                                        <button @click="edit(comment)" class="btn btn-sm btn-primary text-white">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="dlt(comment.id)" class="btn btn-sm btn-danger text-white">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="comment-body custom-scrollbar-wk custom-scrollbar-mz">{{ comment.body }}</p>
                            <div v-if="comment.files.length > 0" class="mt-2">
                                <div v-for="(file, index) in comment.files" :key="file.id">
                                    <div class="card border"> 
                                        <img v-if="['png', 'jpg', 'jpeg', 'bmp', 'svg'].includes(file.extension)" @click="downloadFile(file, comment.id)" class="img-fluid mx-auto p-2" height="auto" width="200" :src="storageUrl + comment.id + '/' + file.stored_filename">
                                        <img v-else-if="file.extension === 'gif'" @click="downloadFile(file, comment.id)" class="img-fluid mx-auto p-2" height="auto" width="126" src="../assets/img/gif-icon.png">
                                        <img v-else-if="file.extension === 'pdf'" @click="downloadFile(file, comment.id)" class="img-fluid mx-auto p-2" height="auto" width="116" src="../assets/img/pdf-icon.png">
                                        <img v-else-if="['amr', 'ogg', 'mp3', 'm4a', 'wav'].includes(file.extension)" @click="downloadFile(file, comment.id)" class="img-fluid mx-auto p-2" height="auto" width="126" src="../assets/img/audio-file-icon.png">
                                        <div class="card-footer border">
                                            <div class="col-sm-10 px-0">
                                                <p @click="seeMoreToggle(index)" :id="'original_filename-' + index" class="text-primary text-truncate" style="max-width: 200px;">
                                                    {{ file.original_filename }}
                                                </p>
                                            </div>
                                            <div class="col-sm-2 px-0 text-right">
                                                <button @click.prevent="downloadFile(file, comment.id)" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" :style="isAvailableCommentAndEdit ? '' : 'pointer-events:none;display:none'">
                                <div class="float-right">
                                    <span
                                            class="px-1 comment-btn comment-agree"
                                            v-bind:class="{ 'comment-agree-selected': getUserVotedAgreeValue(comment.id) }"
                                            @click="addOrEditVote(0, comment.id)"
                                            style="border-radius: 3px;"
                                    >
                                        <i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }}
                                    </span>
                                    <span
                                            class="px-1 comment-btn comment-disagree"
                                            v-bind:class="{ 'comment-disagree-selected': getUserVotedDisagreeValue(comment.id) }"
                                            @click="addOrEditVote(1, comment.id)"
                                            style="border-radius: 3px;"

                                    >
                                        <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}
                                    </span>
                                    <span v-if="commentAreEnabled && isAvailableComment" class="px-1 comment-btn comment-btn-primary" @click="answerComment(comment.id)">
                                        <i class="fas fa-comment"></i> {{ $t('componentes.comentarios.responder') }}
                                    </span>

                                    <span v-if="comment.user && comment.user.id !== userId" class="px-1 comment-btn comment-btn-primary" @click="denounced_comment = comment, verificarLogin()"><i class="fas fa-ban"></i></span>
                                    <span class="px-1 comment-btn comment-btn-primary" @click="shareComment = comment" data-toggle="modal" :data-target="'#ShareModal' + shareComment.id"><i class="fas fa-share-square"></i></span>
                                </div>
                            </div>
                            <Children v-if="comment.children" :comments="comment.children" :parent_id="comment.id" :usuario="userId" :route="route" :title="title" @editSonComment="edit" @dltSonComment="dlt" @addVoteSon="addOrEditVote"></Children>
                        </div>
                    </li>
                    <button v-if="totalComments > comments.length" @click="loadMore" class="vld-parent btn btn-secondary btn-block mt-3 mb-3">
                        <span class="btn-text">{{ $t('componentes.comentarios.mas_comentarios') + ' (' + limit + ')' }}</span>
                        <loading
                                :active.sync="loadBtnLoadMore"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="color"
                        ></loading>
                    </button>
                </ul>
                <div class="col-12">
                    <div v-if="commentAreEnabled && isAvailableCommentAndEdit" class="media pa-10 border border-2 border-light col-12">
                        <div class="media-body">
                            <div v-if="parentComment">
                                <p>{{ $t('componentes.comentarios.respondiendo_comentario') }}:</p>
                                <div class="media border border-light pa-10 my-10">
                                    <div class="media-body">
                                        <div class="row">
                                            <div class="col-9 mb-2">
                                                <router-link v-if="parentComment.user && parentComment.user.username" class="h6" :to="{ path: '/user', query: { 'username': parentComment.user.username } }">
                                                    {{ parentComment.user.username }}<br>
                                                </router-link>
                                                <router-link v-else-if="parentComment.user && !parentComment.user.username" class="h6" :to="{ path: '/user', query: { 'user_id': parentComment.user.id } }">
                                                    {{ parentComment.user.name }} {{ parentComment.user.surname }}<br>
                                                </router-link>
                                                <h6 v-else>{{ $t('componentes.comentarios.no_identificado') }}</h6>
                                                <small class="text-grey">{{ new Date(toLocalDatetime(parentComment.created_at)) | moment($t('componentes.moment.formato_con_hora')) }} {{ $t('componentes.moment.horas') }}</small>
                                            </div>
                                        </div>
                                        <p class="comment-body custom-scrollbar-wk custom-scrollbar-mz">{{ parentComment.body }}</p>
                                        <div class="mt-2">
                                            <div class="float-right">
                                                <span class="px-1"><i class="fas fa-thumbs-up"></i> {{ parentComment.votos_a_favor }}</span>
                                                <span class="px-1"><i class="fas fa-thumbs-down"></i> {{ parentComment.votos_en_contra }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <textarea
                                    v-model="comment.body"
                                    class="form-control filled-input custom-scrollbar-wk custom-scrollbar-mz"
                                    rows="3"
                                    :placeholder="$t('componentes.comentarios.comentar_placeholder')"
                            ></textarea>
                            <div v-if="comment.withFiles" class="mt-2">
                                <vue-dropzone
                                        ref="commentDropzone"
                                        :options="dropzoneOptions"
                                        @vdropzone-removed-file="deleteFile"
                                ></vue-dropzone>
                                <small class="text-grey mt-2">{{ $t('componentes.comentarios.extensiones') }}: png, jpeg, jpg, bmp, gif, svg, amr, ogg, mp3, m4a, wav, pdf</small>
                            </div>
                            <button v-on:click="comment.withFiles = !comment.withFiles" class="btn btn-sm btn-secondary text-white float-right mt-1">
                                <span v-if="!comment.withFiles" class="btn-text">{{ $t('componentes.comentarios.subir') }}</span>
                                <span v-else class="btn-text" @click="deleteFiles">{{ $t('cancelar') }}</span>
                            </button>
                            <div role="group" class="btn-group btn-block text-white mt-3">
                                <button
                                        v-if="!flag_edit"
                                        class="vld-parent btn btn-sm btn-primary"
                                        :disabled="!isCommenting"
                                        @click="addComment"
                                >
                                    {{ $t('componentes.comentarios.comentar') }}
                                    <loading
                                            :active.sync="loadBtnComment"
                                            :is-full-page="fullPage"
                                            :height="24"
                                            :color="color"
                                    ></loading>
                                </button>
                                <button
                                        v-if="flag_edit"
                                        class="btn btn-sm btn-success"
                                        v-on:click="flag_edit = !flag_edit"
                                        @click="sendEdit()"
                                >
                                    {{ $t('componentes.comentarios.editar') }}
                                </button>
                                <button
                                        class="btn btn-sm btn-danger"
                                        :disabled="!isCommenting || loadBtnComment"
                                        @click="clearComment"
                                >
                                    {{ $t('cancelar') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <b-modal id="modal-denunciar"  
                        footer-bg-variant="primary"
                        header-bg-variant="primary"
                        body-bg-variant="light"
                        header-class="justify-content-center"
                        hide-header-close
                        no-close-on-backdrop
                        centered
                >
                    <template v-slot:modal-header>
                        <h4 v-if="!denounced_comment.user" class="hk-sec-title text-white my-3">{{ $t('componentes.comentarios.denunciar.no_identificado') }}</h4>
                        <h4 v-else class="hk-sec-title text-white my-3">{{ $t('componentes.comentarios.denunciar.usuario') }} {{denounced_comment.user.name}}</h4>
                    </template>
                    <div class="form-row">
                        <div class="col-md-12 mb-10">
                            <label for="reason">{{ $t('componentes.comentarios.denunciar.razon') }}</label>
                            <select id="reason" class="form-control custom-select d-block w-100" v-model="denunciation.razon" value=" ">
                                <option>{{ $t('componentes.comentarios.denunciar.lenguaje_indebido') }}</option>
                                <option>Bullying</option>
                                <option>{{ $t('componentes.comentarios.denunciar.acoso') }}</option>
                                <option>{{ $t('componentes.comentarios.denunciar.inapropiado') }}</option>
                                <option>{{ $t('componentes.comentarios.denunciar.violencia') }}</option>
                                <option>Spam</option>
                                <option>{{ $t('componentes.comentarios.denunciar.terrorismo') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-10">
                            <label for="description">{{ $t('componentes.comentarios.denunciar.descripcion') }}</label>
                            <textarea id="description" class="form-control" rows="2" v-model="denunciation.descripcion"></textarea>
                        </div>
                    </div>
                    <template v-slot:modal-footer>
                        <b-button class="btn btn-sm bg-green " block @click="sendDenunciation(),$bvModal.hide('modal-denunciar')"><font-awesome-icon icon="envelope"/><span class="btn-text"> {{ $t('componentes.comentarios.denunciar.enviar') }}</span></b-button>
                        <b-button class="btn btn-sm bg-red  mb-2" block @click="$bvModal.hide('modal-denunciar'),cleanModal()"><font-awesome-icon icon="window-close"/><span class="btn-text"> {{ $t('cancelar') }}</span></b-button>
                    </template>
                </b-modal>
                <div class="modal" :id="'ShareModal' + shareComment.id">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{{ $t('compartir') }}</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <social-sharing :url="APP_URL + '/' + route.type + '/' + route.id"
                                    :title="'Congreso Virtual: '+ title"
                                    :description="'Comentario: ' + shareComment.body"
                                    :quote="title + '\n\n' + 'Comentario: ' + shareComment.body"
                                    hashtags="congresovirtual"
                                    inline-template
                                >
                                    <div>
                                        <network class="btn btn-block btn-social btn-email bg-red-light-2 text-white" network="email">
                                            <i class="fas fa-envelope"></i> Email
                                        </network>
                                        <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                            <span class="fab fa-facebook-square"></span> Facebook
                                        </network>
                                        <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                            <i class="fab fa-linkedin"></i> LinkedIn
                                        </network>
                                        <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                            <i class="fab fa-twitter"></i> Twitter
                                        </network>
                                        <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </network>
                                    </div>
                                </social-sharing>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $t('cerrar') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</template>

<script>
    import Children from './ChildrenComments';
    import vue2Dropzone from 'vue2-dropzone';
    import 'vue2-dropzone/dist/vue2Dropzone.min.css';
    import { API_URL } from '../backend/data_server';
    import { APP_URL } from '../data/globals';
    import SocialSharing from 'vue-social-sharing';
    import HighlightText from 'vue-highlight-text';
    import axioma from 'axios';
    import Loading from 'vue-loading-overlay';
    import axios from '../backend/axios';

    export default {
        name: 'Comments',
        components: {
            Children,
            vueDropzone: vue2Dropzone,
            SocialSharing,
            HighlightText,
            Loading
        },
        props: {
            project: Object,
            idea_id: Number,
            article_id: Number,
            publicConsultation: Object
        },
        data() {
            return {
                comments: [],
                totalComments: 0,
                currentSort: {
                    field: 'created_at',
                    type: 'DESC'
                },
                selectedSortId: 2,
                optionsSort: [
                    { id: 1, field: 'created_at', type: 'ASC', label: null },
                    { id: 2, field: 'created_at', type: 'DESC', label: null },
                    { id: 3, field: 'votos_a_favor', type: 'ASC', label: null },
                    { id: 4, field: 'votos_a_favor', type: 'DESC', label: null },
                    { id: 5, field: 'votos_en_contra', type: 'ASC', label: null },
                    { id: 6, field: 'votos_en_contra', type: 'DESC', label: null },
                ],
                limit: 10,
                offset: 0,
                query: '',
                loadComments: true,
                loadBtnLoadMore: false,
                loadBtnComment: false,
                fullPage: false,
                color: '#ffffff',
                loaderType: 'dots',
                userId: null,
                userVotedAgree: [],
                userVotedDisagree: [],
                votes: [],
                commentAreEnabled: false,
                parentComment: null,
                comment: {
                    body: null,
                    parent_id: null,
                    project_id: null,
                    idea_id: null,
                    article_id: null,
                    public_consultation_id: null,
                    withFiles: false,
                    files: [],
                    position: null
                },

                route: {
                    type: null,
                    id: null
                },

                shareComment: {},

                title: '',
                seleccionFiltro: Number,
                vote: {
                    comment_id: null,
                    user_id: null,
                    vote: null
                },
                comment_has_file: false,
                files: [],
                new_files: new FormData(),
                deleted_files: [],
                actual_files: [],
                comment_form: new FormData(),
                has_file: true,
                flag_edit: false,
                file_flag: false,
                id_com: null,
                comm: null,
                denounced_comment: null,
                index: 10,
                palabrasOfensivas: [],
                denunciation: {
                    razon: '',
                    descripcion: ''
                },
                dropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 150,
                    maxFiles: 4,
                    maxFilesize: 20,
                    addRemoveLinks: true,
                    acceptedFiles: '.png, .jpeg, .jpg, .bmp, .gif, .svg, .amr, .ogg, .mp3, .m4a, .wav, .pdf'
                },
                storageUrl: API_URL + '/api/storage/comments/',
                APP_URL,
                edit_comment: {},
                parent: null,
                answer_flag: false,
                mode: String,
                isAvailableCommentAndEdit: false,
                currentMoment: this.$moment().local(),
                votingStartDate: this.$moment.utc(this.project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local(),
                votingEndDate: this.$moment.utc(this.project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local()
            };
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
            this.search();
            this.setIsAvailableCommentAndEdit();
        },
        methods: {
            configComponent() {
                if(this.$store.getters.isLoggedIn) {
                    this.userId = this.$store.getters.userData.id;
                }

                this.optionsSort.forEach(option => {
                    option.label = this.$t('componentes.comentarios.orden.opciones').find(optionTrans => optionTrans.id === option.id).label;
                });

                this.dropzoneOptions = Object.assign(this.dropzoneOptions, this.$t('componentes.dropzone'));

                if(this.project && !this.idea_id && !this.article_id) {
                    this.comment.project_id = this.project.id;
                    this.route.type = 'project';
                    this.route.id = this.project.id;
                    if(this.project.is_enabled) {
                        this.commentAreEnabled = true;
                        this.title = this.project.titulo;
                    }
                } else if(this.idea_id) {
                    this.comment.idea_id = this.idea_id;
                    this.route.type = 'project';
                    this.route.id = this.project.id;
                    if(this.project.is_enabled) {
                        this.commentAreEnabled = true;
                        this.title = this.project.titulo;
                    }
                } else if(this.article_id) {
                    this.comment.article_id = this.article_id;
                    this.route.type = 'project';
                    this.route.id = this.project.id;
                    if(this.project.is_enabled) {
                        this.commentAreEnabled = true;
                        this.title = this.project.titulo;
                    }
                } else if(this.publicConsultation) {
                    this.comment.public_consultation_id = this.publicConsultation.id;
                    this.route.type = 'public_consultation';
                    this.route.id = this.publicConsultation.id;
                    if(this.publicConsultation.estado === 1) {
                        this.commentAreEnabled = true;
                        this.title = this.publicConsultation.titulo;
                    }
                }
            },
            getComments() {
                let params = {
                    query: this.query,
                    state: 0,
                    order: this.currentSort.type,
                    order_by: this.currentSort.field,
                    limit: this.limit,
                    offset: this.offset
                };
                if(this.project && !this.idea_id && !this.article_id) {
                    params.project_id = this.project.id;
                } else if(this.idea_id) {
                    params.idea_id = this.idea_id;
                } else if(this.article_id) {
                    params.article_id = this.article_id;
                } else if(this.publicConsultation) {
                    params.public_consultation_id = this.publicConsultation.id;
                } else {
                    this.loadComments = false;
                    this.loadBtnLoadMore = false;
                    return;
                }

                axios
                    .get('/comments', {
                        params: params
                    })
                    .then(res => {
                        this.comments = this.comments.concat(res.data.results);
                        this.totalComments = res.data.total_results;
                        this.offset += res.data.returned_results;

                        if(this.$store.getters.isLoggedIn) {
                            this.getUserVotes();
                        }
                    })
                    .finally(() => {
                        this.loadComments = false;
                        this.loadBtnLoadMore = false;
                    });
            },
            search() {
                if(this.comments.length > 0) {
                    this.comments = [];
                    this.totalComments = 0;
                    this.offset = 0;
                }
                this.loadComments = true;
                this.getComments();
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getComments();
            },
            sort(event) {
                let optionSortId = event.target.value;
                let selectedOptionSort = this.optionsSort.find(optionSort => optionSort.id == optionSortId);
                this.currentSort.field = selectedOptionSort.field;
                this.currentSort.type = selectedOptionSort.type;
                this.search();
            },
            getUserVotes() {
                axios
                    .get('/votes', {
                        params: {
                            user_id: this.userId,
                            return_all: 1
                        }
                    })
                    .then(res => {
                        let votes = res.data.results;
                        this.comments.forEach(comment => {
                            votes.forEach(vote => {
                                if(vote.comment_id === comment.id) {
                                    this.votes.push(vote);
                                    if(vote.vote === 0) {
                                        this.toggleAgree(comment.id);
                                    } else if(vote.vote === 1) {
                                        this.toggleDisagree(comment.id);
                                    }
                                }
                            });
                        });
                    });
            },
            toggleAgree(commentId) {
                this.userVotedAgree = this.userVotedAgree.filter(element => element.comment_id !== commentId);
                this.userVotedAgree.push({ comment_id: commentId, value: true });

                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.comment_id !== commentId);
                this.userVotedDisagree.push({ comment_id: commentId, value: false });
            },
            toggleDisagree(commentId) {
                this.userVotedDisagree = this.userVotedDisagree.filter(element => element.comment_id !== commentId);
                this.userVotedDisagree.push({ comment_id: commentId, value: true });

                this.userVotedAgree = this.userVotedAgree.filter(element => element.comment_id !== commentId);
                this.userVotedAgree.push({ comment_id: commentId, value: false });
            },
            getUserVotedAgreeValue(commentId) {
                let userVotedAgree = this.userVotedAgree.find(element => element.comment_id === commentId);
                if(userVotedAgree) {
                    return userVotedAgree.value;
                }
                return false;
            },
            getUserVotedDisagreeValue(commentId) {
                let userVotedDisagree = this.userVotedDisagree.find(element => element.comment_id === commentId);
                if(userVotedDisagree) {
                    return userVotedDisagree.value;
                }
                return false;
            },
            addOrEditVote(voteValue, commentId) {
                let comment = this.comments.find(comment => comment.id === commentId);
                let vote = this.votes.find(vote => vote.comment_id === comment.id);

                if(this.$store.getters.isLoggedIn) {
                    if(vote) {
                        axios
                            .put('/votes/' + vote.id, {
                                vote: voteValue
                            })
                            .then(() => {
                                if(vote.vote === 0) {
                                    comment.votos_a_favor -= 1;
                                } else if(vote.vote === 1) {
                                    comment.votos_en_contra -= 1;
                                }

                                if(voteValue === 0) {
                                    comment.votos_a_favor += 1;
                                    this.toggleAgree(comment.id);
                                } else if(voteValue === 1) {
                                    comment.votos_en_contra += 1;
                                    this.toggleDisagree(comment.id);
                                }

                                vote.vote = voteValue;
                                this.$toastr('success', 'Tu reacción fue cambiada con éxito', 'Reacción actualizada');
                            })
                            .catch(() => {
                                this.$toastr('error', 'No se ha podido cambiar tu reacción', 'Error');
                            });
                    } else {
                        axios
                            .post('/comments/' + comment.id + '/vote', {
                                vote: voteValue
                            })
                            .then(res => {
                                this.votes.push(res.data.data);
                                if(voteValue === 0) {
                                    comment.votos_a_favor += 1;
                                    this.toggleAgree(comment.id);
                                } else if(voteValue === 1) {
                                    comment.votos_en_contra += 1;
                                    this.toggleDisagree(comment.id);
                                }

                                this.$toastr('success', 'Tu reacción fue registrada con éxito', 'Reacción guardada');
                            })
                            .catch(() => {
                                this.$toastr('error', 'No se ha podido registrar tu reacción', 'Error');
                            });
                    }
                } else {
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder reaccionar a comentarios, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            answerComment(commentId) {
                this.parentComment = this.comments.find(comment => comment.id === commentId);
                this.comment.parent_id = this.parentComment.id;
            },
            clearComment() {
                this.comment.body = null;
                this.comment.withFiles = false;
                this.comment.files = [];
                this.comment.position = null;
                this.parentComment = null;
            },
            addComment() {
                this.loadBtnComment = true;
                if(this.$store.getters.isLoggedIn) {
                    if(this.comment.body) {
                        this.$toastr('info', 'Es posible que le solicitemos su ubicación geográfica solo con fines estadísticos, puede aceptar o rechazar la solicitud', 'Localización solicitada');
                        this.getCurrentPosition()
                            .then(position => {
                                this.comment.position = {
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude
                                };
                            })
                            .finally(() => {
                                let commentFormData = this.generateCommentFormData();
                                axios
                                    .post('/comments', commentFormData)
                                    .then(res => {
                                        let newComment = res.data.data;
                                        this.comments.push(newComment);
                                        this.clearComment();

                                        if(this.comment.files.length > 0) {
                                            this.$refs.commentDropzone.removeAllFiles();
                                            this.$toastr('success', 'Tu comentario fue registrado con éxito, pero debido a que contiene archivos pasará a moderación antes de ser publicado', 'Comentario registrado');
                                        } else if (newComment.state === 1) {
                                            this.$toastr('success', 'Tu comentario fue registrado con éxito, pero debido al contenido pasará a moderación antes de ser publicado', 'Comentario registrado');
                                        } else {
                                            this.$toastr('success', 'Tu comentario fue registrado con éxito', 'Comentario registrado');
                                        }

                                        if(newComment.gamification_result && newComment.gamification_result.rewards.length > 0) {
                                            this.$store.dispatch('hasNewNotifications', true);
                                            this.$toastr('success', this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.cuerpo'), this.$t('navbar.notificaciones.mensajes.nuevas_recompensas.titulo'));
                                        }
                                    })
                                    .catch(() => {
                                        this.$toastr('error', 'No se ha podido guardar tu comentario', 'Error');
                                    })
                                    .finally(() => {
                                        this.loadBtnComment = false;
                                    });
                            });
                    } else {
                        this.loadBtnComment = false;
                        this.$toastr('warning', 'El comentario debe tener obligatoriamente un texto que lo componga', 'Falta el cuerpo del comentario');
                    }
                } else {
                    this.loadBtnComment = false;
                    this.$toastr('warning', 'Debes acceder con una cuenta para poder comentar, si no tienes una puedes crearla', 'No has iniciado sesión');
                }
            },
            generateCommentFormData() {
                let commentFormData = new FormData();

                commentFormData.append('body', this.comment.body);

                if(this.comment.parent_id) {
                    commentFormData.append('parent_id', this.comment.parent_id);
                }

                if(this.comment.project_id) {
                    commentFormData.append('project_id', this.comment.project_id);
                } else if(this.comment.idea_id) {
                    commentFormData.append('idea_id', this.comment.idea_id);
                } else if(this.comment.article_id) {
                    commentFormData.append('article_id', this.comment.article_id);
                } else if(this.comment.public_consultation_id) {
                    commentFormData.append('public_consultation_id', this.comment.public_consultation_id);
                }

                if(this.comment.withFiles && this.$refs.commentDropzone) {
                    this.comment.files = this.$refs.commentDropzone.getAcceptedFiles();
                }
                this.comment.files.forEach(file => {
                    commentFormData.append('files[]', file);
                });

                if(this.comment.position) {
                    commentFormData.append('position_latitude', this.comment.position.latitude);
                    commentFormData.append('position_longitude', this.comment.position.longitude);
                }

                return commentFormData;
            },

            async edit(com) {
                if (this.$store.getters.isLoggedIn) {
                    this.id_com=com.id;
                    this.edit_comment = com;

                    if(this.file_flag) {
                        this.deleteFiles();
                    }

                    if(com.files.length) {
                        this.file_flag = true;
                    }

                    try {
                        const res = await axios.get('/comments/' + com.id)
                        this.comment= res.data;
                        this.comm = com.body;
                        this.comment.body = com.body;
                        this.flag_edit=true;

                        if (this.file_flag) {
                            this.comment.files.forEach(archivo => {
                                this.$refs.commentDropzone.manuallyAddFile({size: "", name: archivo.original_name, type: archivo.filetype + "/" + archivo.extension}, API_URL + "/api/storage/comments/" + this.comment.id + "/" + archivo.stored_name + "." + archivo.extension);
                                this.files.push(archivo) 
                            })
                        }  
                    } catch (error) {/* console.log(error); */}
                } else {
                    this.$toastr('warning', 'Debes acceder a tu cuenta para poder comentar, si no tienes cuenta puedes crearte una aquí', 'No has iniciado sesión');
                }
            },
            async sendEdit() {
                if(this.$store.getters.isLoggedIn){
                    try {
                        const res = await axios.put("/comments/"+this.id_com,{body:this.comment.body})
                        this.$toastr('success', 'Tu comentario fue editado con éxito', 'Comentario guardado');
                    } catch (error) {
                        this.$toastr('error','','Comentario no guardado');
                    }

                    if(this.file_flag) {
                        if(this.deleted_files.length) {
                            this.deleted_files.forEach(archivoEliminar => {
                                this.comment.files.forEach(archivo => {
                                    if(archivoEliminar.name + "." + archivoEliminar.type.split('/')[1] === archivo.original_filename) {
                                        axios
                                            .delete('/comments/' + this.id_com + '/file/' + archivo.id)
                                            .then(res => {})
                                            .catch((error) => {
                                                this.$toastr.error('error','','No se ha podido eliminar un archivo');
                                            });
                                    }
                                });
                            });
                        }

                        this.actual_files = this.$refs.commentDropzone.getAcceptedFiles();

                        this.actual_files.forEach(archivoActual => {
                            let index = 0;
                            this.comment.files.forEach(archivoOrigen => {
                                if(archivoActual.name + "." + archivoActual.type.split('/')[1] === archivoOrigen.original_filename) {
                                    this.actual_files.splice(index, 1);
                                }
                                index++;
                            });
                        });

                        if(this.actual_files.length) {
                            this.actual_files.forEach(archivoNuevo => {
                                this.new_files.append("files[]", archivoNuevo);
                            });
                            try {
                                const res = await axios.post("/comments/" + this.id_com + "/files",this.new_files);
                                this.$toastr('success','Se enviarán sus cambios a moderación, y se publicarán una vez que sean aceptados','Cambios guardados');
                            } catch (error) {
                                this.$toastr('error','','Error al subir los archivos');
                            }
                        }
                        this.deleteFiles()
                    }
                    this.editComment()
                } else {
                    this.$toastr('warning', 'Debes acceder a tu cuenta para poder comentar, si no tienes cuenta puedes crearte una aquí.', 'No has iniciado sesión');
                }
            },
            async dlt(id_d) {
                if(this.$store.getters.isLoggedIn) {
                    try {
                        const res = await axios.delete('/comments/' + id_d);
                        this.deleteComment(id_d);
                        this.$toastr('success', 'Tu comentario fue eliminado con éxito.', 'Comentario eliminado');
                    } catch (error) {
                        this.$toastr('error', '', 'Comentario no eliminado');
                    }
                } else {
                    this.$toastr('warning', 'Debes acceder a tu cuenta para poder comentar, si no tienes cuenta puedes crearte una aquí.', 'No has iniciado sesión');
                }
            },

            async addComment2() {
                try {
                    const res = await axios.post('/comments', this.comment_form);

                    if(!this.comment_has_file) {
                        let comment = res.data.data
                        comment.children = []

                        if(this.parent) {
                            this.parent.children.push(comment)
                        } else {
                            this.comments.push(comment);
                        }
                        this.comment.body = "";
                        this.$toastr('success', 'El comentario que enviaste fue guardado con éxito.', 'Comentario guardado');
                    } else {
                        this.$toastr('success','Su comentario ha sido enviado a moderación, se publicará una vez que sea aceptado','Comentario en espera')
                        this.$refs.commentDropzone.removeAllFiles()
                        this.comment_has_file = false
                    }
                } catch (error) {
                    this.$toastr('error', 'No se ha podido ingresar tu comentario', 'ERROR')
                }
                this.files = [];
                this.deleted_files = [];
            },
            editComment() {
                this.comments.forEach(comment => {
                    if(comment.id === this.id_com) {
                        comment.body = this.comment.body;
                    }

                    if(comment.children) {
                        comment.children.forEach(children_comment => {
                            if(children_comment.id === this.id_com) {
                                children_comment.body = this.comment.body;
                            }
                        });
                    }
                });
                this.comment.body = '';
            },
            deleteComment(id) {
                let index = 0
                let children_index = 0

                this.comments.forEach( comment => {
                    if(comment.id == id) {
                        this.comments.splice(index, 1);
                    }

                    if(comment.children) {
                        comment.children.forEach( children_comment => {
                            if (children_comment.id == id) {
                                comment.children.splice(children_index,1);
                            }
                            children_index++;
                        });
                    }
                    children_index = 0;
                    index++;
                });
            },
            verificarLogin() {
                try {
                    if(this.$store.getters.isLoggedIn) {
                        this.$bvModal.show('modal-denunciar');
                    } else {
                        this.$toastr('warning','Debes tener una cuenta para poder denunciar un comentario','No has iniciado sesión');
                    }
                } catch (error) {
                    console.log(error);
                }
            },
            async sendDenunciation() {
                let denounced_comment = {
                    reason: this.denunciation.razon,
                    description: this.denunciation.descripcion,
                    comment_id: this.denounced_comment.id,
                    user_id: this.$store.getters.userData.id,
                };
                
                try {
                    const res = await axios.post('/denounces',denounced_comment)
                    this.$toastr('success', 'Su denuncia ha sido realizada', 'Denuncia enviada')
                    this.cleanModal()
                } catch (error) {
                    this.$toastr('error', 'Su denuncia no se ha podido realizar. Puede que ya hayas denunciado anteriormente este comentario. ','Denuncia no enviada');
                    this.cleanModal()
                }
            },

            seeMoreToggle(elementIndex) {
                let textElement = document.getElementById('original_filename-' + elementIndex);
                if(textElement.style.maxWidth === '200px') {
                    textElement.style.maxWidth = 'none';
                    textElement.classList.remove('text-truncate');
                } else {
                    textElement.style.maxWidth = '200px';
                    textElement.classList.add('text-truncate');
                }
            },
            downloadFile(file, commentId) {
                axios
                    .get('/storage/comments/' + commentId + '/' + file.stored_name + '.' + file.extension, {
                        responseType: 'blob'
                    })
                    .then(res => {
                        let type = res.headers['content-type'];
                        let blob = new Blob([res.data], { type: type });
                        let link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = file.original_name + "." + file.extension;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    })
                    .catch(() => {
                        this.$toastr('error', 'Algo salio mal, no se pudo descargar el archivo', 'Archivo no descargado');
                    });
            },

            deleteFile(file) {
                this.deleted_files.push(file);
            },
            deleteFiles() {
                this.$refs.commentDropzone.removeAllFiles();
            },
            cleanModal () {
                this.denunciation.razon = '';
                this.denunciation.descripcion = '';
            },

            getCurrentPosition(options = {}) {
                return new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, options);
                });
            },
            toLocalDatetime(datetime) {
                return this.$moment.utc(datetime, 'YYYY-MM-DD HH:mm:ss').local();
            },
            setIsAvailableCommentAndEdit() {
                this.isAvailableCommentAndEdit = this.project.is_enabled && this.project.etapa !== 3 && this.currentMoment.isBetween(this.votingStartDate, this.votingEndDate);
            }
        },
        computed: {
            isCommenting() {
                return !!(this.comment.body || this.comment.withFiles || this.parentComment);
            }
        }
    }
</script>

<style scoped>
    .comment-body {
        max-height: 400px;
        overflow: auto;
        display: inline-block !important;
    }

    .comment-btn {
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .comment-btn-primary:hover {
        color: #ffffff;
        background: #2f4595;
        border-radius: 3px;
    }

    .comment-agree:hover {
        color: #ffffff;
        background: #9de19c;
        border-radius: 3px;
    }

    .comment-agree-selected {
        color: #008000;
        pointer-events: none;
        cursor: none;
    }

    .comment-disagree:hover {
        color: #ffffff;
        background: #fc7872;
        border-radius: 3px;
    }

    .comment-disagree-selected {
        color: #f83f37;
        pointer-events: none;
        cursor: none;
    }
</style>
