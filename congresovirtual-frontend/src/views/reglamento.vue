<template>
  <div class="px-0" style="min-height: 720px;" :style="mode==='dark'?'background: #080035; color: #fff':''">
    <nav aria-label="breadcrumb" class="container pt-20">
      <ol class="breadcrumb" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
        <li class="breadcrumb-item"><a href="/#" :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.breadcumb.inicio') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page" :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.breadcumb.reglamento') }}</li>
      </ol>
    </nav>
    <div class="container mt-3">
      <div class="row">
        <div class="col-xl-12">
          <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80); border-color: #fff;':''">
              <div class="card-body mb-30 text-justify" :style="mode==='dark'?'color: #fff':''">
                <h2 class="hk-sec-title text-center mb-40" :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.titulo') }}</h2>
                <h6 :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.contenido.parrafo1.titulo') }}</h6><br>
                {{ $t('reglamento.contenido.parrafo1.descripcion') }}<br><br>
                <h6 :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.contenido.parrafo2.titulo') }}</h6><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo1') }}<br><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo2') }}<br><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo3') }}<br><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo4') }}<br><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo5') }}<br><br>
                {{ $t('reglamento.contenido.parrafo2.descripcion.parrafo6') }}<br><br>
                <h6 :style="mode==='dark'?'color: #fff':''">{{ $t('reglamento.contenido.parrafo3.titulo') }}</h6><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo1') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo2') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo3') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo4') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo5') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo6') }}<br><br>
                {{ $t('reglamento.contenido.parrafo3.descripcion.parrafo7') }}<br><br>
              </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {bus} from "../main";

  export default {
    name: "reglamento",
    components: {
    
    },
    props: {
      mode: String,
    },
    data() {
      return {
    
      }
    },
    methods: {
        
    },
    mounted() {
      if (this.$store.getters.modo_oscuro == "dark") {
        this.mode = "dark"
      } else if (!this.mode) {
        this.mode = "light"
      }
    },
  }
</script>

<style>
  .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>