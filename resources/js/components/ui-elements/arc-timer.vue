<template>
    <svg
        :width="`${size}px`"
        :height="`${size}px`">
        <path
            :stroke="color"
            :stroke-width="width"
            :d="arc"
            fill="none" />
    </svg>
</template>

<script>
export default {
    props: {
        time: Number,
        color: {
            type: String,
            default: '#ffffff',
        },
        width: {
            type: Number,
            default: 4,
        },
        size: {
            type: Number,
            default: 32,
        },
    },
    data() {
        return {
            value: 0,
        };
    },
    computed: {
        arc() {
            return this.describeArc(this.size / 2, this.size / 2, (this.size - this.width) / 2, 360 * this.value / this.time);
        },
    },
    created() {
        this.interval = null;
    },
    beforeDestroy() {
        this.pause();
    },
    methods: {
        start() {
            this.interval = setInterval(() => {
                this.tick();
            }, 1000);
        },
        pause() {
            clearInterval(this.interval);
        },
        stop() {
            this.pause();
            this.value = 0;
        },
        restart() {
            this.stop();
            this.start();
        },
        tick() {
            this.value++;
            if (this.value >= this.time) {
                this.pause();
                this.$nextTick(() => {
                    this.$emit('timeout');
                });
            }
        },
        polarToCartesian(cx, cy, r, a) {
            let rad = (a - 90) * Math.PI / 180.0;
            return {
                x: cx + r * Math.cos(rad),
                y: cy + r * Math.sin(rad),
            };
        },
        describeArc(x, y, r, l) {
            let start = this.polarToCartesian(x, y, r, l >= 360 ? 359.99 : l);
            let end = this.polarToCartesian(x, y, r, 0);
            let large = l <= 180 ? '0' : '1';
            return [
                'M', start.x, start.y, 
                'A', r, r, 0, large, 0, end.x, end.y,
            ].join(' ');
        },
    }
};
</script>