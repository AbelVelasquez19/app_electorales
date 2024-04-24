<template>
    <div class="modal fade" id="partidoModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo Partido Politico</h5>
                    <h5 class="modal-title" v-else>Actualizar nuevo Partido Politico</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend><b>Ingresar informacion del Partido Politico</b></legend>
                        <div class="row mb-1">
                            <div class="col-md-12 mt-2">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombre Partido: </label>
                                        <input type="text" placeholder="Nombre Partido" class="form-control" :class="errors != null && errors.numero_documento ? 'is-invalid' : ''" v-model="partido.nombre">
                                        <span v-if="errors != null && errors.numero_documento" class="text-danger">{{ errors.numero_documento[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>LOGO: </label>
                                        <input type="file" placeholder="LOGO" class="form-control" @change="handleFileChange" :class="errors != null && errors.logo ? 'is-invalid' : ''" accept="image/*">
                                        <span v-if="errors != null && errors.logo" class="text-danger">{{ errors.logo[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>ORDEN: </label>
                                        <input type="number" placeholder="orden" v-model="partido.orden" class="form-control" :class="errors != null && errors.orden ? 'is-invalid' : ''">
                                        <span v-if="errors != null && errors.orden" class="text-danger">{{ errors.orden[0] }}</span>
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
            partido: {
                id: 0,
                nombre: '',
                logo: '',
                orden: '',
            },
            errors: null,
            loading: false,
            option: true,
            profiles: {},
            provinces: {},
            districts: {},
            corrigement: {},
            codigoPais: {}
        }
    },
    mounted() {

    },
    methods: {
        handleFileChange(event) {
            this.partido.logo = event.target.files[0];
        },
        async openPartidoModal(id) {
            $("#partidoModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('partido-politico/show', id);
                    this.partido = {
                        id: id,
                        nombre: result.nombre,
                        logo: result.logo,
                        orden: result.orden,
                    }
                    this.getDistrict(result.provincia_id)
                    this.getCorregiment(result.distrito_id)
                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
                this.clearInput();
            }
        },
        closepersonaModal() {
            this.user.id = 0;
            $("#partidoModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getDepartments() {
            try {
                const result = await Services.getAll('ubigeus/department');
                this.departments = result
            } catch (error) {
                return error;
            }
        },

        getDistrictItem() {
            this.getDistrict(this.user.provincia_id);
        },

        getCorregimientoItem() {
            this.getCorregiment(this.user.distrito_id);
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
                let formData = new FormData();
                formData.append('nombre', this.partido.nombre);
                formData.append('logo', this.partido.logo);
                formData.append('orden', this.partido.orden);
                const result = await Services.addNewInfo('partido-politico/add', formData);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#partidoModal").modal("hide");
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
                let formDataUpdate = new FormData();
                formDataUpdate.append('nombre', this.partido.nombre);
                formDataUpdate.append('logo', this.partido.logo);
                formDataUpdate.append('orden', this.partido.orden);
                formDataUpdate.append('id', this.partido.id);
                const result = await Services.addNewInfo('partido-politico/update', formDataUpdate);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#partidoModal").modal("hide");
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
            this.partido = {
                id: 0,
                nombre: '',
                logo: '',
                orden: '',
            }
        }
    },
}
</script>