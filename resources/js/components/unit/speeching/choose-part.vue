<template>
    <el-dialog
        :title="__('Select a role', 'reading')"
        :visible="visible"
        :show-close="false"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        custom-class="choose-part-dialog"
        width="400px"
        center>
        <div class="role-buttons">
            <el-button 
                type="warning" 
                @click="choosePart(false)">
                <span
                    :style="nativeSpeakerStyle"
                    class="icon second-speaker">
                </span>
                {{ __('Answer questions', 'reading') }}
            </el-button>
            <el-button 
                type="warning" 
                @click="choosePart(true)">
                <span
                    :style="speakerStyle"
                    class="icon first-speaker">
                </span>
                {{ __('Ask questions', 'reading') }}
            </el-button>
        </div>
    </el-dialog>
</template>

<script>
const PLACEHOLDER_IMAGE = '/img/speaker-placeholder.png';

export default {
    props: {
        visible: Boolean,
        unit: Object,
    },
    methods: {
        choosePart(inversed) {
            this.$emit('choice', inversed);
        },
    },
    computed: {
        nativeSpeakerStyle() {
            return {
                backgroundImage: `url(${this.unit.nativeSpeakerImage}),url(${PLACEHOLDER_IMAGE})`,
            };
        },
        speakerStyle() {
            return {
                backgroundImage: `url(${this.unit.speakerImage}),url(${PLACEHOLDER_IMAGE})`,
            };
        },
    },
};
</script>