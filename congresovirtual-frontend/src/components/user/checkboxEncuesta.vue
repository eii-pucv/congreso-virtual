<template>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" :id="'customCheck'+categoria.id">
        <label class="custom-control-label" :for="'customCheck'+categoria.id">{{categoria.nombre}}</label>
    </div>
</template>

<script>
    export default {
        name: "checkboxEncuesta",
        props: {
            categoria: Object
        },
    }
</script>

<style scoped>

</style>