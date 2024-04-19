<template>
    <div class="modal fade" id="centroModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar CENTRO DE VOTACIÓN</h5>
                    <h5 class="modal-title" v-else>Actualizar CENTRO DE VOTACIÓN</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del Centro de votación</legend>


                        

                        <div class="row mb-1">


                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>NOMBRE: </label>
                                        <input type="text" placeholder="nombre centro votación" v-model="centro.nombre"
                                            class="form-control"
                                            :class="errors != null && errors.nombre ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.nombre" class="text-danger">{{
                        errors.nombre[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>DIRECCIÓN: </label>
                                        <input type="text" placeholder="direccion centro votación"
                                            v-model="centro.direccion" class="form-control"
                                            :class="errors != null && errors.direccion ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.direccion" class="text-danger">{{
                        errors.direccion[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>LATITUD: </label>
                                        <input type="text" placeholder="latitud centro votación"
                                            v-model="centro.latitud" class="form-control"
                                            :class="errors != null && errors.latitud ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.latitud" class="text-danger">{{
                        errors.latitud[0] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>LONGITUD: </label>
                                        <input type="text" placeholder="longitud centro votación"
                                            v-model="centro.longitud" class="form-control"
                                            :class="errors != null && errors.longitud ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.longitud" class="text-danger">{{
                        errors.longitud[0] }}</span>
                                    </div>
                                </div>
                            </div>



                        </div>


                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="centro.provincia_id"
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
                                <select class="form-select" v-model="centro.distrito_id"
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
                                <select class="form-select" v-model="centro.corregimiento_id"
                                    :class="errors != null && errors.corregimiento_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="corregt in corrigement" :key="corregt.id" :value="corregt.id">{{
                        corregt.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.corregimiento_id" class="text-danger">{{
                                    errors.corregimiento_id[0] }}</span>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewUser()" v-if="option"> Guardar
                    </button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateUser()" v-else> Actualizar
                    </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closepersonaModal()"> Cerrar
                    </button>


                    
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Services from '../../../services/services';
export default {
    components: {

    },
    data() {
        return {

            centro: {
                id: 0,

                nombre: '',
                direccion: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
                latitud: '',
                longitud: '',

            },
            errors: null,
            loading: false,
            option: true,
            profiles: {},
            provinces: {},
            districts: {},
            corrigement: {},
            codigoPais: {},
            tipoCandidatos: {},
            personas: {}
        }
    },
    mounted() {

    },
    methods: {
        async openCentroModal(id) {
            console.log(id)
            $("#centroModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getPersonas();


            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('centro-votacion/show', id);
                    console.log(result)

                    this.centro = {
                        id: result.centro_votacion_id,
                        nombre: result.nombre,
                        direccion: result.direccion,
                        provincia_id: result.provincia_id,
                        distrito_id: result.distrito,
                        corregimiento_id: result.corregimiento_id,
                        latitud: result.latitud,
                        longitud: result.longitud,

                    }



                    this.getDistrict(result.provincia_id)
                    this.getCorregiment(result.distrito_id)
                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
            }
        },
        closepersonaModal() {
            /* this.user.id = 0; */
            $("#centroModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getPersonas() {
            try {
                const result = await Services.getAll('candidato/tipoCandidatoPersonas');
                this.personas = result
            } catch (error) {
                return error;
            }
        },

        async getDepartments() {
            try {
                const result = await Services.getAll('ubigeus/department');
                this.departments = result
            } catch (error) {
                return error;
            }
        },
        async getTipoDocumentos() {
            try {
                const result = await Services.getAll('candidato/tipoCandidato');
                this.tipoCandidatos = result
            } catch (error) {
                return error;
            }
        },
        getDistrictItem() {
            this.getDistrict(this.centro.provincia_id);
        },
        getCorregimientoItem() {
            this.getCorregiment(this.centro.distrito_id);
        },
        async getProvinces() {
            try {
                const result = await Services.getAll('ubigeus/province');
                this.provinces = result
            } catch (error) {
                return error;
            }
        },
        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/district', province_id);
                this.districts = result
            } catch (error) {
                return error;
            }
        },
        async getCorregiment(distric_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/corregimient', distric_id);
                this.corrigement = result
            } catch (error) {
                return error;
            }
        },
        async getListCodigoPais() {
            try {
                const codigoPais = await Services.getAll('ubigeus/codigo-pais');
                this.codigoPais = codigoPais;
            } catch (error) {
                return error;
            }
        },
        async addNewUser() {
            this.errors = null;
            try {
                const result = await Services.addNewInfo('centro-votacion/add', this.centro);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#centroModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                    } else {
                        this.$toast.error(result.result[0].message);
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },
        async updateUser() {
            this.errors = null;
            try {
                const result = await Services.addNewInfo('centro-votacion/update', this.centro);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#centroModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                    } else {
                        this.$toast.error(result.result[0].message);
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },
        clearInput() {

            this.centro = {
                id: 0,
                nombre: '',
                direccion: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
                latitud: '',
                longitud: '',

            }
        }
    },
}
</script>