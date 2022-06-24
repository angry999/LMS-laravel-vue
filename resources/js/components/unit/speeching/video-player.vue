<template>
  <div :class="{ 'video-player': true, playing: playing }">
    <div v-loading="loading && !hideLoader" :style="previewStyle" class="preview">
      <video
        v-if="video !== null"
        ref="playable"
        preload="preload"
        width="100%"
        height="100%"
      >
        <source :src="video" type="video/mp4" />
      </video>
      <audio v-if="audio !== null" ref="playable" preload="preload" :src="audio"></audio>

      <video
        v-if="replay_video !== null"
        ref="replay_video"
        preload="preload"
        width="100%"
        height="100%"
      >
        <source :src="replay_video" type="video/mp4" />
      </video>

      <div v-if="!playing" class="overlay" @click="replay">
        <a
          class="play icon-48 icon-play gudence-speech-play"
        ></a>
      </div>
    </div>
    <div v-if="progress" class="progress">
      <div :style="{ width: progressValue }" class="bar"></div>
    </div>
  </div>
</template>

<script>
const HAVE_ENOUGH_DATA = 4;

export default {
  props: {
    video: String,
    audio: String,
    preview: String,
    progress: Boolean,
    hideLoader: Boolean,
  },
  data() {
    return {
      progressValue: 0,
      playing: false,
      loading: true,
      replay_video: null,
    };
  },
  computed: {
    previewStyle() {
      if ((!this.playing || this.video === null) && this.preview) {
        return {
          backgroundImage: `url(${this.preview})`,
        };
      }
      return {};
    },
  },
  created() {
    this.progressInterval = null;
    this.playableElement = null;
  },
  beforeDestroy() {
    clearInterval(this.progressInterval);
    this.destroyVideo();
  },
  methods: {
    play(readyCallback = null) {
      this.replay_video = null;
      let playable = this.$refs.playable;
      if (playable) {
        if (playable.readyState == HAVE_ENOUGH_DATA) {
          this.playableReady(playable);
          if (readyCallback) {
            readyCallback(playable);
          }
        } else {
          playable.oncanplaythrough = () => {
            this.playableReady(playable);
            if (readyCallback) {
              readyCallback(playable);
            }
          };
        }
        this.playableElement = playable;
      } else {
        throw "Playable element is not created";
      }
    },
    replay() {
      this.$refs.replay_video.play();
      this.playing = true;
    },
    stop() {
      if (this.playing) {
        this.playableElement.pause();
        clearInterval(this.progressInterval);
        this.playing = false;
        this.playableElement.currentTime = 0;
        this.playableElement.ontimeupdate = null;
        this.playableElement.onended = null;
        this.playableElement.oncanplaythrough = null;
      }
    },
    playableReady(playable) {
      this.setupHandlers(playable);
      playable.play();
      this.loading = false;
      this.playing = true;
      this.$emit("started");
    },
    playableEnded(playable) {
      this.playing = false;
      clearInterval(this.progressInterval);
      if (this.video) this.replay_video = this.video;
      this.$emit("ended");
    },
    setupHandlers(playable) {
      if (this.progress) {
        playable.ontimeupdate = () => {
          this.progressValue = (100 * playable.currentTime) / playable.duration + "%";
        };
      }
      if (this.$listeners && this.$listeners.progress) {
        this.progressInterval = setInterval(() => {
          this.$emit("progress", playable.currentTime * 1000);
        }, 100);
      }
      playable.onended = () => {
        this.playableEnded(playable);
      };
    },
    destroyVideo() {
      if (this.playableElement) {
        this.playableElement.ontimeupdate = null;
        this.playableElement.onended = null;
        this.playableElement.oncanplaythrough = null;
      }
    },
  },
};
</script>
