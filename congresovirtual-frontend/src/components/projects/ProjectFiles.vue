<template>
  <div class="container">
    <div>
      <div v-if="!loadFiles">
        <p>{{ $t('proyecto.componentes.documentos.texto') }}</p>
        <ul v-if="files" class="list-unstyled mt-20">
            <li v-for="file in files" :key="file.id" class="card">
                <div class="card-body">
                    <p>{{ file.original_name }}<small>{{ file.updated_at }}</small></p>
                    <button @click="download(file)" class="btn btn-outline-primary mt-10">{{ $t('proyecto.componentes.documentos.descargar') }}</button>
                </div>
            </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
    import axios from '../../backend/axios';

    export default {
        name: 'ProjectDocuments',
        props: {
            project_id: Number
        },
        data() {
            return {
                files: [],
                loadFiles: true
            };
        },
        created() {
            axios
                .get('/projects/' + this.project_id + '/files')
                .then(res => {
                    this.files = res.data;
                })
                .finally(() => {
                    this.loadFiles = false;
                });
        },
        methods: {
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
        }
    }
</script>