<template>
    <div class="modal fade" id="centroSupervisorModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option"> Centro de Votación - Supervisor</h5>
                    <h5 class="modal-title" v-else>AgregarCentro de Votación | {{ centro.nombre }}</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del Centro de Votación - Supervisor | {{ centro.nombre }}</legend>
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label>Supervisores: </label>
                                        <select class="form-select"  :class="errors != null && errors.supervisor_id ? 'is-invalid' : ''" v-model="centro.supervisor_id">
                                            <option value="" selected disabled>--seleccionar--</option>
                                            <option v-for="supervisor in supervisores" :key="supervisor.id" :value="supervisor.id"> {{supervisor.persona_nombre }} {{supervisor.apellido_paterno }} {{ supervisor.apellido_materno }} </option>
                                        </select>
                                        <span v-if="errors != null && errors.supervisor_id" class="text-danger">{{errors.supervisor_id[0] }}</span>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <button type="button" class="btn btn-primary" @click.prevent="addNewUser()"> Agregar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-5">
                                <label>Lista de Supervisores en Centro de Votación: </label>
                                <div class="row mt-2">
                                    <div class="col-md-12 table-responsive text-nowrap">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-center font-monospace" style="width: 5%;">Items</th>
                                                    <th class="text-center font-monospace" style="width: 8%;">NOMBRES y APELLIDOS</th>
                                                    <th class="text-center font-monospace" style="width: 10%;"> Estado</th>
                                                    <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <tr v-for="(item, index) in centros.data" :key="item.id">
                                                    <td class="text-center">
                                                        {{ index + 1 }}
                                                    </td>
                                                    <td class="text-center font-monospace">{{ item.nombre }} {{item.apellido_paterno }} {{ item.apellido_materno }} </td>
                                                    <td class="text-center font-monospace">
                                                        <span v-if="item.estado == 1"
                                                            class="badge bg-label-success me-1">Activo</span>
                                                        <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button v-if="item.estado == 1" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.id)"><i class="fa-solid fa-trash-can"></i></button>
                                                        <button v-if="item.estado != 1" class="btn btn-success btn-sm" @click.prevent="activeItem(item.id)"><i class="fa-solid fa-circle-check"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click.prevent="closepersonaModal()"> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Services from '../../../services/services';
import { debounce } from 'lodash';

export default {
    components: {

    },
    data() {
        return {
            centro: {
                id: 0,
                supervisor_id: "",
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
            personas: {},
            supervisores: {},
            centros: {},
            searchQuery: '',
            debouncedSearch: null,
            pageSize: 5,
            error: null,
            displayedPages: [],
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchUserList, 500);
        this.fetchUserList();
    },
    methods: {
        async openCentroSupervisorModal(id) {
            $("#centroSupervisorModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getSupervisores(id);
            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('centro-votacion/show-centro-supervisor', id);
                    this.centro = {
                        id: result.centro_votacion_id,
                        nombre: result.nombre,
                        direccion: result.direccion,
                        provincia_id: result.provincia_id,
                        distrito_id: result.distrito,
                        corregimiento_id: result.corregimiento_id,
                        latitud: result.latitud,
                        longitud: result.longitud,
                        supervisor_id: "",
                    }
                    this.getDistrict(result.provincia_id)
                    this.getCorregiment(result.distrito_id)
                    this.fetchUserList();

                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
            }
        },

        closepersonaModal() {
            this.centro.id = 0;
            $("#centroSupervisorModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getSupervisores(id) {
            try {
                const result = await Services.getAll('centro-votacion/list-supervisores?centro_id=' + id);
                this.supervisores = result
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
                const id_centro = this.centro.id;
                const result = await Services.addNewInfo('centro-votacion/add-supervisor', this.centro);
                if (result.status) {
                    this.centro.supervisor_id=''
                    if (result.result[0].status) {
                        this.$toast.success(result.result[0].message);
                        this.getSupervisores(id_centro);
                        this.fetchUserList();
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
                        $("#centroSupervisorModal").modal("hide");
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
            this.centro.supervisor_id = '';
        },

        async fetchUserList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `centro-votacion/list-supervisores-centro?page=${page}&centro_id=${this.centro.id}`, this.pageSize);
                this.centros = result[0];
            } catch (error) {
                console.log(error)
            }
        },

        updateTable() {
            this.fetchUserList();
        },

        async deleteItem(id) {
            try {
                const id_centro = this.centro.id;
                const result = await Services.addNewInfo('centro-votacion/delete-supervisor', { id: id });
                if (result.status) {
                    this.centro.supervisor_id = ''
                    if (result.result[0].status) {
                        this.fetchUserList();
                        this.getSupervisores(id_centro);
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },

        async activeItem(id) {
            try {
                const id_centro = this.centro.id;
                const result = await Services.addNewInfo('centro-votacion/active-supervisor', { id: id });
                if (result.status) {
                    if (result.result[0].status) {
                        this.getSupervisores(id_centro);
                        this.fetchUserList();
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        }
    },
}
</script>