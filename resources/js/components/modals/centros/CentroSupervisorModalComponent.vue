<template>
    <div class="modal fade" id="centroSupervisorModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">centro de votación</h5>
                    <h5 class="modal-title" v-else>Agregar centro de votación</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Agregar supervisor en centro de votacion: {{ centro.nombre }}</legend>
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label>Supervisores: </label>
                                        <select class="form-select" v-model="centro.supervisor_id" :class="errors != null && errors.supervisor_id ? 'is-invalid' : ''">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="supervisor in supervisores" :key="supervisor.id" :value="supervisor.id">{{ supervisor.persona_nombre }} {{ supervisor.apellido_paterno }} {{ supervisor.apellido_materno }}</option>
                                        </select>
                                        <span v-if="errors != null && errors.supervisor_id" class="text-danger">{{ errors.supervisor_id[0] }}</span>
                                    </div>
                                    <div class="col-md-3 mt-3 d-flex justify-content-center">
                                        <button type="button" class="btn btn-primary" @click.prevent="addNewUser()">
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Lista de supervisores: </label>
                                        <div class="card mb-1 p-3">
                                            <div class="row mt-2">
                                                <div class="col-md-12 table-responsive text-nowrap">
                                                    <table class="table table-hover table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center font-monospace"
                                                                    style="width: 5%;">Items
                                                                </th>
                                                                <th class="text-center font-monospace" style="width: 8%;"> Nombres y Apellidos</th>
                                                                <th class="text-center font-monospace" style="width: 10%;"> Estado</th>
                                                                <th class="text-center font-monospace" style="width: 6%;"> Opciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0">
                                                            <tr v-for="(item, index) in centros" :key="item.supervisor_id">
                                                                <td class="text-center">
                                                                    {{ index + 1 }}
                                                                </td>
                                                                <td class="text-center font-monospace">{{ item.personero }}</td>
                                                                <td class="text-center font-monospace">
                                                                    <span v-if="item.estado == 1" class="badge bg-label-success me-1">Activo</span>
                                                                    <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button v-if="item.estado == 1" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.supervisor_id)"><i class="fa-solid fa-trash-can"></i></button>
                                                                    <button v-if="item.estado != 1" class="btn btn-success btn-sm" @click.prevent="activeItem(item.supervisor_id)"><i class="fa-solid fa-circle-check"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div
                                                    class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                                                    <nav aria-label="Page navigation example"
                                                        v-if="centros.last_page > 1">
                                                        <ul
                                                            class="pagination justify-content-center align-items-center">
                                                            <li class="page-item"
                                                                :class="{ 'disabled': centros.current_page === 1 }">
                                                                <a class="page-link"
                                                                    @click.prevent="fetchUserList(1)"><i
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
                                                                <a class="page-link"
                                                                    @click.prevent="fetchUserList(pageNumber)"
                                                                    href="#">{{ pageNumber }}</a>
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
            supervisores: {},
            centros: {},
            pageSize: 5,
            error: null,
            displayedPages: [],

        }
    },
    mounted() {
        this.getSupervisores();
    },
    methods: {
        async openCentroSupervisorModal(id) {
            $("#centroSupervisorModal").modal("show");
            if (id != 0) {
                this.option = false
                try {
                    const result = await Services.getShowInfo('centro-votacion/show-centro-supervisor', id);
                    this.centros = result;
                } catch (error) {
                    return error;
                }
            } else {
                this.option = true;
            }
        },

        closepersonaModal() {
            $("#centroSupervisorModal").modal("hide");
            this.clearInput();
        },

        async getSupervisores() {
            try {
                const result = await Services.getAll('centro-votacion/list-supervisores');
                this.supervisores = result
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
                        this.$toast.success(result.result[0].message);
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