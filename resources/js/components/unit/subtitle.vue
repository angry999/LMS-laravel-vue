<template>
    <div 
        v-if="text !== null" 
        :class="cssClass">
        <div class="spike"></div>
        <div class="subtitle-inner">
            <mic-indicator 
                v-if="indicator" 
                :level="level" />
            <div
                v-if="isDouble"
                class="subtitle-text-container">
                <div
                    v-for="option in text"
                    class="subtitle-text">
                    <span>{{ option }}</span>
                </div>
            </div>
            <div 
                v-else
                class="subtitle-text">
                {{ text }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        speaker: {
            type: [String, Number],
            default: 1,
        },
        text: [String, Array],
        indicator: Boolean,
        level: Number,
    },
    computed: {
        isDouble() {
            return lodash.isArray(this.text);
        },
        cssClass() {
            return [
                'subtitle',
                `speaker-${this.speaker}`,
                {'double': this.isDouble},
            ];
        },
    },
};
</script>