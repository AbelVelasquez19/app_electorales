<template>
    <div class="row">
        <div class="col-md-12">
            <div class="row breadcrumbs-top mb-1">
                <div class="col-12">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="user">Centro de votacion</a>
                            </li>
                            <li class="breadcrumb-item active">Lista Centro de votaciones
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="card mb-1 p-3">
                <div class="d-flex justify-content-between align-items-center row mt-75">
                    <div class="col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                        <div class="dataTables_length" id="DataTables_Table_0_length">
                            <label>
                                Show
                                <select @change="changePageSize" class="form-select" v-model="pageSize">
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
                                            class="form-control form-control-sm" placeholder="Buscar..."
                                            v-model="searchQuery">
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
                                    <th class="text-center font-monospace" style="width: 5%;">Items</th>
                                    <th class="text-center font-monospace" style="width: 8%;">Nombres</th>
                                    <th class="font-monospace">Dirección</th>
                                    <th class="font-monospace">Provincia</th>
                                    <th class="font-monospace">Distrito</th>
                                    <th class="font-monospace">Corregimiento</th>
                                    <th class="text-center font-monospace" style="width: 10%;">Estado</th>
                                    <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr v-for="(item, index) in centros.data" :key="item.id">
                                    <td class="text-center">
                                        {{ index + 1 }} 
                                    </td>
                                    <td class="text-center font-monospace">{{ item.nombre }}</td>
                                    <td class="font-monospace">{{ item.direccion }}</td>
                                    <td class="font-monospace"> {{ item.provincia_nombre }} </td>
                                    <td class="font-monospace">{{ item.distrito_nombre }}</td>
                                    <td class="font-monospace">{{ item.corregimiento_nombre }}</td>
                                    <td class="text-center font-monospace">
                                        <span v-if="item.estado == 1" class="badge bg-label-success me-1">Activo</span>
                                        <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                    </td>
                                    <td class="text-center">
                                        <button v-if="item.estado == 1" class="btn btn-danger btn-sm"
                                            @click.prevent="deleteItem(item.centro_votacion_id)"><i
                                                class="fa-solid fa-trash-can"></i></button>
                                        <button v-if="item.estado != 1" class="btn btn-success btn-sm"
                                            @click.prevent="activeItem(item.centro_votacion_id)"><i
                                                class="fa-solid fa-circle-check"></i></button>

                                                <button class="btn btn-primary btn-sm"
                                            @click.prevent="openRegistrarSupervisor(item.centro_votacion_id)"><i
                                                class="fa-solid fa-bars"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                        <nav aria-label="Page navigation example" v-if="centros.last_page > 1">
                            <ul class="pagination justify-content-center align-items-center">
                                <li class="page-item" :class="{ 'disabled': centros.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchCentroVotacionList(1)"><i
                                            class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item " :class="{ 'disabled': centros.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchCentroVotacionList(centros.current_page - 1)"><i
                                            class="fa-solid fa-backward-step"></i></a>
                                </li>
                                <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber"
                                    :class="{ 'active': centros.current_page === pageNumber }">
                                    <a class="page-link" @click.prevent="fetchCentroVotacionList(pageNumber)" href="#">{{
                                    pageNumber
                                }}</a>
                                </li>
                                <li class="page-item"
                                    :class="{ 'disabled': centros.current_page === centros.last_page }">
                                    <a class="page-link" href="#"
                                        @click.prevent="fetchCentroVotacionList(centros.current_page + 1)"><i
                                            class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item"
                                    :class="{ 'disabled': centros.current_page === centros.last_page }">
                                    <a class="page-link" href="#"
                                        @click.prevent="fetchCentroVotacionList(centros.last_page)"><i
                                            class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <CentroModal ref="RefCentroModal" @data-add="updateTable" />
            <CentroSupervisorModal ref="RefCentroSupervisorModal" @data-add="updateTable" />

        </div>
    </div>
</template>


<script>
import Services from '../../services/services';
import CentroModal from '../modals/centros/CentroModalComponent.vue';
import CentroSupervisorModal from '../modals/centros/CentroSupervisorModalComponent.vue';

import { debounce } from 'lodash';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
    },
    components: {
        CentroModal,
        CentroSupervisorModal
    },
    data() {
        return {
            centros: {},
            displayedPages: [],
            error: null,
            searchQuery: '',
            debouncedSearch: null,
            pageSize: 15,
            errors: null,
        }
    },
    computed: {
        totalPages() {
            return Math.ceil(this.centros.total / this.pageSize);
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchCentroVotacionList, 500);
        this.fetchCentroVotacionList();

    },
    methods: {
        openModal() {
            if (this.$refs.RefCentroModal) {
                this.$refs.RefCentroModal.openCentroModal(0);
            }
        },
        async fetchCentroVotacionList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `centro-votacion/list?page=${page}`, this.pageSize);
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
            this.fetchCentroVotacionList(1);
        },
        updateTable() {
            this.fetchCentroVotacionList();
        },
        openModalEdit(id) {
            console.log(id)
            if (this.$refs.RefCentroModal) {
                this.$refs.RefCentroModal.openCentroModal(id);
            }
        },
        openRegistrarSupervisor(id) {
            if (this.$refs.RefCentroSupervisorModal) {
                this.$refs.RefCentroSupervisorModal.openCentroSupervisorModal(id);
            }
        },
        deleteItem(id) {
            this.$swal({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede revertir. ¿Quieres continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: 'Cancelar',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const result = await Services.addNewInfo('centro-votacion/delete', { id: id });
                        if (result.status) {
                            if (result.result[0].status) {
                                this.fetchCentroVotacionList();
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
                }
            });
        },
        async activeItem(id) {
            try {
                const result = await Services.addNewInfo('centro-votacion/active', { id: id });
                if (result.status) {
                    if (result.result[0].status) {
                        this.fetchCentroVotacionList();
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