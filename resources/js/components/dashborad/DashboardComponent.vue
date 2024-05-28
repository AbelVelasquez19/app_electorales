<template>
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Pais</label>
                                    <select class="form-select" v-model="pais_id" @change="getPaisItem" disabled>
                                        <option value="">--seleccionar--</option>
                                        <option v-for="item in pais" :key="item.id" :value="item.id">{{ item.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Provincia</label>
                                    <select class="form-select" v-model="departaments_id" @change="getDepartamentItem">
                                        <option value="null" disabled selected>--seleccionar--</option>
                                        <option value="0">Todo</option>
                                        <option v-for="item in departaments" :key="item.id" :value="item.id">
                                            {{ item.nombre }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Distrito</label>
                                    <select class="form-select" v-model="provinces_id" @change="getProvincesItem">
                                        <option value="null" disabled selected>--seleccionar--</option>
                                        <option value="0">Todo</option>
                                        <option v-for="item in provinces" :key="item.id" :value="item.id">{{ item.nombre
                                            }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Corregimiento</label>
                                    <select class="form-select" v-model="districts_id" @change="getDistrictItems()">
                                        <option value="null" disabled selected>--seleccionar--</option>
                                        <option value="0">Todo</option>
                                        <option v-for="item in districts" :key="item.id" :value="item.id">{{ item.nombre
                                            }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 mb-4 col-lg-12 col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row" style="position: relative;">
                                <img :src="imgFondoTodo" alt="Fondo del reporte total resumen"
                                    style="height: 300px !important;">
                                <div class="card-text row">
                                    <div
                                        class="col-md-3 d-flex justify-content-center align-items-center flex-column page-link">
                                        <strong>TODOS</strong><br>
                                        <span>Fórmula Presidencial</span>
                                    </div>
                                    <div
                                        class="col-md-3 d-flex justify-content-center align-items-center flex-column page-link">
                                        <strong>{{ totalElectoralHabiles }}</strong> <br>
                                        <span>Electorales Hábiles</span>
                                    </div>
                                    <div
                                        class="col-md-2 d-flex justify-content-center align-items-center flex-column page-link">
                                        <strong>{{ totalParticipacionCiudadana }}</strong> <br>
                                        <span>Participación Ciudadana</span>
                                    </div>
                                    <div
                                        class="col-md-2 d-flex justify-content-center align-items-center flex-column page-link">
                                        <strong>{{ porcentajeParticipacionCiudadana }}%</strong> <br>
                                        <span>(%) Participacion Ciudadana</span>
                                    </div>
                                    <div class="col-md-2 d-flex justify-content-center align-items-center flex-column page-link"
                                        style="background: rgb(13 100 175);">
                                        <strong>{{ porcentajeActasProcesada }}%</strong> <br>
                                        <span>Actas Procesadas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-4 col-lg-4 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <l-map :zoom.sync="zoom" :options="mapOptions" :center="center" :bounds="bounds"
                            :min-zoom="minZoom" :max-zoom="maxZoom" style="width: 100%; height: 424px">
                            <l-control-layers :position="layersPosition" :collapsed="false" :sort-layers="true" />
                            <l-tile-layer v-for="tileProvider in tileProviders" :key="tileProvider.name"
                                :name="tileProvider.name" :visible="tileProvider.visible" :url="tileProvider.url"
                                :attribution="tileProvider.attribution" :token="token" layer-type="base" />
                            <l-control-zoom :position="zoomPosition" />
                            <l-control-attribution :position="attributionPosition" :prefix="attributionPrefix" />
                            <l-control-scale :imperial="imperial" />
                            <l-marker v-for="marker in markers" :key="marker.id" :visible="marker.visible"
                                :draggable="marker.draggable" :lat-lng.sync="marker.position"
                                :icon="getMarkerIcon(marker.color)" @click="alert(marker)">
                                <l-popup :content="marker.tooltip" />
                                <l-tooltip :content="marker.tooltip" />
                            </l-marker>
                        </l-map>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gy-3">
                        <div ref="chart" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5>Total de Votos</h5>
                            <table class="table-bordered table">
                                <thead>
                                    <tr>
                                        <th colspan="2">Organizaciones Políticas</th>
                                        <th>Total</th>
                                        <th>%Validos</th>
                                        <th>%Emitidos</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr v-for="(item, index) in totalVotos" :key="index">
                                        <td><img :src="item.logo" width="30px" height="30px"></td>
                                        <td>{{ item.nombre }}</td>
                                        <td>{{ item.suma }}</td>
                                        <td>
                                            <span v-if="item.id == 4 || item.id == 5">
                                                0 %
                                            </span>

                                            <span v-else>
                                                {{ calcularValidos(item.suma, item.suma_excluyendo_4_5) }}%

                                            </span>

                                        </td>
                                        <td>{{ calcularEmitido(item.suma, item.suma_total) }}%</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Total votos emitidos</td>
                                        <td>{{ totalSuma }}</td>
                                        <td>{{ calcularTotalPorcentajeValidos() }} %</td>
                                        <td>{{ calcularTotalPorcentajeEmitido() }} %</td>
                                        <!-- <td>{{ calcularTotalPorcentajeValidos }}</td>
                                        <td>{{ calcularTotalPorcentajeEmitido }}</td> -->


                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div ref="chartEstadoActas" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div ref="chartDistribucionVotos" style="height: 250px;"></div>
                                </div>
                            </div>
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
import { latLngBounds } from 'leaflet';
import {
    LMap,
    LTileLayer,
    LMarker,
    LPolyline,
    LLayerGroup,
    LTooltip,
    LPopup,
    LControlZoom,
    LControlAttribution,
    LControlScale,
    LControlLayers
} from 'vue2-leaflet';

const tileProviders = [
    {
        name: 'OpenStreetMap',
        visible: true,
        attribution:
            '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    },
    {
        name: 'OpenTopoMap',
        visible: false,
        url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
        attribution:
            'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
    },
];

export default {
    name: 'dashboard-component',
    components: {
        LMap,
        LTileLayer,
        LMarker,
        LPolyline,
        LLayerGroup,
        LTooltip,
        LPopup,
        LControlZoom,
        LControlAttribution,
        LControlScale,
        LControlLayers,
    },
    props: {
        imgRed: {
            type: String,
            default: ''
        },
        imgBlue: {
            type: String,
            default: ''
        },
        imgYelow: {
            type: String,
            default: ''
        },
        imgFondoTodo: {
            type: String,
            default: ''
        }
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
            chartOptionsEstadoActas: {
                chart: {
                    type: 'pie'
                },
                title: {
                    align: 'left',
                    text: 'Estado de Actas'
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
                    enabled: true
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    },
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: [{
                            enabled: false,
                            distance: 20
                        },
                        {
                            enabled: true,
                            distance: -40,
                            format: '{point.percentage:.1f}%',
                            style: {
                                fontSize: '1em',
                                textOutline: 'none',
                                opacity: 0.7
                            },
                            filter: {
                                operator: '>',
                                property: 'percentage',
                                value: 10
                            }
                        }]
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    /*  pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> del total<br/>' */
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> del total<br/>'
                },
                colors: ['#FF5733', '#FFD700'],
                series: [
                    {
                        name: 'Percentage',
                        colorByPoint: true,
                        data: [

                        ]
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
            chartOptionsDistribucionVotos: {
                chart: {
                    type: 'pie'
                },
                title: {
                    align: 'left',
                    text: 'Distribución de Votos'
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
                    enabled: true
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    },
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: [{
                            enabled: false,
                            distance: 20
                        },
                        {
                            enabled: true,
                            distance: -40,
                            format: '{point.percentage:.1f}%',
                            style: {
                                fontSize: '1em',
                                textOutline: 'none',
                                opacity: 0.7
                            },
                            filter: {
                                operator: '>',
                                property: 'percentage',
                                value: 10
                            }
                        }]
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    /*  pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> del total<br/>' */
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> del total<br/>'
                },
                colors: ['#FF5733', '#FFD700', '#FF69B4'],
                series: [
                    {
                        name: 'Percentage',
                        colorByPoint: true,
                        data: [

                        ]
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
            pais_id: 25,
            departaments: {},
            departaments_id: null,
            provinces: {},
            provinces_id: null,
            districts: {},
            districts_id: null,
            totalVotos: [],
            electoralesHbailes: 25287954,
            center: [9.367772770859636, -82.86987304687501],
            opacity: 0.6,
            token: 'your token if using mapbox',
            mapOptions: {
                zoomControl: false,
                attributionControl: false,
                zoomSnap: true,
            },
            zoom: 3,
            minZoom: 1,
            maxZoom: 20,
            zoomPosition: 'topleft',
            attributionPosition: 'bottomright',
            layersPosition: 'topright',
            attributionPrefix: 'Vue2Leaflet',
            imperial: false,
            Positions: ['topleft', 'topright', 'bottomleft', 'bottomright'],
            tileProviders: tileProviders,
            markers: [],
            bounds: latLngBounds(
                { lat: 9.367772770859636, lng: -82.86987304687501 },
                { lat: 8.026594842489562, lng: -78.0413818359375 }
            ),

            totalElectoralHabiles: 25287.954,
            totalParticipacionCiudadana: 0,
            porcentajeParticipacionCiudadana: 0,
            porcentajeActasProcesada: 0

        }

    },
    async created() {
        this.reportePartidoPolTotal()
    },
    mounted() {
        this.getPais()
        this.getPaisItem();
        this.getCentroVotacion();
        this.reporteEstadoActas();
        this.reporteDistribucionVotos();
        this.reporteTotalVotos();
        // this.getTotalSumaPanelHabiles();
    },
    computed: {
        totalSuma: function () {
            let sumaTotal = 0;
            for (let key in this.totalVotos) {
                if (this.totalVotos.hasOwnProperty(key)) {
                    sumaTotal += parseFloat(this.totalVotos[key].suma);
                }
            }
            return sumaTotal;
        }




    },
    methods: {

        calcularValidos(suma, suma_excluyendo_4_5) {
            const porcentaje_validos = (suma / suma_excluyendo_4_5) * 100;
            const porcentaje_valido = porcentaje_validos.toFixed(2);

            return porcentaje_valido;
        },

        calcularEmitido(suma, suma_total) {
            const porcentaje_emitidos = (suma / suma_total) * 100;
            const porcentaje_emitido = porcentaje_emitidos.toFixed(2);

            return porcentaje_emitido;
        },

        async reportePartidoPolTotal() {
            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {
                const result = await Services.addNewInfo('dashboard/polito-voto-total', obj);

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
        async reporteEstadoActas() {
            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {
                const result = await Services.addNewInfo('dashboard/estado-acta', obj);
                console.log(result)
                const seriesData = result.result[0].map(item => ({
                    name: item.nombre,
                    y: parseInt(item.total),
                }));
                this.chartOptionsEstadoActas.series[0].data = seriesData;
                Highcharts.chart(this.$refs.chartEstadoActas, this.chartOptionsEstadoActas);
            } catch (error) {
                return error;
            }
        },
        async reporteDistribucionVotos() {
            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {
                const result = await Services.addNewInfo('dashboard/distribucion-votos', obj);
                const seriesData = result.result[0].map(item => ({
                    name: item.nombre,
                    y: parseInt(item.total),
                }));
                this.chartOptionsDistribucionVotos.series[0].data = seriesData;
                Highcharts.chart(this.$refs.chartDistribucionVotos, this.chartOptionsDistribucionVotos);
            } catch (error) {
                return error;
            }
        },
        async reporteTotalVotos() {
            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {
                const result = await Services.addNewInfo('dashboard/total-votos', obj);

                console.log('TOTAL VOTOSSS', result);

                this.getTotalSumaPanelHabiles();
                this.getTotalSumaParticipacion();

                const result2 = await Services.addNewInfo('dashboard/total-votos-panel', obj);
                console.log('NUEVO', result2);



                this.totalVotos = result.result[0];
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
            this.reportePartidoPolTotal();
            this.reporteEstadoActas();
            this.reporteDistribucionVotos();
            this.reporteTotalVotos();
            this.getTotalSumaPanelHabiles();

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
            this.reportePartidoPolTotal();
            this.reporteEstadoActas();
            this.reporteDistribucionVotos();
            this.reporteTotalVotos();
            this.getTotalSumaPanelHabiles();
        },

        getDistrictItems() {
            this.reportePartidoPolTotal();
            this.reporteEstadoActas();
            this.reporteDistribucionVotos();
            this.reporteTotalVotos();
            this.getTotalSumaPanelHabiles();
            this.getTotalSumaParticipacion();

        },
        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/district', province_id);
                this.districts = result
            } catch (error) {
                return error;
            }
        },

        addMarker: function () {
            const newMarker = {
                position: { lat: 50.5505, lng: -0.09 },
                draggable: true,
                visible: true,
            };
            this.markers.push(newMarker);
        },
        removeMarker: function (index) {
            this.markers.splice(index, 1);
        },
        getMarkerIcon(color) {
            // Define los iconos personalizados para cada color
            const iconos = {
                red: new L.Icon({
                    iconUrl: this.imgRed, // URL de la imagen del icono rojo
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                }),
                blue: new L.Icon({
                    iconUrl: this.imgBlue, // URL de la imagen del icono rojo
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                }),
                yelaow: new L.Icon({
                    iconUrl: this.imgYelow, // URL de la imagen del icono rojo
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                }),
            }

            return iconos[color] || iconos.red;
        },

        async getCentroVotacion() {
            try {
                const result = await Services.getAll('mapas/centro-votacion');
                /* console.lo this.getCentroVotacion(result) */
                if (result.status) {
                    // Si la solicitud fue exitosa
                    this.markers = result.data.map(item => ({
                        id: item.id.toString(), // Convierte el ID a string si es necesario
                        position: { lat: parseFloat(item.latitud), lng: parseFloat(item.longitud) },
                        tooltip: `${item.nombre} ${item.porcentaje_mesa_cerrado}%`,
                        draggable: false,
                        visible: true,
                        color: item.porcentaje_mesa_cerrado == 0 ? 'red' : (item.porcentaje_mesa_cerrado >= 100 ? 'yelaow' : 'blue'), // Define el color de acuerdo a tus requerimientos
                        title: `${item.porcentaje_mesa_cerrado}%`
                    }));
                } else {
                    console.error('Error obteniendo los datos de los centros de votación:', result);
                }
            } catch (error) {
                return error;
            }
        },


        async getTotalSumaPanelHabiles() {


            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {

                const result = await Services.addNewInfo('dashboard/total-votos-panel', obj);
                console.log('NUEVO2', result);

                // this.totalElectoralHabiles = result.result[0].suma;

            } catch (error) {
                return error;
            }
        },

        async getTotalSumaParticipacion() {


            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {

                const result = await Services.addNewInfo('dashboard/total-votos-panel', obj);
                console.log('NUEVO2', result);

                this.totalParticipacionCiudadana = result.result[0].suma;
                this.calculoPorcentajeParticipacion();
                this.getTotalCantidadActas();

            } catch (error) {
                return error;
            }
        }
        ,
        calculoPorcentajeParticipacion() {

            if (this.totalElectoralHabiles === 0) {
                return 0; // Evitar división por cero
            }
            const porcentaje = (this.totalParticipacionCiudadana / this.totalElectoralHabiles) * 100;
            this.porcentajeParticipacionCiudadana = porcentaje.toFixed(2); // Redondear a 2 decimales

        },
        async getTotalCantidadActas() {


            let obj = {
                departaments_id: this.departaments_id,
                provinces_id: this.provinces_id,
                districts_id: this.districts_id,
            }
            try {

                const result = await Services.addNewInfo('dashboard/total-actas', obj);
                const result_procesadas = await Services.addNewInfo('dashboard/total-actas-procesada', obj);


                console.log('TOTAL ACTAS', result);
                console.log('TOTAL ACTAS PROCESADAS', result_procesadas);

                const total_actas = result.result[0].cantidad_actas;
                const total_actas_procesadas = result_procesadas.result[0].cantidad_actas;


                const porcentaje_actas = (total_actas_procesadas / total_actas) * 100;
                this.porcentajeActasProcesada = porcentaje_actas.toFixed(2);


            } catch (error) {
                return error;
            }
        },
        calcularTotalPorcentajeValidos() {
            let totalPorcentajeValido = 0;
            for (let item of this.totalVotos) {

                if (item.id != 4 && item.id != 5) {
                    totalPorcentajeValido += parseFloat(this.calcularValidos(item.suma, item.suma_excluyendo_4_5));
                }
            }
            return totalPorcentajeValido.toFixed(2);
        },
        calcularTotalPorcentajeEmitido() {
            let totalPorcentajeEmitido = 0;
            for (let item of this.totalVotos) {

                totalPorcentajeEmitido += parseFloat(this.calcularEmitido(item.suma, item.suma_total));
            }
            return totalPorcentajeEmitido.toFixed(2)


            // return 0;
        },


    }
}
</script>

<style scoped>
.hello {
    width: 100%;
    height: 500px;
}

.card-text {
    position: absolute;
    background: rgba(17, 16, 16, 0.7);
    bottom: 5px;
    color: #fff;
    left: 37px;
    width: 94%;
    text-align: center;
    padding: 5px 0px;
}
</style>