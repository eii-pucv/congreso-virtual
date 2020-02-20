<template>
    <div style="min-height: 720px;">
        <div class="container mt-20">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper" :class="mode==='dark'?'dark':'light'">
                        <h4 class="hk-sec-title text-center">{{ $t('administrador.componentes.aporte_espera.titulo') }}</h4>
                        <div class="row mt-20">
                            <div class="col-xl-12">
                                <form @submit="changeState" class="needs-validation">
                                    <div class="form-row div-align">
                                        <div class="col-md-10 mb-10">
                                            <label :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.aporte_espera.comentario') }}</label>
                                            <div class="media pa-20 border border-2 border-light col-12">
                                            <div class="media-body" v-if="comment.user">
                                                    <div class="row">
                                                        <div class="col-8 col-sm-10">
                                                            <h6 class="mb-2">{{ comment.user.name }} {{ comment.user.surname }}
                                                                <small>{{ comment.created_at }}</small>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <p>{{ comment.body }}</p>
                                                    <div v-if="comment.files.length" class="row mt-2 mb-3">
                                                        <div class="col-sm" v-for="(file,index) in comment.files" :key="file.id">
                                                            <div class="card border"> 
                                                                    <img v-if="(file.extension === 'png') || (file.extension === 'jpg') || (file.extension === 'jpeg') || (file.extension === 'bmp') || (file.extension === 'svg')" class="img-fluid" @click="downloadFile(file,comment.id)" :src="storageUrl + comment.id + '/' + file.stored_filename">
                                                                    <img v-if="file.extension === 'gif'" class="img-fluid" height="auto" width="126px" @click="downloadFile(file,comment.id)" src="../../../assets/img/gif-icon.png">
                                                                    <img v-if="file.extension === 'pdf'" class="img-fluid" height="auto" width="116px" @click="downloadFile(file,comment.id)" src="../../../assets/img/pdf-icon.png">
                                                                    <img v-if="(file.extension === 'amr') || (file.extension === 'ogg') || (file.extension === 'mp3') || (file.extension === 'm4a') || (file.extension === 'wav') " class="img-fluid" height="auto" width="126px" @click="downloadFile(file,comment.id)" src="../../../assets/img/audio-file-icon.png">
                                                                <div class="card-footer border justify-content-center">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <span :class="mode==='dark'?'text-primary':''">{{ file.original_filename.substring(0, seeMoreLimitText) }}</span><span v-if="file.original_filename.length > seeMoreLimitText" :id="'dots-' + index" :class="mode==='dark'?'text-primary':''">...</span><span v-if="file.original_filename.length > seeMoreLimitText" :id="'seemore-' + index" class="seemore" :class="mode==='dark'?'text-primary':''">{{ file.original_filename.substring( seeMoreLimitText) }}</span>
                                                                            <span v-if="file.original_filename.length > seeMoreLimitText" class="text-primary seemore-trigger" href="" @click="seeMoreToggle(index)" :id="'myBtn-' + index">{{ $t('componentes.comentarios.ver_mas') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <span class="votable float-right ml-1" @click="downloadFile(file,comment.id)"><i class="fa fa-download"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="media-body" v-if="!comment.user">
                                                    <div class="row">
                                                        <div class="col-8 col-sm-10">
                                                            <h6 class="mb-2">{{ $t('administrador.componentes.aporte_espera.no_identificado') }}
                                                                <small>{{ comment.created_at }}</small>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <p>{{ comment.body }}</p>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-20" v-if="comment">
                                        <a
                                            v-if="comment.state === 2 || comment.state === 1"
                                            @click="changeState(0)"
                                            type="submit"
                                            class="btn text-white bg-success mr-2"
                                        >
                                            <font-awesome-icon icon="clipboard-check"/>
                                            <span class="btn-text"> {{ $t('administrador.componentes.aporte_espera.habilitar') }}</span>
                                        </a>
                                        <a
                                                v-else-if="comment.state === 0"
                                                @click="changeState(2)"
                                                type="submit"
                                                class="btn text-white bg-primary mr-2"
                                        >
                                            <font-awesome-icon icon="ban"/>
                                            <span class="btn-text"> {{ $t('administrador.componentes.aporte_espera.bloquear') }}</span>
                                        </a>
                                        <a @click="volver()" class="btn text-white bg-danger">
                                            <font-awesome-icon icon="window-close"/>
                                            <span class="btn-text"> {{ $t('cancelar') }}</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../../../backend/axios"
import {API_URL} from "../../../backend/data_server";

export default {

    name: "aporteReportado",
    component: {

    },
    props: {
        comment_id: Number
    },
    data() {
        return {
            comment: {},
            storageUrl: API_URL + "/api/storage/comments/",
            seeMoreLimitText: 12,
            mode: String
        }
    },
    methods: {
        async changeState(newState) {
            try {
                const res = await axios.patch('/comments/' + this.comment_id + '/state',{
                    state: newState,
                })
                
                if (newState == 0) {
                    this.$toastr('success','','Comentario habilitado')
                }
                else {
                    this.$toastr('success','','Comentario bloqueado')
                }
                this.comment.state = newState
            } catch (error) {
                this.$toastr('error','No se ha podido bloquear el comentario','Comentario no bloqueado')
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
                this.$toastr('error','Algo salio mal, no se pudo descargar la imagen','Imagen no descargado')
            })

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

        volver () {
            window.history.back();
        },
    },
    async mounted() {
        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        try {
            const res = await axios.get('/comments/' + this.comment_id)
            this.comment = res.data
        } catch (error) {
            this.$toastr('error','No se ha encontrado el comentario','Comentarios')
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

.div-align {
    align-items: center;
    justify-content: center;
}
    
</style>