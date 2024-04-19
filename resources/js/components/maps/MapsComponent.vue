<template>
    <div>
        <!--  <div>
           
            <h3>List of Markers</h3>
            <button name="button" @click="addMarker">
                Add a marker
            </button>
            <table>
                <tr>
                    <th>Marker</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Tooltip</th>
                    <th>Is Draggable ?</th>
                    <th>Is Visible ?</th>
                    <th>Remove</th>
                </tr>
                <tr v-for="(item, index) in markers" :key="index">
                    <td>{{ 'Marker ' + (index + 1) }}</td>
                    <td>
                        <input v-model.number="item.position.lat" type="number">
                    </td>
                    <td>
                        <input v-model.number="item.position.lng" type="number">
                    </td>
                    <td>
                        <input v-model="item.tooltip" type="text">
                    </td>
                    <td style="text-align: center">
                        <input v-model="item.draggable" type="checkbox">
                    </td>
                    <td style="text-align: center">
                        <input v-model="item.visible" type="checkbox">
                    </td>
                    <td style="text-align: center">
                        <input type="button" value="X" @click="removeMarker(index)">
                    </td>
                </tr>
            </table>
            <hr>
        </div> -->
        <div class="row p-3">
            <label for="">Leyenda</label>
            <div class="col-md-8">
                <span class="badge bg-danger bg-glow">0% de votos</span>
                <span class="badge bg-warning bg-glow">50% de votos</span>
                <span class="badge bg-success bg-glow">100% de votos</span>
            </div>
            <div class="col-md-4 text-end">
                <a class="btn btn-primary text-white" @click="nuevoRegistroCentVotacion()">Registrar Nuevo</a>
            </div>
        </div>
        <l-map :zoom.sync="zoom" :options="mapOptions" :center="center" :bounds="bounds" :min-zoom="minZoom"
            :max-zoom="maxZoom" style="height: 700px; width: 100%">
            <l-control-layers :position="layersPosition" :collapsed="false" :sort-layers="true" />
            <l-tile-layer v-for="tileProvider in tileProviders" :key="tileProvider.name" :name="tileProvider.name"
                :visible="tileProvider.visible" :url="tileProvider.url" :attribution="tileProvider.attribution"
                :token="token" layer-type="base" />
            <l-control-zoom :position="zoomPosition" />
            <l-control-attribution :position="attributionPosition" :prefix="attributionPrefix" />
            <l-control-scale :imperial="imperial" />
            <l-marker v-for="marker in markers" :key="marker.id" :visible="marker.visible" :draggable="marker.draggable"
                :lat-lng.sync="marker.position" :icon="getMarkerIcon(marker.color)" @click="alert(marker)">
                <l-popup :content="marker.tooltip" />
                <l-tooltip :content="marker.tooltip" />
            </l-marker>
        </l-map>

    </div>

</template>
<script>
import Services from '../../services/services';
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
    name:'maps-component',
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
    },
    data() {
        /* center: latLng(9.07284,  -79.44805), , -79.8463*/
        return {
            center: [0.39550467153201946, -71.10351562500001],
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
            markers: [
                /* {
                    id: 'm1',
                    position: { lat: -11.824341483849048, lng: -76.77246093750001 },
                    tooltip: 'Direccion 1',
                    draggable: true,
                    visible: true,
                    color: 'red',
                }, */
                /*  {
                     id: 'm2',
                     position: { lat:-8.450638800331001, lng: -74.66308593750001 },
                     tooltip: 'Direccion 2',
                     draggable: true,
                     visible: true,
                     color:'blue'
                 },
                 {
                     id: 'm3',
                     position: { lat: -4.959615024698014, lng: -74.75097656250001 },
                     tooltip: 'Direccion 3',
                     draggable: true,
                     visible: true,
                     color:'yelaow'
                 },
                 {
                     id: 'm4',
                     position: { lat: -7.27529233637217, lng: -77.69531250000001 },
                     tooltip: 'Direccion 4',
                     draggable: true,
                     visible: true,
                     color:'red'
                 },
                 {
                     id: 'm5',
                     position: { lat: -3.3818237353282767, lng: -77.29980468750001 },
                     tooltip: 'Direccion 5',
                     draggable: true,
                     visible: true,
                     color:'red'
                 }, */
            ],
            bounds: latLngBounds(
                { lat: 0.39550467153201946, lng: -71.10351562500001 },
                { lat: -16.04581345375217, lng: -64.90722656250001 }
            ),
        };
    },
    mounted() {
        this.getCentroVotacion();
    },
    methods: {
        alert(item) {
            alert('this is ' + JSON.stringify(item));
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
        nuevoRegistroCentVotacion(){
            window.location.href = 'mapas/nuevo-centro-votacion';
        }
    },
}
</script>
