<template>
    <div class="ready-double">
        <div class="logo"></div>
        <div class="text">
            {{ __('Now you get to choose!') }}
        </div>
        <div class="a-or-b">
            <span class="a">A</span>
            <span class="or">or</span>
            <span class="b">B</span>
        </div>
        <div class="indicator"></div>
        <el-button 
            type="warning" 
            @click="start">
            {{ __('Start') }}
        </el-button>
    </div>
</template>

<script>
export default {
    methods: {
        start() {
            this.$emit('start');
        },
    },
};
</script>