require('./bootstrap');
import Vue from 'vue';
import LoginComponent from './components/accesos/LoginComponent.vue';
import DashboardComponent from './components/dashborad/DashboardComponent.vue';
import PersonaComponent from './components/configuracion/PersonaComponent.vue';
Vue.component('login-component', LoginComponent);
Vue.component('dashboard-component', DashboardComponent);
Vue.component('persona-component', PersonaComponent);
const app = new Vue({
    el: '#app',
});

export default app;