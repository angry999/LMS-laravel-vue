<template>
  <span class="play-sentence-action">
    <a
      href="#"
      class="icon-replay-video"
      :class="{ 'audio-btn-disabled': !hasRecord || !showRecorder }"
      @click.prevent="displayVideo"
    ></a>
    <el-dialog
      :visible.sync="showVideo"
      :show-close="false"
      custom-class="without-header"
      width="640px"
      @opened="playVideo"
      @close="stopVideo"
    >
      <video-player
        ref="player"
        :video="video"
        :audio="sentence.audio"
        preview=null
        progress
        @ended="videoEnded"
      />
    </el-dialog>
  </span>
</template>

<script>
export default {
  props: {
    sentence: Object,
    hasRecord: Boolean,
    showRecorder: Boolean,
    example_answer_video: String,
  },
  data() {
    return {
      showVideo: false,
      video: null,
    };
  },
  created() {
    this.video = this.example_answer_video;
  },
  methods: {
    displayVideo() {
      if (!this.hasRecord || !this.showRecorder) {
        return;
      }
      this.video = this.example_answer_video;
      this.showVideo = true;
    },
    videoEnded() {
      this.showVideo = false;
      this.video = null;
    },
    playVideo() {
      this.video = this.example_answer_video;
      
      this.$refs.player.play();
    },
    stopVideo() {
      this.$refs.player.stop();
    },
  },
};
</script>
