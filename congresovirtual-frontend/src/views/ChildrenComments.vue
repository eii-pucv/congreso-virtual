<template>
    <div class="mt-40">
        <li v-for="comment in comments.slice(n,this.index)" :key="comment.id" class="media border border-2 border-light col-12">
            <div class="media-body mt-20" v-if="comment.user">
                <div class="row">
                    <div class="col-8 col-sm-10">
                        <h6 class="mb-2">
                            {{ comment.user.name }}
                            {{ comment.user.surname }}
                            <small>{{ comment.created_at }}</small>
                        </h6>
                    </div>
                    <div v-if="usuario===comment.user.id" class="col-4 col-sm-2 btn-group mt-auto ml-auto float-right" style="margin:0px;padding:0px">
                        <a @click="edit(comment)" class="btn btn-primary mt-3 ">
                            <span class="btn-text"><font-awesome-icon icon="edit"/></span></a>
                        <a @click="dlt(comment.id)" class="btn text-white bg-red mt-3 ">
                            <span class="btn-text"><font-awesome-icon icon="trash-alt"/></span></a>
                    </div>
                </div>
                {{ comment.body }}
                <div v-if="comment.files.length" class="row mt-2 mb-3">
                    <div class="col-sm-4 col-md-3 col-xl-2 mr-3" v-for="(file,index) in comment.files" :key="file.id">
                        <div class="card border" style="max-width: 8rem; min-width: 8rem;">
                                <img v-if="(file.extension === 'png') || (file.extension === 'jpg') || (file.extension === 'jpeg') || (file.extension === 'bmp') || (file.extension === 'svg')" class="img-fluid" @click="downloadFile(file,comment.id)" :src="storageUrl + comment.id + '/' + file.stored_filename">
                                <img v-if="file.extension === 'gif'" class="img-fluid" height="auto" width="126px" @click="downloadFile(file,comment.id)" src="../assets/img/gif-icon.png">
                                <img v-if="file.extension === 'pdf'" class="img-fluid" height="auto" width="116px" @click="downloadFile(file,comment.id)" src="../assets/img/pdf-icon.png">
                                <img v-if="(file.extension === 'amr') || (file.extension === 'ogg') || (file.extension === 'mp3') || (file.extension === 'm4a') || (file.extension === 'wav') " class="img-fluid" height="auto" width="126px" @click="downloadFile(file,comment.id)" src="../assets/img/audio-file-icon.png">
                            <div class="card-footer border justify-content-center">
                                <div class="row">
                                    <div class="col-12">
                                        <span :class="mode==='dark'?'text-primary':''">{{ file.original_filename.substring(0, seeMoreLimitText) }}</span><span v-if="file.original_filename.length > seeMoreLimitText" :id="'dots-' + index" :class="mode==='dark'?'text-primary':''">...</span><span v-if="file.original_filename.length > seeMoreLimitText" :id="'seemore-' + index" class="seemore" :class="mode==='dark'?'text-primary':''">{{ file.original_filename.substring( seeMoreLimitText) }}</span>
                                        <span v-if="file.original_filename.length > seeMoreLimitText" class="text-primary seemore-trigger" href="" @click="seeMoreToggle(index)" :id="'myBtn-' + index">{{ $t('componentes.comentarios.ver_mas') }}</span>
                                    </div>
                                </div>
                                <span class=" float-right ml-1" @click="downloadFile(file,comment.id)"><i class="fa fa-download"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="float-right mb-20">
                        <span class="mr-1 " @click="addOrEditVote(0,comment.id)"><i class="fa fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                        <span class="px-1 " @click="addOrEditVote(1,comment.id)"> <i class="fa fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                        <span v-if="usuario!=comment.user.id" class="px-1 " @click="denounced_comment = comment, verificarLogin()"><i class="fa fa-ban"></i></span>
                        <span class="ml-1 " @click="shareComment = comment" data-toggle="modal" :data-target="'#ShareModal' + shareComment.id"><i class="fa fa-share-square"></i></span>
                    </div>
                </div>
            </div>
            <div class="media-body mt-20" v-if="!comment.user">
                <div class="row">
                    <div class="col-8 col-sm-10">
                        <h6 class="mb-2">{{ $t('componentes.comentarios.no_identificado') }}
                            <small>{{ comment.created_at }}</small>
                        </h6>
                    </div>
                </div>
                {{ comment.body }}
                <div class="col-12">
                    <div class="float-right mb-20">
                        <span class="mr-1 " @click="addOrEditVote(0,comment.id)"><i class="fa fa-thumbs-up"></i> {{ comment.votos_a_favor }} </span>
                        <span class="px-1 " @click="addOrEditVote(1,comment.id)"> <i class="fa fa-thumbs-down"></i> {{ comment.votos_en_contra }}</span>
                        <span class="px-1 " @click="denounced_comment = comment, verificarLogin()"><i class="fa fa-ban"></i></span>
                        <span class="ml-1 " @click="shareComment = comment" data-toggle="modal" :data-target="'#ShareModal' + shareComment.id"><i class="fa fa-share-square"></i></span>
                    </div>
                </div>
            </div>
        </li>
        <a v-if="comments[index-1]" @click="aumento" class="btn text-white btn-secondary mt-3 d-block ">
            <span class="btn-text" style="text-transform: none !important;">{{ $t('componentes.comentarios.mas_comentarios') }} (10)</span>
        </a>
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
            <div class="form-row ">
                <div class="col-md-12 mb-10">
                    <label for="razon">{{ $t('componentes.comentarios.denunciar.razon') }}</label>
                    <select class="form-control custom-select d-block w-100" id="activityBox" v-model="denunciation.razon" value=" ">
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
                    <label for="descripcion">Descripción</label>
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
                            <span><i class="fa fa-times"></i></span>
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
                                    <i class="fa fa-envelope"></i> Email
                                </network>
                                <network class="btn btn-block btn-social btn-fb bg-indigo-dark-1 text-white" network="facebook">
                                    <span class="fa fa-facebook"></span> Facebook
                                </network>
                                <network class="btn btn-block btn-social bg-blue-dark-2 text text-white" network="linkedin">
                                    <i class="fa fa-linkedin"></i> LinkedIn
                                </network>
                                <network class="btn btn-block btn-social btn-twitter bg-blue-light-1 text text-white" network="twitter">
                                    <i class="fa fa-twitter"></i> Twitter
                                </network>
                                <network class="btn btn-block btn-social bg-green-light-1 text text-white" network="whatsapp">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
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
</template>

