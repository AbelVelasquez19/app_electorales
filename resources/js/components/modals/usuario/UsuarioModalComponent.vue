<template>
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo persona</h5>
                    <h5 class="modal-title" v-else>Actualizar persona</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Dni: </label>
                                    <input type="text" placeholder="Dni" class="form-control" :class="errors!=null  && errors.document_number ? 'is-invalid' : '' " v-model="user.document_number">
                                    <span v-if="errors!=null  && errors.document_number" class="text-danger">{{ errors.document_number[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Nombres: </label>
                                    <input type="text" placeholder="Nombres" class="form-control" :class="errors!=null  && errors.name ? 'is-invalid' : '' " v-model="user.name">
                                    <span v-if="errors!=null  && errors.name" class="text-danger">{{ errors.name[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Apellido paterno: </label>
                                    <input type="text" placeholder="Nombres" class="form-control" :class="errors!=null  && errors.last_name ? 'is-invalid' : '' " v-model="user.last_name">
                                    <span v-if="errors!=null  && errors.last_name" class="text-danger">{{ errors.last_name[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Apellido materno: </label>
                                    <input type="text" placeholder="Apellidos" class="form-control" :class="errors!=null  && errors.mother_last_name ? 'is-invalid' : '' " v-model="user.mother_last_name">
                                    <span v-if="errors!=null  && errors.mother_last_name" class="text-danger">{{ errors.mother_last_name[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Usuario: </label>
                                    <input type="text" placeholder="Usuario" class="form-control" :class="errors!=null  && errors.user_name ? 'is-invalid' : '' " v-model="user.user_name">
                                    <span v-if="errors!=null  && errors.user_name" class="text-danger">{{ errors.user_name[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Contraseña: </label>
                                    <input type="password" placeholder="Contraseña" class="form-control" :class="errors!=null  && errors.password ? 'is-invalid' : '' " v-model="user.password">
                                    <span v-if="errors!=null  && errors.password" class="text-danger">{{ errors.password[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="me-1">
                                <div class="dataTables_filter">
                                    <label>Repetir contraseña: </label>
                                    <input type="password" placeholder="Repetir contraseña" class="form-control" :class="errors!=null  && errors.password_confirmation ? 'is-invalid' : '' " v-model="user.password_confirmation">
                                    <span v-if="errors!=null  && errors.password_confirmation" class="text-danger">{{ errors.password_confirmation[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label>Perfil: </label>
                            <select class="form-select" v-model="user.profile_id" :class="errors!=null  && errors.profile_id ? 'is-invalid' : '' ">
                                <option value="" selected disabled>--seleccionar--</option>
                                <option v-for="profile in profiles" :key="profile.id" :value="profile.id">{{ profile.name }}</option>
                            </select>
                            <span v-if="errors!=null  && errors.profile_id" class="text-danger">{{ errors.profile_id[0] }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewUser()" v-if="option"> Guardar </button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateUser()" v-else> Actualizar </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closeUserModal()"> Cerrar </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import UserService from '../../../services/services';
export default {
    data() {
        return {
            user:{  
                id:0,
                document_number:'77777777',
                person_id:0,
                name:'prueba',
                last_name:'prueba',
                mother_last_name:'prueba',
                user_name:'admin',
                password:'12345678',
                password_confirmation:'12345678',
                profile_id:''
            },
            errors: null,
            loading:false,
            option:true,
            profiles:{}
        }
    },
    mounted(){
        this.getListProfile();
    },
    methods: {
        async openUserModal(id){
            $("#userModal").modal("show");
            if(id!=0){
                this.option=false
                try {
                    const result = await UserService.getShowInfo('user/show',id);
                    this.user={  
                        id:id,
                        document_number:result.document_number,
                        name:result.person_name,
                        person_id:result.person_id,
                        last_name:result.last_name,
                        mother_last_name:result.mother_last_name,
                        user_name:result.user_name,
                        password:'',
                        password_confirmation:'',
                        profile_id:result.profile_id
                    }
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        closeUserModal(){
            this.user.id=0;
            $("#userModal").modal("hide");
            this.clearInput();
            this.errors=null;
        },
        async getListProfile(){
            try {
                const profile = await UserService.getAll('user/profile');
                this.profiles = profile;
            } catch (error) {
                return error;
            }
        },
        async addNewUser(){
            this.errors= null;
            try {
                const result = await UserService.addNewInfo('user/add',this.user);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#userModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                    }else{
                        this.$toast.error(result.result[0].message);
                    }
                }else{
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },
        async updateUser(){
            this.errors= null;
            try {
                const result = await UserService.addNewInfo('user/update',this.user);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#userModal").modal("hide");
                        this.$toast.success(result.result[0].message);
                        this.$emit('data-add');
                    }else{
                        this.$toast.error(result.result[0].message);
                    }
                }else{
                    this.errors = result.result;
                }
            } catch (error) {
                return error;
            }
        },
        clearInput(){
            this.user={  
                id:0,
                document_number:'',
                person_id:0,
                name:'',
                last_name:'',
                mother_last_name:'',
                user_name:'',
                password:'',
                password_confirmation:'',
                profile_id:''
            }
        }
    },
}
</script>