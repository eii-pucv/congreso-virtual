<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 class="hk-sec-title text-center">{{ $t('administrador.componentes.configuracion_general.titulo') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadSettings" style="height: 300px;">
                    <Loading
                            :active.sync="loadSettings"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadSettings">
                    <form @submit.prevent="saveSettings">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.datos_basicos.titulo') }}</h5>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="site_name" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.datos_basicos.nombre_sitio') }}</label>
                                <input
                                        id="site_name"
                                        v-model="settings.find(setting => setting.key === 'site_name').value"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-md-6 mb-10">
                                <label for="address" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.datos_basicos.direccion.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.datos_basicos.direccion.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="address"
                                        v-model="settings.find(setting => setting.key === 'address').value"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div
                                class="form-row align-items-center justify-content-center"
                                v-for="(phone, index) in settings.find(setting => setting.key === 'contact_phones').value"
                                :key="'phone-' + index"
                        >
                            <div class="col-9 col-md-8 mb-10">
                                <label v-if="index === 0" for="contact_phone" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.datos_basicos.fonos_contacto.titulo') }}</label>
                                <input
                                        id="contact_phone"
                                        v-model="settings.find(setting => setting.key === 'contact_phones').value[index]"
                                        type="text"
                                        class="form-control"
                                        :placeholder="$t('administrador.componentes.configuracion_general.datos_basicos.fonos_contacto.telefono') + ' ' + (index + 1)"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-3 col-md-2">
                                <div
                                        class="float-right"
                                        v-bind:class="{ 'btn-group': index || (!index && settings.find(setting => setting.key === 'contact_phones').value.length > 1) }"
                                >
                                    <a
                                            @click="removeContactPhone(index)"
                                            v-show="index || (!index && settings.find(setting => setting.key === 'contact_phones').value.length > 1)"
                                            class="btn btn-sm btn-danger text-white"
                                    >
                                        <i class="fas fa-minus"></i>
                                    </a>
                                    <a @click="addContactPhone" class="btn btn-sm btn-primary text-white">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div
                                class="form-row align-items-center justify-content-center"
                                v-for="(email, index) in settings.find(setting => setting.key === 'contact_emails').value"
                                :key="'email-' + index"
                        >
                            <div class="col-9 col-md-8 mb-10">
                                <label v-if="index === 0" for="contact_email" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.datos_basicos.correos_contacto.titulo') }}</label>
                                <v-popover v-if="index === 0">
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.datos_basicos.correos_contacto.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="contact_email"
                                        v-model="settings.find(setting => setting.key === 'contact_emails').value[index]"
                                        type="email"
                                        class="form-control"
                                        :placeholder="$t('administrador.componentes.configuracion_general.datos_basicos.correos_contacto.correo') + ' ' + (index + 1)"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-3 col-md-2">
                                <div
                                        class="float-right"
                                        v-bind:class="{ 'btn-group': index || (!index && settings.find(setting => setting.key === 'contact_emails').value.length > 1) }"
                                >
                                    <a
                                            @click="removeContactEmail(index)"
                                            v-show="index || (!index && settings.find(setting => setting.key === 'contact_emails').value.length > 1)"
                                            class="btn btn-sm btn-danger text-white"
                                    >
                                        <i class="fas fa-minus"></i>
                                    </a>
                                    <a @click="addContactEmail" class="btn btn-sm btn-primary text-white">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.propuestas.titulo') }}</h5>
                            </div>
                            <div class="col-md-10 mb-10">
                                <label for="max_necessary_petitions" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.propuestas.max_peticiones') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button
                                                @click.prevent="addOrSubtractNumberPetitions(-1)"
                                                :disabled="getNumberPetitions() == 0"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input
                                            id="max_necessary_petitions"
                                            v-model="settings.find(setting => setting.key === 'max_necessary_petitions').value.number_petitions"
                                            @input="numberPetitionsFormat"
                                            type="text"
                                            class="form-control"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    >
                                    <div class="input-group-append">
                                        <button
                                                @click.prevent="addOrSubtractNumberPetitions(1)"
                                                class="btn btn-outline-secondary"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes_sociales.titulo') }}</h5>
                            </div>
                            <div class="col-md-5 mb-10">
                                <label for="facebook_rrss" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes_sociales.facebook') }}</label>
                                <input
                                        id="facebook_rrss"
                                        v-model="settings.find(setting => setting.key === 'social_networks').value.facebook"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-md-5 mb-10">
                                <label for="twitter_rrss" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes_sociales.twitter') }}</label>
                                <input
                                        id="twitter_rrss"
                                        v-model="settings.find(setting => setting.key === 'social_networks').value.twitter"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-5 mb-10">
                                <label for="instagram_rrss" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes_sociales.instagram') }}</label>
                                <input
                                        id="instagram_rrss"
                                        v-model="settings.find(setting => setting.key === 'social_networks').value.instagram"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-md-5 mb-10">
                                <label for="youtube_rrss" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.redes_sociales.youtube') }}</label>
                                <input
                                        id="youtube_rrss"
                                        v-model="settings.find(setting => setting.key === 'social_networks').value.youtube"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.google_analytics.titulo') }}</h5>
                            </div>
                            <div class="col-md-10 mb-10">
                                <label for="code_google_analytics" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.google_analytics.codigo') }}</label>
                                <input
                                        id="code_google_analytics"
                                        v-model="settings.find(setting => setting.key === 'code_google_analytics').value"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 style="display: inline-block;" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.titulo') }}</h5>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.popover') }}</p>
                                    </template>
                                </v-popover>
                            </div>
                            <div class="col-md-10 mb-10">
                                <label for="youtube_video" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.youtube') }}</label>
                                <textarea
                                        id="youtube_video"
                                        v-model="settings.find(setting => setting.key === 'video_iframes').value.youtube"
                                        type="text"
                                        class="form-control code"
                                        rows="3"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="vimeo_video" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.vimeo') }}</label>
                                <textarea
                                        id="vimeo_video"
                                        v-model="settings.find(setting => setting.key === 'video_iframes').value.vimeo"
                                        type="text"
                                        class="form-control code"
                                        rows="3"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="dailymotion_video" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.dailymotion') }}</label>
                                <textarea
                                        id="dailymotion_video"
                                        v-model="settings.find(setting => setting.key === 'video_iframes').value.dailymotion"
                                        type="text"
                                        class="form-control code"
                                        rows="3"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></textarea>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 style="display: inline-block;" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.api_externa.titulo') }}</h5>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.api_externa.popover') }}</p>
                                    </template>
                                </v-popover>
                            </div>
                            <div class="col-md-10 mb-10">
                                <label for="general_info_project" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.api_externa.info_general_proyecto.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.api_externa.info_general_proyecto.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="general_info_project"
                                        v-model="settings.find(setting => setting.key === 'external_api').value.general_info_project"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="trace_info_project" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.api_externa.info_seguimiento_proyecto.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.api_externa.info_seguimiento_proyecto.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="trace_info_project"
                                        v-model="settings.find(setting => setting.key === 'external_api').value.trace_info_project"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <label for="parliament_vote_project" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.api_externa.votacion_parlamento_proyecto.titulo') }}</label>
                                <v-popover>
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.api_externa.votacion_parlamento_proyecto.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="parliament_vote_project"
                                        v-model="settings.find(setting => setting.key === 'external_api').value.parliament_vote_project"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                        </div>
                        <div
                                class="form-row align-items-center justify-content-center"
                                v-for="(proposalsUrl, index) in settings.find(setting => setting.key === 'external_api').value.proposals_url_list"
                                :key="'proposals-url-' + index"
                        >
                            <div class="col-9 col-md-8 mb-10">
                                <label v-if="index === 0" for="proposal_url" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.api_externa.propuestas.titulo') }}</label>
                                <v-popover v-if="index === 0">
                                    <span class="tooltip-target ml-1"><i class="fas fa-info-circle"></i></span>
                                    <template slot="popover">
                                        <p>{{ $t('administrador.componentes.configuracion_general.api_externa.propuestas.popover') }}</p>
                                    </template>
                                </v-popover>
                                <input
                                        id="proposal_url"
                                        v-model="settings.find(setting => setting.key === 'external_api').value.proposals_url_list[index]"
                                        type="text"
                                        class="form-control"
                                        :placeholder="$t('administrador.componentes.configuracion_general.api_externa.propuestas.titulo') + ' ' + (index + 1)"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                            </div>
                            <div class="col-3 col-md-2">
                                <div
                                        class="float-right"
                                        v-bind:class="{ 'btn-group': index || (!index && settings.find(setting => setting.key === 'external_api').value.proposals_url_list.length > 1) }"
                                >
                                    <a
                                            @click="removeProposalsUrl(index)"
                                            v-show="index || (!index && settings.find(setting => setting.key === 'external_api').value.proposals_url_list.length > 1)"
                                            class="btn btn-sm btn-danger text-white"
                                    >
                                        <i class="fas fa-minus"></i>
                                    </a>
                                    <a @click="addProposalsUrl" class="btn btn-sm btn-primary text-white">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-5">
                                <hr />
                                <h5 :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.gamificacion.titulo') }}</h5>
                            </div>
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-switch">
                                    <input
                                            id="active_gamification"
                                            v-model="settings.find(setting => setting.key === 'active_gamification').value"
                                            type="checkbox"
                                            class="custom-control-input"
                                    >
                                    <label for="active_gamification" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">
                                        {{ $t('administrador.componentes.configuracion_general.gamificacion.gamificacion_activada') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" type="submit">
                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click.prevent="back" class="btn btn-danger text-white ml-10">
                                <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from "../../../backend/axios";
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'General Settings',
        components: {
            Loading
        },
        data() {
            return {
                settings: [
                    {
                        key: 'site_name',
                        label: null,
                        value: null,
                        json: false
                    },
                    {
                        key: 'max_necessary_petitions',
                        label: null,
                        value: {
                            number_petitions: 100
                        },
                        json: true
                    },
                    {
                        key: 'social_networks',
                        label: null,
                        value: {
                            facebook: null,
                            twitter: null,
                            instagram: null,
                            youtube: null
                        },
                        json: true
                    },
                    {
                        key: 'code_google_analytics',
                        label: null,
                        value: null,
                        json: false
                    },
                    {
                        key: 'address',
                        label: null,
                        value: null,
                        json: false
                    },
                    {
                        key: 'contact_emails',
                        label: null,
                        value: [],
                        json: true
                    },
                    {
                        key: 'contact_phones',
                        label: null,
                        value: [],
                        json: true
                    },
                    {
                        key: 'video_iframes',
                        label: null,
                        value: {
                            youtube: null,
                            vimeo: null,
                            dailymotion: null
                        },
                        json: true
                    },
                    {
                        key: 'external_api',
                        label: null,
                        value: {
                            general_info_project: null,
                            trace_info_project: null,
                            parliament_vote_project: null,
                            proposals_url_list: []
                        },
                        json: true
                    },
                    {
                        key: 'active_gamification',
                        label: null,
                        value: false,
                        json: true
                    }
                ],
                loadSettings: true,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String
            }
        },
        async mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.getSettings();
        },
        methods: {
            getSettings() {
                axios
                    .get('settings')
                    .then(res => {
                        let allSettings = res.data;
                        if(res.data.length > 0) {
                            let keySettings = this.settings.map(setting => setting.key);
                            allSettings = allSettings.filter(setting => keySettings.includes(setting.key));

                            allSettings.forEach(settingData => {
                                let setting = this.settings.find(setting => setting.key === settingData.key);
                                setting.label = settingData.label;
                                setting.value = setting.json ? JSON.parse(settingData.value) : settingData.value;
                            });

                            if(this.settings.find(setting => setting.key === 'contact_phones').value.length === 0) {
                                this.addContactPhone();
                            }
                            if(this.settings.find(setting => setting.key === 'contact_emails').value.length === 0) {
                                this.addContactEmail();
                            }
                            if(this.settings.find(setting => setting.key === 'external_api').value.proposals_url_list.length === 0) {
                                this.addProposalsUrl();
                            }
                        }
                    })
                    .finally(() => {
                        this.loadSettings = false;
                    });
            },
            saveSettings() {
                this.loadBtnSave = true;
                let settings = {
                    settings: []
                };
                let auxSettingsArray = JSON.parse(JSON.stringify(this.settings)); // Clone array of objects
                auxSettingsArray.forEach(setting => {
                    let value = setting.value;
                    if(['contact_phones', 'contact_emails', 'external_api'].includes(setting.key) && setting.json) {
                        if(setting.key !== 'external_api') {
                            value = setting.value.filter(element => element !== null && element !== '');
                        } else {
                            value.proposals_url_list = setting.value.proposals_url_list.filter(element => element !== null && element !== '');
                        }
                    }
                    settings.settings.push({
                        key: setting.key,
                        label: setting.label,
                        value: setting.json ? JSON.stringify(value) : value
                    });
                });
                axios
                    .put('/settings', settings)
                    .then(() => {
                        this.$toastr(
                            'success',
                            this.$t('administrador.componentes.configuracion_general.mensajes.exito.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.configuracion_general.mensajes.exito.modificado.generico.titulo')
                        );
                    })
                    .catch(() => {
                        this.$toastr(
                            'error',
                            this.$t('administrador.componentes.configuracion_general.mensajes.fallido.modificado.generico.cuerpo'),
                            this.$t('administrador.componentes.configuracion_general.mensajes.fallido.modificado.generico.titulo')
                        );
                    })
                    .finally(() => {
                        this.loadBtnSave = false;
                    })

            },
            addContactPhone() {
                let settingValue = this.settings.find(setting => setting.key === 'contact_phones').value;
                settingValue.push(null);
            },
            removeContactPhone(index) {
                let settingValue = this.settings.find(setting => setting.key === 'contact_phones').value;
                settingValue.splice(index, 1);
            },
            addContactEmail() {
                let settingValue = this.settings.find(setting => setting.key === 'contact_emails').value;
                settingValue.push(null);
            },
            removeContactEmail(index) {
                let settingValue = this.settings.find(setting => setting.key === 'contact_emails').value;
                settingValue.splice(index, 1);
            },
            addOrSubtractNumberPetitions(value) {
                let settingValue = this.settings.find(setting => setting.key === 'max_necessary_petitions').value;
                let numberPetitions = parseInt(settingValue.number_petitions);
                value = parseInt(value);

                if(!isNaN(numberPetitions) && !isNaN(value)) {
                    if(numberPetitions + value >= 0) {
                        settingValue.number_petitions = numberPetitions + value;
                        return;
                    }
                }
                settingValue.number_petitions = 0;
            },
            getNumberPetitions() {
                return this.settings.find(setting => setting.key === 'max_necessary_petitions').value.number_petitions;
            },
            numberPetitionsFormat(event) {
                let settingValue = this.settings.find(setting => setting.key === 'max_necessary_petitions').value;
                settingValue.number_petitions = isNaN(event.target.value) ? 0 : event.target.value;
            },
            addProposalsUrl() {
                let settingValue = this.settings.find(setting => setting.key === 'external_api').value.proposals_url_list;
                settingValue.push(null);
            },
            removeProposalsUrl(index) {
                let settingValue = this.settings.find(setting => setting.key === 'external_api').value.proposals_url_list;
                settingValue.splice(index, 1);
            },
            back() {
                this.$router.go(-1);
            }
        }
    }
</script>

<style>
    .code {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;
    }
</style>