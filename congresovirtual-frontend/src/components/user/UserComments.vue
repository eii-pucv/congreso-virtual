<template>
    <div class="row">
        <div class="col-12">
            <div class="card card-profile-feed" :class="mode==='dark'?'dark':'light'">
                <div class="card-header card-header-action">
                    <h2 :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.comentarios.titulo') }}</h2>
                </div>
                <div v-if="loaded">
                    <p class="px-4 mb-2">{{ $t('perfil_usuario.componentes.comentarios.cantidad') }}: {{ totalResults }}</p>
                    <br/>
                    <ul v-if="comments.length > 0" class="list-group list-group-flush">
                        <li v-for="(comment, index) in comments" :key="'comentario_' + index" class="mb-2 mx-10 list-group-item border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                            <div v-if="comment.project" class="media align-items-center">
                                <div class="media-body mb-2">
                                    <h5 v-if="comment.project.titulo" :style="mode==='dark'?'color: #fff':''"><small><b>{{ comment.project.titulo }}</b></small></h5>
                                    <span class="d-block text-dark text-capitalize"></span>
                                    <span class="d-block font-26 mb-2">{{ comment.body }}</span>
                                    <small>{{ comment.created_at }}</small>
                                </div>
                                <a :href="'/project/' + comment.project.id" class="btn ma-5 text-white bg-indigo-light-2  votable">
                                    <span class="btn-text">
                                        <font-awesome-icon icon="share-square"/>
                                    </span>
                                </a>
                            </div>
                            <div class="col-12">
                                <div class="float-right">
                                    <span class="mr-1" ><i class="fas fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                                    <span class="px-1" > <i class="fas fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="mb-20" v-if="totalResults > comments.length">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.comentarios.cargar') }}
                            <loading
                                    :active.sync="loadBtnLoadMore"
                                    :is-full-page="false"
                                    :height="24"
                                    :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div v-if="comments.length == 0">
                        <h6 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.comentarios.error1') }}</h6>
                    </div>
                </div>
                <div v-else>
                    <h6 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('perfil_usuario.componentes.comentarios.cargando') }}</h6>
                </div>
                <div v-if="failed">
                    <h6 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.comentarios.error2') }}</h6>
                </div>
            </div>   
        </div>
    </div>
</template>

<script>
    import axios from "../../backend/axios";

    export default {
        name: "userComments",
        data() {
            return {
                totalResults: 0,
                comments: [],
                limit: 10,
                offset: 0,
                loadBtnLoadMore: false,
                loaded: false,
                failed: false,
                mode: String,
            };
        },
        methods: {
            getComments() {
                axios
                    .get("/users/"+ JSON.parse(localStorage.user).id+"/comments" , {
                        params: {
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results
                        this.comments = this.comments.concat(res.data.results)

                        this.offset += res.data.returned_results;
                    })
                    .catch(() => {
                        console.log("FALLO");
                        this.failed = true;
                    })
                    .finally(() => {
                        this.loaded = true;
                    })
            },
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getComments();
            },
        },
        mounted(){
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getComments();
        },    
    }
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

.dark {
    color: #fff;
    background: rgb(8, 0, 53);
}

.light {
    color: #000;
    background: #fff;
}
</style>