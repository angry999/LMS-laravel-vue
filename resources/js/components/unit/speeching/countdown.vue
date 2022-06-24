<template>
    <el-dialog
        :visible="visible"
        :show-close="false"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        custom-class="countdown-dialog"
        width="300px"
        top="40vh"
        center
        @opened="start">
        <div class="counter text-center">
            <span class="text">{{ __('Read aloud in...') }}</span>
            <span class="number">{{ counter }}</span>
        </div>
    </el-dialog>
</template>

<script>
const DELAY = 3;

export default {
    data() {
        return {
            counter: 0,
            visible: true,
        };
    },
    created() {
        this.timer = null;
        // forcibly starting countdown, because @opened event is not being fired (emmitted) for some reason
        this.start();
    },
    beforeDestroy() {
        clearInterval(this.timer);
    },
    methods: {
        start() {
            this.counter = DELAY;
            this.timer = setInterval(() => {
                this.counter--;
                if (this.counter == 0) {
                    this.end();
                }
            }, 1000);
        },
        end() {
            this.visible = false;
            clearInterval(this.timer);
            this.$emit('ended');
        },
    },
};
</script>
