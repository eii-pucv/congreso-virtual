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
                    Fecha de Ingreso
                    <date-picker v-model="fechaIngreso" :config="dateOptions" :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"></date-picker>
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    Autores
                    <input
                      v-model="autores"
                      class="form-control text mt-5"
                      :placeholder="'Ingrese el/los autores'"
                      :style="mode==='dark'?'background: #080035; color: #fff':''"
                    />
                  </label>
                  <label class="col-12 font-weight-bold" :style="mode==='dark'?'color: #fff':''">
                    Origen
                      <select
                        v-model="origen"
                        class="form-control custom-select d-block"
                        id="filterOrigen"
                      >
                        <option value="C. Diputados">C. Diputados</option>
                        <option value="Senado">Senado</option>
                      </select>
                  </label>
                  <div class="btn-group btn-group-toggle btn-block mt-10">
                    <button
                      @click="clearFilters"
                      class="btn btn-primary mx-3"
                      type="button"
                      :class="mode==='dark'?'btn-light':'btn-primary'"
                      :style="mode==='dark'?'color: #fff':''"
                    >{{ $t('proyectos.contenido.limpiar') }}</button>
                    <!-- <button class="vld-parent btn btn-primary" @click="search">{{ $t('proyectos.contenido.buscar') }}
                      <loading
                        :active.sync="loadBtnSearch"
                        :is-full-page="fullPage"
                        :height="24"
                        :color="color"
                      ></loading>
                    </button> -->
                  </div>
                   <label
                    class="col-12 font-weight-bold"
                    :style="mode==='dark'?'color: #fff':''"
                  >{{this.filteredList.length}} {{ $t('propuestas.contenido.resultados') }}</label>
                </form>
              </div>
            </div>
          </form>
        </div>
        <div v-if="this.isLoading && this.filteredList.length > 0 && !buscar && !autores && !fechaIngreso && !origen" class="col-sm-8">
          <div
            v-for="projectIdx in projectsToShow"
            class="col-12"
            :key="'card-project_'+projectIdx"
          >
            <div class="ma-5 col-lg-13">
              <div
                class="card"
                :style="mode === 'dark' ? 'background: rgb(12, 1, 80); border-color: #fff;' : ''"
              >
                <div class="card-header">
                  <p
                    class="card-title text-justify font-weight-bold"
                    :class="mode === 'dark' ? '' : 'text-primary'"
                  >{{ filteredList[projectIdx - 1].PROYSUMA }}</p>
                </div>
                <div class="card-body pt-0">
                  <p>
                    <strong>{{ $t('propuestas.contenido.boletin') }}</strong>
                    {{ filteredList[projectIdx - 1].PROYNUMEROBOLETIN }}
                  </p>
                  <p>
                    <strong>{{ $t('propuestas.contenido.autoria') }}</strong>
                    {{ filteredList[projectIdx - 1].AUTORES }}
                  </p>
                  <p>
                    <strong>{{ $t('propuestas.contenido.fecha') }}</strong>
                    {{ filteredList[projectIdx - 1].FECHAINGRESO }}
                  </p>
                </div>
                <div class="btn-group-vertical">
                  <a class="font-1"></a>
                  <div class="btn-group mt-auto" role="group" aria-label="Basic example">
                    <button
                      @click="showModal(filteredList[projectIdx - 1],1)"
                      class="btn text-white bg-primary font-12"
                    >
                      <font-awesome-icon icon="vote-yea" />
                      <span class="btn-text">{{ $t('propuestas.contenido.incluir') }}</span>
                    </button>
                    <button
                      @click="showModal(filteredList[projectIdx - 1],2)"
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
            v-if="filteredList.length > projectsToShow"
            @click="loadMore()"
          >{{ $t('propuestas.contenido.cargar') }}</button>
        </div>
        <div class="col-sm-8" v-else>
          <div v-for="(project, index) in filteredList" class="col-12" :key="'card-project_'+index">
            <div class="ma-5 col-lg-13">
              <div
                class="card"
                :style="mode === 'dark' ? 'background: rgb(12, 1, 80); border-color: #fff;' : ''"
              >
                <div class="card-header">
                  <p
                    class="card-title text-justify font-weight-bold"
                    :class="mode === 'dark' ? '' : 'text-primary'"
                  >{{ project.PROYSUMA }}</p>
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
                  <a class="font-1"></a>
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
          <div class="py-50 text-center border my-2" v-if="filteredList.length === 0">{{ $t('proyectos.contenido.no_hay_resultados') }}</div>
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
    clearFilters() {
      this.buscar = "";
      this.fechaIngreso = "";
      this.autores = "";
      this.origen = "";
      this.filteredList = this.projects
    },
    search() {
      if(this.autores) {
        var projectsList = []
        if(this.buscar) {
          projectsList = this.filteredList
        } else {
          projectsList = this.projects
        }
        projectsList = this.filteredList.filter(result => {
          if (result.AUTORES !== undefined) {
            return result.AUTORES.toLowerCase().includes(
              this.autores.toLowerCase()
            );
          } else return false;
        })
        this.filteredList.splice(0,this.filteredList.length)
        this.filteredList.push(...projectsList)
        return this.filteredList
      }
    },
    loadMore() {
      if (this.projectsToShow <= this.filteredList.length) {
        if (this.filteredList.length - this.projectsToShow < this.maxToShow) {
          return (this.projectsToShow +=
            this.filteredList.length - this.projectsToShow);
        } else {
          return (this.projectsToShow += 10);
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
        })
        .catch(e => console.error("FAIL: " + JSON.stringify(e)));
      axioma
        .create()
        .get("https://slr.senado.cl/getListaProyectosConMovto/S/30")
        .then(res => {
          this.projects.push(...res.data);
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
            console.error(e);
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
      buscar: "",
      fechaIngreso: "",
      autores: "",
      origen: "",
      limit: 10,
      descripcion: "",
      project: null,
      type: null,
      projectsToShow: 10,
      maxToShow: 10,
      dateOptions: {
        format: 'DD/MM/YYYY',
        useCurrent: true,
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
    filteredList() {
      var filterProjects = this.projects.filter(project => {
        if (project.PROYSUMA !== undefined && project.AUTORES !== undefined && project.FECHAINGRESO !== undefined && project.ORIGEN !== undefined) {
          return project.PROYSUMA.toLowerCase().includes(
            this.buscar.toLowerCase()
          ) && project.AUTORES.toLowerCase().includes(
              this.autores.toLowerCase())
            && project.FECHAINGRESO.includes(
            this.fechaIngreso)
            && project.ORIGEN.includes(
            this.origen)
        } else return false;
      });
      
      this.projectsToShow = filterProjects.length > this.maxToShow ? 10 : this.maxToShow - filterProjects.length;
      let filterOrderByDate = filterProjects.sort((a, b) => {
        return new Date(b.FECHAINGRESO) - new Date(a.FECHAINGRESO);
      });
      return filterOrderByDate;
    },
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
