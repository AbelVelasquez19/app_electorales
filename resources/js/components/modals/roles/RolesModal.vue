<template>
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo rol</h5>
                    <h5 class="modal-title" v-else>Actualizar rol</h5>
                </div>
                <div class="modal-body p-0">
                    <fieldset>
                        <legend>Agregar Menú Principal</legend>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="me-1 d-flex justify-content-center flex-column align-item-center mt-2">
                                    <label for="sex" class="form-label text-center">Seleccionar</label>
                                    <div class="d-flex justify-content-center">
                                        <label class="switch switch-outline-primary">
                                            <input type="radio" class="switch-input" name="status_children" value="1"
                                                v-model="role.status_children">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            <span class="switch-label">No tiene Sub Menú</span>
                                        </label>

                                        <label class="switch switch-outline-primary">
                                            <input type="radio" class="switch-input" name="status_children" value="0"
                                                v-model="role.status_children">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            <span class="switch-label">Tiene Sub Menu</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Url: </label>
                                        <input type="text" placeholder="Nombre" class="form-control" :disabled="role.status_children==0" v-model="role.url">
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
                    <fieldset v-show="role.status_children == 0 && option">
                        <legend>Agregar Sub Menú</legend>
                        <div class="row mt-2">
                            <div class="col-md-2 mb-2 offset-10 text-end">
                                <button class="btn btn-primary btn-sm" @click="addSubRole()">Agregar</button>
                            </div>
                            <div class="col-md-12">
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
                                        <tr v-for="(subRole, index) in subRoles" :key="index">
                                            <td class="pt-1 pb-0 text-center">{{index+1}}</td>
                                            <td class="pt-1 pb-0 text-center">{{subRole.url}}</td>
                                            <td class="pt-1 pb-0 text-center">{{subRole.name}}</td>
                                            <td class="pt-1 pb-0 text-center">{{subRole.order_sub_role}}</td>
                                            <td class="pt-1 pb-0 text-center">
                                                <button  class="btn btn-danger btn-sm p-2" @click="deleteItem(index)"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewRole()" v-if="option"> Guardar</button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateRole()" v-else> Actualizar </button>

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
            role: {
                id: 0,
                url: '',
                name: '',
                description: '',
                order_role: '',
                status_children: 1
            },
            subRoles:[],
            errors: null,
            loading: false,
            option: true,
        }
    },
    mounted() {
       
    },
    methods: {
        async openRolesModal(id) {
            $("#roleModal").modal("show");
            if(id!=0){
                this.option=false
                try {
                    const result = await Services.getShowInfo('role/show',id);
                    let responde = result[0];
                    console.log(responde)
                    this.role={  
                        id:responde.roles_id,
                        name:responde.roles_name,
                        description:responde.roles_description,
                        order_role:responde.roles_order_role,
                        status_children:responde.roles_status_children,
                    }
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        closeRoleModal() {
            this.role.id = 0;
            $("#roleModal").modal("hide");
            this.clearInput();
            this.errors = null;
        },
        async addNewRole() {
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
                subRole: this.subRoles
            }
            try {
                const result = await Services.addNewInfo('role/add', obj);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#roleModal").modal("hide");
                        this.subRoles=[];
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                        this.role.status_children=1;
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
                }
            } catch (error) {
                return error;
            }
        },
        async updateRole() {
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
            }
            try {
                const result = await Services.addNewInfo('role/update', obj);
                if (result.status) {
                    if (result.result[0].status) {
                        this.clearInput();
                        $("#roleModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                        this.role.status_children=1;
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
                }
            } catch (error) {
                return error;
            }
        },
        addSubRole(){
            if(this.role.name==""){
                this.$toast.error("Campo nombre es obligatorio");
                return;
            }
            if(this.role.order_role==""){
                this.$toast.error("Campo número de orden es obligatorio");
                return;
            }
            if(this.role.description==""){
                this.$toast.error("Campo descripcion es obligatorio");
                return;
            }
            let subRole = {
                id: this.role.id,
                url: this.role.url,
                name: this.role.name,
                description: this.role.description,
                order_sub_role: this.role.order_role,
                status_children: 2,
            }
            this.subRoles.push(subRole);
            this.clearInput();
        },
        deleteItem(id){
            this.subRoles.splice(id, 1);
        },
        clearInput() {
            this.role.id= 0;
            this.role.url= '';
            this.role.name= '';
            this.role.description= '';
            this.role.order_role= '';
        }
    },
}
</script>