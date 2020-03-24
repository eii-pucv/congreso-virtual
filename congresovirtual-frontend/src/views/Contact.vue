<template>
    <div :class="mode==='dark'?'dark':'light'">
        <section class="jumbotron text-center" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <div class="container">
                <h1 class="jumbotron-heading" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.titulo') }}</h1>
                <br>
                <p class="lead mb-0" :class="mode==='dark'?'':'text-muted'" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('contacto.contenido.texto') }}
                </p>
            </div>
        </section>
        <div class="px-100 text-center" :style="mode==='dark'?'background: #080035;':''">
            <div class="row">
                <div class="col">
                    <nav class="container px-0" aria-label="breadcrumb">
                        <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                            <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.breadcumb.inicio') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="/contact" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.breadcumb.contacto') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container text-center" :style="mode==='dark'?'background: #080035;':''">
            <div class="row">
                <div class="col">
                    <div class="card" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                        <div class="card-header bg-primary text-white"><i class="fas fa-envelope"></i> {{ $t('contacto.breadcumb.contacto') }}</div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.nombre.titulo') }}</label>
                                    <input
                                            id="name"
                                            v-model="contact.name"
                                            type="text"
                                            class="form-control"
                                            :placeholder="$t('contacto.contenido.nombre.placeholder')"
                                            required
                                            :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="email" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.correo.titulo') }}</label>
                                    <input
                                            id="email"
                                            v-model="contact.email"
                                            type="email"
                                            class="form-control"
                                            :placeholder="$t('contacto.contenido.correo.placeholder')"
                                            required
                                            :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="message" :style="mode==='dark'?'color: #fff':''">{{ $t('contacto.contenido.mensaje.titulo') }}</label>
                                    <textarea
                                            id="message"
                                            v-model="contact.message"
                                            type="text"
                                            class="form-control"
                                            rows="6"
                                            :placeholder="$t('contacto.contenido.mensaje.placeholder')"
                                            required
                                            :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    ></textarea>
                                </div>
                                <div class="mx-auto text-center">
                                    <button @click.prevent="createMail" class="btn btn-success text-white">
                                        <i class="fas fa-envelope"></i> {{ $t('enviar') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card text-center bg-light mb-3">
                        <div class="card-header bg-success text-white text-uppercase"><i class="fas fa-home"></i> {{ $t('contacto.contenido.info') }}</div>
                        <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                            <ul class="list-group list-group-flush w-100 align-items-stretch">
                                <li>
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                    <p v-if="address">{{ address }}</p>
                                </li>
                                <li>
                                    <i class="fas fa-phone-alt mt-4 fa-2x"></i>
                                    <p v-if="contactPhones.length > 0">{{ contactPhonesToString }}</p>
                                </li>
                                <li>
                                    <i class="fas fa-envelope mt-4 fa-2x"></i>
                                    <p v-if="contactEmails.length > 0">{{ contactEmailsToString }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../backend/axios';
    import VueRecaptcha from 'vue-recaptcha';

    export default {
        name: 'Contact',
        components: {
            VueRecaptcha
        },
        data() {
            return {
                address: null,
                contactPhones: [],
                contactEmails: [],
                contact: {
                    name: null,
                    email: null,
                    message: null,
                },
                mode: String
            }
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();
        },
        methods: {
            configComponent() {
                this.getAddress();
                this.getContactPhones();
                this.getContactEmails();
            },
            getAddress() {
                axios
                    .get('settings', {
                        params: {
                            key: 'address'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.address = res.data[0].value;
                        }
                    });
            },
            getContactPhones() {
                axios
                    .get('settings', {
                        params: {
                            key: 'contact_phones'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.contactPhones = JSON.parse(res.data[0].value);
                        }
                    });
            },
            getContactEmails() {
                axios
                    .get('settings', {
                        params: {
                            key: 'contact_emails'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.contactEmails = JSON.parse(res.data[0].value);
                        }
                    });
            },
            createMail() {
                window.location.href = `mailto:${this.contactEmails[0]}?subject=${this.contact.name}&body=${this.contact.message}`
            }
        },
        computed: {
            contactPhonesToString() {
                return this.contactPhones.join(' - ');
            },
            contactEmailsToString() {
                return this.contactEmails.join(', ');
            }
        }
    }
</script>

<style scoped>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>