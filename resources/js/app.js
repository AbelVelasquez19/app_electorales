require('./bootstrap');
import Vue from 'vue';
import LoginComponent from './components/accesos/LoginComponent.vue';
import DashboardComponent from './components/dashborad/DashboardComponent.vue';
Vue.component('login-component', LoginComponent);
Vue.component('dashboard-component', DashboardComponent);
const app = new Vue({
    el: '#app',
});

export default app;