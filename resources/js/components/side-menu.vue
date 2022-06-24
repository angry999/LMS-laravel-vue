<template>
    <div 
        v-if="authorized" 
        :class="{visible: showMenu}" 
        class="side-menu">
        <div class="menu-inner">
            <button 
                class="menu-icon" 
                @click="toggleMenu">
                <span class="icon-32 icon-burger"></span>
            </button>
            <div class="logo">
                {{ __('SPEAKING') }}<span>{{ __('PAL') }}</span>
            </div>
            <div class="menu-items">
                <router-link 
                    :to="{name: 'home'}" 
                    class="menu-item home">{{ __('Home') }}</router-link>
                <a 
                    href="#" 
                    class="menu-item logout" 
                    @click.prevent="logout">{{ __('Logout') }}</a>
                <a 
                    href="https://www.facebook.com/SpeakingPal"
                    target="_blank" 
                    class="menu-item join">{{ __('Join us') }}</a>
                <router-link 
                    :to="{name: 'about'}" 
                    class="menu-item about">{{ __('About') }}</router-link>
                <a 
                    href="mailto:support@speakingpal.com" 
                    class="menu-item contact">{{ __('Contact us') }}</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            showMenu: true
        }
    },
    computed: {
        authorized() {
            return this.$store.state.user.auth;
        },
    },
    methods: {
        logout () {
            this.$store.commit('logout');
            this.$router.push({name: 'login'});
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
        },
        hideMenu() {
            this.showMenu = false;
        },
    }
}
</script>