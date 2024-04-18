<template>
    <div class="modal fade" id="PersoneroModel" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo personero</h5>
                    <h5 class="modal-title" v-else>Actualizar personero</h5>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <legend>Ingresar informacion del personero</legend>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Dni: </label>
                                        <input type="text" placeholder="Dni" class="form-control" :class="errors!=null  && errors.document_number ? 'is-invalid' : '' " v-model="user.document_number"  @input="debouncedSearch">
                                        <span v-if="errors!=null  && errors.document_number" class="text-danger">{{ errors.document_number[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombres: </label>
                                        <input disabled type="text" placeholder="Nombres" class="form-control" :class="errors!=null  && errors.name ? 'is-invalid' : '' " v-model="user.name">
                                        <span v-if="errors!=null  && errors.name" class="text-danger">{{ errors.name[0] }}</span>
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
                            <div class="col-md-4">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Celular </label>
                                        <input disabled type="text" placeholder="Numero de celular" class="form-control" :class="errors!=null  && errors.celular ? 'is-invalid' : '' " v-model="user.celular">
                                        <span v-if="errors!=null  && errors.celular" class="text-danger">{{ errors.celular[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewUser()" v-if="option"> Guardar </button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateUser()" v-else> Actualizar </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closePersoneroModel()"> Cerrar </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Services from '../../../services/services';
import { debounce } from 'lodash';
export default {
    data() {
        return {
            user:{  
                id:0,
                document_number:'',
                person_id:0,
                name:'',
                user_name:'',
                password:'',
                password_confirmation:'',
                profile_id:3,
                celular:''
            },
            errors: null,
            loading:false,
            option:true,
            profiles:{},
            debouncedSearch: null,
        }
    },
    mounted(){
        this.debouncedSearch = debounce(this.getPersona, 500);
    },
    methods: {
        async openPersoneroModal(id){
            console.log(id)
            $("#PersoneroModel").modal("show");
            if(id!=0){
                this.option=false
                try {
                    const result = await Services.getShowInfo('personero/show',id);
                    this.user={  
                        id:id,
                        document_number:result.numero_documento,
                        name:result.nombre+' '+result.apellido_paterno+' '+result.apellido_materno,
                        person_id:result.personas_id,
                        user_name:result.email,
                        profile_id:3,
                        celular:result.codigo_pais+result.celular
                    }
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        async getPersona() {
            try {
                const result = await Services.getShowInfo('personero/person',this.user.document_number);
                if(result.status){
                    this.user={  
                        document_number:result.data.numero_documento,
                        person_id:result.data.id,
                        name:result.data.nombre+' '+result.data.apellido_paterno+' '+result.data.apellido_materno,
                        user_name:result.data.email,
                        celular:result.data.codigo_pais+result.data.celular,
                        profile_id:3,
                    }
                }
            } catch (error) {
                console.log(error)
            }
        },
        closePersoneroModel(){
            this.user.id=0;
            $("#PersoneroModel").modal("hide");
            this.clearInput();
            this.errors=null;
        },
        async addNewUser(){
            this.errors= null;
            try {
                const result = await Services.addNewInfo('personero/add',this.user);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#PersoneroModel").modal("hide");
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
                const result = await Services.addNewInfo('personero/update',this.user);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#PersoneroModel").modal("hide");
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
                user_name:'',
                password:'',
                password_confirmation:'',
                profile_id:'',
                celular:''
            }
        }
    },
}
</script>