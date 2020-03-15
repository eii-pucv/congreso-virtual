<template>
    <div class="col-12 px-0 vld-parent" style="min-height: 550px;">
        <div v-if="loadTopicModel" class="vld-parent" style="height: 550px;">
            <Loading
                    :active.sync="loadTopicModel"
                    :is-full-page="fullPage"
                    :height="128"
                    :color="'#000000'"
            ></Loading>
        </div>
        <div v-if="!loadTopicModel && !error" v-html="topicModelHtmlContent" id="topic_model">
            {{ topicModelHtmlContent }}
        </div>
        <div v-else-if="error">
            <h6 class="text-center my-20">{{ $t('administrador.componentes.analitica.topic_model_no_carga') }}</h6>
        </div>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import axios from '../../../backend/axios';

    export default {
        name: 'TopicModeL',
        components: {
            Loading
        },
        props: {
            project_id: Number
        },
        data() {
            return {
                topicModelHtmlContent: String,
                error: false,
                loadTopicModel: true,
                fullPage: false
            }
        },
        mounted() {
            this.getTopicModel();
        },
        methods:{
            getTopicModel() {
                axios
                    .get('/topicmodel', {
                        params:{
                            project_id: this.project_id
                        }
                    })
                    .then(res => {
                        this.topicModelHtmlContent = res.data;
                        this.defer();
                    })
                    .catch(() => {
                        this.error = true;
                    })
                    .finally(() => {
                        this.loadTopicModel = false;
                    });
            },
            LDAvis_load_lib(url, callback) {
                var s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onreadystatechange = s.onload = callback;
                s.onerror = function() {
                    console.warn("failed to load library " + url);
                };
                document.getElementsByTagName("head")[0].appendChild(s);
            },
            defer() {
                if (document.getElementById("topic_model")) {
                    if(document.getElementById("topic_model").getElementsByTagName("script")[0]) {
                        eval(document.getElementById("topic_model").getElementsByTagName("script")[0].innerHTML);
                    } else {
                        setTimeout(() => {
                            this.defer();
                        }, 50);
                    }
                } else {
                    setTimeout(() => {
                        this.defer();
                    }, 50);
                }
            }
        }
    }
</script>