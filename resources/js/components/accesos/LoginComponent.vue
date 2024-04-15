<template>
    <div>
        <form class="mb-3" action="#" method="POST" @submit.prevent="verifyCode" v-show="next == 1 ? true : false">
            <div class="mb-3">
                <label for="email" class="form-label">Usuario</label>
                <input type="text" class="form-control" :class="errors != null && errors.email ? 'is-invalid' : ''"
                    id="email" name="email-username" v-model="userName" placeholder="Enter your email or username"
                    autofocus />
                <span v-if="errors != null && errors.email" class="danger text-danger">{{ errors.email[0] }}</span>
            </div>
            <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                    <input type="password" id="password" class="form-control"
                        :class="errors != null && errors.password ? 'is-invalid' : ''" name="password"
                        placeholder="**********" v-model="password" aria-describedby="password" />
                </div>
                <span v-if="errors != null && errors.password" class="danger text-danger">{{ errors.password[0] }}</span>
            </div>
            <div class="mb-3">
                <span class="danger text-center mb-1 text-danger" v-show="errorMessage.messageLogin !== ''">{{
            errorMessage.messageLogin }}</span>
            </div>
            <button class="btn btn-primary d-grid w-100" type="submit">{{ loading ? 'Ingresando...' : 'Iniciar sesión'
                }}</button>
        </form>
        <form class="mb-3" action="#" method="POST" @submit.prevent="login" v-show="next == 2 ? true : false">
            <div class="mb-3">
                <label for="email" class="form-label">Ingresar codigo de verificación de 6 digitos</label>
                <input type="text" class="form-control" :class="errors != null && errors.codigo_confirm ? 'is-invalid' : ''"
                    id="email" name="email-username" v-model="codeVerify"
                    autofocus />
                <span v-if="errors != null && errors.codigo_confirm" class="danger text-danger">{{ errors.codigo_confirm[0] }}</span>
            </div>
            <div class="mb-3">
                <span class="danger text-center mb-1 text-danger" v-show="errorMessage.messageLogin !== ''">{{
            errorMessage.messageLogin }}</span>
            </div>
            <button class="btn btn-primary d-grid w-100" type="submit">{{ loading ? 'Verificando...':'Verificar'
                }}</button>
        </form>
    </div>
</template>
<script>
import Service from '../../services/services';
export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        urlCodeVerify: {
            type: String,
            default: '',
        },
        dashboard: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            userName: 'homenick.josianne@example.org',
            password: 'password',
            codeVerify:null,
            loading: false,
            errorMessage: {
                messageLogin: '',
            },
            array: [],
            errors: null,
            next: 1
        }
    },
    methods: {
        async verifyCode() {
            this.array = []
            let form = {
                email: this.userName,
                password: this.password
            }
            this.errors = null;
            this.loading = true;
            try {
                const result = await Service.addNewInfo(this.urlCodeVerify, form);
                if (result.status) {
                    this.next = 2;
                    this.loading = false;
                    this.errorMessage.messageLogin = '';
                    this.errors = null;
                } else {
                    this.errorMessage.messageLogin = result.result.message,
                    this.loading = false;
                    this.errors = result.result;
                    this.next = 1;
                }
            } catch (error) {
                this.loading = false;
                return error;
            }
        },
        async login() {
            this.array = []
            let form = {
                email: this.userName,
                password: this.password,
                codeVerify: this.codeVerify,
            }

            this.errors = null;
            this.loading = true;
            try {
                const result = await Service.addNewInfo(this.url, form);
                if (result.status) {
                    if (result.result[0].status) {
                        this.loading = false;
                        window.location.href = this.dashboard;
                        this.errors = null;
                        this.errorMessage.messageLogin = ''
                    }
                } else {
                    this.errorMessage.messageLogin = result.result.message,
                    this.loading = false;
                    this.errors = result.result;
                    this.next = 1;
                    this.codeVerify='';
                }
            } catch (error) {
                this.loading = false;
                return error;
            }
        }
    },
}
</script>