<template>
    <div class="row">
        <div class="col-12">
            <div class="card card-profile-feed" :class="mode === 'dark' ? 'dark' : 'light'">
                <div class="card-header card-header-action">
                    <h2 :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.votos.titulo') }}</h2>
                </div>
                <div v-if="loaded">
                    <p class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.votos.cantidad') }}: {{ totalResults }}</p> 
                    <br/>
                    <ul class="list-group list-group-flush px-10" v-if="votes.length > 0">
                        <router-link v-for="(vote, index) in votes" v-if="vote.project" :to="{ path: '/project/' + vote.project_id }" :key="'votos_proyecto_' + index" class="mb-2 list-group-item list-group-item-action border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block  text-capitalize" :style="mode === 'dark' ? 'color: #fff' : ''">
                                        {{ vote.project ? vote.project.titulo : '' }}
                                    </span>                            
                                    <span class="d-block font-26">{{ new Date(vote.project.updated_at) | moment("D MMM YYYY")}}</span>
                                </div>
                                <a href="#" class="text-light-40 ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 col-6 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 col-6 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 col-6 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </a>
                            </div>
                        </router-link>
                        <router-link v-for="(vote, index) in votes" v-if="vote.article" :to="{ path: '/project/' + vote.article.project_id }" :key="'votos_articulo_' + index" class="mb-2 list-group-item list-group-item-action border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <span class="d-block  text-capitalize" :style="mode === 'dark' ? 'color: #fff' : ''">
                                        "{{ vote.article ? vote.article.titulo : '' }}" {{ $t('perfil_usuario.componentes.votos.proyecto') }} n° {{ vote.article.project_id }}
                                    </span>                            
                                    <span class="d-block font-26">{{ new Date(vote.article.updated_at) | moment("D MMM YYYY")}}</span>
                                </div>
                                <a href="#" class="text-light-40 ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 col-6 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 col-6 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 col-6 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </a>
                            </div>
                        </router-link>
                        <router-link v-for="(vote, index) in votes" v-if="vote.idea" :to="{ path: '/project/' + vote.idea.project_id }" :key="'votos_idea_' + index" class="mb-2 list-group-item list-group-item-action border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                            <div class="media align-items-center" >
                                <div class="media-body">
                                    <span class="d-block  text-capitalize" :style="mode === 'dark' ? 'color: #fff' : ''">
                                        "{{ vote.idea ? vote.idea.titulo : '' }}" {{ $t('perfil_usuario.componentes.votos.proyecto') }} n° {{vote.idea.project_id}}
                                    </span>                            
                                    <span class="d-block font-26">{{ new Date(vote.idea.updated_at) | moment("D MMM YYYY")}}</span>
                                </div>
                                <a href="#" class="text-light-40 ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 col-6 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 col-6 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 col-6 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </a>
                            </div>
                        </router-link>
                        <router-link v-for="(vote, index) in votes" v-if="vote.public_consultation" :to="{ path: '/project/' + vote.public_consultation }" :key="'votos_idea_' + index" class="mb-2 list-group-item list-group-item-action border border-success rounded" :class="mode === 'dark' ? 'dark' : 'light'">
                            <div class="media align-items-center" >
                                <div class="media-body">
                                    <span class="d-block  text-capitalize" :style="mode === 'dark' ? 'color: #fff' : ''">
                                        "{{ vote.public_consultation ? vote.public_consultation.titulo : '' }}" {{ $t('perfil_usuario.componentes.votos.proyecto') }} n° {{vote.public_consultation.project_id}}
                                    </span>                            
                                    <span class="d-block font-26">{{ new Date(vote.public_consultation.updated_at) | moment("D MMM YYYY")}}</span>
                                </div>
                                <a href="#" class="text-light-40 ml-auto">
                                    <span v-if="vote.vote === 0" class="font-30 col-6 text-green"><i class="fas fa-thumbs-up"></i></span>
                                    <span v-if="vote.vote === 1" class="font-30 col-6 text-red"><i class="fas fa-thumbs-down"></i></span>
                                    <span v-if="vote.vote === 2" class="font-30 col-6 text-grey"><i class="fas fa-minus-circle"></i></span>
                                </a>
                            </div>
                        </router-link>
                    </ul>
                    <div class="mb-20" v-if="totalResults > votes.length">
                        <button class="vld-parent btn btn-secondary btn-block" @click="loadMore">{{ $t('perfil_usuario.componentes.votos.cargar') }}
                            <loading
                                :active.sync="loadBtnLoadMore"
                                :is-full-page="fullPage"
                                :height="24"
                                :color="color"
                            ></loading>
                        </button>
                    </div>
                    <div v-if="votes.length == 0">
                        <h6 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.votos.error1') }}</h6>
                    </div>
                </div>
                <div v-else>
                    <h6 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''"> {{ $t('perfil_usuario.componentes.votos.cargando') }}</h6>
                </div>
                <div v-if="failed">
                    <h4 class="px-4 mb-2" :style="mode === 'dark' ? 'color: #fff' : ''">{{ $t('perfil_usuario.componentes.votos.error2') }}</h4>
                </div>
            </div>   
        </div>
    </div>
</template>

<script>
    import axios from "../../backend/axios";

    export default {
        name: 'UserVotes',
        data() {
            return {
                votes: [],
                totalResults: 0,
                limit: 10,
                offset: 0,
                loadBtnLoadMore: false,
                loaded: false,
                failed: false,
                mode: String,
            };
        },
        methods: {
            loadMore() {
                this.loadBtnLoadMore = true;
                this.getVotes();
            },
            getVotes() {
                axios
                    .get("/users/"+  JSON.parse(localStorage.user).id+"/votes", {
                        params: {
                            limit: this.limit,
                            offset: this.offset
                        }
                    })
                    .then(res => {
                        this.totalResults = res.data.total_results
                        this.votes = this.votes.concat(res.data.results)
                        this.offset += res.data.returned_results
                    })
                    .catch(() => {
                        // console.log("FALLO");
                        this.failed = true;
                    })
                    .finally(() => {
                        this.loaded = true;
                    })
            },
        },
        mounted(){
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getVotes()
        }
    }
</script>

<style scoped>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }

    a:hover{
        color: black; 
        text-decoration: none; 
    }

    a:hover span {
        color: black; 
        text-decoration: none; 
    }

</style>