import Vue from 'vue'
import VueI18n from 'vue-i18n'
import english from "../data/en.json"
import spanish from "../data/es.json"

Vue.use(VueI18n)

const messages = {
  es: spanish,
  en: english
}

export const i18n = new VueI18n({
    locale: 'es',
    fallbackLocale: 'en',
    messages
})