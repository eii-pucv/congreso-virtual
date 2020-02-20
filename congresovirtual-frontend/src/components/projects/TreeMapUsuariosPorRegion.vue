<template>
  <div v-if="!loading" id="vueapp" class="vue-app">
    <TreeMap :data-source="localDataSource" :value-field="'value'" :text-field="'name'"></TreeMap>
  </div>
</template>

<script>
import '@progress/kendo-ui';
import '@progress/kendo-theme-default/dist/all.css';
import { TreeMap} from '@progress/kendo-treemap-vue-wrapper'
import axios from '../../backend/axios';

export default {
  name: "TreemapUsuariosPorRegion",
  components: {
    TreeMap,
  },
  props: {
        project_id: Number,
  },
  data() {
    return {
      localDataSource: Array,
      localDataSourceDefault:  [
        {
          name: "Population in USA",
          value: 316128839,
          items: [
            {
              name: "Alabama",
              value: 4833722,
              items: [
                {
                  name: "Birmingham",
                  value: 212113
                },
                {
                  name: "Montgomery",
                  value: 201332
                },
                {
                  name: "Mobile",
                  value: 194899
                },
                {
                  name: "Huntsville",
                  value: 186254
                },
                {
                  name: "Tuscaloosa",
                  value: 95334
                },
                {
                  name: "Hoover",
                  value: 84126
                },
                {
                  name: "Dothan",
                  value: 68001
                },
                {
                  name: "Auburn",
                  value: 58582
                },
                {
                  name: "Decatur",
                  value: 55816
                }
              ]
            },
            {
              name: "Alaska",
              value: 735132,
              items: [
                {
                  name: "Anchorage",
                  value: 300950
                },
                {
                  name: "Badger",
                  value: 20200
                },
                {
                  name: "College",
                  value: 13400
                },
                {
                  name: "Fairbanks",
                  value: 32324
                },
                {
                  name: "Juneau",
                  value: 32660
                },
                {
                  name: "Ketchikan",
                  value: 8214
                },
                {
                  name: "Sitka",
                  value: 9020
                }
              ]
            },
            {
              name: "Arizona",
              value: 6626624,
              items: [
                {
                  name: "Phoenix",
                  value: 1513367
                },
                {
                  name: "Tucson",
                  value: 526116
                },
                {
                  name: "Mesa",
                  value: 457587
                },
                {
                  name: "Chandler",
                  value: 249146
                },
                {
                  name: "Glendale",
                  value: 234632
                },
                {
                  name: "Gilbert",
                  value: 229972
                },
                {
                  name: "Scottsdale",
                  value: 226918
                },
                {
                  name: "Tempe",
                  value: 168228
                },
                {
                  name: "Peoria",
                  value: 162592
                },
                {
                  name: "Surprise",
                  value: 123546
                }
              ]
            },
            {
              name: "Arkansas",
              value: 2959373,
              items: [
                {
                  name: "Little Rock",
                  value: 197357
                },
                {
                  name: "Fort Smith",
                  value: 87650
                },
                {
                  name: "Fayetteville",
                  value: 78960
                },
                {
                  name: "Springdale",
                  value: 75229
                },
                {
                  name: "Jonesboro",
                  value: 71551
                },
                {
                  name: "North Little Rock",
                  value: 66075
                },
                {
                  name: "Conway",
                  value: 63816
                },
                {
                  name: "Rogers",
                  value: 60112
                },
                {
                  name: "Pine Bluff",
                  value: 46094
                },
                {
                  name: "Bentonville",
                  value: 40167
                }
              ]
            },
            {
              name: "California",
              value: 38332521,
              items: [
                {
                  name: "Los Angeles",
                  value: 3884307
                },
                {
                  name: "San Diego",
                  value: 1355896
                },
                {
                  name: "San Jose",
                  value: 998537
                },
                {
                  name: "San Francisco",
                  value: 837442
                },
                {
                  name: "Fresno",
                  value: 509924
                },
                {
                  name: "Sacramento",
                  value: 479686
                },
                {
                  name: "Long Beach",
                  value: 469428
                },
                {
                  name: "Oakland",
                  value: 406253
                },
                {
                  name: "Bakersfield",
                  value: 363630
                },
                {
                  name: "Anaheim",
                  value: 345012
                },
                {
                  name: "Santa Ana",
                  value: 334227
                }
              ]
            },
            {
              name: "Colorado",
              value: 5268367,
              items: [
                {
                  name: "Denver",
                  value: 649495
                },
                {
                  name: "Colorado Springs",
                  value: 439886
                },
                {
                  name: "Aurora",
                  value: 345803
                },
                {
                  name: "Fort Collins",
                  value: 152061
                },
                {
                  name: "Lakewood",
                  value: 147214
                },
                {
                  name: "Thornton",
                  value: 127359
                },
                {
                  name: "Arvada",
                  value: 111707
                },
                {
                  name: "Westminster",
                  value: 110945
                },
                {
                  name: "Pueblo",
                  value: 108249
                },
                {
                  name: "Centennial",
                  value: 106114
                },
                {
                  name: "Boulder",
                  value: 103166
                },
                {
                  name: "Highlands Ranch",
                  value: 102000
                }
              ]
            },
            {
              name: "Connecticut",
              value: 3596080,
              items: [
                {
                  name: "Bridgeport",
                  value: 147216
                },
                {
                  name: "New Haven",
                  value: 130660
                },
                {
                  name: "Stamford",
                  value: 126456
                },
                {
                  name: "Hartford",
                  value: 125017
                },
                {
                  name: "Waterbury",
                  value: 109676
                },
                {
                  name: "Norwalk",
                  value: 87776
                },
                {
                  name: "Danbury",
                  value: 83684
                },
                {
                  name: "New Britain",
                  value: 72939
                },
                {
                  name: "West Hartford",
                  value: 63371
                },
                {
                  name: "Bristol",
                  value: 60568
                },
                {
                  name: "Meriden",
                  value: 60456
                }
              ]
            }
          ]
        }
      ],
      dataAux: Array,
      usuarios: Array,
      loading: true,
      project: {},
    };
  },

  methods: {
    lugarEsta(pais,paises){

      for(var i=0;i<paises.length;i++){
        if(pais==paises[i]){
          console.log("Already exist.");
          return true;
        }
      };
      return false;
    },

    contarVecesEnQueApareceRegion(lugar,participantes){
      var veces=0;
      for(var i=0;i<participantes.length;i++){
        var lugarAux;
        if(participantes[i].region!=null){
          lugarAux = participantes[i].region;
        }else{
          lugarAux = "No definido.";
        };

        if(lugar == lugarAux){
          veces++;
        }
      }
      return veces;
    },

  },
  
  created(){

    this.localDataSource = this.localDataSourceDefault;

    axios
      .get("/projects/" + this.project_id)
      .then(res => {
        console.log("Entró al axios");
        this.project = res.data;
        const comentarios  =  this.project.comments;
        var usuarios = comentarios.map(a => a.user);
        var unicos = {};
        var distinct = [];

        usuarios = usuarios.filter(Boolean);

        for( let i in usuarios ){
          if( typeof(unicos[usuarios[i].id]) == "undefined"){
            distinct.push(usuarios[i]);
          }
          unicos[usuarios[i].id] = 0;
        }
        this.participantes = distinct;

        var listaObjetos = [{name: "Población de Chile", value: 1234567890, items:[]}];

        if(this.participantes.length>0){

          var region = [];
          
          for (var i = 0; i < this.participantes.length; i++) {
            var p = this.participantes[i].region;
            if(p==null){
              p="No definido.";
            }
            if(!this.lugarEsta(p,region)){
              region.push(p);
            };
          };

          for (var i = 0; i<region.length; i++){
            var aux = new Object();
            aux.name = region[i];
            aux.value = this.contarVecesEnQueApareceRegion(region[i],this.participantes);

            (listaObjetos[0].items).push(aux);

          };

        }else{
         listaObjetos = this.localDataSourceDefault; 
        };

        this.localDataSource = listaObjetos;

        console.log(this.localDataSource);

        this.loading = false;
    })
    .catch(() => {
    console.log("FAIL");
    });
  },
};
</script>

<style>
#vueapp {
  display: block;
  width: 100%;
  overflow: hidden;
  min-height: 80px;
  box-sizing: border-box;
  padding: 30px;
}
.example-wrapper {
  min-height: 280px;
  align-content: flex-start;
}
.example-wrapper p,
.example-col p {
  margin: 0 0 10px;
}
.example-wrapper p:first-child,
.example-col p:first-child {
  margin-top: 0;
}
.example-col {
  display: inline-block;
  vertical-align: top;
  padding-right: 20px;
  padding-bottom: 20px;
}
.example-config {
  margin: 0 0 20px;
  padding: 20px;
  background-color: rgba(0, 0, 0, 0.03);
  border: 1px solid rgba(0, 0, 0, 0.08);
}
.event-log {
  margin: 0;
  padding: 0;
  max-height: 100px;
  overflow-y: auto;
  list-style-type: none;
  border: 1px solid rgba(0, 0, 0, 0.08);
  background-color: #fff;
}
.event-log li {
  margin: 0;
  padding: 0.3em;
  line-height: 1.2em;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}
.event-log li:last-child {
  margin-bottom: -1px;
}
</style>