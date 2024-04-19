<template>
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" v-if="option">Agregar nuevo perfil</h5>
                    <h5 class="modal-title" v-else>Actualizar perfil</h5>
                </div>
                <div class="modal-body p-0">
                    <fieldset>
                        <legend>Agregar perfil</legend>
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Nombre: </label>
                                        <input type="text" placeholder="Nombre" class="form-control" :class="errors!=null  && errors.name ? 'is-invalid' : '' " v-model="profile.name">
                                        <span v-if="errors!=null  && errors.name" class="text-danger">{{ errors.name[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Descripcion: </label>
                                        <textarea class="form-control" :class="errors!=null  && errors.description ? 'is-invalid' : '' " rows="3" v-model="profile.description"></textarea>
                                        <!-- <input type="text" placeholder="Descripcion" class="form-control" :class="errors!=null  && errors.description ? 'is-invalid' : '' " v-model="profile.description"> -->
                                        <span v-if="errors!=null  && errors.description" class="text-danger">{{ errors.description[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewProfile()" v-if="option"> Guardar </button>
                    <button type="button" class="btn btn-primary" @click.prevent="updateProfile()" v-else> Actualizar </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closeProfileModal()"> Cerrar </button>
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
            profile:{  
                id:0,
                name:'',
                description:'',
            },
            errors: null,
            loading:false,
            option:true,
        }
    },
    mounted(){
        
    },
    methods: {
        async openProfileModal(id){
            $("#profileModal").modal("show");
            if(id!=0){
                this.option=false
                try {
                    const result = await Services.getShowInfo('profile/show',id);
                    this.profile={  
                        id:id,
                        name:result.nombre,
                        description:result.descripcion,
                    }
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        closeProfileModal(){
            this.profile.id=0;
            $("#profileModal").modal("hide");
            this.clearInput();
            this.errors=null;
        },
        async addNewProfile(){
            this.errors= null;
            try {
                const result = await Services.addNewInfo('profile/add',this.profile);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#profileModal").modal("hide");
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
        async updateProfile(){
            this.errors= null;
            try {
                const result = await Services.addNewInfo('profile/update',this.profile);
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#profileModal").modal("hide");
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
            this.profile={  
                id:0,
                name:'',
                description:'',
            }
        }
    },
}
</script>