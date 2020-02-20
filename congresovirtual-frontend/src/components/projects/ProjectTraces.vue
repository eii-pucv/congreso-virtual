<template>
  <div class="">
    <div class="container py-2">
      <!-- timeline item 2 -->
      <div class="row" v-for="(event,index) in seguimiento" :key="event.id">
        <div class="col-auto text-center flex-column d-none d-sm-flex">
          <div class="row h-50">
            <div class="col border-right">&nbsp;</div>
            <div class="col">&nbsp;</div>
          </div>
          <h5 class="m-2">
            <span class="badge badge-pill bg-secondary" v-bind:class="{'bg-success' : (index===0)}">&nbsp;</span>
          </h5>
          <div class="row h-50">
            <div class="col border-right">&nbsp;</div>
            <div class="col">&nbsp;</div>
          </div>
        </div>
        <div class="col py-2">
          <div class="card shadow" v-bind:class="{'border-success' : (index===0)}">
            <div class="card-body" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
              <div class="float-right" v-bind:class="{'text-success' : (index===0)}">{{ new Date(event.FECHA_SORT) | moment("D MMM YYYY") }}</div>
              <h5 class="card-title" v-bind:class="{'text-success' : (index===0)}" :style="mode==='dark'?'color: #fff':''">{{event.TRAMITE}} </h5>
              <h5><span class="badge badge-info">{{event.CAMDELTRAMITE}}</span></h5>
              <p class="card-text"></p>
            </div>
          </div>
        </div>
      </div>
      <!--/row-->
      <div class="row" v-if="seguimiento.length < 1">
        <div class="col-auto text-center flex-column d-none d-sm-flex">
          <div class="row h-50">
            <div class="col border-right">&nbsp;</div>
            <div class="col">&nbsp;</div>
          </div>
          <h5 class="m-2">
            <span class="badge badge-pill bg-success">&nbsp;</span>
          </h5>
          <div class="row h-50">
            <div class="col border-right">&nbsp;</div>
            <div class="col">&nbsp;</div>
          </div>
        </div>
        <div class="col py-2">
          <div class="card border-success shadow">
            <div class="card-body">
              <p class="card-text">{{ $t('proyecto.componentes.seguimiento.sin_datos') }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!--container-->

  </div>
</template>

<script>
import axios from 'axios'
import { bus } from "../../main";

export default {
  name: "ProjectTraces",
  props: {
    project_id: Number,
    project_boletin: String
  },
    data() {
        return {
          seguimiento: [],
          mode: String,
        }
    },
  created() {

    if ((this.$store.getters.modo_oscuro == "dark") || (window.location.href.includes("dark"))) {
      this.mode = "dark"
    } else {
      this.mode = "light"
    }


    axios.create({
      headers: {
      }
    })
      .get("https://slr.senado.cl/getTramitacionProyecto/" + this.project_boletin)
      .then(res => {
        //console.log("URL: "+ "/projects/" + this.project_id + "/traces")
        //console.log("SEGUIMIENTO: "+ JSON.stringify(res.data,false,4))
        this.seguimiento = res.data;
        this.seguimiento.sort(function(a,b){
          // Turn your strings into dates, and then subtract them
          // to get a value that is either negative, positive, or zero.
          return new Date(b.FECHA_SORT) - new Date(a.FECHA_SORT);
        });
      })
      .catch((e) => console.error("FAIL: "+ JSON.stringify(e)));
  },
  methods:{
    
  }
};
</script>

<style >
  .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }

</style>
