<template>
    <div class="row">
        <div class="col-xl-12 mb-4 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <div ref="chart" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Services from '../../services/services';
import Highcharts from 'highcharts';
/* import HighchartsVue from 'highcharts-vue'; */
export default {
    name: 'dashboard-component',
    data() {
        return {
            chartOptions: {
                chart: {
                    type: 'column'
                },
                title: {
                    align: 'left',
                    text: 'Reporte por candidato 2024'
                },
                subtitle: {
                    align: 'left',
                    /* text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>' */
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    
                    title: {
                         text: 'Total porcentaje'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        pointWidth: 60, // Ajusta el ancho de las columnas según lo necesites
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            /* enabled: true, */
                            useHTML: true,
                            /* format: '{point.y}' */
                            formatter: function() {
                                return '<div style="display: flex; align-items: center;"><img src="' + this.point.logo + '" style="width: 35px; height: 35px; margin-right: 5px;"> ' + this.y + '</div>';
                            }
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                   /*  pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> del total<br/>' */
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> del total<br/>'
                },
                /* colors: ['#FF5733', '#FFD700', '#32CD32', '#4169E1', '#FF69B4'], */
                series: [
                    {
                        name: 'Electoral',
                        colorByPoint: true,
                        data: []
                    }
                ],
                drilldown: {
                    breadcrumbs: {
                        position: {
                            align: 'right'
                        }
                    },

                }
            }
        }

    },
    async created() {
        this.reportePartidoPolTotal()
    },
 /*    mounted() {
        Highcharts.chart(this.$refs.chart, this.chartOptions);
    }, */
    methods: {
        async reportePartidoPolTotal() {
            try {
                const result = await Services.getAll('dashboard/polito-voto-total');
                console.log(result)
                const seriesData = result.map(item => ({
                    name: item.nombre,
                    y: parseInt(item.suma),
                    color: item.color,
                    drilldown: item.nombre,
                    logo: item.logo
                }));
                this.chartOptions.series[0].data = seriesData;
                Highcharts.chart(this.$refs.chart, this.chartOptions);
            } catch (error) {
                return error;
            }
        },
    }
}
</script>

<style scoped></style>