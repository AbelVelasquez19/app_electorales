<template>
    <div class="modal fade" id="candidatoModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar CANDIDATO</h5>
                    <h5 class="modal-title" v-else>Actualizar CANDIDATO</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del CANDIDATO</legend>



                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label>PERSONAS: </label>
                                <select class="form-select" v-model="candidato.persona_id"
                                    :class="errors != null && errors.persona_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="persona in personas" :key="persona.id" :value="persona.id">{{
                        persona.nombre }} {{ persona.nombre }} {{ persona.apellido_paterno }}
                                        {{ persona.apellido_paterno }} </option>
                                </select>
                                <span v-if="errors != null && errors.persona_id" class="text-danger">{{
                        errors.persona_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Tipo Candidato: </label>
                                <select class="form-select" v-model="candidato.tipo_candidato_id"
                                    :class="errors != null && errors.tipo_candidato_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="tipoCandidato in tipoCandidatos" :key="tipoCandidato.id"
                                        :value="tipoCandidato.id">{{
                        tipoCandidato.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.tipo_candidato_id" class="text-danger">{{
                        errors.tipo_candidato_id[0] }}</span>
                            </div>
                            <div class="col-md-2">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>ORDEN: </label>
                                        <input type="number" placeholder="orden" v-model="candidato.orden"
                                            class="form-control"
                                            :class="errors != null && errors.orden ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.orden" class="text-danger">{{
                        errors.orden[0] }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="candidato.provincia_id"
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
                                <select class="form-select" v-model="candidato.distrito_id"
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
                                <select class="form-select" v-model="candidato.corregimiento_id"
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
            user: {
                id: 0,
                numero_documento: '77777777',
                nombre: 'prueba',
                apellido_paterno: 'prueba',
                apellido_materno: 'prueba',
                sexo: 1,
                codigo_pais_id: '',
                celular: '',
                email: '',
                direccion: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
            },
            candidato: {
                id: 0,

                orden: '',
                persona_id: '',
                tipo_candidato_id: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
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
        async openCandidatoModal(id) {
            console.log(id)
            $("#candidatoModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getPersonas();


            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('candidato/show', id);
                    console.log(result)

                    this.candidato = {
                        id: result.candidato_id,

                        orden: result.orden,
                        persona_id: result.persona_id,
                        tipo_candidato_id: result.tipo_candidato_id,
                        provincia_id: result.provincia_id,
                        distrito_id: result.distrito,
                        corregimiento_id: result.provincia_id,
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
            this.user.id = 0;
            $("#candidatoModal").modal("hide");
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
            this.getDistrict(this.candidato.provincia_id);
        },
        getCorregimientoItem() {
            this.getCorregiment(this.candidato.distrito_id);
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
                const result = await Services.addNewInfo('candidato/add', this.candidato);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#candidatoModal").modal("hide");
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
                const result = await Services.addNewInfo('candidato/update', this.candidato);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#candidatoModal").modal("hide");
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
            this.candidato = {
                id: 0,
                orden: '',
                persona_id: '',
                tipo_candidato_id: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',

            }
        }
    },
}
</script>