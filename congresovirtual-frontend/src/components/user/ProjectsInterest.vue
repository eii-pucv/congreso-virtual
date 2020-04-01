<template>
    <div class="hk-col col-12">
        <div v-if="loadProjects" class="vld-parent" style="height: 500px;">
            <loading
                    :active.sync="loadProjects"
                    :is-full-page="fullPage"
                    :height="128"
            ></loading>
        </div>
        <div v-if="!loadProjects">
            <ProjectInterestCard
                    v-for="project in projects"
                    :project="project"
                    :key="project.id"
            ></ProjectInterestCard>
            <div v-if="projects.length === 0" class="py-50 text-center">{{ $t('perfil_usuario.componentes.proyectos_interes.no_hay_resultados') }}</div>
        </div>
    </div>
</template>

<script>
    import ProjectInterestCard from "./ProjectInterestCard";
    import axios from "../../backend/axios";
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectsInterest',
        components: {
            ProjectInterestCard,
            Loading
        },
        data() {
            return {
                projects: [],
                largo: 7,
                currentMoment: this.$moment().local(),
                loadProjects: true,
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

            axios
                .get('/users/' + JSON.parse(localStorage.user).id + '/terms')
                .then(res => {
                    let terms = res.data;
                    let termsId = [];
                    terms.forEach(term => {
                        termsId.push(term.id);
                    });
                    this.getProjects(termsId);
                });
        },
        methods: {
            getProjects(termsId) {
                axios
                    .get('/projects', {
                        params: {
                            'is_public': 1,
                            'terms': termsId.length > 0 ? JSON.stringify(termsId) : null,
                            'order_by': 'fecha_inicio',
                            'order': 'DESC',
                            'limit': 7
                        }
                    })
                    .then(res => {
                        let auxProjects = res.data.results;
                        this.projects = auxProjects.filter(project => this.getIsAvailableVoting(project));
                        this.projects = this.projects.concat(auxProjects.filter(project => !this.getIsAvailableVoting(project)));
                    })
                    .finally(() => {
                        this.loadProjects = false;
                    });
            },
            getIsAvailableVoting(project) {
                let votingStartDate = this.$moment.utc(project.fecha_inicio, 'YYYY-MM-DD HH:mm:ss').local();
                let votingEndDate = this.$moment.utc(project.fecha_termino, 'YYYY-MM-DD HH:mm:ss').local();
                return project.is_enabled && project.etapa !== 3 && this.currentMoment.isBetween(votingStartDate, votingEndDate);
            }
        }
    };
</script>
