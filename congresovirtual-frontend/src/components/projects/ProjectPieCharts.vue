<template>
    <div v-if="!loadProject" class="row mx-0">
        <div class="col-md-6 pa-20">
            <h3 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">
                {{ $t('proyecto.contenido.congreso_virtual') }}
            </h3>
            <pie-chart :data="generalVotingChartData" :options="genericStackedChartOptions"></pie-chart>
        </div>
        <div class="col-md-6 pa-20">
            <h3 class="hk-sec-title text-center" :style="mode==='dark'?'color: #fff':''">
                {{ $t('proyecto.contenido.senado') }}
            </h3>
            <div v-if="loadParliamentaryVotingData" class="vld-parent" style="height: 200px;">
                <Loading
                        :active.sync="loadParliamentaryVotingData"
                        :is-full-page="fullPage"
                        :height="128"
                        :color="'#000000'"
                        :background-color="'transparent'"
                ></Loading>
            </div>
            <pie-chart v-if="!loadParliamentaryVotingData && parliamentaryVotingChartData" :data="parliamentaryVotingChartData" :options="genericStackedChartOptions"></pie-chart>
            <div v-else>
                <p>{{ $t('proyecto.contenido.no_hay_datos_senado') }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import PieChart from "../../PieChart.js";
    import ChartDataLabels from 'chartjs-plugin-datalabels';
    import Loading from 'vue-loading-overlay';
    import axios from '../../backend/axios';
    import axioma from 'axios';

    export default {
        name: 'ProjectStackedChart',
        components: {
            'pie-chart': PieChart,
            Loading
        },
        props: {
            project: Object,
            project_id: Number
        },
        data() {
            return {
                generalVotingChartData: null,
                parliamentaryVotingChartData: null,
                genericStackedChartOptions: {
                    hoverBorderWidth: 20
                },
                agreeColor: '#008000',
                disagreeColor: '#f83f37',
                abstentionColor: '#808080',
                loadProject: true,
                loadParliamentaryVotingData: true,
                fullPage: false,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            if(!this.project && this.project_id) {
                this.getProject();
            } else {
                this.configComponent();
                this.loadProject = false;
            }
        },
        methods: {
            getProject() {
                axios
                    .get('/projects/' + this.project_id)
                    .then(res => {
                        this.project = res.data;
                        this.configComponent();
                    })
                    .finally(() => {
                        this.loadProject = false;
                    });
            },
            configComponent() {
                this.generateGeneralVotingChartData();
                this.generateParliamentaryVotingChartData();
            },
            generateGeneralVotingChartData() {
                this.generalVotingChartData = {
                    labels: [this.$t('proyecto.contenido.a_favor'), this.$t('proyecto.contenido.en_contra'), this.$t('proyecto.contenido.abstenciones')],
                    datasets: [
                        {
                            backgroundColor: [this.agreeColor, this.disagreeColor, this.abstentionColor],
                            data: [
                                Math.round((this.project.votos_a_favor / (this.project.votos_a_favor + this.project.votos_en_contra + this.project.abstencion)) * 100),
                                Math.round((this.project.votos_en_contra / (this.project.votos_a_favor + this.project.votos_en_contra + this.project.abstencion)) * 100),
                                Math.round((this.project.abstencion / (this.project.votos_a_favor + this.project.votos_en_contra + this.project.abstencion)) * 100)
                            ]
                        }
                    ]
                };
            },
            generateParliamentaryVotingChartData() {
                axios
                    .get('settings', {
                        params: {
                            key: 'external_api'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            let parliamentVoteProjectUrl = JSON.parse(res.data[0].value).parliament_vote_project;
                            axioma
                                .create()
                                .get(parliamentVoteProjectUrl + this.project.boletin)
                                .then(res => {
                                    let parliamentaryVotingArray = res.data;
                                    if(parliamentaryVotingArray.length > 0) {
                                        let parliamentaryVotingData = {
                                            agreeeVotes: parliamentaryVotingArray.filter(voteData => voteData.VOTO === 1).length,
                                            disagreeVotes: parliamentaryVotingArray.filter(voteData => voteData.VOTO === 2).length,
                                            abstentionVotes: parliamentaryVotingArray.filter(voteData => voteData.VOTO === 3).length,
                                            totalVotes: parliamentaryVotingArray.length
                                        };

                                        this.parliamentaryVotingChartData = {
                                            labels: [this.$t('proyecto.contenido.a_favor'), this.$t('proyecto.contenido.en_contra'), this.$t('proyecto.contenido.abstenciones')],
                                            datasets: [
                                                {
                                                    backgroundColor: [this.agreeColor, this.disagreeColor, this.abstentionColor],
                                                    data: [
                                                        Math.round((parliamentaryVotingData.agreeeVotes / parliamentaryVotingData.totalVotes) * 100),
                                                        Math.round((parliamentaryVotingData.disagreeVotes / parliamentaryVotingData.totalVotes) * 100),
                                                        Math.round((parliamentaryVotingData.abstentionVotes / parliamentaryVotingData.totalVotes) * 100)
                                                    ]
                                                }
                                            ]
                                        };
                                    }
                                })
                                .finally(() => {
                                    this.loadParliamentaryVotingData = false;
                                });
                        } else {
                            this.loadParliamentaryVotingData = false;
                        }
                    })
                    .catch(() => {
                        this.loadParliamentaryVotingData = false;
                    });
            }
        }
    }
</script>