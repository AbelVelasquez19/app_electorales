<template>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="modalPermission"
        aria-labelledby="offcanvasBothLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasBothLabel" class="offcanvas-title">Permisos</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <div class="col-md-12 col-12">
                <small class="text-light fw-semibold mb-2">Opciones de permiso</small>
                <div class="accordion mt-3" v-for="(rolesItem, index) in roles" :key="index" id="accordionExample">
                    <div class="card accordion-item active mb-2" v-show="rolesItem.status_children != 2">
                        <h2 class="accordion-header" :id="`heading${index}`">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                :data-bs-target="`#accordion${index}`" aria-expanded="true"
                                :aria-controls="`accordion${index}`">
                                <label class="switch switch-success">
                                    <input type="checkbox" class="switch-input" :value="rolesItem.id" @click="changeRole(rolesItem.id)"/>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label">{{ rolesItem.name }}</span>
                                </label>
                            </button>
                        </h2>
                        <div :id="`accordion${index}`" class="accordion-collapse collapse"
                            :aria-labelledby="`heading${index}`" data-bs-parent="#accordionExample"
                            v-show="rolesItem.status_children == 0">
                            <div class="accordion-body d-flex flex-column" style="padding-left: 10%;" v-for="subRole in roles" :key="subRole.id" v-if="subRole.status_children == 2 && subRole.sub_role == rolesItem.id">
                                <label class="switch switch-success mb-2">
                                    <input type="checkbox" class="switch-input" :value="subRole.id" @click="changeRole(subRole.id)"/>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label">{{ subRole.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
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
            idProfile: null,
            role_id:null
        }
    },
    mounted() {

    },
    methods: {
        async openPermissionsModal(id) {
            this.idProfile = id;
            $("#modalPermission").offcanvas('show');
            try {
                const result = await Services.getAll('profile/list-role');
                this.roles = result.result;
            } catch (error) {
                return error;
            }
        },
        async changeRole (role_id){
            console.log(role_id)
        }

    }
}
</script>
<style>
.accordion {
    padding: 0 !important;
    margin: 0 !important;
}</style>