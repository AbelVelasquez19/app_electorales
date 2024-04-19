<template>
    <div class="modal fade" id="mesaModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar MESA</h5>
                    <h5 class="modal-title" v-else>Actualizar MESA</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion de la Mesa</legend>



                        <div class="row mb-1">


                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombre: </label>
                                        <input type="text" placeholder="nombre mesa" v-model="mesa.nombre"
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
                                        <label>Número: </label>
                                        <input type="number" placeholder="número mesa" v-model="mesa.numero"
                                            class="form-control"
                                            :class="errors != null && errors.numero ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.numero" class="text-danger">{{
                        errors.nombre[0] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>Centro de Votación: </label>
                                <select class="form-select" v-model="mesa.centro_votacion_id"
                                    :class="errors != null && errors.centro_votacion_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="centro in centros" :key="centro.id" :value="centro.id">{{
                        centro.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.centro_votacion_id" class="text-danger">{{
                        errors.centro_votacion_id[0] }}</span>
                            </div>


                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Cantidad de votantes: </label>
                                        <input type="number" placeholder="número mesa" v-model="mesa.cantidad_votantes"
                                            class="form-control"
                                            :class="errors != null && errors.cantidad_votantes ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.cantidad_votantes" class="text-danger">{{
                        errors.cantidad_votantes[0] }}</span>
                                    </div>
                                </div>
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

            mesa: {
                id: 0,
                nombre: '',
                numero: '',
                centro_votacion_id: '',
                cantidad_votantes: '',
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
            personas: {},
            centros: {}

        }
    },
    mounted() {

    },
    methods: {
        async openMesaModal(id) {
            console.log(id)
            $("#mesaModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getCentrosVotacion();
            this.clearInput();


            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('mesa/show', id);
                    // console.log(result)

                    this.mesa = {
                        id: result.mesa_id,
                        nombre: result.nombre,
                        numero: result.numero,
                        centro_votacion_id: result.centro_votacion_id,
                        cantidad_votantes: result.cantidad_votantes,
                    }



                  
                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
            }
        },
        closepersonaModal() {
            this.mesa.id = 0;
            $("#mesaModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getCentrosVotacion() {
            try {
                const result = await Services.getAll('mesa/list-centros-votacion');
                this.centros = result
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
                const result = await Services.addNewInfo('mesa/add', this.mesa);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#mesaModal").modal("hide");
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
                const result = await Services.addNewInfo('mesa/update', this.mesa);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#mesaModal").modal("hide");
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

            this.mesa= {
                id: 0,
                nombre: '',
                numero: '',
                centro_votacion_id: '',
                cantidad_votantes: '',
            }
        }
    },
}
</script>