<template>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card h-100 card-pers">
                <div class="card-body card-body-pers">
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-center align-items-center">
                            <img :src="imgLogo" alt="" style="width: 50%;">
                        </div>
                        <div class="col-md-2 col-right-blue">
                            ELECCIÓN GENERAL 2024
                        </div>
                        <div class="col-md-2 col-right-blue" style="font-size: 1.5rem;">
                            PRESIDENTE
                        </div>
                        <div class="col-md-2 col-right-blue text-center">
                            RESULTADOS EXTRAOFICIALES
                        </div>
                        <div class="col-md-3 color-blue text-center">
                            {{ formattedTime }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 mb-2 col-lg-12 col-12">
            <div class="card h-100">
                <div class="card-body card-body-pers card-pers-two">
                    <div class="row gy-3">
                        <div class="col-md-7 text-center">
                            {{ formattedDate }}
                        </div>
                        <div class="col-md-5 text-end">
                            Despues de 2 Horas y 0 minutos del cierre de votación
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 mb-1 col-lg-12 col-12">
            <div class="row">
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <div class="targeta">
                        <span>Padrón Electoral</span>
                        <span>3.004.08</span>
                    </div>
                    <div class="targeta">
                        <span>Electorales en Mesas Escrutadas</span>
                        <span>81,421</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="targeta">
                        <span>Votos Emitidos</span>
                        <span>3.004.08 &nbsp;&nbsp; 100.00%</span>
                    </div>
                    <div class="targeta">
                        <span>Validos</span>
                        <span>59,006 &nbsp;&nbsp; 96.59%</span>
                    </div>
                    <div class="targeta">
                        <span>Blancos</span>
                        <span>894 &nbsp;&nbsp; 1.46%</span>
                    </div>
                    <div class="targeta">
                        <span>Nulos</span>
                        <span>1.187 &nbsp;&nbsp; 1.94%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 mb-1 col-lg-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <div ref="chart" style="height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Services from '../../services/services';
import Highcharts from 'highcharts';
import Echo from "laravel-echo";
export default {
    props: {
        imgLogo: {
            type: String,
            default: '',
        },
        rutaReporte: {
            type: String,
            default: '',
        },
    },
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
                            formatter: function () {
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
            },
            currentTime: new Date(),
            currentDate: new Date()
        }
    },
    computed: {
        formattedTime() {
            let hours = this.currentTime.getHours();
            let minutes = this.currentTime.getMinutes();
            let seconds = this.currentTime.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            return `${hours}:${minutes}:${seconds} ${ampm}`;
        },
        formattedDate() {
            const days = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
            const months = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            const day = this.currentDate.getDate();
            const month = months[this.currentDate.getMonth()];
            const year = this.currentDate.getFullYear();
            return `${day < 10 ? '0' + day : day} DE ${month} DE ${year}`;
        }
    },
    mounted() {
        this.reportePartidoPolTotal();
        window.Echo.channel('reporte-columna')
            .listen('ReporteStatusChangedEvent', (e) => {
                console.log("OMG realtieme");
                console.log(e);
                console.log('new-balance', e);

                const seriesData = e.data.map(item => ({
                    name: item.nombre,
                    y: parseInt(item.suma),
                    color: item.color,
                    drilldown: item.nombre,
                    logo: item.logo
                }));
                this.chartOptions.series[0].data = seriesData;
                Highcharts.chart(this.$refs.chart, this.chartOptions);
            }
            );

        this.updateTime();
        this.interval = setInterval(this.updateTime, 1000);
        this.currentDate = new Date();
    },
    methods: {
        updateTime() {
            this.currentTime = new Date();
        },
        async reportePartidoPolTotal() {
            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {
                const result = await Services.addNewInfo(this.rutaReporte, obj);
                console.log(result.result[0])
                const seriesData = result.result[0].map(item => ({
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
    },
    beforeDestroy() {
        clearInterval(this.interval);
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap');

.card-pers {
    border: 1px solid #0e77cd;
}

.card-pers-two {
    background: #0e77cd;
    color: #fff;
    margin-top: -14px !important;
    height: 24px !important;
    border-radius: 2px !important;
}

.card-body-pers {
    padding: 0px !important;
}

.col-right-blue {
    border-right: 1px solid #0e77cd;
    color: #0e77cd;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Oswald", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
}

.color-blue {
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: red;
    justify-content: center;
    font-family: "Oswald", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
}

.targeta {
    background: #0e77cd;
    padding: 2px 5px;
    border-radius: 5px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    margin-bottom: 2px;
    font-weight: 700;
}
</style>