<template>
    <div class="paused-screen">
        <div class="quiz-logo"></div>
        <div class="text">{{ __('Quiz paused') }}</div>
        <el-button
            type="success"
            @click="resume">
            <span class="icon-48 icon-quiz"></span>
            {{ __('Continue') }}
        </el-button>
        <el-button
            type="success"
            @click="restart">
            <span class="icon-32 icon-repeat"></span>
            {{ __('Restart') }}
        </el-button>
    </div>
</template>

<script>
export default {
    methods: {
        resume() {
            this.$emit('resume');
        },
        restart() {
            this.$emit('restart');
        },
    },
};
</script>