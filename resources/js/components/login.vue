<template>
    <div>
        <div class="login-component centered-box">
            <div class="login-header">
                <div class="logo">
                    {{ __('Speaking') }}<span>{{ __('Pal') }}</span>
                </div>
                <div class="headline">
                    {{ __('Learn English. Speak English.') }}
                </div>
                <div class="sub">
                    {{ __('The new way to learn English') }}
                    <br />
                    {{ __('on your mobile, tablet &amp; PC') }}
                </div>
            </div>
            <div class="narrow-form narrow-layout">
                <div class="form-row">
                    <label>{{ __('Email') }}</label>
                    <el-input v-model="email"></el-input>
                </div>
                <div class="form-row">
                    <label>{{ __('Password') }}</label>
                    <el-input
                        v-model="password"
                        type="password"></el-input>
                </div>
                <div class="form-row">
                    <el-button
                        :loading="loading"
                        :disabled="loading"
                        type="primary"
                        @click="login">{{ __('Login') }}</el-button>
                </div>
                <div class="form-row text-center small-font">
                    <router-link :to="{name: 'forgot-password'}">{{ __('Forgot your password?') }}</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import auth from '../services/auth';
import Unit from '../models/unit';
import catalogue from '../services/catalogue';
import { mapGetters } from "vuex";

export default {
    data () {
        return {
            email: '',
            password: '',
            loading: false,
        };
    },
    methods: {
        login () {
            this.loading = true;
            auth.login(this.email, this.password).then((response) => {
                this.loading = false;
                catalogue.fresh();
                Unit.invalidateCache();
                this.$router.push({name: 'home'});
                firebase.analytics().logEvent('Login', {premium: this.user.premium});
            })
                .catch((error) => {
                    this.loading = false;
                    if (error === 'invalid_credentials') {
                        this.$error(this.__('Invalid email or password. Please check your login details and try again.'));
                    } else if (error === 'license_expired') {
                        this.$error(this.__('Your license has been expierd.'));
                    }
                });
        },
    },
    computed: {
        ...mapGetters({
            user : 'user'
        }),
    }
};
</script>
