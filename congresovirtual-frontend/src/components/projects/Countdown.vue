<template id="countdown-template">
    <div class="countdown row">
        <div class="block col-4" style="padding:0px">
            <a class="digit">{{ days | two_digits }}</a>
            <br>
            <a class="text">{{ $t('componentes.cuenta_regresiva.dias') }}</a>
        </div>
        <div class="block col-4" style="padding:0px">
            <a class="digit">{{ hours | two_digits }}</a>
            <br>
            <a class="text">{{ $t('componentes.cuenta_regresiva.horas') }}</a>
        </div>
        <div class="block col-4" style="padding:0px">
            <a class="digit">{{ minutes | two_digits }}</a>
            <br>
            <a class="text">{{ $t('componentes.cuenta_regresiva.minutos') }}</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Countdown',
        template: '#countdown-template',
        mounted() {
            window.setInterval(() => {
                this.now = Math.trunc((new Date()).getTime() / 1000);
            },1000);
        },
        props: {
            date: {
                type: String
            }
        },
        data() {
            return {
                now: Math.trunc((new Date()).getTime() / 1000)
            }
        },
        computed: {
            dateInMilliseconds() {
                return Math.trunc(Date.parse(this.date) / 1000)
            },
            seconds() {
                return (this.dateInMilliseconds - this.now) % 60;
            },
            minutes() {
                return Math.trunc((this.dateInMilliseconds - this.now) / 60) % 60;
            },
            hours() {
                return Math.trunc((this.dateInMilliseconds - this.now) / 60 / 60) % 24;
            },
            days() {
                return Math.trunc((this.dateInMilliseconds - this.now) / 60 / 60 / 24);
            }
        },
        filters:{
            two_digits: (value) => {
                if (value < 0) {
                    return '00';
                }
                if (value.toString().length <= 1) {
                    return `0${value}`;
                }
                return value;
            }
        }
    }
</script>
