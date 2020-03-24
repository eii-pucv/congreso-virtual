<template>
    <div class="">
        <label for="language" class="text-white my-0">{{ $t('navbar.idioma') }}</label>
        <select id="language" @change="changeLanguage" v-model="selection" class="form-control form-control-sm custom-select ml-10">
            <option v-for="language in languages" :key="language.locale" :value="language.locale">{{ language.title }}</option>
        </select>
    </div>
</template>

<script>
    import { i18n } from '@/plugins/i18n';

    export default {
        name: 'LanguageComponent',
        computed: {
            languages() {
                return [
                    { locale: 'es', title: this.$t('spanish') },
                    { locale: 'en', title: this.$t('english') }
                ]
            }
        },
        data() {
            return {
                selection: ''
            }
        },
        methods: {
            changeLanguage() {
                i18n.locale = this.selection;
                this.$store.commit('setLanguage');
                this.$moment.locale(this.selection);
            }
        },
        mounted() {
            if(this.$store.getters.language) {
                i18n.locale = this.$store.getters.language;
                this.$moment.locale(this.$store.getters.language);
            }
            this.$store.commit('setLanguage');
            this.selection = i18n.locale;
        }
    }
</script>