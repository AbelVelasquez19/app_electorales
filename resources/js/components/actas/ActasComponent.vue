<template>
    <div class="row">
        <div class="col-md-12">
            <div class="row breadcrumbs-top mb-1">
                <div class="col-12">
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="user">Actas</a>
                            </li>
                            <li class="breadcrumb-item active">listado de actas
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
                                        @click.prevent="openModal()"><span>Nuevo Acta</span></button>
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
                                    <th class="text-center font-monospace" style="width: 8%;">Numero de Mesa</th>
                                    <th class="font-monospace">Total de votos</th>
                                    <th class="font-monospace">Personero</th>
                                    <th class="font-monospace">Centro de votación</th>
                                    <th class="text-center font-monospace" style="width: 6%;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr v-for="(item,index) in actas.data" :key="item.id">
                                    <td class="text-center">
                                        {{ index +1 }}
                                    </td>
                                    <td class="text-center font-monospace">{{ item.nombre }}</td>
                                    <td class="font-monospace">{{ item.cantidad_votantes }} </td>
                                    <td class="font-monospace">{{item.personero  }}</td>
                                    <td class="font-monospace">{{item.nombre_centro_votacion  }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" @click.prevent="openModalEdit(item.id)"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                        <nav aria-label="Page navigation example" v-if="actas.last_page > 1">
                            <ul class="pagination justify-content-center align-items-center">
                                <li class="page-item" :class="{ 'disabled': actas.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchList(1)"><i
                                            class="fa fa-fast-backward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item " :class="{ 'disabled': actas.current_page === 1 }">
                                    <a class="page-link" @click.prevent="fetchList(actas.current_page - 1)"><i
                                            class="fa-solid fa-backward-step"></i></a>
                                </li>
                                <li class="page-item" v-for="pageNumber in displayedPages" :key="pageNumber"
                                    :class="{ 'active': actas.current_page === pageNumber }">
                                    <a class="page-link" @click.prevent="fetchList(pageNumber)" href="#">{{ pageNumber
                                    }}</a>
                                </li>
                                <li class="page-item" :class="{ 'disabled': actas.current_page === actas.last_page }">
                                    <a class="page-link" href="#" @click.prevent="fetchList(actas.current_page + 1)"><i
                                            class="fa fa-step-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                                <li class="page-item" :class="{ 'disabled': actas.current_page === actas.last_page }">
                                    <a class="page-link" href="#" @click.prevent="fetchList(actas.last_page)"><i
                                            class="fa fa-fast-forward font-medium-3" aria-hidden="true"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <ActaModalComponent ref="RefActaModalComponent" @data-add="updateTable" />
    </div>
</template>


<script>
import Services from '../../services/services';
import ActaModalComponent from '../modals/actas/ActaModalComponent.vue';
import { debounce } from 'lodash';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        userId:{
            type:Number,
            default:0
        },
    },
    components: {
        ActaModalComponent
    },
    data() {
        return {
            users: {},
            displayedPages: [],
            error: null,
            searchQuery: '',
            debouncedSearch: null,
            pageSize: 15,
            errors: null,
            actas:{}
        }
    },
    computed: {
        totalPages() {
            return Math.ceil(this.actas.total / this.pageSize);
        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchList, 500);
        this.fetchList();
        
    },
    methods: {  
        openModal() {
            if (this.$refs.RefActaModalComponent) {
                this.$refs.RefActaModalComponent.openModalActa(this.userId);
            }
        },
        async fetchList(page = 1) {
            try {
                const result = await Services.getListInfo(this.searchQuery, `actas/list-actas?page=${page}`, this.pageSize);
                this.actas = result[0];
                this.updateDisplayedPages();
            } catch (error) {
                console.log(error)
            }
        },
        updateDisplayedPages() {
            const totalDisplayedPages = 6;
            const halfDisplayedPages = Math.floor(totalDisplayedPages / 2);

            let startPage = Math.max(1, this.actas.current_page - halfDisplayedPages);
            let endPage = Math.min(this.actas.last_page, startPage + totalDisplayedPages - 1);

            if (endPage - startPage + 1 < totalDisplayedPages) {
                startPage = Math.max(1, endPage - totalDisplayedPages + 1);
            }

            this.displayedPages = Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
        },
        changePageSize() {
            this.fetchList(1);
        },
        updateTable() {
            this.fetchList();
        },
        openModalEdit(id) {
            console.log(id)
            if (this.$refs.RefUserModal) {
                this.$refs.RefUserModal.openUserModal(id);
            }
        },
    },
}
</script>