import Vue from "vue";
import App from "./App.vue";
import store from "./store";
import "./registerServiceWorker";
import VueToastr from "@deveodk/vue-toastr";
import "@deveodk/vue-toastr/dist/@deveodk/vue-toastr.css";
import axios from "./backend/axios";
import router from "./router";
import VTooltip from "v-tooltip";
import { library } from "@fortawesome/fontawesome-svg-core";
import tinymce from "vue-tinymce-editor";
import VueGlide from 'vue-glide-js';
import 'vue-glide-js/dist/vue-glide.css';
import 'tiny-slider/src/tiny-slider.scss';
import VueCarousel from 'vue-carousel';
import BootstrapVue from 'bootstrap-vue';
import vSelect from 'vue-select';
import VueAnalytics from 'vue-analytics';
import Loading from 'vue-loading-overlay';
import { far } from "@fortawesome/free-regular-svg-icons";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import VueAxios from "vue-axios";
import VuejsDialog from 'vuejs-dialog';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import BModal from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import HighlightText from 'vue-highlight-text';
import LiquorTree from 'liquor-tree';
import { i18n } from '@/plugins/i18n';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';

Vue.use('tree', LiquorTree);

Vue.use(datePicker);

Vue.use(BootstrapVue);

Vue.use(VueAnalytics, {
    id: async () => {
        try {
            const res = await axios.get('/settings?key=code_google_analytics');
            if(res.data.length > 0) {
                let idGA = res.data[0].value;
                return idGA;
            }
        } catch (error) {
            console.log(error);
        }
    }
});

Vue.use(BModal);
Vue.use(VuejsDialog);

Vue.component('HighlightText', HighlightText);

Vue.component("font-awesome-icon", FontAwesomeIcon);
Vue.use(VueAxios, axios);

const accessToken = localStorage.getItem('access_token');
if(accessToken) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken;
}

const moment = require('moment');
require('moment/locale/es');

Vue.use(require('vue-moment'), {
    moment
});

library.add(fas, far);
Vue.config.productionTip = false;
Vue.use(VueCarousel);
Vue.component('tinymce', tinymce);
Vue.use(VueGlide);
Vue.use(VueToastr, {
    defaultPosition: 'toast-bottom-right',
    defaultType: 'info',
    defaultTimeout: 7500
});
Vue.use(VTooltip);

Vue.use(Loading);

Vue.component('v-select', vSelect);
new Vue({
    router,
    store,
    axios,
    i18n,
    render: h => h(App),
    el: '#app'
});

export const bus = new Vue();
