<template>
    <div class="modal fade" id="centroSupervisorModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option"> CENTRO DE VOTACIÓN - SUPERVISOR</h5>
                    <h5 class="modal-title" v-else>Agregar CENTRO DE VOTACIÓN | {{ centro.nombre }}</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del CENTRO DE VOTACIÓN - SUPERVISOR | {{ centro.nombre }}</legend>



                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="row">

                                    <div class="col-md-12">
                                        <label>SUPERVISORES: </label>
                                        <select class="form-select" v-model="centro.supervisor_id"
                                            :class="errors != null && errors.supervisor_id ? 'is-invalid' : ''">
                                            <option value="" selected disabled>--seleccionar--</option>
                                            <option v-for="supervisor in supervisores" :key="supervisor.id"
                                                :value="supervisor.id">
                                                {{
                        supervisor.persona_nombre }} {{
                        supervisor.apellido_paterno }} {{
                        supervisor.apellido_materno }} </option>
                                        </select>
                                        <span v-if="errors != null && errors.supervisor_id" class="text-danger">{{
                        errors.supervisor_id[0] }}</span>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <button type="button" class="btn btn-primary" @click.prevent="addNewUser()">
                                            Agregar Supervisor
                                        </button>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-8">
                                <label>LISTA DE SUPERVISORES EN CENTRO DE VOTACIÓN: </label>



                                <div class="card mb-1 p-3">
                                    <div class="d-flex justify-content-between align-items-center row mt-75">
                                        <div
                                            class="col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                                            <div class="dataTables_length" id="DataTables_Table_0_length">
                                                <label>
                                                    Show
                                                    <select @change="changePageSize" class="form-select"
                                                        v-model="pageSize">
                                                        <option value="5">5</option>

                                                        <option value="15">15</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                    entries
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                                            <div
                                                class="dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap">
                                                <div class="me-1">
                                                    <div class="dataTables_filter">
                                                        <label>Buscar:<input type="search" @input="debouncedSearch"
                                                                class="form-control form-control-sm"
                                                                placeholder="Buscar..." v-model="searchQuery">
                                                        </label>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 table-responsive text-nowrap">
                                            <table class="table table-hover table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center font-monospace" style="width: 5%;">Items
                                                        </th>
                                                        <th class="text-center font-monospace" style="width: 8%;">
                                                            NOMBRES y APELLIDOS</th>


                                                        <th class="text-center font-monospace" style="width: 10%;">
                                                            Estado</th>
                                                        <th class="text-center font-monospace" style="width: 6%;">
                                                            Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    <tr v-for="(item, index) in centros.data" :key="item.id">
                                                        <td class="text-center">
                                                            {{ index + 1 }}
                                                        </td>
                                                        <td class="text-center font-monospace">{{ item.nombre }} {{
                        item.apellido_paterno }} {{ item.apellido_materno }} </td>


                                                        <td class="text-center font-monospace">
                                                            <span v-if="item.estado == 1"
                                                                class="badge bg-label-success me-1">Activo</span>
                                                            <span v-else
                                                                class="badge bg-label-danger me-1">Inactivo</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <!-- <button class="btn btn-primary btn-sm"
                                                                @click.prevent="openModalEdit(item.id)"><i
                                                                    class="fa-solid fa-pen-to-square"></i></button> -->




                                                            <button v-if="item.estado == 1"
                                                                class="btn btn-danger btn-sm"
                                                                @click.prevent="deleteItem(item.id)"><i
                                                                    class="fa-solid fa-trash-can"></i></button>
                                                            <button v-if="item.estado != 1"
                                                                class="btn btn-success btn-sm"
                                                                @click.prevent="activeItem(item.id)"><i
                                                                    class="fa-solid fa-circle-check"></i></button>



                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                                            <nav aria-label="Page navigation example" v-if="centros.last_page > 1">
                                                <ul class="pagination justify-content-center align-items-center">
                                                    <li class="page-item"
                                                        :class="{ 'disabled': centros.current_page === 1 }">
                                                        <a class="page-link" @click.prevent="fetchUserList(1)"><i
                                                                class="fa fa-fast-backward font-medium-3"
                                                                aria-hidden="true"></i></a>
                                                    </li>
                                                    <li class="page-item "
                                                        :class="{ 'disabled': centros.current_page === 1 }">
                                                        <a class="page-link"
                                                            @click.prevent="fetchUserList(centros.current_page - 1)"><i
                                                                class="fa-solid fa-backward-step"></i></a>
                                                    </li>
                                                    <li class="page-item" v-for="pageNumber in displayedPages"
                                                        :key="pageNumber"
                                                        :class="{ 'active': centros.current_page === pageNumber }">
                                                        <a class="page-link" @click.prevent="fetchUserList(pageNumber)"
                                                            href="#">{{
                        pageNumber
                    }}</a>
                                                    </li>
                                                    <li class="page-item"
                                                        :class="{ 'disabled': centros.current_page === centros.last_page }">
                                                        <a class="page-link" href="#"
                                                            @click.prevent="fetchUserList(centros.current_page + 1)"><i
                                                                class="fa fa-step-forward font-medium-3"
                                                                aria-hidden="true"></i></a>
                                                    </li>
                                                    <li class="page-item"
                                                        :class="{ 'disabled': centros.current_page === centros.last_page }">
                                                        <a class="page-link" href="#"
                                                            @click.prevent="fetchUserList(centros.last_page)"><i
                                                                class="fa fa-fast-forward font-medium-3"
                                                                aria-hidden="true"></i></a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>






                            </div>







                        </div>

                    </fieldset>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-primary" @click.prevent="addNewUser()"> Agregar Supervisor
                    </button> -->
                    <!-- <button type="button" class="btn btn-primary" @click.prevent="updateUser()" v-else> Guardar
                    </button> -->
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
                supervisor_id: '',
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
            console.log(id)
            $("#centroSupervisorModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getSupervisores();


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
                    this.fetchUserList();

                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
            }
        },

        closepersonaModal() {
            this.user.id = 0;
            $("#centroSupervisorModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },

        async getSupervisores() {
            try {
                const result = await Services.getAll('centro-votacion/list-supervisores');
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
                const result = await Services.addNewInfo('centro-votacion/add-supervisor', this.centro);
                if (result.status) {
                    if (result.result[0].status) {
                        // this.clearInput();
                        // $("#centroSupervisorModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        // this.$emit('data-add');
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
                this.updateDisplayedPages();
            } catch (error) {
                console.log(error)
            }
        },
        updateDisplayedPages() {
            const totalDisplayedPages = 6;
            const halfDisplayedPages = Math.floor(totalDisplayedPages / 2);

            let startPage = Math.max(1, this.centros.current_page - halfDisplayedPages);
            let endPage = Math.min(this.centros.last_page, startPage + totalDisplayedPages - 1);

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
            // this.$swal({
            //     title: "¿Estás seguro?",
            //     text: "Esta acción no se puede revertir. ¿Quieres continuar?",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Sí, eliminar",
            //     cancelButtonText: 'Cancelar',
            // }).then(async (result) => {
            //     if (result.isConfirmed) {
            try {
                const result = await Services.addNewInfo('centro-votacion/delete-supervisor', { id: id });
                if (result.status) {
                    if (result.result[0].status) {
                        this.fetchUserList();
                        this.$swal({
                            title: "Eliminado!",
                            text: result.result[0].message,
                            icon: "success"
                        });
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
            // }
            // });
        },
        async activeItem(id) {
            try {
                const result = await Services.addNewInfo('centro-votacion/active-supervisor', { id: id });
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