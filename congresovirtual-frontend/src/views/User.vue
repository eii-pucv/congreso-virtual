<template>
    <div class="container-fluid px-0" :class="mode==='dark'?'dark':'light'">
        <div class="profile-cover-wrap overlay-wrap vld-parent">
            <div v-if="loadUser" style="height: 200px;">
                <Loading
                        :active.sync="loadUser"
                        :loader="'dots'"
                        :is-full-page="false"
                        :height="128"
                ></Loading>
            </div>
            <h3 v-if="!loadUser && !user" class="container profile-cover-content text-center py-50">
                {{ $t('perfil_usuario.contenido.usuario_no_existe') }}
            </h3>
            <div v-if="!loadUser && user" class="container profile-cover-content py-50">
                <div class="hk-row">
                    <div class="col-12">
                        <div class="media align-items-center">
                            <div class="media-img-wrap d-flex">
                                <b-avatar
                                        size="100px"
                                        variant="light"
                                        :src="getImgUrl()"
                                        :text="user.name.slice(0, 2)"
                                ></b-avatar>
                            </div>
                            <div class="media-body">
                                <router-link
                                        v-if="user.username"
                                        class="display-6 font-weight-400 mb-5"
                                        :class="mode==='dark'?'text-white':''"
                                        :to="{ path: '/user', query: { 'username': user.username } }"
                                >
                                    {{ user.username }} ({{ user.name }} {{ user.surname }})
                                </router-link>
                                <router-link
                                        v-else
                                        class="display-6 font-weight-400 mb-5"
                                        :class="mode==='dark'?'text-white':''"
                                        :to="{ path: '/user', query: { 'user_id': user.id } }"
                                >
                                    {{ user.name }} {{ user.surname }}
                                </router-link>
                                <star-rating
                                        v-if="isAvailableGamification"
                                        :rating="getCalculatedRating()"
                                        :increment="0.01"
                                        :read-only="true"
                                        :show-rating="false"
                                        :star-size="20"
                                        class="justify-content-center justify-content-lg-start mb-5"
                                ></star-rating>
                                <div class="font-14">
                                    <span class="mr-10">
                                        <span class="font-weight-500">{{ user.votes_count }}</span>
                                        {{ $t('perfil_usuario.contenido.votos') }}
                                    </span>
                                    <span class="mr-10">
                                        <span class="font-weight-500">{{ user.comments_count }}</span>
                                        {{ $t('perfil_usuario.contenido.comentarios') }}
                                    </span>
                                    <span v-if="isAvailableGamification">
                                        <span class="font-weight-500">{{ user.player.points }}</span>
                                        {{ $t('perfil_usuario.contenido.puntos') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!loadUser && user" class="profile-tab shadow-bottom">
            <div class="container">
                <ul class="nav nav-light nav-tabs" role="tablist">
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
                    <li v-if="isAvailableGamification" class="nav-item">
                        <a
                                id="user-rewards-tab"
                                data-toggle="tab"
                                href="#user-rewards"
                                role="tab"
                                aria-controls="user-rewards"
                                aria-selected="true"
                                class="nav-link"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('perfil_usuario.contenido.tab.recompensas') }}
                        </a>
                    </li>
                    <li v-if="!readOnly && isAvailableGamification" class="nav-item">
                        <a
                                id="available-rewards-tab"
                                data-toggle="tab"
                                href="#available-rewards"
                                role="tab"
                                aria-controls="available-rewards"
                                aria-selected="true"
                                class="nav-link"
                                :style="mode==='dark'?'color: #fff':''"
                        >
                            {{ $t('perfil_usuario.contenido.tab.recompensas_disponibles') }}
                        </a>
                    </li>
                    <li v-if="!readOnly" class="nav-item">
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
                </ul>
            </div>
        </div>
        <div v-if="loadUser || !user" class="tab-content mt-20 mb-20" style="height: 300px;"></div>
        <div v-if="!loadUser && user" class="tab-content mt-20 mb-20">
            <div
                    id="projects-interest"
                    class="tab-pane fade show active"
                    role="tabpanel"
                    aria-labelledby="projects-interest-tab"
            >
                <ProjectsInterest :user_id="user.id"></ProjectsInterest>
            </div>
            <div
                    id="user-votes"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="user-votes-tab"
            >
                <UserVotes :user_id="user.id"></UserVotes>
            </div>
            <div
                    id="user-comments"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="user-comments-tab"
            >
                <UserComments :user_id="user.id"></UserComments>
            </div>
            <div
                    id="user-proposals"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="user-proposals-tab"
            >
                <UserProposals :user_id="user.id" :readOnly="readOnly"></UserProposals>
            </div>
            <div
                    id="user-rewards"
                    v-if="isAvailableGamification"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="user-rewards-tab"
            >
                <UserRewards :user_id="user.id" :readOnly="readOnly"></UserRewards>
            </div>
            <div
                    id="available-rewards"
                    v-if="!readOnly && isAvailableGamification"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="available-rewards-tab"
            >
                <AvailableRewards :user_id="user.id"></AvailableRewards>
            </div>
            <div
                    id="profile-edit"
                    v-if="!readOnly"
                    class="tab-pane fade show"
                    role="tabpanel"
                    aria-labelledby="profile-edit-tab"
            >
                <ProfileEdit></ProfileEdit>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../backend/axios';
    import { API_URL } from '../backend/data_server';
    import { BAvatar } from 'bootstrap-vue';
    import StarRating from 'vue-star-rating';
    import Loading from 'vue-loading-overlay';
    import ProjectsInterest from '../components/user/ProjectsInterest';
    import UserVotes from '../components/user/UserVotes';
    import UserComments from '../components/user/UserComments';
    import UserProposals from '../components/user/UserProposals';
    import UserRewards from '../components/user/UserRewards';
    import AvailableRewards from '../components/user/AvailableRewards';
    import ProfileEdit from '../components/user/ProfileEdit';

    export default {
        name: 'User',
        components: {
            BAvatar,
            StarRating,
            Loading,
            ProjectsInterest,
            UserVotes,
            UserComments,
            UserProposals,
            UserRewards,
            AvailableRewards,
            ProfileEdit,
        },
        data() {
            return {
                user_id: null,
                username: null,
                user: null,
                readOnly: true,
                playerRatingSetting: {
                    votes_ponderation: 0.3,
                    comments_ponderation: 0.3,
                    points_ponderation: 0.4,
                    factor: 100
                },
                loadUser: true,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
            this.getUser();
            this.getRatingSetting();
        },
        methods: {
            configComponent() {
                if(this.$route.query.user_id) {
                    this.user_id = this.$route.query.user_id;
                } else if(this.$route.query.username) {
                    this.username = this.$route.query.username;
                }
            },
            getUser() {
                if(this.user_id) {
                    axios
                        .get('/users/' + this.user_id)
                        .then(res => {
                            this.user = res.data;
                            if(this.$store.getters.isLoggedIn && this.user.id === this.$store.getters.userData.id) {
                                this.readOnly = false;
                            }
                        })
                        .finally(() => {
                            this.loadUser = false;
                        });
                } else if(this.username) {
                    axios
                        .get('/users/username/' + this.username)
                        .then(res => {
                            this.user = res.data;
                            if(this.$store.getters.isLoggedIn && this.user.id === this.$store.getters.userData.id) {
                                this.readOnly = false;
                            }
                        })
                        .finally(() => {
                            this.loadUser = false;
                        });
                } else if(this.$store.getters.isLoggedIn) {
                    axios
                        .get('/auth/user')
                        .then(res => {
                            this.user = res.data;
                            this.readOnly = false;
                        })
                        .finally(() => {
                            this.loadUser = false;
                        });
                } else {
                    this.$router.push({ path: '/' });
                }
            },
            getRatingSetting() {
                axios
                    .get('/settings', {
                        params: {
                            key: 'player_rating'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.playerRatingSetting = JSON.parse(res.data[0].value);
                        }
                    });
            },
            getImgUrl() {
                if(this.user.avatar) {
                    return (API_URL + '/api/storage/avatars/' + this.user.id + '/' + this.user.avatar);
                }
                return null;
            },
            getCalculatedRating() {
                return ((this.user.votes_count * this.playerRatingSetting.votes_ponderation) +
                    (this.user.comments_count * this.playerRatingSetting.comments_ponderation) +
                    (this.user.player.points * this.playerRatingSetting.points_ponderation)) / (5 * this.playerRatingSetting.factor);
            }
        },
        computed: {
            isAvailableGamification() {
                return !!(this.user.player && this.user.player.active_gamification && this.$store.getters.activeGamification);
            }
        }
    }
</script>
