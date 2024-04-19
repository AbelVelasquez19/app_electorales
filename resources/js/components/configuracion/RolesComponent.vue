<template>
    <div class="col-md-12">
        <div class="row breadcrumbs-top mb-1">
            <div class="col-12">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="role">roles</a>
                        </li>
                        <li class="breadcrumb-item active">lista de roles
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
                            <select @change="changePageSize" class="form-control" v-model="pageSize">
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
                                    @click.prevent="openModal()"><span>Nuevo Role</span></button>
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
                                <th class="font-monospace">Nombre</th>
                                <th class="font-monospace">Descripción</th>
                                <th class="font-monospace">Perfil</th>
                                <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="(item,index) in roles.data" :key="item.id">
                                <td class="text-center">
                                    {{ index+1 }}
                                </td>
                                <td class="font-monospace">{{ item.roles_name.toUpperCase() }}</td>
                                <td class="font-monospace">{{ item.roles_description.toUpperCase() }}</td>
                                <td class="text-center font-monospace">
                                    <span v-if="item.roles_status == 1" class="badge bg-label-success me-1">Activo</span>
                                    <span v-else class="badge bg-label-danger me-1">Inactivo</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm" title="Editar rol" @click.prevent="openModalEdit(item.roles_id)"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button  v-if="item.roles_status == 1" title="Eliminar rol" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.roles_id)"><i class="fa-solid fa-trash-can"></i></button>
                                    <button  v-if="item.roles_status != 1" title="Activar rol" class="btn btn-success btn-sm" @click.prevent="activeItem(item.roles_id)"><i class="fa-solid fa-circle-check"></i></button>
                                    <button  v-if="item.roles_status_children == 0" class="btn btn-info btn-sm" title="Ver sub rol" @click.prevent="modalSubMenu(item.roles_id)"><i class="fa-solid fa-list-check"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                    <nav aria-label="Page navigation example" v-if="roles.last_page > 1">
                        <ul class="pagination justify-content-center align-items-center">
                            <li class="page-item" :class="{ 'disabled': roles.current_page === 1 }">
                                <a class="page-link" @click.prevent="fetchRolesList(1)"><i
                                        class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                            </li>
                            <li class="page-item " :class="{ 'disabled': roles.current_page === 1 }">
                                <a class="page-link" @click.prevent="fetchRolesList(roles.current_page - 1)"><i
                                        class="fa-solid fa-backward-step"></i></a>
                            </li>
                            <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber"
                                :class="{ 'active': roles.current_page === pageNumber }">
                                <a class="page-link" @click.prevent="fetchRolesList(pageNumber)" href="#">{{ pageNumber
                                }}</a>
                            </li>
                            <li class="page-item" :class="{ 'disabled': roles.current_page === roles.last_page }">
                                <a class="page-link" href="#" @click.prevent="fetchRolesList(roles.current_page + 1)"><i
                                        class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                            </li>
                            <li class="page-item" :class="{ 'disabled': roles.current_page === roles.last_page }">
                                <a class="page-link" href="#" @click.prevent="fetchRolesList(roles.last_page)"><i
                                        class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <RolesModal ref="RefRolesModal" @data-add="updateTable" />
        <RolesSubMenu ref="RefSubMenuModal" @update-table="updateTable"/>
    </div>
</template>
<script>
import Services from '../../services/services';
import RolesModal from '../modals/roles/RolesModal.vue';
import RolesSubMenu from '../modals/roles/RolesSubMenu.vue';
import { debounce } from 'lodash';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
    },
    components: {
        RolesModal,
        RolesSubMenu
    },
    data() {
        return {
            roles: {},
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
            return Math.ceil(this.profiles.total / this.pageSize);
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchRolesList, 500);
        this.fetchRolesList();        
    },
    methods: {
        modalSubMenu(id){
            if(this.$refs.RefSubMenuModal){
                this.$refs.RefSubMenuModal.openModalSubMenu(id)
            }
        },  
        openModal() {
            if (this.$refs.RefRolesModal) {
                this.$refs.RefRolesModal.openRolesModal(0);
            }
        },
        async fetchRolesList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `role/list?page=${page}`, this.pageSize);
                this.roles = result[0];
                this.updateDisplayedPages();
            } catch (error) {
                console.log(error)
            }
        },
        updateDisplayedPages() {
            const totalDisplayedPages = 6;
            const halfDisplayedPages = Math.floor(totalDisplayedPages / 2);

            let startPage = Math.max(1, this.roles.current_page - halfDisplayedPages);
            let endPage = Math.min(this.roles.last_page, startPage + totalDisplayedPages - 1);

            if (endPage - startPage + 1 < totalDisplayedPages) {
                startPage = Math.max(1, endPage - totalDisplayedPages + 1);
            }

            this.displayedPages = Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
        },
        changePageSize() {
            this.fetchRolesList(1);
        },
        updateTable() {
            this.fetchRolesList();
        },
        openModalEdit(id) {
            if (this.$refs.RefRolesModal) {
                this.$refs.RefRolesModal.openRolesModal(id);
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
                        const result = await Services.addNewInfo('role/delete', { id: id });
                        if (result.status) {
                            if (result.result[0].status) {
                                this.fetchRolesList();
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
                const result = await Services.addNewInfo('role/active', { id: id });
                if (result.status) {
                    if (result.result[0].status) {
                        this.fetchRolesList();
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