<template>
    <div :class="mode==='dark'?'dark':'light'">
        <div class="container">
            <h3 class="mt-3 mb-2">{{ $t('tematica_video.leyes') }}</h3>
            <p class="mb-4">{{ $t('tematica_video.parrafo') }}</p>
            <div id="flashContent">
                <object classid="clsid:166B1BCA-3F9C-11CF-8075-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,0,0" width="800" height="650">
                    <param name="src" value="/img/index.swf" />
                    <param name="bgcolor" value="#FFFFFF">
                    <embed src="/img/index.swf" pluginspage="http://www.macromedia.com/shockwave/download/" width="800" height="650"></embed>
                </object>
            </div>
        </div>
    </div>
    
</template>

<script>
    export default {
        name: "TematicVideo",
        props: {
            mode: String
        },
        data() {
            return {
                
            }
        },
        mounted() {
            if (this.$store.getters.modo_oscuro == "dark") {
                this.mode = "dark"
            } else if (!this.mode) {
                this.mode = "light"
            }
        },
    }
</script>

<style media="screen" scoped>
    html, body { height:100%; background-color: #ffffff;}
    body { margin:0; padding:0; overflow:hidden; }
    #flashContent { width:100%; height:100%; }
</style>