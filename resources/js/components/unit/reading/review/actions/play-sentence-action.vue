<template>
    <span class="play-sentence-action">
        <a 
            href="#" 
            class="icon-32 icon-replay-video" 
            @click.prevent="displayVideo"></a>
        <el-dialog
            :visible.sync="showVideo"
            :show-close="false"
            custom-class="without-header"
            width="640px"
            @opened="playVideo"
            @close="stopVideo">
            <video-player 
                ref="player" 
                :video="sentence.video" 
                :audio="sentence.audio" 
                :preview="sentence.startSnapshot" 
                progress 
                @ended="videoEnded" />
        </el-dialog>
    </span>
</template>

<script>
export default {
    props: {
        sentence: Object,
    },
    data() {
        return {
            showVideo: false,
        };
    },
    methods: {
        displayVideo() {
            this.showVideo = true;
        },
        videoEnded() {
            this.showVideo = false;
        },
        playVideo() {
            this.$refs.player.play();
        },
        stopVideo() {
            this.$refs.player.stop();
        },
    },
};
</script>