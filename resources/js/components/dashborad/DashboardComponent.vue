<template>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label>Seleccionar Pais</label>
                            <select class="form-select" v-model="pais_id" @change="getPaisItem">
                                <option value="">--seleccionar--</option>
                                <option v-for="item in pais" :key="item.id" :value="item.id">{{ item.nombre }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label>Seleccionar Provincia</label>
                            <select class="form-select" v-model="departaments_id" @change="getDepartamentItem">
                                <option value="">--seleccionar--</option>
                                <option v-for="item in departaments" :key="item.id" :value="item.id">{{ item.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label>Seleccionar Distrito</label>
                            <select class="form-select" v-model="provinces_id" @change="getProvincesItem">
                                <option value="">--seleccionar--</option>
                                <option v-for="item in provinces" :key="item.id" :value="item.id">{{ item.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label>Seleccionar Corregimiento</label>
                            <select class="form-select" v-model="districts_id">
                                <option value="">--seleccionar--</option>
                                <option v-for="item in districts" :key="item.id" :value="item.id">{{ item.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 mb-4 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <div ref="chart" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mb-4 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="hello" ref="chartdiv"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Services from '../../services/services';
import Highcharts from 'highcharts';

import * as am5 from '@amcharts/amcharts5';
import * as am5xy from '@amcharts/amcharts5/xy';
import * as am5map from "@amcharts/amcharts5/map";
import am5geodata_worldLow from "@amcharts/amcharts5-geodata/worldLow";
import am5geodata_region_usa_congressional2022_worldLow from "@amcharts/amcharts5-geodata/region/usa/congressional2022/flLow";
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';

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
            pais: {},
            pais_id: '',
            departaments: {},
            departaments_id: '',
            provinces: {},
            provinces_id: '',
            districts: {},
            districts_id: '',
            chart: null,
            yourGeoJSONData: {}
        }

    },
    async created() {
        this.reportePartidoPolTotal()
    },
    mounted() {
        this.getPais()
       /*  let root = am5.Root.new(this.$refs.chartdiv);

        root.setThemes([am5themes_Animated.new(root)]);

        let chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panY: false,
                layout: root.verticalLayout
            })
        );

        // Define data
        let data = [{
            category: "Research",
            value1: 1000,
            value2: 588
        },
        {
            category: "Marketing",
            value1: 1200,
            value2: 1800
        }, {
            category: "Sales",
            value1: 850,
            value2: 1230
        }
        ];

        // Create Y-axis
        let yAxis = chart.yAxes.push(
            am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {})
            })
        );

        // Create X-Axis
        let xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                renderer: am5xy.AxisRendererX.new(root, {}),
                categoryField: "category"
            })
        );
        xAxis.data.setAll(data);

        // Create series
        let series1 = chart.series.push(
            am5xy.ColumnSeries.new(root, {
                name: "Series",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value1",
                categoryXField: "category"
            })
        );
        series1.data.setAll(data);

        let series2 = chart.series.push(
            am5xy.ColumnSeries.new(root, {
                name: "Series",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value2",
                categoryXField: "category"
            })
        );
        series2.data.setAll(data);

        // Add legend
        let legend = chart.children.push(am5.Legend.new(root, {}));
        legend.data.setAll(chart.series.values);

        // Add cursor
        chart.set("cursor", am5xy.XYCursor.new(root, {}));

        this.root = root; */



        let root = am5.Root.new("chartdiv");
        let chart = root.container.children.push(
        am5map.MapChart.new(root, {})
        );

        let polygonSeries = chart.series.push(
        am5map.MapPolygonSeries.new(root, {
            geoJSON: am5geodata_worldLow
        })
        );

    },
    beforeDestroy() {
        if (this.root) {
        this.root.dispose();
        }
    },
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

        //ubigeo
        async getPais() {
            try {
                const result = await Services.getAll('ubigeus/pais');
                this.pais = result
            } catch (error) {
                return error;
            }
        },

        getPaisItem() {
            this.getDepartamento(this.pais_id);
            console.log(this.pais_id)
        },

        async getDepartamento(pais_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/department', pais_id);
                this.departaments = result
            } catch (error) {
                return error;
            }
        },

        getDepartamentItem() {
            this.getProvinces(this.departaments_id);
        },

        async getProvinces(departaments_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/province', departaments_id);
                this.provinces = result
            } catch (error) {
                return error;
            }
        },

        getProvincesItem() {
            this.getDistrict(this.provinces_id);
        },

        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/district', province_id);
                this.districts = result
            } catch (error) {
                return error;
            }
        },

        //mapa
        createChart() {
            this.chart = create(this.$refs.chartContainer, am5xy.XYChart);
            this.chart.xAxes.push(new am5xy.CategoryAxis());
            this.chart.yAxes.push(new am5xy.ValueAxis());

            // Configura otros aspectos del gráfico según tus necesidades
        },
        loadData() {
            // Aquí cargarías tus datos desde alguna fuente, como una API
            const data = [
                { category: "Category 1", value: 100 },
                { category: "Category 2", value: 200 },
                { category: "Category 3", value: 300 }
            ];

            // Asigna los datos al gráfico
            this.chart.data = data;
        }

    }
}
</script>

<style scoped>
.hello {
  width: 100%;
  height: 500px;
}
</style>