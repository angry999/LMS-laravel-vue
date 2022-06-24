<template>
    <div class="unit-listening-container">
        <titlebar :back="routeTo('unit-menu', unit.id)">
            {{ unit.title }}
            <router-link 
                slot="right" 
                :to="routeTo('dialog-review', unit.id)" 
                class="next text-link icon-right">
                {{ __('Next') }} <span class="icon-32 icon-next"></span>
            </router-link>
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
            this.$router.push(this.routeTo('dialog-review', this.unit.id));
        },
    },
};
</script>