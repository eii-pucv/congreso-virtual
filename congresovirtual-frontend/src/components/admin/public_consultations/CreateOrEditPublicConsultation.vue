<template>
    <div class="container mt-20">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                    <h4 v-if="!consultation_id" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.titulo1') }}</h4>
                    <h4 v-else="" class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.titulo2') }}</h4>
                    <div class="row mt-20">
                        <div class="col-xl-12">
                            <form @submit="saveConsultation" class="needs-validation">
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.titulo') }}</label>
                                        <input type="text" class="form-control" v-model="consultation.titulo" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-4 mb-10">
                                        <label for="autor" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.autor') }}</label>
                                        <input type="text" class="form-control" v-model="consultation.autor" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                                    </div>
                                    <div class="col-md-3 mb-10">
                                        <label for="fechaInicio" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.fecha_inicio') }}</label>
                                        <date-picker v-model="consultation.fecha_inicio" :config="dateOptions" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></date-picker>
                                    </div>
                                    <div class="col-md-3 mb-10">    
                                        <label for="fechaTermino" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.fecha_termino') }}</label>
                                        <date-picker v-model="consultation.fecha_termino" :config="dateOptions" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></date-picker>
                                    </div>
                                </div>
                                <div class="row div-align">
                                    <div class="col-md-5 mb-10">
                                        <label for="categoria" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.categorias') }}</label>
                                        <multiselect v-model="categoryValue" tag-placeholder="Añadir como nueva categoria" placeholder="Buscar o añadir categoria" label="value" track-by="id" :options="categoryOptions" :multiple="true" :taggable="true" @tag="addCategory"></multiselect>
                                    </div>
                                    <div class="col-md-5 mb-10">
                                        <label for="tag" :style="mode==='dark'?'color: #fff':''">Tags</label>
                                        <multiselect v-model="tagsValue" tag-placeholder="Añadir como nuevo tag" placeholder="Buscar o añadir nuevo tag" label="value" track-by="id" :options="tagsOptions" :multiple="true" :taggable="true" @tag="addTag"></multiselect>
                                    </div>
                                </div>
                                <div class="row div-align"> 
                                    <div class="col-md-8 mb-20">
                                        <label for="iconPicker" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_consulta.icono') }}</label>
                                        <vue-icon-picker v-model="icon" height="150px" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></vue-icon-picker>
                                    </div>
                                </div>
                                <div class="row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="imagen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.imagen') }}</label>
                                        <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" @vdropzone-file-added="setImg" @vdropzone-file-added-manually="setImg" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></vue-dropzone>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="resumen" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.resumen') }}</label>
                                        <textarea class="form-control" id="resumen" rows="6" v-model="consultation.resumen" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></textarea>
                                    </div>
                                </div>
                                <div class="form-row div-align">
                                    <div class="col-md-10 mb-10">
                                        <label for="detalle" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_proyecto.detalle') }}</label>
                                        <tinymce id="edit" :tag="'textarea'" :config="config" v-model="consultation.detalle"></tinymce>
                                    </div>
                                </div>
                                <div class="text-center mt-20">        
                                    <a @click="saveConsultation()" type="submit" class="btn text-white bg-green votable"><font-awesome-icon icon="envelope"/><span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('administrador.componentes.crear_proyecto.enviar') }}</span></a>
                                    <a @click="volver()" class="btn text-white bg-red ml-10 votable"><font-awesome-icon icon="window-close"/><span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('cancelar') }}</span></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "../../../backend/axios";
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import VueIconPicker from 'vue-icon-picker';
    import Multiselect from 'vue-multiselect';
    import {API_URL} from "../../../backend/data_server";

    export default {
        name: "CreateOrEditPublicConsultation",
        components: {
            vueDropzone: vue2Dropzone,
            VueIconPicker,
            Multiselect
        },
        props:{
            consultation_id: Number
        },
        data (){
            return {
                mode: String,
                consulta: new FormData(),
                imagenAparte: new FormData(),
                imagenActual: "",
                extension: "",
                consultation: {
                    titulo: "",
                    autor: "",
                    resumen: "",
                    detalle: "",
                    imagen: new File([""],""),
                    estado: 1,
                    fecha_inicio: "",
                    fecha_termino: "",
                },
                dropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 10,
                    headers: { "My-Awesome-Header": "header value" },
                    addRemoveLinks: true
                },
                icon: {
                    name:"flag",
                    type:"fontawesome",
                    color:"#000000"
                },
                terminos: [],
                categoryValue: [],
                tagsValue: [],
                categoryOptions: [],
                tagsOptions: [],
                dateOptions: {
                    format: 'YYYY-MM-DD hh:mm:ss',
                    useCurrent: true,
                },
                actual_categories: [],
                actual_tags: []    
            }
        },
        methods: {

            removerImg(File) {
                this.$refs.myVueDropzone.removeFile(File);
            },

            setImg(file) {
                this.consultation.imagen = file;
            },

            addCategory (newTag) {

                const tag = {
                    value: newTag,
                    id: this.generatedRandom()
                }

                this.categoryValue.push(tag);
                this.categoryOptions.push(tag);
            },

            addTag(newTag) {

                const tag = {
                    value: newTag,
                    id: this.generatedRandom()
                }

                this.tagsValue.push(tag);
                this.tagsOptions.push(tag);
            },

            generatedRandom(){
                return window.crypto.getRandomValues();
            },

            volver () {
                window.history.back();
            },

            async saveConsultation (){

                let terms = this.categoryValue.concat(this.tagsValue)
                let consultation_terms = new Array()
                terms.filter((terminos) => {
                    consultation_terms.push(terminos.id)
                })

                this.consulta.append('titulo',this.consultation.titulo)
                this.consulta.append('autor',this.consultation.autor)
                this.consulta.append('estado',this.consultation.estado)
                this.consulta.append('detalle',this.consultation.detalle)
                this.consulta.append('resumen',this.consultation.resumen)
                this.consulta.append('fecha_inicio',this.consultation.fecha_inicio)
                this.consulta.append('fecha_termino',this.consultation.fecha_termino)

                for (var termino of consultation_terms) {
                    this.consulta.append('terms_id[]',termino)
                }

                if (this.consultation_id){

                    if ((JSON.stringify(this.categoryValue) !== JSON.stringify(this.actual_categories)) || (JSON.stringify(this.tagsValue) !== JSON.stringify(this.actual_tags))) {

                        try {
                            const res = await axios.delete('/consultations/' + this.consultation_id + '/terms')
                            console.log("términos eliminados")
                        } catch (error) {
                            console.log(error)
                        }

                        try {
                            const res = await axios.post('/consultations/' + this.consultation_id + '/terms', {
                                terms_id: consultation_terms
                            })
                            console.log("términos actualizados")
                        } catch (error) {
                            console.log(error)
                        }
                    }


                    if (this.imagenActual !== this.consultation.imagen.name + '.' + this.extension ) {
                    
                        this.imagenAparte.append('imagen', this.consultation.imagen, this.consultation.imagen.name)

                        try {
                            const res = await axios.post("/consultations/" + this.consultation_id + "/image", this.imagenAparte)
                            console.log("IMAGEN ACTUALIZADA")
                            this.$toastr('success','','Imagen actualizada')
                        } catch (error) {
                            console.log(error)
                            this.$toastr('error','','No se pudo subir la imagen')
                        }
                    }

                    try {
                        const res = await axios.put("/consultations/" + this.consultation_id, this.consultation)
                        console.log("CONSULTA PÚBLICA ACTUALIZADA")
                        this.$toastr('success', '', 'Consulta pública actualizada')
                    } catch (error) {
                        console.log(error)
                        this.$toastr('error', '', 'Consulta pública no actualizada')
                    }

                } else {

                    if (this.consultation.imagen) {
                        this.consulta.append('imagen',this.consultation.imagen,this.consultation.imagen.name)
                    }

                    try {
                        const res = await axios.post("/consultations", this.consulta)
                        console.log("Consulta GUARDADO")
                        this.$toastr('success', '', 'Consulta pública creada')
                    } catch (error) {
                        console.log("FALLO")
                        this.$toastr('error', '', 'Consulta pública no creada')
                    }

                }
            },
        },
        async mounted() {

            if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
                this.mode = "dark"
            } else {
                this.mode = "light"
            }

            if(this.consultation_id) {

                try {
                    const res = await axios.get("/consultations/" + this.consultation_id);
                    this.consultation = res.data;
                } catch (error) {
                    console.log("Ha ocurrido un error");
                }

                try {
                    const res = await axios.get('/consultations/' + this.consultation_id + '/terms')
                    this.terminos = res.data
                    this.terminos.forEach(term => {
                        if (term.taxonomies[0].id == 1) {
                            this.categoryValue.push(term)
                        }
                        if (term.taxonomies[0].id == 2) {
                            this.tagsValue.push(term)
                        }
                    })

                    this.actual_categories = this.categoryValue
                    this.actual_tags = this.tagsValue
                } catch (error) {
                    console.log(error)
                }

                if (this.consultation.imagen) {
                    
                    try {
                        let image = this.consultation.imagen
                        this.imagenActual = this.consultation.imagen
                        let imageName = this.consultation.imagen.split(".")[0] 
                        this.extension = image.substring(this.consultation.imagen.indexOf(".") + 1)

                        this.$refs.myVueDropzone.manuallyAddFile({size: "", name: imageName, type: "image/" + this.extension}, API_URL + "/api/storage/consultations/" + this.consultation_id + "/" + this.consultation.imagen)
                    } catch (error) {
                        console.log(error)
                        this.$toastr('error','','No se pudo cargar la imagen')
                    }
                }

                
            }

            try {
                const res = await axios.get('/terms');
                let terminos = res.data.results;
                terminos.forEach(term => {
                    if (term.taxonomies[0].id == 1) {
                        this.categoryOptions.push(term)
                    }
                    if (term.taxonomies[0].id == 2) {
                        this.tagsOptions.push(term)
                    }
                })
            } catch (error) {
                console.log("Algo ha salido mal al obtener las categorias ");
            } 
        }
    }

</script>

<style scoped>

.div-align {
    align-items: center;
    justify-content: center;
}

</style>