<script>
import axios from '../backend/axios';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import { API_URL } from '../backend/data_server';
import { APP_URL } from '../data/globals';
import SocialSharing from 'vue-social-sharing';

export default {
    name:"ChildrenComments",
    components: {
        SocialSharing,
    },
    props: {
        comments: Array,
        usuario: Number,
        route: Object,
        title: String,
        parent_id: Number
    },
    data() {
        return {
            buscar: "",
            seeMoreLimitText: 12,
            shareComment: {},
            vote: {
                comment_id: null,
                user_id: null,
                vote: null
            },
            comment: {
                body: null,
                user_id: null,
                project_id: this.project_id,
                files: []
            },
            comentario_ofensivo: false,
            comm: null,
            denounced_comment: null,
            index:10,
            palabrasOfensivas: ["mierda","conchetumare","culiao","qlo","culiaos","maricon","maricón","maraco","wea","puta"],
            denunciation: {
                razon: "",
                descripcion: ""
            },
            storageUrl: API_URL + "/api/storage/comments/",
            APP_URL,
            indexComments: [],
            mode: String
        }
    },

    methods: {

        async aumento(){

            try {
                const res = await axios.get("/comments?query=&state=0&parent_id=" + this.parent_id + "&offset=" + this.index)
                let newComments = res.data
                let flag = false
                this.index += 10;

                for (let i = 0 ;  i < newComments.length ; i++) {

                    for (let j = (this.index - 11) ; j < this.comments.length ; j++) {

                        if (newComments[i].id == this.comments[j].id) {
                            flag = true
                        }
                    }

                    if (!flag) {
                        this.comments.push(newComments[i])
                    }

                    flag = false
                }
                
            } catch (error) {
                console.log(error)
            }
        },

        addOrEditVote(voteValue, commentId) {
            this.$emit('addVoteSon', voteValue, commentId);
        },
        
        edit(com) {
            this.$emit('editSonComment',com)
        },

        dlt(id_d) {
            this.$emit('dltSonComment',id_d)
        },

        offensiveComment() {
            
            this.palabrasOfensivas.forEach(palabra => {

                if (this.comment.body.includes(palabra)) {
                    this.comentario_ofensivo = true
                }
            })
        },

        verificarLogin() {

            try {
                if (this.$store.getters.isLoggedIn) {
                    this.$bvModal.show('modal-denunciar')
                }
                else {
                    this.$toastr('warning','Debes tener una cuenta para poder denunciar un comentario','No has iniciado sesión')
                } 
            } catch (error) {
                console.log(error)
            }

            
        },

        async sendDenunciation() {
            
            let denounced_comment = {
                reason: this.denunciation.razon,
                description: this.denunciation.descripcion,
                comment_id: this.denounced_comment.id,
                user_id: this.$store.getters.userData.id,
            }
            
            try {
                const res = await axios.post('/denounces',denounced_comment)
                //console.log(res.status)
                this.$toastr('success','Su denuncia ha sido realizada','Denuncia enviada')
                this.cleanModal()
            } catch (error) {
                console.log(error)
                this.$toastr('error','Su denuncia no se ha podido realizar','Denuncia no enviada')
                this.cleanModal()
            }
        },

        seeMoreToggle: function (elementIndex) {
            let dots = document.getElementById("dots-" + elementIndex);
            let moreText = document.getElementById("seemore-" + elementIndex);
            let btnText = document.getElementById("myBtn-" + elementIndex);

            if (dots.style.display === "none") {
            dots.style.display = "inline";
            btnText.innerHTML = "Ver más";
            moreText.style.display = "none";
            } else {
            dots.style.display = "none";
            btnText.innerHTML = "Ver menos";
            moreText.style.display = "inline";
            }
        },

        downloadFile(file, idComentario) {

            axios({
                method:'get',
                url: '/storage/comments/' + idComentario + '/' + file.stored_name + '.' + file.extension,
                responseType: 'blob'
            })
            .then(function(response) {
                let type = response.headers['content-type'];
                let blob = new Blob([response.data], { type: type });
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = file.original_name + '.' + file.extension;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            .catch((error) => {
                console.log(error)
                this.$toastr('error','Algo salio mal, no se pudo descargar la imagen','Imagen no descargado')
            })

        },

        cleanModal () {
            this.denunciation.razon = ""
            this.denunciation.descripcion = ""
        }
        
    },

    created() {

        this.indexComments = this.comments
        
    },

    mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }
        
    },
}
</script>

<style scoped>

    .seemore {
        display: none;
    }

    .seemore-trigger {
        text-decoration: underline;
        cursor: pointer;
    }

    .Button {
        font-size:13px;
        font-weight: bold;
        background:white;
        color: black
    }
    .greenC {
        color: white;
        background:green;
    }
    .redC {
        color: white;
        background:red;
    }
    .greyC {
        color: white;
        background:grey;
    }

</style>