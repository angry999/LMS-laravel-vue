<template>
    <div :class="cssClass">
        <div class="inner">
            <span :style="{color: `#${color}`}">{{ percent }}%</span>
        </div>
    </div>
</template>

<script>
const SEGMENTS = 24;

export default {
    props: {
        value: Number,
        max: {
            type: Number,
            default: SEGMENTS,
        },
        color: {
            type: String,
            default: 'ffffff',
        },
        size: {
            type: String,
            default: 'normal',
        },
    },
    computed: {
        normalValue() {
            return Math.max(0, Math.min(this.max, this.value));
        },
        cssClass() {
            let v = Math.round((this.normalValue / this.max) * SEGMENTS).toFixed(0);
            return [
                'progress-meter',
                `size-${this.size}`,
                `v-${v}`,
            ];
        },
        percent() {
            return Math.round((this.normalValue / this.max) * 100).toFixed(0);
        },
    },
};
</script>