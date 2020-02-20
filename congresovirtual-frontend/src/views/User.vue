<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 pa-0" :class="mode==='dark'?'dark':'light'">
                    <div class="profile-cover-wrap overlay-wrap">
                        <div class="container profile-cover-content py-50">
                            <div class="hk-row">
                                <div class="col-lg-6">
                                    <div v-if="loaded && user" class="media align-items-center">
                                        <div class="media-img-wrap d-flex">
                                            <div class="avatar">
                                                <img
                                                        class="avatar-img rounded-circle default-avatar-img"
                                                        :src="getImgUrl()"
                                                        style="object-fit: cover;"
                                                />
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="text-capitalize display-6 mb-5 font-weight-400">{{ user.name }} {{ user.surname }}</div>
                                            <star-rating
                                                    :rating="1"
                                                    :read-only="true"
                                                    :show-rating="false"
                                                    :star-size="20"
                                                    class="mb-5 justify-content-center justify-content-md-start"
                                            ></star-rating>
                                            <div class="font-14">
                                                <span class="mr-5">
                                                    <span class="font-weight-500 pr-5">{{ user.votes_count }}</span>
                                                    <span class="mr-5">{{ $t('perfil_usuario.contenido.votos') }}</span>
                                                </span>
                                                <span>
                                                    <span class="font-weight-500 pr-5">{{ user.comments_count }}</span>
                                                    <span class="mr-5">{{ $t('perfil_usuario.contenido.comentarios') }}</span>
                                                </span>
                                                <span>
                                                    <span class="font-weight-500 pr-5">{{(user.comments_count * 10) + (user.votes_count * 5) }}</span>
                                                    <span>{{ $t('perfil_usuario.contenido.puntos') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-tab shadow-bottom">
                        <div class="container">
                            <ul id="myTab" class="nav nav-light nav-tabs" role="tablist">
                                <li class="nav-item active">
                                    <a
                                            id="projects-interest-tab"
                                            data-toggle="tab"
                                            href="#projects-interest"
                                            role="tab"
                                            aria-controls="projects-interest"
                                            aria-selected="true"
                                            class="nav-link active"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.proyectos_interes') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                            id="profile-edit-tab"
                                            data-toggle="tab"
                                            href="#profile-edit"
                                            role="tab"
                                            aria-controls="profile-edit"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.editar_perfil') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                            id="topics-interest-tab"
                                            data-toggle="tab"
                                            href="#topics-interest"
                                            role="tab"
                                            aria-controls="topics-interest"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.temas_interes') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                            id="user-votes-tab"
                                            data-toggle="tab"
                                            href="#user-votes"
                                            role="tab"
                                            aria-controls="user-votes"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.votaciones') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                            id="user-comments-tab"
                                            data-toggle="tab"
                                            href="#user-comments"
                                            role="tab"
                                            aria-controls="user-comments"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.comentarios') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                            id="user-proposals-tab"
                                            data-toggle="tab"
                                            href="#user-proposals"
                                            role="tab"
                                            aria-controls="user-proposals"
                                            aria-selected="true"
                                            class="nav-link"
                                            :style="mode==='dark'?'color: #fff':''"
                                    >
                                        {{ $t('perfil_usuario.contenido.tab.propuestas') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content mt-20 mb-20">
                        <div
                                class="tab-pane fade show active"
                                id="projects-interest"
                                role="tabpanel"
                                aria-labelledby="projects-interest-tab"
                        >
                            <div class="container">
                                <div v-if="loaded && user" class="hk-row">
                                    <ProjectsInterest v-bind:mode="mode"></ProjectsInterest>
                                </div>
                            </div>
                        </div>
                        <div
                                class="tab-pane fade show"
                                id="profile-edit"
                                role="tabpanel"
                                aria-labelledby="profile-edit-tab"
                        >
                            <div v-if="loaded && !loadUser" class="container">
                                <ProfileEdit></ProfileEdit>
                            </div>
                        </div>
                        <div
                                class="tab-pane fade show"
                                id="topics-interest"
                                role="tabpanel"
                                aria-labelledby="topics-interest-tab"
                        >
                            <div v-if="loaded && user" class="container">
                                <TopicsInterest></TopicsInterest>
                            </div>
                        </div>
                        <div
                                class="tab-pane fade show"
                                id="user-votes"
                                role="tabpanel"
                                aria-labelledby="user-votes-tab"
                        >
                            <div v-if="loaded && user" class="container">
                                <userVotes></userVotes>
                            </div>
                        </div>
                        <div
                                class="tab-pane fade show"
                                id="user-comments"
                                role="tabpanel"
                                aria-labelledby="user-comments-tab"
                        >
                            <div v-if="loaded && user" class="container">
                                <userComments></userComments>
                            </div>
                        </div>
                        <div
                                class="tab-pane fade show"
                                id="user-proposals"
                                role="tabpanel"
                                aria-labelledby="user-proposals-tab"
                        >
                            <div v-if="loaded && user" class="container">
                                <userProposals :maxPetitions="maxPetitions"></userProposals>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "../backend/axios";
    import ProfileEdit from "../components/user/ProfileEdit";
    import TopicsInterest from "../components/user/TopicsInterest";
    import { API_URL } from "../backend/data_server";
    import ProjectsInterest from "../components/user/ProjectsInterest";
    import StarRating from "vue-star-rating";
    import userComments from "../components/user/UserComments";
    import userVotes from "../components/user/UserVotes";
    import userProposals from "../components/user/UserProposals";

    export default {
        name: "User",
        components: {
            ProjectsInterest,
            ProfileEdit,
            TopicsInterest,
            StarRating,
            userComments,
            userVotes,
            userProposals
        },
        props: {
            mode: String
        },
        data() {
            return {
                user: {
                    votes_count: 0,
                    comments_count: 0,
                    name: "",
                    surname: ""
                },
                loaded: false,
                loadUser: true,
                comments: [],
                votes: [],
                maxPetitions: null
            };
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getActividad();

            try {
                const res = await axios.get('/settings?key=max_necessary_petitions');
                this.maxPetitions = res.data.length > 0 ? JSON.parse(res.data[0].value).number_petitions : 0;
            } catch (error) {
                //console.log(error);
            }
            //this.loaded = true;
        },
        methods: {
            getImgUrl() {
                if(this.user.avatar) {
                    return (API_URL + '/api/storage/avatars/' + this.user.id + '/' + this.user.avatar);
                }
                return null;
            },
            getActividad() {
                axios
                    .get('/users/' + JSON.parse(localStorage.user).id + '/comments')
                    .then(res => {
                        this.comments = res.data;
                    })
                    .catch(() => {
                        //console.log('FALLO');
                    })
                    .finally(() => {
                        this.loaded = true;
                    });

                axios
                    .get('/auth/user')
                    .then(res => {
                        this.user = res.data;
                    })
                    .catch(() => {
                        //console.log('FALLO');
                    })
                    .finally(() => {
                        this.loadUser = false;
                    });

                axios
                    .get('/users/' + JSON.parse(localStorage.user).id + '/votes')
                    .then(res => {
                        this.votes = res.data;
                    })
                    .catch(() => {
                        //console.log("FALLO");
                    });

                $("#myTab a").on("click", function(e) {
                    e.preventDefault();
                    $(this).tab("show");
                });
            }
        }
    };
</script>

<style>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>
