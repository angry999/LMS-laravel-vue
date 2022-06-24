<template>
    <div class="quiz-start">
        <div class="quiz-logo"></div>
        <el-button
            type="success"
            @click="start">
            {{ __('Start') }}
        </el-button>
    </div>
</template>

<script>
import sfx from '../../../tools/sfx';

export default {
    mounted() {
        sfx.play('quiz-intro');
    },
    methods: {
        start() {
            this.$emit('start');
        },
    },
};
</script>