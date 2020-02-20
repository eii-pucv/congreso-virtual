<template>
    <div>
        <ListaArticulos v-for="article in articles" :key="article.id" :article="article"></ListaArticulos>
        <ListaIdeas v-for="idea in ideas" :key="idea.id" idea="idea"></ListaIdeas>
    </div>
</template>

<script>
    import ListaArticulos from "./ProjectArticles";
    import ListaIdeas from "./ProjectIdeas";
    import axios from "../../backend/axios";
    export default {
        name: "TabArticles&Ideas",
        components: {ListaArticulos,ListaIdeas},
        props: {
            project_id: Number
        },
        data() {
            return {
                articles: [],
                ideas: []
            }
        },
        created() {
            if(this.project_id) {
                axios
                    .get("projects/" + this.project_id + "/articles")
                    .then(res => {
                        this.articles = res.data;
                        console.log(this.articles)
                    })
                    .catch(() => console.error("FAIL"));
                axios
                    .get("projects/" + this.project_id + "/ideas")
                    .then(res => {
                        this.ideas = res.data;
                    })
                    .catch(() => console.error("FAIL"));
            }

        }
    }
</script>

<style scoped>

</style>
