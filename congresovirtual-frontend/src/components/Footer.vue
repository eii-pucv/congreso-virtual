<template>
    <footer class="page-footer font-small blue pt-4 mt-auto" style="background-color: #082054">
        <div v-if="categories.length > 0" class="container text-center mb-20">
            <div class="row">
                <div class="col mx-auto" v-for="category in categories" :key="category.id">
                    <h5 class="font-weight-bold text-uppercase text-white mt-3 mb-4">
                        <router-link :to="{ path: category.url }" class="text-white">{{ category.text }}</router-link>
                    </h5>
                    <ul v-if="category.subcategorias" class="list-unstyled">
                        <li v-for="subcategory in category.subcategorias" :key="subcategory.id">
                            <router-link :to="{ path: subcategory.url }" class="text-white">{{ subcategory.text }}</router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container text-center text-white mb-30">
            <div class="row">
                <div class="col-md-4 my-auto">
                    <router-link :to="{ path: '/' }" class="h5 text-md-left text-uppercase text-white">
                        <p>{{ siteName }}</p>
                    </router-link>
                </div>
                <div class="col-md-4 my-auto">
                    <p v-if="address">{{ $t('componentes.footer.direccion') }}: {{ address }}</p>
                    <p v-if="contactPhones.length > 0">{{ $t('componentes.footer.telefonos') }}: {{ contactPhonesToString }}</p>
                </div>
                <div class="col-md-4 text-md-right" v-bind:class="{ 'text-right': !isMobileDevice }">
                    <a v-if="socialNetworks.facebook" :href="socialNetworks.facebook" class="mr-1" style="font-size: 3rem; color: white;">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a v-if="socialNetworks.twitter" :href="socialNetworks.twitter" class="mr-1" style="font-size: 3rem; color: white;">
                        <i class="fab fa-twitter-square"></i>
                    </a>
                    <a v-if="socialNetworks.youtube" :href="socialNetworks.youtube" class="mr-1" style="font-size: 3rem; color: white;">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a v-if="socialNetworks.instagram" :href="socialNetworks.instagram" class="mr-1" style="font-size: 3rem; color: white;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            <div class="row mt-20">
                <div class="col-md-12">
                    Desarrollado por la <a href="http://inf.ucv.cl/" class="text-white">Escuela de Ingeniería Informática</a> de la <a href="http://pucv.cl/" class="text-white">PUCV</a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
    import axios from '../backend/axios';

    export default {
        name: 'Footer',
        data() {
            return {
                siteName: null,
                categories: [],
                address: null,
                contactPhones: [],
                socialNetworks: {
                    facebook: null,
                    twitter: null,
                    instagram: null,
                    youtube: null
                }
            };
        },
        mounted() {
            this.configComponent();
        },
        methods: {
            configComponent() {
                this.getSiteName();
                this.getSiteFooter();
                this.getAddress();
                this.getContactPhones();
                this.getSocialNetworks();
            },
            getSiteName() {
                axios
                    .get('settings', {
                        params: {
                            key: 'site_name'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.siteName = res.data[0].value;
                        }
                    });
            },
            getSiteFooter() {
                axios
                    .get('settings', {
                        params: {
                            key: 'site_footer'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.categories = JSON.parse(res.data[0].value).categorias;
                        }
                    });
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
            getSocialNetworks() {
                axios
                    .get('settings', {
                        params: {
                            key: 'social_networks'
                        }
                    })
                    .then(res => {
                        if(res.data.length > 0) {
                            this.socialNetworks = JSON.parse(res.data[0].value);
                        }
                    });
            }
        },
        computed: {
            isMobileDevice() {
                return (
                    typeof window.orientation !== 'undefined' ||
                    navigator.userAgent.indexOf('IEMobile') !== -1
                );
            },
            contactPhonesToString() {
                return this.contactPhones.join(' - ');
            }
        }
    }
</script>
