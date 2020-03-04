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
    import ProjectInterestCard from "../projects/ProjectInterestCard";
    import axios from "../../backend/axios";
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'ProjectsInterest',
        components: {
            ProjectInterestCard,
            Loading
        },
        props: {
            mode: String
        },
        data() {
            return {
                projects: [],
                largo: 7,
                loadProjects: true,
                fullPage: false
            };
        },
        mounted() {
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
                        this.projects = res.data.results;
                    })
                    .finally(() => {
                        this.loadProjects = false;
                    });
            }
        }
    };
</script>
