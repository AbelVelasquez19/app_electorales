<template>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="modalPermission"
        aria-labelledby="offcanvasBothLabel" style="width: 70%;">
        <div class="offcanvas-header">
            <h5 id="offcanvasBothLabel" class="offcanvas-title">Perfiles y Opciones</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <div class="row">
                <div class="col-md-6 col-6">
                    <small class="text-center fw-semibold mb-2">Opciones</small>
                    <table class="table table-hover table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Opciones</th>
                                <th>Permiso</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="(item,index) in roles" :key="item.id">
                                <td>{{ index++ }}</td>
                                <td>{{ item.nombre }}</td>
                                <td>
                                  <a href="#" @click="addPermisos(item.id)"><i class="fa-solid fa-circle-right"></i></a>  
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-6">
                    <small class="text-center fw-semibold mb-2">Permisos</small>
                    <table class="table table-hove table-sm table-bordered">
                        <thead>
                            <tr >
                                <th>Desactivar</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="(item,index) in permision" :key="item.id">
                                <td>
                                    <a href="#" @click="deletePermisos(item.id)"><i class="fa-solid fa-circle-left"></i></a>  
                                </td>
                                <td>{{ item.nombre }}</td>
                            </tr>
                        </tbody>
                    </table>
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
            selectedValues: [],
            roles: {},
            permision:{},
            idProfile: null,
            role_id:null
        }
    },
    mounted() {

    },
    methods: {
        async openPermissionsModal(id) {
            this.openPermisionAsing(id);
            this.idProfile = id;
            $("#modalPermission").offcanvas('show');
            try {
                const result = await Services.getAll('profile/list-role');
                this.roles = result.result;
            } catch (error) {
                return error;
            }
        },
        async openPermisionAsing(id){
            try {
                const result = await Services.getId('permisos/list-permisions-asing',{'id':id});
                this.permision = result;
            } catch (error) {
                return error;
            }
        },

        async addPermisos(id){
            try {
                const result = await Services.addNewInfo('permisos/add-permisions-asing',{'idRole':id,'idProfile':this.idProfile});
                if(result.status){
                    this.openPermisionAsing(this.idProfile);
                }
            } catch (error) {
                return error;
            }
        },
        async deletePermisos(id){
            try {
                const result = await Services.addNewInfo('permisos/delete-permisions-asing',{'permisosId':id});
                if(result.status){
                    this.openPermisionAsing(this.idProfile);
                }
            } catch (error) {
                return error;
            }
        }

    }
}
</script>
<style>
.accordion {
    padding: 0 !important;
    margin: 0 !important;
}</style>