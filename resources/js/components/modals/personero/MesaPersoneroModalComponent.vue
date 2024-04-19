<template>
    <div class="modal fade" id="mesaPersoneroSaveModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option"> MESA - DEL PERSONERO</h5>
                    <h5 class="modal-title" v-else>Agregar MESA </h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del MESA - PERSONERO | </legend>

                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="centro.provincia_id"
                                    :class="errors != null && errors.provincia_id ? 'is-invalid' : ''"
                                    @change="getDistrictItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="province in provinces" :key="province.id" :value="province.id">{{
                        province.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.provincia_id" class="text-danger">{{
                        errors.provincia_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Distrito: </label>
                                <select class="form-select" v-model="centro.distrito_id"
                                    :class="errors != null && errors.distrito_id ? 'is-invalid' : ''"
                                    @change="getCorregimientoItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="distrito in districts" :key="distrito.id" :value="distrito.id">{{
                        distrito.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.distrito_id" class="text-danger">{{
                        errors.distrito_id[0] }}</span>
                            </div>
                            <!-- <div class="col-md-4">
                                <label>Corregimientos: </label>
                                <select class="form-select" v-model="centro.corregimiento_id" :class="errors!=null  && errors.corregimiento_id ? 'is-invalid' : '' ">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="corregt in corrigement" :key="corregt.id" :value="corregt.id">{{ corregt.nombre }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.corregimiento_id" class="text-danger">{{ errors.corregimiento_id[0] }}</span>
                            </div> -->

                            <div class="col-md-4">
                                <label>Centros de votación: </label>
                                <select class="form-select" v-model="centro.id"
                                    :class="errors != null && errors.corregimiento_id ? 'is-invalid' : ''"
                                    @change="getMesaCentroItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="centro in centros" :key="centro.id" :value="centro.id">{{
                        centro.nombre }}</option>
                                </select>
                                <span v-if="errors != null && errors.centro_votacion_id" class="text-danger">{{
                        errors.centro_votacion_id[0] }}</span>
                            </div>
                        </div>



                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="row">

                                    <div class="col-md-12">
                                        <label>MESAS: </label>
                                        <select id="personero_id"
                                            class="select2 form-select form-select-sm form-select-lg"
                                            v-model="personero.mesa_id"
                                            :class="errors != null && errors.mesa_id ? 'is-invalid' : ''">
                                            <option value="" selected disabled>--seleccionar--</option>
                                            <option v-for="mesa in mesas" :key="mesa.id" :value="mesa.id">
                                                {{ mesa.nombre }}</option>
                                        </select>
                                        <!-- <v-select :options="mesas" v-model="mesa.personero_id" label="persona_nombre" index="id">
