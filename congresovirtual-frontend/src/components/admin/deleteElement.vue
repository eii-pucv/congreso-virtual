<template>
    <section>
        <div class="ma-80 text-center">
            <img class="img-fluid" width="200" src="../../assets/img/fail-icon.png">
            <h1 class="display-4">{{ $t('administrador.componentes.eliminar.seguro') }}</h1>
            <br>
            <p class="lead">{{ $t('administrador.componentes.eliminar.acepta') }}</p>
            <div class="button-list text-center">
                <button type="button" class="btn text-white bg-danger votable" @click="eliminarItem()"><font-awesome-icon icon="trash"/><span class="btn-text"> {{ $t('administrador.componentes.eliminar.eliminar') }}</span></button>
                <button class="btn text-white bg-primary ml-10 votable" @click="volver()"><font-awesome-icon icon="window-close"/><span class="btn-text"> {{ $t('cancelar') }}</span></button>
            </div>
        </div>
    </section>
</template>
<script>
import axios from "../../backend/axios";

export default {
    name: "deleteElement",
    components: {},
    props: {
        item_id: Number,
        subItem_id: Number,
        list_name: String,
    },
    data() {
        return {}
    },
    methods: { 
        async eliminarItem() {
            if (this.subItem_id) {
                try {
                    const res = await axios.delete('/' + this.list_name + 's' + '/' + this.subItem_id);
                    this.$toastr('success','','Se ha eliminado el item');
                } catch (error) {
                    this.$toastr('error','','Algo ha salido mal, intente nuevamente');
                }
            }
            else {
                try {
                    const res = await axios.delete('/' + this.list_name + 's' + '/' + this.item_id);
                    this.$toastr('success','','Se ha eliminado el item');
                    setTimeout(() => {this.volver()}, 2000);
                } catch (error) {
                    this.$toastr('error','','Algo ha salido mal, intente nuevamente');
                }
            }   
        },

        volver () {
            // window.history.back()
            // se reemplazó por este nuevo metodo porque al borrar el elemento y hacer un history.back, quedaba el objeto eliminado mostrado en pantalla
            location.replace(document.referrer);
        }

    },
    mounted() { /* console.log('/' + this.list_name + 's' + '/' + this.item_id) */},
}
</script>

<style>
    
</style>


