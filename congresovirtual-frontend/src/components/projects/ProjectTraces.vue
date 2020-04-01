<template>
    <div class="">
        <div class="container py-2 custom-scrollbar-wk custom-scrollbar-mz" style="max-height: 1000px; overflow: auto;">
            <div class="row" v-for="(event, index) in seguimiento" :key="event.id">
                <div class="col-auto text-center flex-column d-none d-sm-flex">
                    <div class="row h-50">
                        <div class="col border-right">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                    <h5 class="m-2">
                        <span class="badge badge-pill" :class="index === 0 ? 'bg-success' : 'bg-secondary'">&nbsp;</span>
                    </h5>
                    <div class="row h-50">
                        <div class="col border-right">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                </div>
                <div class="col py-2">
                    <div class="card shadow" v-bind:class="{ 'border-success' : (index === 0) }">
                        <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                            <div class="float-right" v-bind:class="{ 'text-success' : (index === 0) }">{{ new Date(event.FECHA_SORT) | moment($t('componentes.moment.formato_sin_hora')) }}</div>
                            <h5 class="card-title" v-bind:class="{ 'text-success' : (index === 0) }" :style="mode==='dark'?'color: #fff':''">{{ event.TRAMITE }} </h5>
                            <h5><span class="badge badge-info">{{ event.CAMDELTRAMITE }}</span></h5>
                            <p class="card-text"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="seguimiento.length < 1">
                <div class="col-auto text-center flex-column d-none d-sm-flex">
                    <div class="row h-50">
                        <div class="col border-right">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                    <h5 class="m-2">
                        <span class="badge badge-pill bg-success">&nbsp;</span>
                    </h5>
                    <div class="row h-50">
                        <div class="col border-right">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                </div>
                <div class="col py-2">
                    <div class="card border-success shadow">
                        <div class="card-body">
                            <p class="card-text">{{ $t('proyecto.componentes.seguimiento.sin_datos') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    import axioma from 'axios';

    export default {
        name: 'ProjectTraces',
        props: {
            project: Object
        },
        data() {
            return {
                seguimiento: [],
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getTraces();
        },
        methods: {
            getTraces() {
                axios
                    .get('settings', {
                        params: {
                            key: 'external_api'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            let traceInfoProjectUrl = JSON.parse(res.data[0].value).trace_info_project;
                            axioma
                                .create()
                                .get(traceInfoProjectUrl + this.project.boletin)
                                .then(res => {
                                    this.seguimiento = res.data;
                                    this.seguimiento.sort((a, b) => {
                                        return new Date(b.FECHA_SORT) - new Date(a.FECHA_SORT);
                                    });
                                });
                        }
                    });
            }
        }
    };
</script>
