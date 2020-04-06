<template>
    <div v-if="loadWordCloud" class="vld-parent" style="height: 300px;">
        <loading
                :active.sync="loadWordCloud"
                :is-full-page="fullPage"
                :height="128"
                :color="color"
                :background-color="backgroundColor"
        ></loading>
    </div>
    <div v-else-if="!loadWordCloud && !error" id="div-wordcloud">
        <wordcloud
                :data="wordCloudData"
                nameKey="word"
                valueKey="freq"
                :rotate="rotate"
                :margin="margin"
                :showTooltip="false"
        ></wordcloud>
    </div>
    <div v-else-if="error">
        <h6 class="text-center my-20">{{ $t('proyecto.componentes.nube.no_carga') }}</h6>
    </div>
</template>

<script>
    import axios from '../../backend/axios';
    //import wordcloud from 'vue-wordcloud';
    import wordcloud from 'vue-wordcloud-tooltip-fix';
    import Loading from 'vue-loading-overlay';

    export default {
        name: 'WordCloud',
        components: {
            wordcloud,
            Loading
        },
        props: {
            project_id: Number,
            mode: String
        },
        data() {
            return {
                /* wordCloudData is a object array with format:
                    [
                        {
                            "word": 'foo',
                            "freq": 54
                        },
                        {...}
                   ]
                */
                wordCloudData: [],
                rotate: {
                    from: 0,
                    to: 0,
                    numOfOrientation: 1
                },
                margin: {
                    top: 15,
                    right: 8,
                    bottom: 15,
                    left: 8
                },
                error: false,
                loadWordCloud: true,
                fullPage: false,
                color: '#000000',
                backgroundColor: 'transparent'
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.color = '#FFFFFF';
            } else {
                this.color = '#000000';
            }

            axios
                .get('/wordcloud', {
                    params: {
                        'project_id': this.project_id,
                        'max_words': 100
                    }
                })
                .then(res => {
                    if(res.data && res.data.length > 0) {
                        this.wordCloudData = res.data.slice(0, 100);
                    } else {
                        this.error = true;
                    }
                })
                .catch(() => {
                    this.error = true;
                })
                .finally(() => {
                    this.loadWordCloud = false;
                });
        }
    };
</script>

<style>
    wordcloud {
        object-fit: cover;
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 1px;
        width: 100%;
        height: 100%;
        position: absolute;
    }

    #div-wordcloud {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
</style>