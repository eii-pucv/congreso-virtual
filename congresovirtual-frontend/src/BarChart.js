import {Bar, mixins} from 'vue-chartjs'
export default {
    extends: Bar,
    props: ["data", "options"],

    mounted() {
        Chart.defaults.global.legend.display = false;
        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        // this.chartData is created in the mixin.
        // If you want to pass options please create a local options object
        this.renderChart(this.data, this.options);
    }
}