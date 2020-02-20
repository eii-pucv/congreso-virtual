<template>
    <div style="min-height: 720px;">
        <div class="container container-fluid mt-25 mb-10" :style="mode==='dark'?'background: #080035; color: #fff':''">
            <div class="row">
                <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                        <h3 class="hk-sec-title text-center" :class="mode==='dark'?'text-primary':''">{{ $t('administrador.componentes.palabras_ofensivas') }}</h3>
                        <mdb-container>
                            <mdb-datatable
                                :data="data"
                                bordered
                                arrows
                                responsive
                                :searching="true"
                                headerColor="elegant-color"
                                headerWhite
                                :sorting="true"
                            />
                        </mdb-container>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "../../backend/axios";
import { mdbDatatable, mdbContainer  } from 'mdbvue';
import { bus } from "../../main";

export default {
    
    name: "palabrasOfensivas",
    components: {
        mdbDatatable,
        mdbContainer
    },
    props: {

    },
    data() {
        return {
            mode: String,
            data: {
                columns: [
                    {
                        label: "N°",
                        field: "numero"
                    },
                    {
                        label: "Palabra",
                        field: "palabra",
                    },
                    {
                        label: "Acciones",
                        field: "botones"
                    }
                ],
                rows: [
                    {
                        numero: 1,
                        palabra: "mierda",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 2,
                        palabra: "conchetumare",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 3,
                        palabra: "culiao",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 4,
                        palabra: "qlo",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 5,
                        palabra: "culiaos",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 6,
                        palabra: "maricon",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 7,
                        palabra: "maricón",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 8,
                        palabra: "maraco",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 9,
                        palabra: "marako",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    },
                    {
                        numero: 10,
                        palabra: "puta",
                        botones: `<button type="button" class="btn btn-primary btn-sm dropdown-toggle text-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acción</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin">Eliminar</a>
                                    </div>`
                    }
                ]
            },
        }
    },
    methods: {
              
    },
    async mounted() {

        if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
            this.mode = "dark"
        } else {
            this.mode = "light"
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
            console.log("Algo ha salido mal al obtener las categorías");
        }    
    },

}

</script>

<style>
    
</style>