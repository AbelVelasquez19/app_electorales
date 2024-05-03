<template>
    <div class="modal fade" id="personaModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp">
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
                                        <label>Numero Cedula: </label>
                                        <input type="text" placeholder="Numero Cedula" class="form-control" :class="errors!=null  && errors.numero_documento ? 'is-invalid' : '' " v-model="user.numero_documento">
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
                            <div class="col-md-2">
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
                            <div class="col-md-2">
                                <div class="me-1">
                                    <div class="dataTables_filter">
                                        <label>Codigo pais: </label>
                                        <select class="form-select" v-model="user.codigo_pais_id" :class="errors!=null  && errors.codigo_pais_id ? 'is-invalid' : '' ">
                                            <option value="" selected disabled>--Seleccionar--</option>
                                            <option value="1" v-for="item in codigoPais" :key="item.id" :value="item.codigo">{{ item.nombre}}(+{{ item.codigo }})</option>
                                        </select>
                                        
                                        <span v-if="errors!=null  && errors.codigo_pais_id" class="text-danger">{{ errors.codigo_pais_id[0] }}</span>
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
                                <select class="form-select" v-model="user.departaments_id" :class="errors!=null  && errors.departaments_id ? 'is-invalid' : '' " @change="getDepartamentItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="item in departaments" :key="item.id" :value="item.id">{{ item.nombre }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.departaments_id" class="text-danger">{{ errors.departaments_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Provincia: </label>
                                <select class="form-select" v-model="user.provincia_id" :class="errors!=null  && errors.provincia_id ? 'is-invalid' : '' " @change="getProvincesItem">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.nombre }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.provincia_id" class="text-danger">{{ errors.provincia_id[0] }}</span>
                            </div>
                            <div class="col-md-4">
                                <label>Distrito: </label>
                                <select class="form-select" v-model="user.distrito_id" :class="errors!=null  && errors.distrito_id ? 'is-invalid' : '' ">
                                    <option value="" selected disabled>--seleccionar--</option>
                                    <option v-for="distrito in districts" :key="distrito.id" :value="distrito.id">{{ distrito.nombre }}</option>
                                </select>
                                <span v-if="errors!=null  && errors.distrito_id" class="text-danger">{{ errors.distrito_id[0] }}</span>
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
import Services from '../../../services/services';
export default {
    components:{
       
    },
    data() {
        return {
            pais_id:25,
            user:{  
                id:0,
                numero_documento:'',
                nombre:'',
                apellido_paterno:'',
                apellido_materno:'',
                sexo:1,
                codigo_pais_id:'',
                celular:'',
                email:'',
                direccion:'',
                departaments_id:'',
                provincia_id : '',
                distrito_id : '',
            },
            errors: null,
            loading:false,
            option:true,
            profiles:{},
            pais:{},
            departaments:{},
            provinces:{},
            districts:{},
            codigoPais:{}
        }
    },
    mounted(){
  
    },
    methods: {
        async openpersonaModal(id){
            $("#personaModal").modal("show");
            this.getPais();
            this.getListCodigoPais();
            if(id!=0){
                this.option=false
                try {
                    const result = await Services.getShowInfo('persona/show',id);
                    console.log(result)
                    let codigoPais = result.codigo_pais.split('+');
                    this.user={  
                        id:id,
                        numero_documento:result.numero_documento,
                        nombre:result.nombre,
                        apellido_paterno:result.apellido_paterno,
                        apellido_materno:result.apellido_materno,
                        sexo:result.sexo,
                        codigo_pais_id:codigoPais[1],
                        celular:result.celular,
                        email:result.email,
                        direccion:result.direccion,
                        departaments_id:result.departmento_id,
                        provincia_id : result.provincia_id,
                        distrito_id : result.distrito_id,
                    }
                    this.getProvinces(result.departmento_id)
                    this.getDistrict(result.provincia_id)
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

       //ubigeo
       async getPais() {
            try {
                const result = await Services.getAll('ubigeus/pais');
                this.pais = result
                this.getDepartamento(this.pais_id);
            } catch (error) {
                return error;
            }
        },

        getPaisItem() {
            this.getDepartamento(this.pais_id);
        },

        async getDepartamento(pais_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/department', pais_id);
                this.departaments = result
            } catch (error) {
                return error;
            }
        },

        getDepartamentItem() {
            this.getProvinces(this.user.departaments_id);
        },

        async getProvinces(departaments_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/province', departaments_id);
                this.provinces = result
            } catch (error) {
                return error;
            }
        },

        getProvincesItem() {
            this.getDistrict(this.user.provincia_id);
        },

        async getDistrict(province_id) {
            try {
                const result = await Services.getShowInfo('ubigeus/district', province_id);
                this.districts = result
            } catch (error) {
                return error;
            }
        },
        async getListCodigoPais(){
            try {
                const codigoPais = await Services.getAll('ubigeus/codigo-pais');
                this.codigoPais = codigoPais;
            } catch (error) {
                return error;
            }
        },
        async addNewUser(){
            this.errors= null;
            try {
                const result = await Services.addNewInfo('persona/add',this.user);
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
                const result = await Services.addNewInfo('persona/update',this.user);
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
            this.user={  
                id:0,
                numero_documento:'',
                nombre:'',
                apellido_paterno:'',
                apellido_materno:'',
                sexo:1,
                codigo_pais_id:'',
                celular:'',
                email:'',
                direccion:'',
                provincia_id : '',
                distrito_id : '',
                departaments_id:'',
            }
        }
    },
}
</script>