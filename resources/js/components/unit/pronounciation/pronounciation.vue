<template>
    <div class="unit-pronounciation-container">
        <titlebar :back="routeTo('units')">
            {{ unit.title }}
        </titlebar>
        <div class="video-player-wrapper">
            <video-player 
                ref="player" 
                :video="video" 
                :preview="preview" 
                progress 
                @progress="progressChanged" 
                @ended="videoEnded" />
        </div>
        <subtitle 
            v-if="sentence" 
            :text="sentence.text" />
    </div>
</template>

<script>
import UnitMixin from '../../../mixins/unit';
import VideoUnitMixin from '../../../mixins/video-unit';
    
export default {
    mixins: [
        UnitMixin,
        VideoUnitMixin,
    ],
    methods: {
        videoEnded() {
            this.$router.push(this.routeTo('units'));
        },
    },
};
</script>