<template>
    <div style="min-height: 720px;">
        <div class="container mt-20" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <div class="row">
                    <div class="col-xl-12">
                        <form @submit.prevent="saveData">
                            <div class="form-group">    
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.articulo.titulo') }}</label>
                                <input type="text" class="form-control" v-model="article.titulo" id="titulo" placeholder="Ingrese título" name="titulo" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                            </div>
                            <div class="form-group">
                                <label for="idea_fundamental" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.articulo.detalle') }}</label>
                                <textarea type="text" class="form-control" rows="3" v-model="article.detalle"  placeholder="Ingrese descripción" name="idea_fundamental" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></textarea>
                            </div>
                            <div class="button-list">
                                <button type="submit" class="btn btn-primary">{{ $t('guardar') }}</button>
                                <button class="btn btn-danger" @click="volver()">{{ $t('volver') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    import axios from "../../../backend/axios";
    export default {
        name: 'CreateOrEditArticle',
        props: {
            project_id: Number,
            article_id: Number
        },
        data() {
            return {
                article: {},
                mode: String
            }
        },
        mounted() {

            if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
                this.mode = "dark"
            } else {
                this.mode = "light"
            }

            if(this.article_id) {
                axios
                    .get("/articles/" + this.article_id)
                    .then(res => {
                        this.article = res.data;

                    })
                    .catch(() => console.log("FALLO"));
            }
        },
        methods: {
            saveData(){

                if (!this.article_id){
                    axios
                        .post("/articles",{
                            project_id: this.project_id,
                            titulo: this.article.titulo,
                            detalle: this.article.detalle
                        })
                        .then(res => {
                            this.article = res.data;
                            this.$toastr('success', '', 'Artículo creado')
                        })
                        .catch(() => console.log("FALLO"));
                } else {
                    axios
                        .put("/articles/" + this.article_id ,{
                            project_id: this.project_id,
                            titulo: this.article.titulo,
                            detalle: this.article.detalle
                        })
                        .then(res => {
                            this.article = res.data;
                            this.$toastr('success', '', 'Artículo actualizado')
                        })
                        .catch((error) => console.log(error));
                }
            },

            volver () {
                window.history.back();
            }
        }
    }
</script>

<style scoped>

</style>