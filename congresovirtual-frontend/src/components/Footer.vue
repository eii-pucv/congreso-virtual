<template>
  <!-- Footer -->
  <footer class="page-footer font-small blue pt-4 mt-auto" STYLE="background-color: #082054">
    <div v-if="categorias.length" class="container text-center mb-20">
      <div class="row">
        <div class="col- mx-auto" v-for="categoria in categorias" :key="categoria.id">
          <h5 class="font-weight-bold text-uppercase text-white mt-3 mb-4">{{ categoria.text }}</h5>
          <ul v-if="categoria.subcategorias" class="list-unstyled">
            <li v-for="subcategoria in categoria.subcategorias" :key="subcategoria.id">
              <a class="text-white" :href="subcategoria.url">{{ subcategoria.text }}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container text-center text-md-left text-white mb-30">
      <div class="row">
        <div class="col-md-4 mt-md-20 mt-10">
          <h5 class="text-uppercase text-white">{{ $t('componentes.footer.congreso') }}</h5>
        </div>
        <div class="col-md-4 mt-md-10 mt-20 mb-3">
            <p>
                {{ $t('componentes.footer.avenida') }} Pedro Montt s/n Valparaíso.

                {{ $t('componentes.footer.telefonos') }}: (56-32) 250 5000 - (56-2) 2674 7800
            </p>
        </div>
        <div class="col-md-4 mb-md-0 mb-3" v-bind:class="{ 'text-right': !isMobileDevice }">
            <a v-if="social_networks.facebook" :href="social_networks.facebook" class="mr-1" style="font-size: 3rem; color: white">
                <span>
                    <i class="fa fa-facebook-square"></i>
                </span>
            </a>
            <a v-if="social_networks.twitter" :href="social_networks.twitter" class="mr-1" style="font-size: 3rem; color: white">
                <span>
                    <i class="fa fa-twitter-square"></i>
                </span>
            </a>
            <a v-if="social_networks.youtube" :href="social_networks.youtube" class="mr-1" style="font-size: 3rem; color: white">
                <span>
                    <i class="fa fa-youtube-square"></i>
                </span>
            </a>
            <a v-if="social_networks.instagram" :href="social_networks.instagram" class="mr-1" style="font-size: 3rem; color: white">
                <span>
                    <i class="fa fa-instagram"></i>
                </span>
            </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>

import axios from "../backend/axios"

export default {
  name: "Footer",
  data() {
    return {
      categorias: [],
      social_networks: {
          facebook: "",
          twitter: "",
          instagram: "",
          youtube: ""
      }
    }
  },

  computed: {

    isMobileDevice() {
      return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
    }
  },

  async mounted() {
    try {
      const res = await axios.get('/settings?key=site_footer')
      if(res.data.length > 0) {
        this.categorias = JSON.parse(res.data[0].value).categorias
      }
    } catch (error) {
        console.log(error)
    }

    try {
      const res = await axios.get('/settings?key=social_networks')
      if(res.data.length > 0) {
        this.social_networks = JSON.parse(res.data[0].value) 
      }
    } catch (error) {
      console.log(error)
    }
  },
};

</script>

<style scoped></style>
