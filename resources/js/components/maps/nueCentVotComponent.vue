<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <fieldset>
                        <legend>Ingresar informacion del usuario</legend>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombre de centro votación: </label>
                                        <input type="text" class="form-control" placeholder="centro votación" :class="errors!=null  && errors.nombre ? 'is-invalid' : '' " v-model="centroVotacion.nombre">
                                          <span v-if="errors!=null  && errors.nombre" class="text-danger">{{ errors.nombre[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Dirección: </label>
                                        <input type="text" placeholder="Nombres" class="form-control"  :class="errors!=null  && errors.direccion ? 'is-invalid' : '' "  v-model="centroVotacion.direccion">
                                          <span v-if="errors!=null  && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="centroVotacion.provincia_id"
                                    :class="errors != null && errors.provincia_id ? 'is-invalid' : ''"
                                    @change="getDistrictItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="province in provinces" :key="province.id" :value="province.id">{{
                                    province.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.provincia_id" class="text-danger">{{
                                    errors.provincia_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Distrito: </label>
                                <select class="form-select" v-model="centroVotacion.distrito_id"
                                    :class="errors != null && errors.distrito_id ? 'is-invalid' : ''"
                                    @change="getCorregimientoItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="distrito in districts" :key="distrito.id" :value="distrito.id">{{
                                    distrito.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.distrito_id" class="text-danger">{{
                                    errors.distrito_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Corregimientos: </label>
                                <select class="form-select" v-model="centroVotacion.corregimiento_id"
                                    :class="errors != null && errors.corregimiento_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="corregt in corrigement" :key="corregt.id" :value="corregt.id">{{
                                    corregt.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.corregimiento_id" class="text-danger">{{
                                    errors.corregimiento_id[0] }}</span>
                            </div>
                        </div>
                        <div class="row mb-1" v-for="(item, index) in markers" :key="index">
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>latitud </label>
                                        <!-- :class="errors!=null  && errors.document_number ? 'is-invalid' : '' " v-model="user.document_number"  @input="debouncedSearch" -->
                                        <input type="number" placeholder="latitud" class="form-control" v-model.number="item.position.lat">
                                        <!--   <span v-if="errors!=null  && errors.document_number" class="text-danger">{{ errors.document_number[0] }}</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>longitud: </label>
                                        <!-- :class="errors!=null  && errors.name ? 'is-invalid' : '' " v-model="user.name" -->
                                        <input type="number" placeholder="longitud" class="form-control" v-model.number="item.position.lng">
                                        <!--   <span v-if="errors!=null  && errors.name" class="text-danger">{{ errors.name[0] }}</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <label for=""></label>
                                <l-map :zoom.sync="zoom" :options="mapOptions" :center="center" :bounds="bounds"
                                    :min-zoom="minZoom" :max-zoom="maxZoom" style="height: 450px; width: 100%">
                                    <l-control-layers :position="layersPosition" :collapsed="false"
                                        :sort-layers="true" />
                                    <l-tile-layer v-for="tileProvider in tileProviders" :key="tileProvider.name"
                                        :name="tileProvider.name" :visible="tileProvider.visible"
                                        :url="tileProvider.url" :attribution="tileProvider.attribution" :token="token"
                                        layer-type="base" />
                                    <l-control-zoom :position="zoomPosition" />
                                    <l-control-attribution :position="attributionPosition"
                                        :prefix="attributionPrefix" />
                                    <l-control-scale :imperial="imperial" />
                                    <l-marker v-for="marker in markers" :key="marker.id" :visible="marker.visible"
                                        :draggable="marker.draggable" :lat-lng.sync="marker.position"
                                        :icon="marker.icon" @click="alert(marker)">
                                        <l-popup :content="marker.tooltip" />
                                        <l-tooltip :content="marker.tooltip" />
                                    </l-marker>
                                </l-map>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-danger" @click="cancelarCentVotacion">Cancelar</button>
                            <button class="btn btn-primary" @click="guardarCentroVotacion">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Services from '../../services/services';
import { latLngBounds, Icon } from 'leaflet';
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

delete Icon.Default.prototype._getIconUrl;
Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

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
        urlProvince: {
            type: String,
            default: ''
        },
        urlDistric: {
            type: String,
            default: ''
        },
        urlCoregimient: {
            type: String,
            default: ''
        },
        urlMaps: {
            type: String,
            default: ''
        },
        urlGuardar: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            centroVotacion: {
                nombre:'',
                direccion:'',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: ''
            },
            errors: null,
            provinces: {},
            districts: {},
            corrigement: {},

            //mapa
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
                {
                    id: 'm1',
                    position: { lat: -11.824341483849048, lng: -76.77246093750001 },
                    tooltip: 'Seleccionar cordinadas',
                    draggable: true,
                    visible: true,
                    color: 'red',
                },
            ],
            bounds: latLngBounds(
                { lat: 0.39550467153201946, lng: -71.10351562500001 },
                { lat: -16.04581345375217, lng: -64.90722656250001 }
            ),
        }
    },
    mounted() {
        this.getProvinces();
    },
    methods: {
        getDistrictItem() {
            this.getDistrict(this.centroVotacion.provincia_id);
        },
        getCorregimientoItem() {
            this.getCorregiment(this.centroVotacion.distrito_id);
        },
        async getProvinces() {
            try {
                const result = await Services.getAll(this.urlProvince);
                this.provinces = result
            } catch (error) {
                return error;
            }
        },
        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo(this.urlDistric, province_id);
                this.districts = result
            } catch (error) {
                return error;
            }
        },
        async getCorregiment(distric_id) {
            try {
                const result = await Services.getShowInfo(this.urlCoregimient, distric_id);
                this.corrigement = result
            } catch (error) {
                return error;
            }
        },
        cancelarCentVotacion(){
            window.location.href = this.urlMaps;
        },
        async guardarCentroVotacion(){
            this.errors= null;
            let obj = {
                centroVotacion:this.centroVotacion,
                latitud:this.markers[0].position.lat,
                longitud:this.markers[0].position.lng
            }
            try {
                const result = await Services.addNewInfo(this.urlGuardar,obj);
                console.log(result)
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        window.location.href = this.urlMaps;
                        this.$toast.success(result.result[0].message);
                    }else{
                        this.$toast.error(result.result[0].message);
                    }
                }else{
                    let dataError = {
                        nombre: result.result['centroVotacion.nombre'],
                        direccion: result.result['centroVotacion.direccion'],
                        provincia_id: result.result['centroVotacion.provincia_id'],
                        distrito_id: result.result['centroVotacion.distrito_id'],
                        corregimiento_id: result.result['centroVotacion.corregimiento_id'],
                    }
                     this.errors = dataError;
                }
            } catch (error) {
                return error;
            }
        },
        clearInput(){
            this.centroVotacion= {
                nombre:'',
                direccion:'',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: ''
            }
        }
    }
}
</script>