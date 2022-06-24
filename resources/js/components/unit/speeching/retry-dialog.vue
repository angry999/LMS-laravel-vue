<template>
    <el-dialog
        :title="showTips ? __('Tips', 'reading') : __('Please say again')"
        :visible="visible"
        :show-close="false"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        custom-class="retry-dialog"
        width="300px"
        center
        @opened="start">
        <div 
            v-if="showTips" 
            class="tips">
            <ol>
                <li>{{ __('Speak aloud') }}</li>
                <li>{{ __('Speak clearly') }}</li>
                <li>{{ __('Try a quiet place') }}</li>
                <li>{{ __('Speak closer to microphone or use external microphone') }}</li>
                <li>{{ __('Noisy background = lower scores') }}</li>
            </ol>
            <el-button 
                type="warning" 
                @click="hideTips">
                {{ __('Ok') }}
            </el-button>
        </div>
        <div 
            v-else 
            class="options">
            <el-button 
                type="warning" 
                @click="retry">
                <span class="icon-32 icon-repeat"></span>
                {{ __('Retry', 'reading') }}
                <span v-if="showCounter">({{ counter }})</span>
            </el-button>
            <el-button 
                type="warning" 
                @click="skip">
                <span class="icon-32 icon-forward"></span>
                {{ __('Skip', 'reading') }}
            </el-button>
            <el-button 
                type="warning" 
                @click="displayTips">
                <span class="icon-32 icon-info"></span>
                {{ __('Tips', 'reading') }}
            </el-button>
        </div>
    </el-dialog>
</template>

<script>
const DELAY = 6;

export default {
    props: {
        visible: Boolean,
    },
    data() {
        return {
            counter: 0,
            showCounter: false,
            showTips: false,
        };
    },
    created() {
        this.timer = null;
    },
    beforeDestroy() {
        clearInterval(this.timer);
    },
    methods: {
        start() {
            this.counter = DELAY;
            this.timer = setInterval(() => {
                this.counter--;
                this.showCounter = true;
                if (this.counter == 0) {
                    this.retry();
                }
            }, 1000);
        },
        retry() {
            clearInterval(this.timer);
            this.$emit('retry');
        },
        skip() {
            clearInterval(this.timer);
            this.$emit('skip');
        },
        displayTips() {
            clearInterval(this.timer);
            this.showTips = true;
            this.showCounter = false;
        },
        hideTips() {
            this.showTips = false;
        },
    },
};
</script>