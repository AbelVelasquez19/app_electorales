<template>
    <div class="modal fade" id="roleSubMenuModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title">Listado de Sub Menus</h5>
                </div>
                <div class="modal-body p-0">
                    <fieldset class="mb-2">
                        <legend>Agregar sub menu</legend>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Url: </label>
                                        <input type="text" placeholder="Nombre" class="form-control" :class="errors != null && errors.url ? 'is-invalid' : ''" v-model="role.url">
                                        <span v-if="errors != null && errors.url" class="text-danger">{{ errors.url[0]}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombre: </label>
                                        <input type="text" placeholder="Nombre" class="form-control" :class="errors != null && errors.name ? 'is-invalid' : ''" v-model="role.name">
                                        <span v-if="errors != null && errors.name" class="text-danger">{{ errors.name[0]
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Número de Orden: </label>
                                        <input type="text" placeholder="Numero de orden" class="form-control"
                                            :class="errors != null && errors.order_role ? 'is-invalid' : ''"
                                            v-model="role.order_role">
                                        <span v-if="errors != null && errors.order_role" class="text-danger">{{
                                            errors.order_role[0]
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Descripcion: </label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"
                                            v-model="role.description"></textarea>
                                        <span v-if="errors != null && errors.description" class="text-danger">{{
                                            errors.description[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row mb-2">
                       <div class="col-md-2 offset-10">
                            <button class="btn btn-primary" @click.prevent="addNewSubRole()"><i class="fa-solid fa-plus"></i> Guardar</button>
                       </div>
                    </div>
                    <table class="table table-hover table-sm">
                        <thead class="bg-label-vimeo">
                            <tr>
                                <th class="pt-1 pb-0 text-center font-monospace" style="width: 5%;">Items</th>
                                <th class="pt-1 pb-0 font-monospace">Url</th>
                                <th class="pt-1 pb-0 font-monospace">Nombre</th>
                                <th class="pt-1 pb-0 font-monospace">Orden</th>
                                <th class="pt-1 pb-0 text-center font-monospace" style="width: 6%;">Opción</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="(subRole, index) in subRoles" :key="subRole.roles_id">
                                <td class="pt-1 pb-0 text-center">{{ index+1 }}</td>
                                <td class="pt-1 pb-0">{{ subRole.roles_url }}</td>
                                <td class="pt-1 pb-0">{{ subRole.roles_name }}</td>
                                <td class="pt-1 pb-0 text-center">{{ subRole.roles_order_sub_role }}</td>
                                <td class="pt-1 pb-0 text-center"><button class="btn btn-danger btn-sm" @click="deleteSubRole(subRole.roles_id)"><i class="fa-solid fa-trash-can"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click.prevent="closeRoleModal()"> Cerrar </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Services from '../../../services/services';
export default {
    data() {
        return {
           subRoles:{},
           role_id:0,
           errors: null,
           role: {
                id: 0,
                url: '',
                name: '',
                description: '',
                order_role: '',
                status_children: 2
            },
        }
    },
    mounted() {

    },
    methods: {
        async openModalSubMenu(id) {
            this.role_id = id;
            this.role.id = id;
            $("#roleSubMenuModal").modal("show");
            this.getListSubRole(id);
        },
        closeRoleModal() {
            $("#roleSubMenuModal").modal("hide");
        },
        async getListSubRole(id){
            try {
                const result = await Services.getShowInfo('role/sub-role',id);
                this.subRoles = result;
            } catch (error) {
                return error;
            }
        },
        async deleteSubRole(id) {
            try {
                const result = await Services.addNewInfo('role/delete/sub-role', { id: id,role_id:this.role_id });
                if (result.status) {
                    if (result.result[0].status) {
                        this.$swal({
                            title: "Eliminado!",
                            text: result.result[0].message,
                            icon: "success"
                        });
                        $("#roleSubMenuModal").modal("hide");
                        this.$emit('update-table');
                    }
                } else {
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },
        async addNewSubRole() {
            this.errors = null;
            let obj = {
                role: {
                    id: this.role.id,
                    url: this.role.url,
                    name: this.role.name,
                    description: this.role.description,
                    order_role: this.role.order_role,
                    status_children: this.role.status_children
                },
                role_id: this.role_id
            }
            try {
                const result = await Services.addNewInfo('role/sub-role/add', obj);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $('#module_id_s').val('');
                        this.getListSubRole(this.role_id);
                        this.$toast.success(result.result[0].message);
                    } else {
                        this.$toast.error(result.result[0].message);
                    }
                } else {
                    let dataError = {
                        name:result.result['role.name'],
                        description:result.result['role.description'],
                        order_role:result.result['role.order_role'],
                    }
                    this.errors = dataError;
                    this.$toast.error(result.result[0].message);
                }
            } catch (error) {
                return error;
            }
        },
        clearInput(){
            this.role= {
                id:0,
                url: '',
                name: '',
                description: '',
                order_role: '',
                status_children: 2
            }
        }
    },
}
</script>