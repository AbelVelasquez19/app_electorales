<template>
    <div class="modal fade" id="actaModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp">
            <div class="modal-content p-3">
                <div class="modal-body">
                    <fieldset>
                        <legend>Registrar Acta</legend>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Mesa</label>
                                <select class="form-select" v-model="mesa_id">
                                    <option value="" disabled selected>--Seleccionar--</option>
                                    <option v-for="(items,index) in actas.mesa" :key="index" :value="items.id">{{ items.nombre }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Centro de votación</label>
                               <select class="form-select">
                                    <option v-for="(items,index) in actas.centroVotacion" :key="index" :value="items.id">{{items.nombre}}</option>
                               </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Supervisor</label>
                                <select class="form-control">
                                    <option v-for="(items,index) in actas.supervisor" :key="index" :value="items.user_id">{{items.supervisor}}</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Personero</label>
                                <select class="form-control">
                                    <option v-for="(items,index) in actas.personero" :key="index" :value="items.user_id">{{items.personero}}</option>
                               </select>
                            </div>
                        </div>
                        <div class="row mt-1 d-flex justify-content-center align-items-center" v-for="(partido,index) in partidos" :key="partido.id">
                            <div class="col-md-2">
                                <img :src="partido.logo" alt="" class="rounded" width="70px" height="70px">
                            </div>
                            <div class="col-md-4 d-flex justify-content-center align-items-center">
                                <input disabled type="text" class="form-control" :value="partido.nombre">
                            </div>
                            <div class="col-md-2 d-flex justify-content-center align-items-center">
                                <input type="text" class="form-control" v-model.number="partidos[index].votos" @input="sumarTotalVotos">
                            </div>
                        </div>
                        <div class="row mt-1 d-flex justify-content-center align-items-center">
                            <div class="col-md-2">
                               
                            </div>
                            <div class="col-md-4 d-flex justify-content-end align-items-center">
                                <h4 class="d-flex justify-content-end align-items-center mt-2">Total</h4>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center align-items-center">
                                <input type="text" class="form-control" :value="total">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="addNewActa()"> Guardar </button>
                    <button type="button" class="btn btn-secondary" @click.prevent="closeUserModal()"> Cerrar </button>
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
            mesa_id:'',
            centro_votacion_id:null,
            supervirsor_id:null,
            personero_id:null,

            userId:null,
            userName:'',
            errors: null,
            loading:false,
            option:true,
            partidos:{},
            actas:{},
            total:0,
            debouncedSearch: null,
        }
    },
    mounted(){
        /* this.debouncedSearch = debounce(this.getPersona, 500); */
       /*  this.getListProfile(); */
       this.getListPartidoPoliticos();
    },
    methods: {
        async openModalActa(id){
            this.userId = id;
            $("#actaModal").modal("show");
            this.getPersonero();
            if(id!=0){
                this.option=false
                try {
                    const result = await Services.getShowInfo('actas/user-name',id);
                    this.userName=result.nombre+' '+result.apellido_paterno+' '+result.apellido_materno;
                } catch (error) {
                    return error;
                }
            }else{
                this.option=true;
            }
        },
        closeUserModal(){
            $("#actaModal").modal("hide");
           /*  this.clearInput(); */
            this.errors=null;
        },
        async getListPartidoPoliticos(){
            try {
                const partido = await Services.getAll('actas/partido-politico');
                this.partidos = partido.map(partido=>({...partido,votos:0}));
            } catch (error) {
                return error;
            }
        },
        async getPersonero(){
            try {
                const actas = await Services.getAll('actas/personero');
                this.actas = actas;
                this.centro_votacion_id=actas.centroVotacion[0].id
                this.supervirsor_id=actas.supervisor[0].user_id
                this.personero_id=actas.personero[0].user_id
            } catch (error) {
                return error;
            }
        },
        sumarTotalVotos() {
            this.total = this.partidos.reduce((total, partido) => {
                return total + (partido.votos ? parseInt(partido.votos) : 0);
            }, 0);
        },
        async addNewActa(){
            this.errors= null;
            let obj  = {
                mesa_id:this.mesa_id,
                centro_votacion_id:this.centro_votacion_id,
                supervirsor_id:this.supervirsor_id,
                personero_id:this.personero_id,
                total:this.total,
                partidos:this.partidos
            }
            try {
                const result = await Services.addNewInfo('actas/add',obj);
                console.log(result)
                if(result.status){
                    if(result.result[0].status){
                        this.clearInput();
                        $("#actaModal").modal("hide");
                        this.getListPartidoPoliticos();
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
            this.mesa_id=''
            this.total = 0;
        }

        /* async getPersona() {
            try {
                const result = await Services.getShowInfo('user/person',this.user.document_number);
                if(result.status){
                    this.user={  
                        document_number:result.data.numero_documento,
                        person_id:result.data.id,
                        name:result.data.nombre+' '+result.data.apellido_paterno+' '+result.data.apellido_materno,
                        user_name:result.data.email,
                        password:'12345678',
                        password_confirmation:'12345678',
                        celular:result.data.codigo_pais+result.data.celular,
                        profile_id:'',
                    }
                }
            } catch (error) {
                console.log(error)
            }
        },
        async getListProfile(){
            try {
                const profile = await Services.getAll('user/profile');
                this.profiles = profile;
            } catch (error) {
                return error;
            }
        },
        async addNewUser(){
            this.errors= null;
            try {
                const result = await Services.addNewInfo('user/add',this.user);
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
                const result = await Services.addNewInfo('user/update',this.user);
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
                user_name:'',
                password:'',
                password_confirmation:'',
                profile_id:'',
                celular:''
            }
        } */
    },
}
</script>