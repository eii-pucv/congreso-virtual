<template>
    <label class="toggle ml-10 my-0">
        <input
                type="checkbox"
                :checked="mode ? 'checked' : false"
                @change="changeMode"
        >
        <span class="toggler round"></span>
    </label>
</template>

<script>
    import { dark_mode } from '../data/dark_mode';

    export default {
        data() {
            return {
                mode: false
            }
        },
        methods: {
            changeMode() {
                if(!this.mode) {
                    this.mode = true;
                    dark_mode.estado = "dark";
                    this.$store.commit('setDarkMode');
                } else {
                    this.mode = false;
                    dark_mode.estado = "light";
                    this.$store.commit('setDarkMode');
                }

                if(window.location.href.includes("dark")) {
                    window.location.href = window.location.href.replace("dark", "light");
                } else if (window.location.href.includes("light")) {
                    window.location.href = window.location.href.replace("light", "dark");
                } else {
                    window.location.reload(true);
                }
            }
        },
        mounted() {
            if(window.location.href.includes('dark')) {
                this.mode = true
            }

            if(this.$store.getters.modo_oscuro == 'dark') {
                this.mode = true;
                dark_mode.estado = 'dark';
            }
            this.$store.commit('setDarkMode');
        }
    };
</script>

<style>
    .toggle {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggler {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #15202B;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    .toggler:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background: #FFF;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    input:checked + .toggler {
        background: #2196F3;
    }

    input:focus + .toggler {
        box-shadow: 0 0 2px #2196F3;
    }

    input:checked + .toggler:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .toggler.round {
        border-radius: 34px;
    }

    .toggler.round::before {
        border-radius: 50%;
    }
</style>