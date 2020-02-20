<template>
  <div class="container">
    <div>
      <div v-if="!loading">
        <p>{{ $t('proyecto.componentes.documentos.texto') }}</p>
        <ul v-if="documents" class="list-unstyled mt-20">
            <li v-for="document in documents.slice(n,this.index)" :key="document.id" class="card">
                <div class="card-body" v-if="document">
                    <p>{{document.original_name}}<small>{{document.updated_at}}</small></p>
                    <button v-on:click="download(document)" class="btn btn-outline-primary mt-10">{{ $t('proyecto.componentes.documentos.descargar') }}</button>
                </div>
            </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
    import axios from "../../backend/axios";
    export default {
        name: "ProjectDocuments",
        props: {
            project_id: Number,
            documents: Array,
        },
        data() {
            return {
                document: Object,
                loading: true,
            };
        },
        methods: {
            /*
                Descargar archivo
            */
            download(file) {
                axios({
                method:'get',
                url: '/storage/projects/' + this.project_id + '/' + file.stored_name + '.' + file.extension,
                responseType: 'blob'
                })
                .then(function(response) {
                    let type = response.headers['content-type'];
                    let blob = new Blob([response.data], { type: type });
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = file.original_name + '.' + file.extension;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            },
        },
        created(){
            axios
                .get("/projects/" + this.project_id + "/files")
                .then(res => {
                this.documents = res.data;
                this.loading = false;
                })
                .catch(() => console.error("FAIL"));
        },
    }
</script>