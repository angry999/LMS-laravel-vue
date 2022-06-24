<template>
    <div :class="`stars size-${size}`">
        <span 
            v-if="score !== 0 || allowZero" 
            :class="`v-${score}`"></span>
    </div>
</template>

<script>
export default {
    props: {
        score: {
            type: Number,
            default: 0,
        },
        size: {
            type: String,
            default: 'normal',
        },
        allowZero: {
            type: Boolean,
            default: false,
        },
    },
};
</script>