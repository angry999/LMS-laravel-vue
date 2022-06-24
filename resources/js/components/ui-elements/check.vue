<template>
    <div class="check-container">
        <span v-if="completed" 
            :class="`v-checked`" ></span>
        <span v-else 
            :class="`v-unchecked`" ></span> 
    </div>
</template>

<script>
export default {
    props: { 
        completed: {
            type: Boolean,
            default: false,
        },
    },
};
</script>