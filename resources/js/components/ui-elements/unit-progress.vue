<template>
    <div :class="cssClass">
        <div class="inner">
            <span><slot></slot></span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: Number,
        max: {
            type: Number,
            default: 10,
        },
    },
    computed: {
        normalValue() {
            return Math.round(10 * Math.max(0, Math.min(this.max, this.value)) / this.max);
        },
        cssClass() {
            let v = this.normalValue.toFixed(0);
            return [
                'unit-progress',
                `v-${v}`,
            ];
        },
    },
};
</script>