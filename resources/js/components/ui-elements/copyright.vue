<template>
    <span class="copyright">{{ from }}-{{ to }} &copy; <slot></slot></span>
</template>

<script>
export default {
    props: {
        from: String,
    },
    computed: {
        to() {
            return (new Date()).getFullYear();
        },
    },
};
</script>