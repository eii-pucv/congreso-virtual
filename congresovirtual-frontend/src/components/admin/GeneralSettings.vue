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
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
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
                                        @input="showPhonesArray"
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
                                        <span class="btn-text"><font-awesome-icon icon="minus"/></span>
                                    </a>
                                    <a @click="addContactPhone" class="btn btn-sm btn-primary text-white">
                                        <span class="btn-text"><font-awesome-icon icon="plus"/></span>
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
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
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
                                        <span class="btn-text"><font-awesome-icon icon="minus"/></span>
                                    </a>
                                    <a @click="addContactEmail" class="btn btn-sm btn-primary text-white">
                                        <span class="btn-text"><font-awesome-icon icon="plus"/></span>
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
                                <input
                                        id="max_necessary_petitions"
                                        v-model="settings.find(setting => setting.key === 'max_necessary_petitions').value.number_petitions"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
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
                                <label class="h5" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.configuracion_general.codigos_insercion_videos.titulo') }}</label>
                                <v-popover>
                                    <font-awesome-icon class="tooltip-target ml-1" icon="info-circle"></font-awesome-icon>
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
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" type="submit">
                                <font-awesome-icon icon="save" />
                                <span class="btn-text"> {{ $t('guardar') }}</span>
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click="back" class="btn btn-danger text-white ml-10">
                                <font-awesome-icon icon="window-close" />
                                <span class="btn-text" :style="mode==='dark'?'color: #fff':''"> {{ $t('cancelar') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from "../../backend/axios";
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
            showPhonesArray(event) {
                console.log(event.target.value);
                console.log(this.settings.find(setting => setting.key === 'contact_phones'));
            },
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
                this.settings.forEach(setting => {
                    settings.settings.push({
                        key: setting.key,
                        label: setting.label,
                        value: setting.json ? JSON.stringify(setting.value) : setting.value
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
            back() {
                location.replace(document.referrer);
            }
        }
    }
</script>

<style scope>
    .code {
        font-family: SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;
    }
</style>