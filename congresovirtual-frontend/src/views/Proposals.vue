<template>
  <section class="mt-1" :style="mode==='dark'?'background: #080035; color: #fff':''">
    <div class="container" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
      <div class="row">
        <div class="col-12 mt-20">
          <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('propuestas.contenido.titulo') }}</h3>
          <br />
          <p class="mb-5">{{ $t('propuestas.contenido.parrafo1') }}</p>
          <p class="mt-5">{{ $t('propuestas.contenido.parrafo2') }}</p>
        </div>
      </div>
    </div>
    <nav aria-label="breadcrumb" class="container px-0">
      <ol class="breadcrumb" :style="mode === 'dark' ? 'background: rgb(12, 1, 80);' : ''">
        <li class="breadcrumb-item">
          <a
            href="/#"
            :style="mode==='dark'?'color: #fff':''"
          >{{ $t('propuestas.breadcumb.inicio') }}</a>
        </li>
        <li
          class="breadcrumb-item active"
          aria-current="page"
          :style="mode==='dark'?'color: #fff':''"
        >{{ $t('propuestas.breadcumb.propuestas') }}</li>
      </ol>
    </nav>
    <div class="container card" :style="mode === 'dark' ? 'background: rgb(12, 1, 80);' : ''">
      <div class="hk-row">
        <div class="col-sm-4">
          <form class="form-group row ma-20">
            <div class="col-12">
              <div class="col-12 row-md-4 mt-15">
                <form class="row" v-on:submit.prevent>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.buscar') }}
                    <input
                      v-model="buscar"
                      class="form-control text mt-5"
                      :placeholder="$t('propuestas.contenido.busqueda')"
                      :style="mode==='dark'?'background: #080035; color: #fff':''"
                    />
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">            
                    {{ $t('propuestas.contenido.fecha') }}
                    <date-picker v-model="fechaIngreso" :config="dateOptions" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></date-picker>
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.autores') }}
                    <input
                      v-model="autores"
                      class="form-control text mt-5"
                      :placeholder="$t('propuestas.contenido.autores_placeholder')"
                      :style="mode==='dark'?'background: #080035; color: #fff':''"
                    />
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.origen') }}
                      <select
                        v-model="origen"
                        class="form-control custom-select d-block"
                        id="filterOrigen"
                      >
                       <option v-for="(source, index) in sourceProposals" :key="'origen_tipo_' + index" :value="source">{{ source }}</option>
                      </select>
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    {{ $t('propuestas.contenido.ordenar') }}
                      <select
                        @change="sort"
                        v-model="ordenar"
                        class="form-control custom-select d-block"
                        id="filterOrigen"
                      >
                       <option v-for="(sort, index) in sortOptions" :key="'ordenar_' + index" :value="sort.value">{{ sort.name }}</option>
                      </select>
                  </label>
                  <div class="btn-group btn-group-toggle btn-block mt-10 mx-15">
                    <button
                      @click="clearFilters"
                      class="btn btn-outline-primary"
                      type="button"
                      :class="mode==='dark'?'btn-light':'btn-primary'"
                      :style="mode==='dark'?'color: #fff':''"
                    >{{ $t('proyectos.contenido.limpiar') }}</button>
                    <button class="vld-parent btn btn-primary" @click="search">{{ $t('proyectos.contenido.buscar') }}
                      <loading
                        :active.sync="loadBtnSearch"
                        :is-full-page="fullPage"
                        :height="24"
                        :color="color"
                      ></loading>
                    </button>
                  </div>
                   <label
                    class="col-12 font-weight-bold"
                    :style="mode==='dark'?'color: #fff':''"
                  >{{ projects.length }} {{ $t('propuestas.contenido.resultados') }}</label>
                </form>
              </div>
            </div>
          </form>
        </div>
        <div class="col-sm-8 mt-25" v-if="projects.length > 0">
          <div class="col-12" v-for="(project, index) in projects.slice(0, limit)" :key="'card-project_' + index">
            <div class="ma-5 col-lg-12">
              <div class="card" :style="mode === 'dark' ? 'background: rgb(12, 1, 80); border-color: #fff;' : ''">
                <div class="card-header">
                  <p class="card-title text-justify font-weight-bold" :class="mode === 'dark' ? '' : 'text-primary'">
                    {{ project.PROYSUMA }}
                  </p>
                </div>
                <div class="card-body pt-0">
                  <p>
                    <strong>{{ $t('propuestas.contenido.boletin') }}</strong>
                    {{ project.PROYNUMEROBOLETIN }}
                  </p>
                  <p>
                    <strong>{{ $t('propuestas.contenido.autoria') }}</strong>
                    {{ project.AUTORES }}
                  </p>
                  <p>
                    <strong>{{ $t('propuestas.contenido.fecha') }}</strong>
                    {{ project.FECHAINGRESO }}
                  </p>
                </div>
                <div class="btn-group-vertical">
                  <div class="btn-group mt-auto" role="group" aria-label="Basic example">
                    <button
                      @click="showModal(project, 1)"
                      class="btn text-white bg-primary font-12"
                    >
                      <font-awesome-icon icon="vote-yea" />
                      <span class="btn-text">{{ $t('propuestas.contenido.incluir') }}</span>
                    </button>
                    <button
                      @click="showModal(project, 2)"
                      class="btn text-white bg-warning font-12"
                    >
                      <i class="fa fa-warning"></i>
                      <span class="btn-text text-primary">{{ $t('propuestas.contenido.pedir') }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button
            class="btn btn-primary btn-block"
            v-if="limit < projects.length"
            @click="loadMoreProjects()"
          >{{ $t('propuestas.contenido.cargar') }}</button>
        </div>
        <div class="col-sm-8 my-30 border d-flex align-self-start" v-if="projects.length === 0 && !loadBtnSearch">
          <p class="text-center w-100 h-100 my-50">
            {{ $t('propuestas.contenido.no_hay_resultados') }}
          </p>
        </div>
      </div>
    </div>
    <b-modal
      id="modal-descripcion"
      footer-bg-variant="primary"
      header-bg-variant="primary"
      body-bg-variant="light"
      header-class="justify-content-center"
      hide-header-close
      no-close-on-backdrop
      centered
    >
      <template v-slot:modal-header>
        <h5 class="hk-sec-title text-white my-3">{{ $t('propuestas.contenido.modal.detalle') }}</h5>
      </template>
      <div class="form-row">
        <div class="col-md-12 mb-10">
          <label for="description">{{ $t('propuestas.contenido.modal.descripcion') }}</label>
          <textarea id="description" class="form-control" rows="3" v-model="descripcion"></textarea>
        </div>
      </div>
      <template v-slot:modal-footer>
        <b-button
          class="btn btn-sm bg-green votable"
          block
          @click="createProposal(),$bvModal.hide('modal-descripcion')"
        >
          <font-awesome-icon icon="envelope" />
          <span class="btn-text">{{ $t('propuestas.contenido.modal.enviar') }}</span>
        </b-button>
        <b-button
          class="btn btn-sm bg-red votable mb-2"
          block
          @click="$bvModal.hide('modal-descripcion'),descripcion = ''"
        >
          <font-awesome-icon icon="window-close" />
          <span class="btn-text">{{ $t('cancelar') }}</span>
        </b-button>
      </template>
    </b-modal>
  </section>
</template>

<script>
import "vue-loading-overlay/dist/vue-loading.css";
import { BModal } from "bootstrap-vue";
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import axios from "../backend/axios";
import axioma from "axios";

export default {
  name: "Proposals",
  components: {
    BModal,
    datePicker
  },
  props: {
    mode: String
  },
  methods: {
    sort() {
      if(this.ordenar === 'Desc') {
        this.projects = this.projects.sort((a, b) => {
          return new Date(b.PROYFECHAINGRESO) - new Date(a.PROYFECHAINGRESO);
        });
      } else {
        this.projects = this.projects.sort((a, b) => {
          return new Date(a.PROYFECHAINGRESO) - new Date(b.PROYFECHAINGRESO);
        });
      }
    },
    clearFilters() {
      this.buscar = "";
      this.fechaIngreso = "";
      this.autores = "";
      this.origen = "";
      this.limit = 10;
      this.projects = [...this.startProjects]
    },
    search() {
      var projectsList = []
      this.projects = [...this.startProjects]
      projectsList = this.projects.filter(project => {
        if (project.PROYSUMA !== undefined
          && project.AUTORES !== undefined
          && project.FECHAINGRESO !== undefined
          && project.ORIGEN !== undefined) {
          return project.PROYSUMA.toLowerCase().includes(
            this.buscar.toLowerCase()
          ) && project.AUTORES.toLowerCase().includes(
              this.autores.toLowerCase())
            && project.FECHAINGRESO.includes(
            this.fechaIngreso)
            && project.ORIGEN.includes(
            this.origen)
        } else return false;
      })
      this.projects = [...projectsList]
      this.sort();
      return this.projects
    },
    loadMoreProjects() {
      let projectsLength = this.projects.length
      if(this.limit < projectsLength) {
        if(projectsLength - this.limit < 10) {
          this.limit += projectsLength - this.limit
        } else {
          this.limit += 10
        }
      }
    },
    toggleClass: function(event) {
      if (this.isVoted) {
        this.isVoted = false;
      } else {
        this.isVoted = true;
      }
    },
    getProjectPropusalsFromSIR() {
      axioma
        .create()
        .get("https://slr.senado.cl/getListaProyectosConMovto/D/30")
        .then(res => {
          this.projects.push(...res.data);
          this.startProjects.push(...res.data);
        })
        .catch(e => console.error("FAIL: " + JSON.stringify(e)));
      axioma
        .create()
        .get("https://slr.senado.cl/getListaProyectosConMovto/S/30")
        .then(res => {
          this.projects.push(...res.data);
          this.startProjects.push(...res.data);
          this.sourceProposals = [...new Set(this.projects.map(project => project.ORIGEN))];
          this.isLoading = true;
        })
        .catch(e => console.error("FAIL: " + JSON.stringify(e)));
    },
    createProposal() {
      let fecha = this.project.FECHAINGRESO.split("/").reverse().join("-");
      if (this.isLoggedIn) {
        axios
          .post("/proposals", {
            titulo: this.project.PROYSUMA,
            detalle: this.descripcion,
            autoria: this.project.AUTORES,
            boletin: this.project.PROYNUMEROBOLETIN,
            fecha_ingreso: fecha,
            type: this.type,
          })
          .then(res => {
            this.$toastr("success", "", "Propuesta creada correctamente. Debe esperar a ser aprobado por el administrador");
          })
          .catch(e => {
            this.$toastr("warning", "", "Ya fue propuesto este proyecto");
          });
      } else {
        this.$toastr("warning", "", "Debes iniciar sesión");
      }
      this.descripcion = "";
    },

    showModal(project, type) {
      this.project = project;
      this.type = type;
      this.$bvModal.show("modal-descripcion");
    }
  },
  data: function() {
    return {
      isLoading: false,
      fullPage: false,
      componente: "ListaProyectos",
      arrayPreferencias: ["postulante", "ASC", "6", []],
      isVoted: false,
      projects: [],
      startProjects: [],
      buscar: "",
      fechaIngreso: "",
      autores: "",
      origen: "",
      ordenar: "Desc",
      sortOptions: [{
          name:"Fecha de Ingreso (Desc.)",
          value: 'Desc'
        },{
          name: "Fecha de Ingreso (Asc.)", 
          value:'Asc'
        }
      ],
      limit: 10,
      offset: 0,
      descripcion: "",
      project: null,
      type: null,
      projectsToShow: 10,
      maxToShow: 10,
      sourceProposals: [],
      dateOptions: {
        format: 'DD/MM/YYYY',
        useCurrent: false,
      }, 
    };
  },
  mounted() {
    if (this.$store.getters.modo_oscuro == "dark") {
      this.mode = "dark";
    } else if (!this.mode) {
      this.mode = "light";
    }
    this.getProjectPropusalsFromSIR();
  },
  computed: {
    isLoggedIn: function() {
      return this.$store.getters.isLoggedIn;
    },
    userData: function() {
      return this.$store.getters.userData;
    }
  }
};
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
