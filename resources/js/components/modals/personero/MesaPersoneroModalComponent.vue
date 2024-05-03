<template>
    <div class="modal fade" id="mesaPersoneroSaveModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Actualizar Mesa con el Personero</h5>
                    <h5 class="modal-title" v-else>Agregar Mesa con el Personero </h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del Mesa - Personero</legend>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Departamento: </label>
                                <select class="form-select" v-model="centro.departamento_id" :class="errors != null && errors.departamento_id ? 'is-invalid' : ''" @change="getDepartamentoItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="item in departments" :key="item.id" :value="item.id">{{ item.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.departamento_id" class="text-danger">{{ errors.departamento_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="centro.provincia_id" :class="errors != null && errors.provincia_id ? 'is-invalid' : ''" @change="getDistrictItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.provincia_id" class="text-danger">{{ errors.provincia_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Distrito: </label>
                                <select class="form-select" v-model="centro.distrito_id" :class="errors != null && errors.distrito_id ? 'is-invalid' : ''" @change="getListCentroVotacion">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="distrito in districts" :key="distrito.id" :value="distrito.id">{{ distrito.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.distrito_id" class="text-danger">{{ errors.distrito_id[0] }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Centros de votación: </label>
                                <select class="form-select" v-model="centro.id" :class="errors != null && errors.corregimiento_id ? 'is-invalid' : ''" @change="getMesaCentroItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="centro in centros" :key="centro.id" :value="centro.id">{{ centro.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.centro_votacion_id" class="text-danger">{{ errors.centro_votacion_id[0] }}</span>
                            </div>
                            <div class="col-md-5">
                                <label>Mesas: </label>
                                <select id="personero_id" class="select2 form-select form-select-sm form-select-lg" v-model="personero.mesa_id" :class="errors != null && errors.mesa_id ? 'is-invalid' : ''">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="mesa in mesas" :key="mesa.id" :value="mesa.id"> {{ mesa.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.mesa_id" class="text-danger">{{ errors.mesa_id[0] }}</span>
                            </div>
                            <div class="col-md-3 mt-3">
                                <button type="button" class="btn btn-primary" @click.prevent="addPersoneroMesa()">
                                    Agregar Mesa
                                </button>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Listado de mesas por Personero: </legend>
                        <div class="row">
                            <div class="row mt-2">
                                <div class="col-md-12 table-responsive text-nowrap">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center font-monospace" style="width: 5%;">Items
                                                </th>
                                                <th class="text-center font-monospace" style="width: 8%;"> NOMBRES y APELLIDOS</th>
                                                <th class="text-center font-monospace" style="width: 10%;"> Estado</th>
                                                <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <tr v-for="(item, index) in personeros.data" :key="item.id_mesa_personero">
                                                <td class="text-center">
                                                    {{ index + 1 }}
                                                </td>
                                                <td class="text-center font-monospace">{{ item.nombre }}  </td>
                                                <td class="text-center font-monospace">
                                                    <span v-if="item.estado_mesa_personero == 1"
                                                        class="badge bg-label-success me-1">Activo</span>
                                                    <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                                </td>
                                                <td class="text-center">
                                                    <button v-if="item.estado_mesa_personero == 1" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.id_mesa_personero)"><i class="fa-solid fa-trash-can"></i></button>
                                                    <button v-if="item.estado_mesa_personero != 1" class="btn btn-success btn-sm" @click.prevent="activeItem(item.id_mesa_personero)"><i class="fa-solid fa-circle-check"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                                    <nav aria-label="Page navigation example" v-if="personeros.last_page > 1">
                                        <ul class="pagination justify-content-center align-items-center">
                                            <li class="page-item" :class="{ 'disabled': personeros.current_page === 1 }">
                                                <a class="page-link" @click.prevent="fetchUserList(1)"><i class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                                            </li>
                                            <li class="page-item" :class="{ 'disabled': personeros.current_page === 1 }">
                                                <a class="page-link" @click.prevent="fetchUserList(personeros.current_page - 1)"><i class="fa-solid fa-backward-step"></i></a>
                                            </li>
                                            <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber" :class="{ 'active': personeros.current_page === pageNumber }">
                                                <a class="page-link" @click.prevent="fetchUserList(pageNumber)" href="#">{{ pageNumber }}</a>
                                            </li>
                                            <li class="page-item" :class="{ 'disabled': personeros.current_page === personeros.last_page }">
                                                <a class="page-link" href="#" @click.prevent="fetchUserList(personeros.current_page + 1)"><i class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                                            </li>
                                            <li class="page-item" :class="{ 'disabled': personeros.current_page === personeros.last_page }">
                                                <a class="page-link" href="#" @click.prevent="fetchUserList(personeros.last_page)"><i class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </nav>
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
import "vue-select/dist/vue-select.css";
import VueSelect from 'vue-select';
export default {
    components: {
        VueSelect
    },
    data() {
        return {
            centro: {
                id: 0,
                personero_id: '',
                nombre: '',
                direccion: '',
                departamento_id: '',
                provincia_id: '',
                distrito_id: '',
                latitud: '',
                longitud: '',
            },
            mesa: {
                id: 0,
                personero_id: '',
                nombre: '',
                numero: '',
                centro_votacion_id: '',
                cantidad_votantes: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',

            },
            personero: {
                id: '',
                mesa_id:''
            },
            errors: null,
            loading: false,
            option: true,
            profiles: {},
            departments:{},
            provinces: {},
            districts: {},
            corrigement: {},
            codigoPais: {},
            tipoCandidatos: {},
            personas: {},
            supervisores: {},
            personeros: {},
            searchQuery: '',
            debouncedSearch: null,
            pageSize: 5,
            error: null,
            displayedPages: [],
            mesas: [],
            centros: {},
            pais_id:25,
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchUserList, 500);
        this.fetchUserList();
    },
    methods: {
        async openMesaPersoneroModal(id) {
            $("#mesaPersoneroSaveModal").modal("show");
            this.getDepartments();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getMesas(id);
            this.getCentros();
            this.personero.id = id
            this.centro.id = ''
            if (id != 0) {
                this.option = false
                try {
                    this.fetchUserList();
                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;

            }
        },

        closepersonaModal() {
            this.mesa.id = 0;
            $("#mesaPersoneroSaveModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getMesas(id) {
            try {
                const result = await Services.getAll(`personero/list-mesas-personero?persona_id=` + id);
                this.mesas = result
            } catch (error) {
                return error;
            }
        },
        getDepartamentoItem(){
            this.getProvinces(this.centro.departamento_id);
        },
        async getDepartments() {
            try {
                const result = await Services.getShowInfo('ubigeus/department',this.pais_id);
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

        getMesaCentroItem() {
            this.getMesasCentro(this.centro.id);
        },

        getDistrictItem() {
            this.getDistrict(this.centro.provincia_id);
        },

        getCorregimientoItem() {
            this.getCorregiment(this.centro.distrito_id);
        },

        async getProvinces(id_deparamento) {
            try {
                const result = await Services.getShowInfo('ubigeus/province',id_deparamento);
                this.provinces = result
            } catch (error) {
                return error;
            }
        },

        async getCentros() {
            try {
                const result_centros = await Services.getAll(`personero/list-centros-votacion`);
                this.centros = result_centros;
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

        async getListCentroVotacion(){
            const result_centros = await Services.getAll(`personero/list-centros-votacion?persona_id=` + this.personero.id + '&provincia_id=' + province_id);
            this.centros = result_centros

            const result_mesas = await Services.getAll(`personero/list-mesas-personero?persona_id=` + this.personero.id + '&provincia_id=' + province_id);
            this.mesas = result_mesas
        },

        async getMesasCentro(centro_id) {
            const result_mesas = await Services.getAll(`personero/list-mesas-personero?persona_id=` + this.personero.id + '&centro_id=' + centro_id);
            this.mesas = result_mesas
        },

        async getListCodigoPais() {
            try {
                const codigoPais = await Services.getAll('ubigeus/codigo-pais');
                this.codigoPais = codigoPais;
            } catch (error) {
                return error;
            }
        },

        async addPersoneroMesa() {
            this.errors = null;
            try {
                const result = await Services.addNewInfo('personero/add-mesa-personero', this.personero);
                if (result.status) {
                    this.personero.mesa_id="";
                    if (result.result[0].status) {
                        this.$toast.success(result.result[0].message);
                        this.getMesas(this.personero.id);
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
                        $("#mesaPersoneroSaveModal").modal("hide");
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
                const result = await Services.getListInfo(this.searchQuery, `personero/list-personeros-mesa?page=${page}&persona_id=${this.personero.id}`, this.pageSize);
                this.personeros = result[0];
                this.updateDisplayedPages();
            } catch (error) {
                console.log(error)
            }
        },
        updateDisplayedPages() {
            const totalDisplayedPages = 6;
            const halfDisplayedPages = Math.floor(totalDisplayedPages / 2);
            let startPage = Math.max(1, this.personeros.current_page - halfDisplayedPages);
            let endPage = Math.min(this.personeros.last_page, startPage + totalDisplayedPages - 1);
            if (endPage - startPage + 1 < totalDisplayedPages) {
                startPage = Math.max(1, endPage - totalDisplayedPages + 1);
            }
            this.displayedPages = Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
        },
        changePageSize() {
            this.fetchUserList(1);
        },
        updateTable() {
            this.fetchUserList();
        },

        async deleteItem(id) {
            try {
                const result = await Services.addNewInfo('personero/delete-mesa', { id: id });
                if (result.status) {
                    this.getMesas(this.personero.id);
                    if (result.result[0].status) {
                        this.fetchUserList();
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
                const result = await Services.addNewInfo('personero/active-mesa', { id: id });
                if (result.status) {
                    if (result.result[0].status) {
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

<style src="vue-select/dist/vue-select.css"></style>
