<template>
  <div class="container mt-20">
    <section class="hk-sec-wrapper col-xl-6 mx-auto" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
        <h4 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.documentos.titulo') }}</h4>
        <div class="row justify-content-center">
            <div class="border p-3 m-1">
                <label>{{ $t('administrador.componentes.documentos.archivo') }}:
                    <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
                </label>
                <div class="row justify-content-center">
                    <button :disabled="disabledBtn" v-on:click="submitFile()" :class="mode==='dark'?'btn btn-outline-grey px-30':'btn btn-outline-primary px-30'">{{ $t('administrador.componentes.documentos.subir') }}</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center">
            <div class="w-100 p-3 m-1" v-if="!loading">
                <ul v-if="documents" class="list-unstyled px-20 col-12">
                    <li v-for="(document, index) in documents" :key="document.id" class="media pa-20 border border-2 border-light col-12 mb-5">
                        <div class="media-body" v-if="document">
                            <h7>{{document.original_name}}</h7>
                            <br/>
                            <small>{{document.updated_at}}</small>
                            <div class="btn-group btn-group-toggle btn-block mt-10">
                                <button v-on:click="download(document)" :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-primary'">{{ $t('administrador.componentes.documentos.descargar') }}</button>
                                <button v-on:click="eliminarFile(document, index)" :class="mode==='dark'?'btn btn-outline-grey':'btn btn-outline-danger'">{{ $t('administrador.componentes.documentos.eliminar') }}</button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
  </div>
</template>

<script>
import axios from '../../../backend/axios';
export default {
    name: 'ProjectFiles',
    props: {
        project_id: Number,
    },
    components: {
    },
    data(){
        return {
            file: '',
            document: Object,
            documents: Array,
            loading: true,
            mode: String,
            disabledBtn: true
        }
    },
    mounted() {
        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }
    },
    methods: {
        submitFile() {
            let formData = new FormData();
            /*
                Agrega el form data que necesitamos submit
            */
            formData.append('files[]', this.file);
            // console.log(this.file);
            /*
              Hace la petición de POST (CONSULTAR A DÓNDE DEBERÍA SUBIRLO)
            */
            axios.post('/projects/' + this.project_id + '/files',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(() => {
                console.log('EXITO!!');
                this.disabledBtn = true;
                let fileInput = document.getElementById("file")
                if(fileInput) {
                    document.getElementById("file").value = "";
                }
                this.getDocuments()
                this.$toastr('success', '', 'Archivo subido');
            })
            .catch(() => {
                console.log('ERROR!!');
                this.disabledBtn = true;
                let fileInput = document.getElementById("file")
                if(fileInput) {
                    document.getElementById("file").value = "";
                }
                this.$toastr('error','','No se pudo subir el archivo, quizás superó el tamaño máximo del archivo permitido');
            });
        },

      /*
        Maneja un cambio en el archivo subido
      */
      handleFileUpload(){
        this.file = this.$refs.file.files[0];
        this.disabledBtn = false;
      },

      /*
        Descargar archivo
      */
      download(file) {
        axios({
          method:'get',
          url: '/storage/projects/' + this.project_id + '/' + file.stored_name + '.' + file.extension,
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
              this.$toastr("success", '', 'Archivo listo para descargar');
              document.body.removeChild(link);
              console.log('pasa');
            })
            .catch((e) => {
                console.log('error');
                console.log(e);
                this.$toastr('error','','No se pudo descargar el archivo');
            });
      },

      eliminarFile(file, index){
        axios
          .delete("/projects/" + this.project_id + "/file/"+file.id)
          .then(res => {
            this.documents.splice(index, 1);
            this.$toastr('success', '', 'Archivo eliminado');
            console.log("Éxito al eliminar el archivo");
          })
          .catch(() => {
              console.error("FAIL");
              this.$toastr('error','','No se pudo eliminar el archivo');
          });
      },
        getDocuments() {
            axios
                .get("/projects/" + this.project_id + "/files")
                .then(res => {
                this.documents = res.data;
                this.loading = false;
                })
                .catch(() => {
                    console.error("FAIL");
                });
        }
    },

    created(){
        this.getDocuments()
    },
  }
</script>