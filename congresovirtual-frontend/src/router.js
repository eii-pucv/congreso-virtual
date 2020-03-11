import Vue from "vue";
import Router from "vue-router";

import store from "./store";

import UsersList from "./components/admin/users/UsersList";
import OrganizationsList from "./components/admin/users/OrganizationsList";
import CreateOrEditUser from "./components/admin/users/CreateOrEditUser";

import ProjectsList from "./components/admin/projects/ProjectsList";
import ProjectArticlesList from "./components/admin/projects/ProjectArticlesList";
import ProjectIdeasList from "./components/admin/projects/ProjectIdeasList";
import CreateOrEditProject from "./components/admin/projects/CreateOrEditProject";
import CreateOrEditArticle from "./components/admin/projects/CreateOrEditArticle";
import CreateOrEditIdea from "./components/admin/projects/CreateOrEditIdea";
import ProjectFiles from "./components/admin/projects/ProjectFiles";
import ProjectSendNotification from "./components/admin/projects/ProjectSendNotification";
import ProjectAnalytic from "./components/admin/projects/ProjectAnalytic";

import PublicConsultationsList from "./components/admin/public_consultations/PublicConsultationsList";
import CreateOrEditPublicConsultation from "./components/admin/public_consultations/CreateOrEditPublicConsultation";

import ProposalsList from "./components/admin/proposals/ProposalsList";
import CreateOrEditProposal from "./components/admin/proposals/CreateOrEditProposal";

import TaxonomiesList from "./components/admin/taxonomies_terms/TaxonomiesList";
import TaxonomyTermsList from "./components/admin/taxonomies_terms/TaxonomyTermsList";
import CreateOrEditTaxonomy from "./components/admin/taxonomies_terms/CreateOrEditTaxonomy";

import WaitingCommentsList from "./components/admin/comments/WaitingCommentsList";
import BlockedCommentsList from "./components/admin/comments/BlockedCommentsList";
import ClassifiedCommentsList from "./components/admin/comments/ClassifiedCommentsList";
import DenouncesList from "./components/admin/comments/DenouncesList";
import aporteReportado from "./components/admin/comments/aporteReportado";
import aporteEnEspera from "./components/admin/comments/aporteEnEspera";
import aporteBloqueado from "./components/admin/comments/aporteBloqueado";
import ClassifiedComment from "./components/admin/comments/ClassifiedComment";
import editPerception from "./components/admin/comments/editPerception";

import OffensiveWordsList from "./components/admin/offensive_words/OffensiveWordsList";
import CreateOrEditOffensiveWord from "./components/admin/offensive_words/CreateOrEditOffensiveWord";

import PagesList from "./components/admin/pages/PagesList";
import CreateOrEditPage from "./components/admin/pages/CreateOrEditPage";

import deleteElement from "./components/admin/deleteElement";

import ProfileEdit from "./components/user/ProfileEdit";
import GeneralSettings from "./components/admin/GeneralSettings";
import menuConfig from "./components/admin/menuConfig";
import footerConfig from "./components/admin/footerConfig";

Vue.use(Router);

