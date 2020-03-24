<template>
    <section>
        <div class="ma-80 text-center">
            <img class="img-fluid" width="200" src="../../assets/img/fail-icon.png">
            <h1 class="display-4">{{ $t('administrador.componentes.eliminar.seguro') }}</h1>
            <br>
            <p class="lead">{{ $t('administrador.componentes.eliminar.acepta') }}</p>
            <div class="button-list text-center">
                <button
                        @click="deleteElement"
                        type="button"
                        class="btn text-white bg-danger"
                >
                    <i class="fas fa-trash"></i> {{ $t('administrador.componentes.eliminar.eliminar') }}
                </button>
                <button @click.prevent="back" class="btn btn-danger text-white ml-10">
                    <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                </button>
            </div>
        </div>
    </section>
</template>
<script>
    import axios from "../../backend/axios";

    export default {
        name: 'DeleteElement',
        props: {
            item_id: Number,
            subItem_id: Number,
            list_name: String,
        },
        data() {
            return { };
        },
        methods: {
            async deleteElement() {
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
            back() {
                this.$router.go(-1);
            }
        }
    }
</script>

