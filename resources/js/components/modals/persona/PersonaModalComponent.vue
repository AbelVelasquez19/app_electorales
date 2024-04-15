<template>
    <div class="modal fade" id="personaModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo persona</h5>
                    <h5 class="modal-title" v-else>Actualizar persona</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del personal</legend>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Dni: </label>
                                        <input type="text" placeholder="Dni" class="form-control" :class="errors!=null  && errors.numero_documento ? 'is-invalid' : '' " v-model="user.numero_documento">
                                        <span v-if="errors!=null  && errors.numero_documento" class="text-danger">{{ errors.numero_documento[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombres: </label>
                                        <input type="text" placeholder="Nombres" class="form-control" :class="errors!=null  && errors.nombre ? 'is-invalid' : '' " v-model="user.nombre">
                                        <span v-if="errors!=null  && errors.nombre" class="text-danger">{{ errors.nombre[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Apellido paterno: </label>
                                        <input type="text" placeholder="Nombres" class="form-control" :class="errors!=null  && errors.apellido_paterno ? 'is-invalid' : '' " v-model="user.apellido_paterno">
                                        <span v-if="errors!=null  && errors.apellido_paterno" class="text-danger">{{ errors.apellido_paterno[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Apellido materno: </label>
                                        <input type="text" placeholder="Apellidos" class="form-control" :class="errors!=null  && errors.apellido_materno ? 'is-invalid' : '' " v-model="user.apellido_materno">
                                        <span v-if="errors!=null  && errors.apellido_materno" class="text-danger">{{ errors.apellido_materno[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>sexo: </label>
                                        <select class="form-select" v-model="user.sexo" :class="errors!=null  && errors.sexo ? 'is-invalid' : '' ">
                                            <option value="1">Masculino</option>
                                            <option value="2">Femenino</option>
                                        </select>
                                        <span v-if="errors!=null  && errors.sexo" class="text-danger">{{ errors.sexo[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Celular: </label>
                                        <input type="text" placeholder="Celular" class="form-control" :class="errors!=null  && errors.celular ? 'is-invalid' : '' " v-model="user.celular">
                                        <span v-if="errors!=null  && errors.celular" class="text-danger">{{ errors.celular[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Correo Electrónico: </label>
                                        <input type="email" placeholder="Correo Electrónico" class="form-control" :class="errors!=null  && errors.email ? 'is-invalid' : '' " v-model="user.email">
                                        <span v-if="errors!=null  && errors.email" class="text-danger">{{ errors.email[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Dirección fiscal: </label>
                                        <input type="text" placeholder="Dirección fiscal" class="form-control" :class="errors!=null  && errors.direccion ? 'is-invalid' : '' " v-model="user.direccion">
                                        <span v-if="errors!=null  && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label>Departamento: </label>
                                <select class="form-select" v-model="user.profile_id" :class="errors!=null  && errors.profile_id ? 'is-invalid' : '' ">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="profile in profiles" :key="profile.id" :value="profile.id">{{ profile.name }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.profile_id" class="text-danger">{{ errors.profile_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="user.profile_id" :class="errors!=null  && errors.profile_id ? 'is-invalid' : '' ">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="profile in profiles" :key="profile.id" :value="profile.id">{{ profile.name }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.profile_id" class="text-danger">{{ errors.profile_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Distrito: </label>
                                <select class="form-select" v-model="user.profile_id" :class="errors!=null  && errors.profile_id ? 'is-invalid' : '' ">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="profile in profiles" :key="profile.id" :value="profile.id">{{ profile.name }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.profile_id" class="text-danger">{{ errors.profile_id[0] }}</span>
                            </div>
                        </div>
                     </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewUser()" v-if="option"> Guardar </button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateUser()" v-else> Actualizar </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closepersonaModal()"> Cerrar </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import UserService from '../../../services/services';
import vSelect from 'vue-select';
export default {
    components:{
        'v-select': vSelect
    },
    data() {
        return {
            user:{  
                id:0,
                numero_documento:'77777777',
                nombre:'prueba',
                apellido_paterno:'prueba',
                apellido_materno:'prueba',
                sexo:1,
                celular:'',
                email:'',
                direccion:'',
            },
            errors: null,
            loading:false,
            option:true,
            profiles:{},
            options: [
                { label: 'Opción 1', value: 'opcion1' },
                { label: 'Opción 2', value: 'opcion2' },
                { label: 'Opción 3', value: 'opcion3' }
            ]
        }
    },
    mounted(){
        this.getListProfile();
    },
    methods: {
        async openpersonaModal(id){
            $("#personaModal").modal("show");
            if(id!=0){
                this.option=false
                try {
                    const result = await UserService.getShowInfo('user/show',id);
                   /*  this.user={  
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
                    } */
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        closepersonaModal(){
            this.user.id=0;
            $("#personaModal").modal("hide");
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
                        $("#personaModal").modal("hide");
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
                        $("#personaModal").modal("hide");
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
            /* this.user={  
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
            } */
        }
    },
}
</script>