<template>
    <div :class="cssClass"></div>
</template>

<script>
const SEGMENTS = 35;

export default {
    props: {
        level: Number,
        skin: {
            type: String,
            default: 'default',
        },
    },
    computed: {
        cssClass() {
            let v = Math.max(0, Math.min(SEGMENTS, Math.round(SEGMENTS * this.level / 100))).toFixed(0);
            return [
                'mic-indicator',
                this.skin,
                `v-${v}`,
            ];
        },
    },
};
</script>