require('./bootstrap');
import Vue from 'vue';
import VueToast from 'vue-toast-notification';
import VueSweetalert2 from 'vue-sweetalert2';
import 'vue-toast-notification/dist/theme-sugar.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'leaflet/dist/leaflet.css';
const options = {
    /* position: 'top-right', */
    duration: 3000,
    keepOnHover: true,
    theme: 'sugar',
};
Vue.use(VueToast,options);
Vue.use(VueSweetalert2);
import LoginComponent from './components/accesos/LoginComponent.vue';
import DashboardComponent from './components/dashborad/DashboardComponent.vue';
import PersonaComponent from './components/configuracion/PersonaComponent.vue';
import UserComponent from './components/configuracion/UserComponent.vue';
import MapsComponent from './components/maps/MapsComponent.vue';
import nueCentVotComponent from './components/maps/nueCentVotComponent.vue';
import ActasComponent from './components/actas/ActasComponent.vue';
Vue.component('login-component', LoginComponent);
Vue.component('dashboard-component', DashboardComponent);
Vue.component('persona-component', PersonaComponent);
Vue.component('user-component', UserComponent);
Vue.component('maps-component', MapsComponent);
Vue.component('nue-cent-vot-component', nueCentVotComponent);
Vue.component('actas-component', ActasComponent);
const app = new Vue({
    el: '#app',
});

export default app;