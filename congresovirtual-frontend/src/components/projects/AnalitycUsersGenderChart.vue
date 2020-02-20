<template>
  <div id="app" class="col-6 px-4">
    <div v-if="!loading">
      <horizontal-barChart :data="chartData" :options="chartOptions"></horizontal-barChart>
    </div>
  </div>
</template>

<script>
import  horizontalBarChart from "../../horizontalBarChart.js";
import axios from '../../backend/axios';
export default {
  name: "HorizontalUserGenderBarChart",
  components: {
    "horizontal-barChart": horizontalBarChart
  },

  data() {
    return {
      usuarios: [],
      chartData: Object,
      chartOptions: {
        scales: {
          xAxes: [
            {
              stacked: true
            }
          ],
          yAxes: [
            {
              stacked: true
            }
          ]
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
    };
  },

  created(){

    var chartData = this.chartData;

    var array;    

    axios
      .get("/users")
      .then(res => {
        this.array = res.data;

        var devolverFechasDeInscripcion = function(array){
          var fechas = [];
          for(var i=0; i<array.length; i++){
            var fecha = array[i].created_at;
            fechas.push(fecha.substring(0,4));
          };
          var userByYear = devolverUsuariosPorAño(array,fechas[0]);
          return fechas;
        };

        var devolverLabels = function (datosArray){
          return datosArray.filter(function(item, index, inputArray) {
            return inputArray.indexOf(item) == index;
          });
        };

        var devolverUsuariosPorAño = function (datosArray, año){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var fecha = datosArray[i].created_at;
              fecha = fecha.substring(0,4);;
              if(fecha==año){
                arrayAux.push(datosArray[i]);
              }
            }
          }
          return arrayAux;
        };

        var devolverUsuariosMasculinos = function (datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var genero = datosArray[i].genero;
              if(genero=="Masculino"){
                arrayAux.push(datosArray[i]);
              }
            }
          }
          return arrayAux;
        };

        var devolverUsuariosFemeninos = function (datosArray){
          //genero
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var genero = datosArray[i].genero;
              if(genero=="Femenino"){
                arrayAux.push(datosArray[i]);
              }
            }
          }
          return arrayAux;
        };

        var devolverUsuariosDisidentes = function (datosArray){
          //genero
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var genero = datosArray[i].genero;
              if((genero!="Femenino")&&(genero!="Masculino")){
                arrayAux.push(datosArray[i]);
              }
            }
          }
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios masculinos por año
        var devolverUsuariosMasculinosPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosMasculinos(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios femeninos por año
        var devolverUsuariosDisidentesPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosDisidentes(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios disidentes por año
        var devolverUsuariosFemeninosPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosFemeninos(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //devuelve las fechas
        var fechas = devolverLabels(devolverFechasDeInscripcion(this.array));

        //Devuelve usuarios masculinos
        var masculinos = devolverUsuariosMasculinosPorAño(this.array,fechas);

        //Devuelve usuarios femeninos
        var femeninos = devolverUsuariosFemeninosPorAño(this.array,fechas);

        //Devuelve usuarios disidentes
        var disidentes = devolverUsuariosDisidentesPorAño(this.array,fechas);

        this.chartData = {
          labels:devolverLabels(devolverFechasDeInscripcion(this.array)),
          datasets: [
            {
              label: "Masculino",
              data: masculinos,
              backgroundColor: "#3a55b1"
            },
            {
              label: "Femenino",
              data: femeninos,
              backgroundColor: "#536bbb"
            },
            {
              label: "Otros",
              data: disidentes,
              backgroundColor: "#9caad8"
            }
          ]
        };
        this.loading = false;
      })
      .catch(() => {
      console.log("FAIL");
      });

  },

};
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
