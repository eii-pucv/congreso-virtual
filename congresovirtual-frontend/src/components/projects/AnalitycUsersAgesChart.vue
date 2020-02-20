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
import { arrayExpression } from '@babel/types';
export default {
  name: "HorizontalUserAgesBarChart",
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
        
        //devuelve las fechas de cumpleaños de cada usuario
        var devolverArrayCumpleaños = function (datosArray){
            var arrayAux=[];
            if(datosArray.length>0){
                for(var i=0;i<datosArray.length;i++){
                if(datosArray[i].birthday!=null){
                    arrayAux.push(datosArray[i].birthday);
                }
                }
            };
            return arrayAux;
        };

        //Este es necesario para los labels
        var devolverFechasDeInscripcion = function(array){
          var fechas = [];
          for(var i=0; i<array.length; i++){
            var fecha = array[i].created_at;
            fechas.push(fecha.substring(0,4));
          };
          var userByYear = devolverUsuariosPorAño(array,fechas[0]);
          return fechas;
        };
       
       //Devuelve la edad de un usuario
        var devolverEdadUsuario = function (fecha) {
            var hoy = new Date();
            var cumpleanos = new Date(fecha);
            var edad = hoy.getFullYear() - cumpleanos.getFullYear();
            var m = hoy.getMonth() - cumpleanos.getMonth();

            if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                edad--;
            }
            return edad;
        };

        //Devuelve las edades de cada usuario
        var devolverEdadesUsuario = function(fechas){
            var arrayAux=[];
            if(fechas.length>0){
                for(var i=0;i<fechas.length;i++){
                    arrayAux.push(devolverEdadUsuario(fechas[i]));
                };
            };
            return arrayAux;
        };

        //Devuelve los años de inscripción/labels
        var devolverLabels = function (datosArray){
          return datosArray.filter(function(item, index, inputArray) {
            return inputArray.indexOf(item) == index;
          });
        };

        //Devuelve un Array con los usuario de un específico año
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

        //Devuelve Array de cantidad de usuarios menores de edad
        var devolverUsuariosMenoresDeDieciocho = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if((usuario<18)&&(usuario>0)){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios menores de edad por año
        var devolverUsuariosMenoresDeDieciochoPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosMenoresDeDieciocho(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios entre 18-30
        var devolverUsuariosEntreDieciochoYTreinta = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if((usuario>=18)&&(usuario<30)){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios entre 18-30 por año
        var devolverUsuariosEntreDieciochoYTreintaPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosEntreDieciochoYTreinta(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios entre 30-45
        var devolverUsuariosEntreTreintaYCuarentaicinco = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if((usuario>=30)&&(usuario<45)){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios entre 30-45 por año
        var devolverUsuariosEntreTreintaYCuarentaicincoPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosEntreTreintaYCuarentaicinco(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios entre 45-60
        var devolverUsuariosEntreCuarentaicincoYSesenta = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if((usuario>=45)&&(usuario<60)){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios entre 45-60 por año
        var devolverUsuariosEntreCuarentaicincoYSesentaPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosEntreCuarentaicincoYSesenta(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios de más de 60
        var devolverUsuariosMayoresDeSesenta = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if(usuario>=60){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios de más de 60 por año
        var devolverUsuariosMayoresDeSesentaPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosMayoresDeSesenta(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //Devuelve Array de cantidad de usuarios de más de 60
        var devolverUsuariosIndefinidos = function(datosArray){
          var arrayAux=[];
          if(datosArray.length>0){
            for(var i=0;i<datosArray.length;i++){
              var usuario=devolverEdadUsuario(datosArray[i].birthday);
              if((usuario<0)||(usuario==null)){
                arrayAux.push(usuario);
              }
            };
          };
          return arrayAux;
        };

        //Devuelve Array con cantidad de usuarios de más de 60 por año
        var devolverUsuariosIndefinidosPorAño = function(datosArray,fechas){
          var arrayAux=[];
          if(fechas.length>0){
            for(var i=0;i<fechas.length;i++){
              var dato = (devolverUsuariosIndefinidos(devolverUsuariosPorAño(datosArray,fechas[i])));
              if(dato.length>0){
                arrayAux.push(dato.length);
              }else{
                arrayAux.push(0);
              }
            }
          };
          return arrayAux;
        };

        //
        var fechas = devolverLabels(devolverFechasDeInscripcion(this.array));

        //
        var arrayCumpleaños = devolverArrayCumpleaños(this.array);
        //
        var arrayEdades = devolverEdadesUsuario(arrayCumpleaños);
        //

        //
        var rango1 = devolverUsuariosMenoresDeDieciochoPorAño(this.array,fechas);

        //
        var rango2 = devolverUsuariosEntreDieciochoYTreintaPorAño(this.array,fechas);

        //
        var rango3 = devolverUsuariosEntreTreintaYCuarentaicincoPorAño(this.array,fechas);

        //
        var rango4 = devolverUsuariosEntreCuarentaicincoYSesentaPorAño(this.array,fechas);

        //
        var rango5 = devolverUsuariosMayoresDeSesentaPorAño(this.array,fechas);

        //
        var rango6 = devolverUsuariosIndefinidosPorAño(this.array,fechas);


      



        this.chartData = {
          labels:devolverLabels(devolverFechasDeInscripcion(this.array)),
          datasets: [
            {
              label: "18-",
              data: rango1,
              backgroundColor: "#172428"
            },
            {
              label: "18-30",
              data: rango2,
              backgroundColor: "#24424c"
            },
            {
              label: "30-45",
              data: rango3,
              backgroundColor: "#306373"
            },
            {
              label: "45-60",
              data: rango4,
              backgroundColor: "#3c869d"
            },
            {
              label: "60+",
              data: rango5,
              backgroundColor: "#47abc8"
            },
            {
              label: "Indefinido",
              data: rango6,
              backgroundColor: "#51d1f6"
            },
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