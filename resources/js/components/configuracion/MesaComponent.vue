<template>
    <div class="row">
        <div class="col-md-12">
            <div class="row breadcrumbs-top mb-1">
                <div class="col-12">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="user">MESAS</a>
                            </li>
                            <li class="breadcrumb-item active">LISTA MESAS
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
                                    <button class="dt-button add-new btn btn-primary" type="button"
                                        @click.prevent="openModal()"><span>NUEVA MESA</span></button>
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
                                    <th class="text-center font-monospace" style="width: 8%;">NOMBRE</th>
                                    <th class="font-monospace">NÚMERO</th>
                                    <th class="font-monospace">CENTRO VOTACIÓN</th>
                                    <th class="font-monospace">CANTIDAD VOTANTES</th>


                                    <th class="text-center font-monospace" style="width: 10%;">Estado</th>
                                    <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr v-for="(item, index) in centros.data" :key="item.id">
                                    <td class="text-center">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="font-monospace">{{ item.nombre }}</td>
                                    <td class="font-monospace"> {{ item.numero }}
                                    </td>
                                    <td class="text-center font-monospace">{{ item.centro_votacion_nombre }}</td>
                                    <td class="text-center font-monospace">{{ item.cantidad_votantes }}</td>

                                    <td class="text-center font-monospace">
                                        <span v-if="item.estado == 1" class="badge bg-label-success me-1">Activo</span>
                                        <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm"
                                            @click.prevent="openModalEdit(item.mesa_id)"><i
                                                class="fa-solid fa-pen-to-square"></i></button>






                                                
                                        <button v-if="item.estado == 1" class="btn btn-danger btn-sm"
                                            @click.prevent="deleteItem(item.mesa_id)"><i
                                                class="fa-solid fa-trash-can"></i></button>
                                        <button v-if="item.estado != 1" class="btn btn-success btn-sm"
                                            @click.prevent="activeItem(item.mesa_id)"><i
                                                class="fa-solid fa-circle-check"></i></button>

                                        <!-- <button class="btn btn-primary btn-sm"
                                            @click.prevent="openRegistrarPersonero(item.mesa_id)"><i
                                                class="fa-solid fa-bars"></i></button> -->

                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                        <nav aria-label="Page navigation example" v-if="centros.last_page > 1">
                            <ul class="pagination justify-content-center align-items-center">
                                <li class="page-item" :class="{ 'disabled': centros.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchUserList(1)"><i
                                            class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item " :class="{ 'disabled': centros.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchUserList(centros.current_page - 1)"><i
                                            class="fa-solid fa-backward-step"></i></a>
                                </li>
                                <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber"
                                    :class="{ 'active': centros.current_page === pageNumber }">
                                    <a class="page-link" @click.prevent="fetchUserList(pageNumber)" href="#">{{
                                    pageNumber
                                }}</a>
                                </li>
                                <li class="page-item"
                                    :class="{ 'disabled': centros.current_page === centros.last_page }">
                                    <a class="page-link" href="#"
                                        @click.prevent="fetchUserList(centros.current_page + 1)"><i
                                            class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item"
                                    :class="{ 'disabled': centros.current_page === centros.last_page }">
                                    <a class="page-link" href="#" @click.prevent="fetchUserList(centros.last_page)"><i
                                            class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <MesaModal ref="RefMesaModal" @data-add="updateTable" />
            <MesaPersoneroModal ref="RefMesaPersoneroModal" @data-add="updateTable" />

        </div>
    </div>
</template>


<script>
import Services from '../../services/services';
import MesaModal from '../modals/mesas/MesaModalComponent.vue';
import MesaPersoneroModal from '../modals/mesas/MesaPersoneroModalComponent.vue';

import { debounce } from 'lodash';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
    },
    components: {
        MesaModal,
        MesaPersoneroModal
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
        this.debouncedSearch = debounce(this.fetchUserList, 500);
        this.fetchUserList();

    },
    methods: {
        openModal() {
            if (this.$refs.RefMesaModal) {
                this.$refs.RefMesaModal.openMesaModal(0);
            }
        },
        async fetchUserList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `mesa/list?page=${page}`, this.pageSize);
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
        openModalEdit(id) {
            console.log(id)
            if (this.$refs.RefMesaModal) {
                this.$refs.RefMesaModal.openMesaModal(id);
            }
        },
        openRegistrarPersonero(id) {
            console.log(id)
            if (this.$refs.RefMesaPersoneroModal) {
                this.$refs.RefMesaPersoneroModal.openMesaPersoneroModal(id);
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
                        const result = await Services.addNewInfo('candidato/delete', { id: id });
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
                }
            });
        },
        async activeItem(id) {
            try {
                const result = await Services.addNewInfo('candidato/active', { id: id });
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