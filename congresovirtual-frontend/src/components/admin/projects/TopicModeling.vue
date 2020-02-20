<template>
    <div class="col-12">
        <div v-if="loaded" v-html="topic_model_html_content" id="topic_model">
            {{ topic_model_html_content }}
        </div>
        <div>
            <div id="ldavis"></div>
        </div>
    </div>
</template>

<script>
    import axios from "../../../backend/axios";

    export default {
        name: "TopicModeling",
        props:{
          project_id: Number
        },
        mounted() {
            this.getTopicModeling()
        },
        methods:{
            getTopicModeling: function () {
                axios
                    .get("/topicmodel",{
                        params:{
                            project_id: this.project_id
                        }
                    })
                    .then(res => {
                        console.log(res.data);
                        this.topic_model_html_content = res.data;
                        this.loaded = true;
                        this.defer();
                    })
                    .catch((e) => console.error("FAIL: " + JSON.stringify(e)));
            },
            LDAvis_load_lib: function (url, callback) {
                var s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onreadystatechange = s.onload = callback;
                s.onerror = function(){console.warn("failed to load library " + url);};
                document.getElementsByTagName("head")[0].appendChild(s);
            },
            defer: function () {
                if (document.getElementById("topic_model")){
                    if(document.getElementById("topic_model").getElementsByTagName("script")[0]){
                        this.loadTopicModel();
                    } else
                        setTimeout(() => { this.defer() }, 50);
                }
                else
                    setTimeout(() => { this.defer() }, 50);
            },
            loadTopicModel: function () {
                eval(document.getElementById("topic_model").getElementsByTagName("script")[0].innerHTML);
            }
    },
        data () {
            return {
                topic_model_html_content: String,
                loaded: false
            }
        }
    }
</script>

<style scoped>

</style>