¿                                        </v-select> -->
                                        <!-- <v-select :options="mesas" v-model="mesa.personero_id"
                                            label="persona_nombre" item-value="id" >
                                        </v-select> -->
                                        <span v-if="errors != null && errors.mesa_id" class="text-danger">{{
                        errors.mesa_id[0] }}</span>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <button type="button" class="btn btn-primary" @click.prevent="addNewUser()">
                                            Agregar Mesa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>LISTA DE PERSONEROS EN MESA: </legend>
                        <div class="row">



                            <div class="card mb-1 p-3">
                                <div class="d-flex justify-content-between align-items-center row mt-75">
                                     <div
                                        class="col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                                        <div class="dataTables_length" id="DataTables_Table_0_length">
                                            <label>
                                                Show
                                                <select @change="changePageSize" class="form-select" v-model="pageSize">
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
<!--
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
                                    </div> -->
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
                                                        <!-- <button class="btn btn-primary btn-sm"
                                                                @click.prevent="openModalEdit(item.id)"><i
                                                                    class="fa-solid fa-pen-to-square"></i></button> -->




                                                        <button v-if="item.estado_mesa_personero == 1" class="btn btn-danger btn-sm"
                                                            @click.prevent="deleteItem(item.id_mesa_personero)"><i
                                                                class="fa-solid fa-trash-can"></i></button>
                                                        <button v-if="item.estado_mesa_personero != 1" class="btn btn-success btn-sm"
                                                            @click.prevent="activeItem(item.id_mesa_personero)"><i
                                                                class="fa-solid fa-circle-check"></i></button>



                                                    </td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12 mt-1 d-flex justify-content-end align-items-center ">
                                        <nav aria-label="Page navigation example" v-if="personeros.last_page > 1">
                                            <ul class="pagination justify-content-center align-items-center">
                                                <li class="page-item"
                                                    :class="{ 'disabled': personeros.current_page === 1 }">
                                                    <a class="page-link" @click.prevent="fetchUserList(1)"><i
                                                            class="fa fa-fast-backward font-medium-3"
                                                            aria-hidden="true"></i></a>
                                                </li>
                                                <li class="page-item "
                                                    :class="{ 'disabled': personeros.current_page === 1 }">
                                                    <a class="page-link"
                                                        @click.prevent="fetchUserList(personeros.current_page - 1)"><i
                                                            class="fa-solid fa-backward-step"></i></a>
                                                </li>
                                                <li class="page-item" v-for="pageNumber in displayedPages"
                                                    :key="pageNumber"
                                                    :class="{ 'active': personeros.current_page === pageNumber }">
                                                    <a class="page-link" @click.prevent="fetchUserList(pageNumber)"
                                                        href="#">{{
                        pageNumber
                    }}</a>
                                                </li>
                                                <li class="page-item"
                                                    :class="{ 'disabled': personeros.current_page === personeros.last_page }">
                                                    <a class="page-link" href="#"
                                                        @click.prevent="fetchUserList(personeros.current_page + 1)"><i
                                                            class="fa fa-step-forward font-medium-3"
                                                            aria-hidden="true"></i></a>
                                                </li>
                                                <li class="page-item"
                                                    :class="{ 'disabled': personeros.current_page === personeros.last_page }">
                                                    <a class="page-link" href="#"
                                                        @click.prevent="fetchUserList(personeros.last_page)"><i
                                                            class="fa fa-fast-forward font-medium-3"
                                                            aria-hidden="true"></i></a>
                                                </li>
                                            </ul>
                                        </nav>
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
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
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
            centros: {}

        }
    },
    mounted() {
        this.debouncedSearch = debounce(this.fetchUserList, 500);
        this.fetchUserList();

        // $('#personero_id').select2();
        // $('#personero_id').on('select2:select', () => {
        //         this.mesa.personero_id = $('#personero_id').val();
        //     });
    },
    methods: {
        async openMesaPersoneroModal(id) {
            console.log(id)
            $("#mesaPersoneroSaveModal").modal("show");
            this.getProvinces();
            this.getListCodigoPais();
            this.getTipoDocumentos();
            this.getMesas(id);
            this.getCentros();
            this.personero.id = id


            if (id != 0) {
                this.option = false
                try {
                    // this.personeros.id = id
                    // const result = await Services.getShowInfo('mesa/show', id);
                    // console.log(result)

                    // this.mesa = {
                    //     id: result.mesa_id,
                    //     nombre: result.nombre,
                    //     numero: result.numero,
                    //     centro_votacion_id: result.centro_votacion_id,
                    //     cantidad_votantes: result.cantidad_votantes,
                    // }




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

        /*async getMesasFilterProvincia(){
            try {
                const result = await Services.getAll(`personero/list-mesas-personero?persona_id=` + this.personero.id);
                this.mesas = result
            } catch (error) {
                return error;
            }
        },*/
        getMesaCentroItem() {
            this.getMesasCentro(this.centro.id);
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


        async getCentros() {
            try {

                const result_centros = await Services.getAll(`personero/list-centros-votacion`);
                this.centros = result_centros;

                console.log('peepepewpep')
            } catch (error) {
                return error;
            }
        },

        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/district', province_id);
                this.districts = result


                const result_centros = await Services.getAll(`personero/list-centros-votacion?persona_id=` + this.personero.id + '&provincia_id=' + province_id);
                this.centros = result_centros


                const result_mesas = await Services.getAll(`personero/list-mesas-personero?persona_id=` + this.personero.id + '&provincia_id=' + province_id);
                this.mesas = result_mesas


                console.log('peepepewpep')
            } catch (error) {
                return error;
            }
        },
        async getCorregiment(distric_id) {
            try {
                // const result = await Services.getShowInfo('ubigeus/corregimient', distric_id);
                // this.corrigement = result
                const result_centros = await Services.getAll(`personero/list-centros-votacion?persona_id=` + this.personero.id + '&distrito_id=' + province_id);
                this.centros = result_centros


                const result_mesas_dis = await Services.getAll(`personero/list-mesas-personero?persona_id=` + this.personero.id + '&distrito_id=' + distric_id);
                this.mesas = result_mesas_dis
            } catch (error) {
                return error;
            }
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
        async addNewUser() {
            this.errors = null;
            try {
                const result = await Services.addNewInfo('personero/add-mesa-personero', this.personero);
                if (result.status) {
                    if (result.result[0].status) {
                        // this.clearInput();
                        // $("#mesaPersoneroSaveModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        // this.$emit('data-add');
                        // this.getMesas();
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
                const result = await Services.addNewInfo('personero/delete-mesa', { id: id });
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
