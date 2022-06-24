<template>
    <div class="forgot-password-component centered-box narrow-form narrow-layout">
        <div class="form-row">
            <p>{{ __('Please fill in the email address you used to register, and we will send you an email with instructions on how to reset your password.') }}</p>
        </div>
        <div class="form-row">
            <el-input v-model="email"></el-input>
        </div>
        <div class="form-row">
            <el-button 
                :loading="loading" 
                :disabled="loading" 
                type="primary" 
                @click="reset">{{ __('Reset password') }}</el-button>
        </div>
        <div class="form-row text-center small-font">
            <p class="mt-50 mb-10">{{ __('Need help? Contact us at:') }}</p>
            <a href="mailto:support@speakingpal.com">support@speakingpal.com</a>
        </div>
    </div>
</template>


<script>
import auth from '../services/auth';
    
export default {
    data () {
        return {
            email: '',
            loading: false,
        };
    },
    methods: {
        reset() {
            this.loading = true;
            auth.resetPassword(this.email).then((response) => {
                this.loading = false;
                this.$info(this.__('We have sent an email with instructions. Please check your mailbox.'));
                this.$router.push({name: 'login'});
            })
                .catch((error) => {
                    this.loading = false;
                    if (error === 'invalid_username') {
                        this.$error(this.__('Email that you have entered does not match our records.'));
                    }
                });
        },
    },
};
</script>