<template>
    <div style="min-height: 720px;">
        <div class="container" v-if="comment">
            <form @submit.prevent="savePerception">
                <div class="form-group">
                    <label for="titulo" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.editar_percepcion') }}</label>
                    <input type="number" class="form-control" v-model="perception" id="percepcion" placeholder="Ingrese la percepción" :style="mode==='dark'?'background: #080035; color: #fff':''">
                </div>
                <div class="button-list">
                    <button type="submit" class="btn btn-primary">{{ $t('guardar') }}</button>
                    <button class="btn btn-danger" @click="volver()">{{ $t('volver') }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>    
import axios from "../../../backend/axios";

export default {
    name: "crearTaxonomia",
    props: {
        comment_id: Number,
    },
    data() {
        return {
            perception: 0,
            comment: {},
            mode: String
        }
    },
    methods: {
        savePerception() {
            let percepcion = parseFloat(this.perception)
            axios.patch(`comments/${this.comment_id}/perception`, {
                perception: percepcion
            })
            .then(res => {
                this.$toastr('success','','Se ha actualizado la percepción del comentario')
            }).catch(err => {
                this.$toastr('error','No se ha podido actualizar la percepción del comentario','Percepción no actualizado')
            })
        },
                

        volver () {
            window.history.back();
        }
        
    },

    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
        }

        if (this.comment_id) {
            
            try {
                const res = await axios.get('/comments/' + this.comment_id)
                this.comment = res.data
                // console.log(this.comment)

                if (this.comment.perception == null) {
                    this.perception = parseInt(this.comment.perception)
                }

            } catch (error) {
                console.log(error)
                console.log("Algo ha salido mal");
            }
        }
        
    },
}
</script>

<style>
    
</style>