<template>
    <div class="speeching-actions">
        <el-tooltip content="Play Sample" placement="top">
            <play-sentence-speeching-action :sentence="result.sentence" />
        </el-tooltip>

        <el-tooltip content="Speak Again" placement="top">
            <improve-reading-speeching-action
                :sentence="result.sentence"
                :unit="unit" />
        </el-tooltip>

        <el-tooltip content="My Recording" placement="top">
            <play-record-speeching-action :sentence="result.sentence" />
        </el-tooltip>
    </div>
</template>

<script>
    export default {
        props: {
            result: Object,
            unit: Object,
        },
        methods: {
            scoreUpdated(result) {
                this.$emit('scoreUpdated', result);
            },
        }
    };
</script>
