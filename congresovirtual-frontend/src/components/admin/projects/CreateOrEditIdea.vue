<template>
    <div style="min-height: 720px;">
        <div class="container mt-20" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <div class="row">
                    <div class="col-xl-12">
                        <form @submit.prevent="saveData">
                            <div class="form-group">
                                <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.articulo.titulo') }}</label>
                                <input type="text" class="form-control" v-model="idea.titulo" id="titulo" placeholder="Ingrese título" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''">
                            </div>
                            <div class="form-group">
                                <label for="idea_fundamental" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.idea') }}</label>
                                <textarea type="text" class="form-control" rows="3" v-model="idea.detalle" id="detalle" placeholder="Ingrese descripción" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></textarea>
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
        name: 'CreateOrEditIdea',
        props: {
            project_id: Number,
            idea_id: Number,
        },
        data() {
            return {
                idea:{},
                ideas: [],
                mode: String
            }
        },
        async mounted() {

            if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
                this.mode = "dark"
            } else {
                this.mode = "light"
            }

            if(this.idea_id) {
                axios
                    .get("/ideas/" + this.idea_id)
                    .then(res => {
                        this.idea = res.data;

                    })
                    .catch(() => console.log("FALLO"));
            }
            
        },
        methods: {
            saveData(){

                if(!this.idea_id){
                    axios
                        .post("/ideas",{
                            project_id: this.project_id,
                            titulo: this.idea.titulo,
                            detalle: this.idea.detalle
                        })
                        .then(res => {
                            this.idea = res.data;
                            this.$toastr('success', '', 'Idea fundamental creado')
                        })
                        .catch(() => console.log("FALLO"));
                } else {
                    axios
                        .put("/ideas/" +this.idea_id ,{
                            project_id: this.project_id,
                            titulo: this.idea.titulo,
                            detalle: this.idea.detalle
                        })
                        .then(res => {
                            this.idea = res.data;
                            this.$toastr('success', '', 'Idea fundamental actualizado')
                        })
                        .catch(() => console.log("FALLO"));
                }
            },

            volver () {
                window.history.back();
            },
        }
    }
</script>

<style scoped>

</style>