<template>
    <div class="component-userbar">
        <router-link
            :to="{name: 'profile'}" 
            class="avatar">
        </router-link>
        <div class="username">{{ username }}</div>
    </div>
</template>

<script>
export default {
    computed: {
        username() {
            return this.$store.state.user.name;
        }
    },
};
</script>