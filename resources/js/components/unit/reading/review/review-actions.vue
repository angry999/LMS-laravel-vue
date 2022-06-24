<template>
    <div class="actions">
        <el-tooltip content="Play Sample" placement="top">
            <play-sentence-action :sentence="result.sentence" />
        </el-tooltip>

        <el-tooltip content="Speak Again" placement="top">
            <improve-reading-action
                :sentence="result.sentence"
                :unit="unit"
                @scoreUpdated="scoreUpdated"/>
        </el-tooltip>

        <el-tooltip content="My Recording" placement="top">
            <play-record-action :sentence="result.sentence" />
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
