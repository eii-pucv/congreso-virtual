<template>
    <div class="bg-indigo-light-5 d-flex flex-column min-vh-100">
        <preload></preload>
        <topnavbar></topnavbar>
        <navbar :userRol="userRol" :isLoggedIn="isLoggedIn"></navbar>
        <router-view></router-view>
        <Footer></Footer>
    </div>
</template>

<style lang="scss">
    @import './assets/css/vue-toastr.min.css';
    @import './assets/css/style.css';
    @import './assets/css/tiny-slider.css';

    html,
    body {
        height:auto;
    }

    .dark {
        color: #ffffff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000000;
        background: #ffffff;
    }

    .bg-dark {
        color: #ffffff;
        background: rgb(12, 1, 80);
    }

    .header-img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        filter: brightness(50%);
    }

    @media (max-width: 767px) {
        .header-img {
            height: 600px;
        }
    }

    .container {
        position: relative;
    }

    .bottom {
        position: absolute;
        bottom: 8px;
    }

    .top-right {
        position: absolute;
        top: 8px;
        right: 16px;
    }

    .top-left {
        position: absolute;
        top: 8px;
        left: 16px;
    }

    .default-project-img {
        background-image: url('./assets/img/defaults/project_header.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        min-height: 160px;
    }

    .default-consultation-img {
        background-image: url('./assets/img/defaults/consultation_header.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        min-height: 160px;
    }

    .default-proposal-img {
        background-image: url('./assets/img/defaults/proposal_header.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        min-height: 160px;
    }

    .default-page-img {
        background-image: url('./assets/img/defaults/page_header.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
        min-height: 160px;
    }

    .default-avatar-img {
        background-image: url('./assets/img/defaults/avatar.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #2e2e2e;
    }

    .agree {
        color: #008000;
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .agree:hover {
        color: #ffffff;
        background: #9de19c;
    }

    .agree-selected {
        color: #ffffff !important;
        background: #008000 !important;
        pointer-events: none;
        cursor: none;
    }

    .agree-blocked {
        color: #008000;
        background: #e8e8e8;
        pointer-events: none;
        cursor: none;
    }

    .disagree {
        color: #f83f37;
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .disagree:hover {
        color: #ffffff;
        background: #fc7872;
    }

    .disagree-selected {
        color: #ffffff !important;
        background: #f83f37 !important;
        pointer-events: none;
        cursor: none;
    }

    .disagree-blocked {
        color: #f83f37;
        background: #e8e8e8;
        pointer-events: none;
        cursor: none;
    }

    .abstention {
        color: #808080;
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .abstention:hover {
        color: #ffffff;
        background: #b1b1b1;
    }

    .abstention-selected {
        color: #ffffff !important;
        background: #808080 !important;
        pointer-events: none;
        cursor: none;
    }

    .abstention-blocked {
        color: #808080;
        background: #e8e8e8;
        pointer-events: none;
        cursor: none;
    }

    .tooltip {
        display: block !important;
        z-index: 10000;
    }

    .tooltip .tooltip-inner {
        background: white;
        color: black;
        border-radius: 16px;
        padding: 5px 10px 4px;
    }

    .tooltip .tooltip-arrow {
        width: 0;
        height: 0;
        border-style: solid;
        position: absolute;
        margin: 5px;
        border-color: black;
    }

    .tooltip[x-placement^="top"] {
        margin-bottom: 5px;
    }

    .tooltip[x-placement^="top"] .tooltip-arrow {
        border-width: 5px 5px 0 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        bottom: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="bottom"] {
        margin-top: 5px;
    }

    .tooltip[x-placement^="bottom"] .tooltip-arrow {
        border-width: 0 5px 5px 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        top: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="right"] {
        margin-left: 5px;
    }

    .tooltip[x-placement^="right"] .tooltip-arrow {
        border-width: 5px 5px 5px 0;
        border-left-color: transparent !important;
        border-top-color: transparent !important;
        border-bottom-color: transparent !important;
        left: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip[x-placement^="left"] {
        margin-right: 5px;
    }

    .tooltip[x-placement^="left"] .tooltip-arrow {
        border-width: 5px 0 5px 5px;
        border-top-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        right: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip[aria-hidden='true'] {
        visibility: hidden;
        opacity: 0;
        transition: opacity .15s, visibility .15s;
    }

    .tooltip[aria-hidden='false'] {
        visibility: visible;
        opacity: 1;
        transition: opacity .15s;
    }

    .v-popover {
        display: inline;
    }

    .bm-burger-bars {
        background-color: #ffffff  !important;
    }
    .bm-burger-button {
        position: fixed;
        width: 36px;
        height: 30px;
        left: 36px;
        top: 30% !important;
        cursor: pointer;
    }

    .custom-scrollbar-wk::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);
        background-color: #F5F5F5;
    }

    .custom-scrollbar-wk::-webkit-scrollbar {
        width: 10px;
        background-color: #F5F5F5;
    }

    .custom-scrollbar-wk::-webkit-scrollbar-thumb {
        background-color: #536bbb !important;
        border: 1px solid #555555;
    }

    .custom-scrollbar-mz {
        scrollbar-color: #536bbb #F5F5F5;
        scrollbar-width: thin;
    }
</style>

<script>
    import Navbar from "./components/Navbar";
    import Topnavbar from "./components/TopNavbar";
    import Preload from "./components/Preload";
    import jQuery from 'jquery';
    import Footer from "./components/Footer";

    window.jQuery = jQuery;
    window.$ = jQuery;
    require("popper.js/dist/umd/popper.min");
    require("bootstrap/dist/js/bootstrap.min");

    require("../src/assets/js/jquery.slimscroll");

    require("../src/assets/js/dropdown-bootstrap-extended");

    require("../src/assets/js/twitterFetcher");
    require("../src/assets/js/widgets-data");

    require("../src/assets/js/feather.min");
    require("jquery-toggles/toggles.min");
    require("../src/assets/js/toggle-data");

    require("waypoints/lib/jquery.waypoints.min");
    require("jquery.counterup/jquery.counterup.min");
    require("echarts/dist/echarts-en.common.min");

    require("jquery-toast-plugin/dist/jquery.toast.min");
    require("owl.carousel/dist/owl.carousel.min");
    require("../src/assets/js/owl-data");
    require("../src/assets/js/init");
    import "vue-select/src/scss/vue-select.scss";

    export default {
        components: {
            Footer,
            Preload,
            Navbar,
            Topnavbar
        },
        computed: {
            isLoggedIn() {
                return this.$store.getters.isLoggedIn;
            },
            userRol() {
                return this.$store.getters.user ? this.$store.getters.user.rol : null;
            }
        },
        data() {
            return {
                user: Object
            }
        },
        mounted() {
            this.user = JSON.parse(localStorage.getItem('user'));
        }
    }
</script>