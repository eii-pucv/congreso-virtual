<template>
  <div id="app" >
    <div v-if="!loading"> 
      <div class="row">
        <div class="col-12">
          <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_articulos') }}</h3> 
          <br/>
          <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_articulos') }}</p>
          <div class="col-12 px-4">
            <horizontal-barChart :data="chartData" :options="chartOptions"></horizontal-barChart>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_ideas') }}</h3>
          <br/>
          <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_ideas') }}</p>      
          <div class="col-12 px-4">
            <horizontal-barChart :data="chartData2" :options="chartOptions"></horizontal-barChart>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <h3 :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.votos_general') }}</h3>
          <br/>
          <p :style="mode==='dark'?'color: #fff':''">{{ $t('proyecto.componentes.votos_stacked.resultados_general') }}</p>      
          <div class="col-12 px-4">
            <horizontal-barChart :data="chartData3" :options="chartOptions2"></horizontal-barChart>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <div v-if="!dontExist">
        <p :style="mode==='dark'?'color: #fff':''"><b>{{ $t('proyecto.componentes.votos_stacked.cargando') }}</b></p>
      </div>
      <div v-else>
        <p :style="mode==='dark'?'color: #fff':''"><b>{{ $t('proyecto.componentes.votos_stacked.no_carga') }}</b></p>
      </div>
    </div>
  </div>
</template>

<script>
import  horizontalBarChart from "../../horizontalBarChart.js";
import axios from '../../backend/axios';
import { arrayExpression } from '@babel/types';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { bus } from "../../main";

export default {
  name: "VotosStackedChart",
  components: {
    "horizontal-barChart": horizontalBarChart,
  },
  props: {
    project_id: Number,
  },
  data() {
    return {
        mode: String,

        articles: [],
        ideas: [],
        project: {},
        articlesVotos: [],
        ideasVotos: [],
        projectVotos: [],

        article_votos_a_favor: [],
        article_votos_en_contra: [],
        article_abstencion: [],

        ideas_votos_a_favor: [],
        ideas_votos_en_contra: [],
        ideas_abstencion: [],

        labels: [],
        labels2: [],

        usuarios: [],
        chartData: Object,
        chartData2: Object,
        chartData3: Object,
        chartOptions: {
          scales: {
          xAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero:true
              },
            }
          ],
          yAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero:true
              },
            },
          ]
          },
          plugins: {
            datalabels: {
                color: '#FFFFFF',
                display: false,
            }
          },
          legend: {
          display: true,
          labels: {
              fontColor: 'rgb(0, 0, 0)',
              fontSize: 12,
          },
          position: 'top'
          },
        },
        chartOptions2: {
          scales: {
          xAxes: [
            {
              stacked: false
            }
          ],
          yAxes: [
            {
              stacked: false,
              ticks: {
                  beginAtZero:true
              },
            },
          ]
          },
          plugins: {
            datalabels: {
              color: '#FFFFFF',
              display: false,
            }
          },
          legend: {
          display: true,
          labels: {
              fontColor: 'rgb(0, 0, 0)',
              fontSize: 12,
          },
          position: 'top'
          },
      },
      loading: true,
      dontExist: false,
    };
  },
  methods:{
    toggle() {
      if (this.mode === "dark") {
        this.mode = "light";
      } else {
        this.mode = "dark";
      }
    },
  },
  created(){
    bus.$on("toggle", function(mode) {
        this.toggle();
    }.bind(this));

    var chartData = this.chartData;
    var array; 
    
    if(this.project_id == null || this.project_id==-1 || this.project_id == undefined){
      this.project_id = this.$route.params.id;
    };
    var project_id = this.project_id;

    axios
    .get("/projects/" + this.project_id)
    .then(res => {
      this.project = res.data;
      this.projectVotos.push(this.project.votos_a_favor);
      this.projectVotos.push(this.project.votos_en_contra);
      this.projectVotos.push(this.project.abstencion);

      for(var i = 0; i < this.project.articles.length; i++){
        let article = this.project.articles[i]

        this.labels.push("Articulo: "+(i+1));

        this.article_votos_a_favor.push(article.votos_a_favor);
        this.article_votos_en_contra.push(article.votos_en_contra);
        this.article_abstencion.push(article.abstencion);
      };

      for(var i = 0; i < this.project.ideas.length; i++){
        let idea = this.project.ideas[i]

        this.labels2.push("Idea fundamental: "+(i+1));

        this.ideas_votos_a_favor.push(idea.votos_a_favor);
        this.ideas_votos_en_contra.push(idea.votos_en_contra);
        this.ideas_abstencion.push(idea.abstencion);
      };

      this.chartData = {
        labels:this.labels,
        datasets: [
          {
            label: "A favor",
            data: this.article_votos_a_favor,
            
            backgroundColor: "#9DE19C"
          },
          {
            label: "Abstención",
            data: this.article_votos_en_contra,
            backgroundColor: "#E19C9C"
          },
          {
            label: "En contra",
            data: this.article_abstencion,
            backgroundColor: "#b1b1b1"
          },
        ]
      };
      this.chartData2 = {
        labels:this.labels2,
        datasets: [
          {
            label: "A favor",
            data: this.ideas_votos_a_favor,
            
            backgroundColor: "#9DE19C"
          },
          {
            label: "Abstención",
            data: this.ideas_votos_en_contra,
            backgroundColor: "#E19C9C"
          },
          {
            label: "En contra",
            data: this.ideas_abstencion,
            backgroundColor: "#b1b1b1"
          },
        ]
      };
      this.chartData3 = {
        datasets: [
          {
            label: ["A favor"],
            backgroundColor: ["#9DE19C"],
            data: [this.projectVotos[0],0],
          },
          {
            label: ["Abstención"],
            backgroundColor: ["#E19C9C"],
            data: [this.projectVotos[1]],
          },
          {
            label: ["En contra"],
            backgroundColor: ["#b1b1b1"],
            data: [this.projectVotos[2]],
          },
        ],
      };
      this.loading = false;
    })
    .catch(() => {
      this.$toastr(
        "error",
        "No se pudieron cargar las votaciones del proyecto",
        "Votaciones"
      );
      this.dontExist = true;
    });
  },
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
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>