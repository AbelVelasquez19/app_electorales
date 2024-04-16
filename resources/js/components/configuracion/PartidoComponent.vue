<template>
    <div class="row">
        <div class="col-md-12">
            <div class="row breadcrumbs-top mb-1">
                <div class="col-12">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="user">PARTIDO POLITICO</a>
                            </li>
                            <li class="breadcrumb-item active">listas de partidos politicos
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
                                            class="form-control form-control-sm" placeholder="Buscar..." v-model="searchQuery">
                                    </label>
                                    <button class="dt-button add-new btn btn-primary" type="button"
                                        @click.prevent="openModal()"><span>Nuevo Partido Politico</span></button>
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
                                    <th class="text-center font-monospace" style="width: 8%;">DESCRIPCIÓN</th>
                                    <th class="font-monospace">Nombre</th>
                                    <th class="font-monospace">LOGO</th>
                                    <th class="text-center font-monospace" style="width: 10%;">Estado</th>
                                    <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr v-for="(item,index) in partidos.data" :key="item.id">
                                    <td class="text-center">
                                        {{ index +1 }}
                                    </td>
                                    <td class="text-center font-monospace">{{ item.partido_politico }}</td>
                                    <td class="font-monospace">{{ item.nombre }}</td>
                                    <td class="font-monospace">
                                        <img :src="item.logo" alt="" class="rounded" width="70px" height="70px">
                                    </td>
                                    <td class="text-center font-monospace">
                                        <span v-if="item.estado == 1" class="badge bg-label-success me-1">Activo</span>
                                        <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" @click.prevent="openModalEdit(item.id)"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <button  v-if="item.estado == 1" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.id)"><i class="fa-solid fa-trash-can"></i></button>
                                        <button  v-if="item.estado != 1" class="btn btn-success btn-sm" @click.prevent="activeItem(item.id)"><i class="fa-solid fa-circle-check"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                        <nav aria-label="Page navigation example" v-if="partidos.last_page > 1">
                            <ul class="pagination justify-content-center align-items-center">
                                <li class="page-item" :class="{ 'disabled': partidos.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchUserList(1)"><i
                                            class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item " :class="{ 'disabled': partidos.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchUserList(partidos.current_page - 1)"><i
                                            class="fa-solid fa-backward-step"></i></a>
                                </li>
                                <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber"
                                    :class="{ 'active': partidos.current_page === pageNumber }">
                                    <a class="page-link" @click.prevent="fetchUserList(pageNumber)" href="#">{{ pageNumber
                                    }}</a>
                                </li>
                                <li class="page-item" :class="{ 'disabled': partidos.current_page === partidos.last_page }">
                                    <a class="page-link" href="#" @click.prevent="fetchUserList(partidos.current_page + 1)"><i
                                            class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item" :class="{ 'disabled': partidos.current_page === partidos.last_page }">
                                    <a class="page-link" href="#" @click.prevent="fetchUserList(partidos.last_page)"><i
                                            class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <PartidoModal ref="RefPartidoModal" @data-add="updateTable" />
        </div>
    </div>
</template>


<script>
import Services from '../../services/services';
import PartidoModal from '../modals/partidos/PartidoModalComponent.vue';
import { debounce } from 'lodash';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
    },
    components: {
        PartidoModal
    },
    data() {
        return {
            partidos: {},
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
            return Math.ceil(this.partidos.total / this.pageSize);
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchUserList, 500);
        this.fetchUserList();
        
    },
    methods: {  
        openModal() {
            if (this.$refs.RefPartidoModal) {
                this.$refs.RefPartidoModal.openPartidoModal(0);
            }
        },
        async fetchUserList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `partido-politico/list?page=${page}`, this.pageSize);
                this.partidos = result[0];
                this.updateDisplayedPages();
            } catch (error) {
                console.log(error)
            }
        },
        updateDisplayedPages() {
            const totalDisplayedPages = 6;
            const halfDisplayedPages = Math.floor(totalDisplayedPages / 2);

            let startPage = Math.max(1, this.partidos.current_page - halfDisplayedPages);
            let endPage = Math.min(this.partidos.last_page, startPage + totalDisplayedPages - 1);

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
            if (this.$refs.RefPartidoModal) {
                this.$refs.RefPartidoModal.openPartidoModal(id);
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
                        const result = await Services.addNewInfo('partido-politico/delete', { id: id });
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
        async activeItem(id){
            try {
                const result = await Services.addNewInfo('partido-politico/active', { id: id });
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