const routes = [
    {
        path: '/',
        name: 'Home',
        props: true,
        component: () => import('./views/Home.vue')
    },
    {
        path: '/projects',
        name: 'Projects',
        props: true,
        component: () => import('./views/Projects.vue')
    },
    {
        path: '/project/:project_id',
        name: 'Project',
        props: true,
        component: () => import('./views/Project.vue')
    },
    {
        path: '/public_consultations',
        name: 'PublicConsultations',
        props: true,
        component: () => import('./views/PublicConsultations.vue')
    },
    {
        path: '/public_consultation/:public_consultation_id',
        name: 'PublicConsultation',
        props: true,
        component: () => import('./views/PublicConsultation.vue')
    },
    {
        path: '/proposals',
        name: 'Proposals',
        props: true,
        component: () => import('./views/Proposals.vue')
    },
    {
        path: '/proposal/:id',
        name: 'Proposal',
        props: true,
        component: () => import('./views/Proposal.vue')
    },
    {
        path: '/page/:slug',
        name: 'Page',
        props: true,
        component: () => import('./views/Page.vue')
    },
    {
        path: '/signup',
        name: 'Register',
        props: true,
        component: () => import('./views/Register.vue')
    },
    {
        path: '/confirmation',
        name: 'UserConfirmation',
        props: true,
        component: () => import('./components/user/UserConfirmation.vue')
    },
    {
        path: '/login',
        name: 'Login',
        props: true,
        component: () => import('./views/Login.vue')
    },
    {
        path: '/auth/:provider/callback',
        component: {
            template: '<div class="auth-component"></div>'
        }
    },
    {
        path: '/reset',
        name: 'ResetPassword',
        props: true,
        component: () => import('./views/ResetPassword.vue')
    },
    {
        path: '/request',
        name: 'RequestResetPassword',
        props: true,
        component: () => import('./views/RequestResetPassword.vue')
    },
    {
        path: '/search',
        props: true,
        component: () => import('./views/Search.vue')
    },
    {
        path: '/contact',
        name: 'Contact',
        props: true,
        component: () => import('./views/Contact.vue')
    },
    {
        path: '/user',
        name: 'User',
        props: true,
        component: () => import('./views/User.vue'),
        meta: {
            requiresAuth: true,
            user_rol: 'USER'
        }
    },
    {
        path: '/user/my-comments',
        props: true,
        component: () => import('./components/user/UserComments.vue'),
        meta: {
            requiresAuth: true,
            user_rol: 'USER'
        }
    },
    {
        path: '/user/my-votes',
        props: true,
        component: () => import('./components/user/UserVotes.vue'),
        meta: {
            requiresAuth: true,
            user_rol: 'USER'
        }
    },
    {
        path: '/user/my-proposals',
        props: true,
        component: () => import('./components/user/UserProposals'),
        meta: {
            requiresAuth: true,
            user_rol: 'USER'
        }
    },
    {
        path: '/admin',
        name: 'admin',
        props: true,
        component: () => import('./components/admin/AdminNavbar'),
        meta: {
            requiresAuth: true,
            user_rol: 'ADMIN'
        },
        children: [
            /*-- USERS AND ORGANIZATIONS ADMINISTRATION --*/
            {
                path: 'users',
                props: true,
                component: UsersList
            },
            {
                path: 'organizations',
                props: true,
                component: OrganizationsList
            },
            {
                path: 'user',
                props: true,
                component: CreateOrEditUser
            },
            {
                path: 'user/:user_id',
                props: true,
                component: CreateOrEditUser
            },
            /*-- PROJECTS (ARTICLES AND FUNDAMENTAL IDEAS) ADMINISTRATION --*/
            {
                path: 'projects',
                props: true,
                component: ProjectsList
            },
            {
                path: 'project/:project_id/articles',
                props: true,
                component: ProjectArticlesList
            },
            {
                path: 'project/:project_id/ideas',
                props: true,
                component: ProjectIdeasList
            },
            {
                path: 'project',
                props: true,
                component: CreateOrEditProject
            },
            {
                path: 'project/:project_id',
                props: true,
                component: CreateOrEditProject
            },
            {
                path: 'project/:project_id/article',
                props: true,
                component: CreateOrEditArticle
            },
            {
                path: 'project/:project_id/idea',
                props: true,
                component: CreateOrEditIdea
            },
            {
                path: 'project/:project_id/article/:article_id',
                props: true,
                component: CreateOrEditArticle
            },
            {
                path: 'project/:project_id/idea/:idea_id',
                props: true,
                component: CreateOrEditIdea
            },
            {
                path: 'project/:project_id/documents',
                props: true,
                component: ProjectFiles
            },
            {
                path: 'project/:project_id/send',
                props: true,
                component: ProjectSendNotification
            },
            {
                path: 'project/:project_id/analytic',
                props: true,
                component: ProjectAnalytic
            },
            /*-- PUBLIC CONSULTATIONS ADMINISTRATION --*/
            {
                path: 'public_consultations',
                props: true,
                component: PublicConsultationsList
            },
            {
                path: 'public_consultation',
                props: true,
                component: CreateOrEditPublicConsultation
            },
            {
                path: 'public_consultation/:public_consultation_id',
                props: true,
                component: CreateOrEditPublicConsultation
            },
            /*-- PROPOSALS ADMINISTRATION --*/
            {
                path: 'proposals',
                props: true,
                component: ProposalsList
            },
            {
                path: 'proposal',
                props: true,
                component: CreateOrEditProposal
            },
            {
                path: 'proposal/:proposal_id',
                props: true,
                component: CreateOrEditProposal
            },
            /*-- TAXONOMIES ADMINISTRATION --*/
            {
                path: 'taxonomies',
                props: true,
                component: TaxonomiesList
            },
            {
                path: 'taxonomy/:taxonomy_id/terms',
                props: true,
                component: TaxonomyTermsList
            },
            {
                path: 'taxonomy',
                props: true,
                component: CreateOrEditTaxonomy
            },
            {
                path: 'taxonomy/:taxonomy_id',
                props: true,
                component: CreateOrEditTaxonomy
            },
            /*-- COMMENTS ADMINISTRATION --*/
            {
                path: 'waiting',
                props: true,
                component: WaitingCommentsList
            },
            {
                path: 'blocked',
                props: true,
                component: BlockedCommentsList
            },
            {
                path: 'classified',
                props: true,
                component: ClassifiedCommentsList
            },
            {
                path: 'denounces',
                props: true,
                component: DenouncesList
            },
            {
                path: 'offensive-words',
                props: true,
                component: OffensiveWordsList
            },
            {
                path: 'offensive-word',
                props: true,
                component: CreateOrEditOffensiveWord
            },
            {
                path: 'offensive-word/:offensiveword_id',
                props: true,
                component: CreateOrEditOffensiveWord
            },
            {
                path: 'waiting/:comment_id',
                props: true,
                component: aporteEnEspera
            },
            {
                path: 'blocked/:comment_id',
                props: true,
                component: aporteBloqueado
            },
            {
                path: 'classified/:comment_id',
                props: true,
                component: ClassifiedComment
            },
            {
                path: 'classified/:comment_id/perception',
                props: true,
                component: editPerception
            },
            {
                path: 'denounce/:comment_id',
                props: true,
                component: aporteReportado
            },
            /*-- PAGES ADMINISTRATION --*/
            {
                path: 'pages',
                props: true,
                component: PagesList
            },
            {
                path: 'page',
                props: true,
                component: CreateOrEditPage
            },
            {
                path: 'page/:page_id',
                props: true,
                component: CreateOrEditPage
            },
            /*-- PERSONAL ACCOUNT ADMINISTRATION --*/
            {
                path: 'profile',
                props: true,
                component: ProfileEdit
            },
            /*-- SETTINGS ADMINISTRATION --*/
            {
                path: 'general_settings',
                props: true,
                component: GeneralSettings
            },
            {
                path: 'menu',
                props: true,
                component: menuConfig
            },
            {
                path: 'footer',
                props: true,
                component: footerConfig
            },
            // Para borrar proyectos, usuarios, consultas y taxonomias
            {
                path: ':list_name/:item_id/delete',
                props: true,
                component: deleteElement
            },
            // Para borrar elementos que contienen los proyectos, usuarios, consultas y taxonomias
            {
                path: 'project/:item_id/:list_name/:subItem_id/delete',
                props: true,
                component: deleteElement
            },
        ]
    }
];

const router = new Router({
    mode: 'history',
    routes: routes
});

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiresAuth)) {
        if(store.getters.isLoggedIn) {
            if(to.matched.some(record => store.state.user.rol === record.meta.user_rol)) {
                next();
            } else {
                next('/');
            }
        } else {
            next('login');
        }
    } else {
        next();
    }
});
export default router;