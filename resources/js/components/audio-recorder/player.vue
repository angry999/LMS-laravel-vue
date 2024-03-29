<template>
  <div class="ar-player">
    <div class="ar-player-actions">
      <icon-button
        id="play"
        class="ar-icon ar-icon__lg ar-player__play"
        :name="playBtnIcon"
        :class="{
          'ar-player__play--active': isPlaying,
          'audio-btn-disabled': !hasRecord || !showRecorder,
        }"
        @click.native="playback"
      />
    </div>

    <audio :id="playerUniqId" :src="audioSource"></audio>
  </div>
</template>

<script>
import IconButton from "./icon-button";
import { convertTimeMMSS } from "../../lib/utils";

export default {
  props: {
    src: { type: String },
    record: { type: Object },
    filename: { type: String },
    hasRecord: Boolean,
    showRecorder: Boolean,
  },
  data() {
    return {
      isPlaying: false,
      duration: convertTimeMMSS(0),
      playedTime: convertTimeMMSS(0),
      progress: 0,
    };
  },
  components: {
    IconButton,
  },
  mounted: function () {
    this.player = document.getElementById(this.playerUniqId);

    this.player.addEventListener("ended", () => {
      this.isPlaying = false;
    });

    this.player.addEventListener("loadeddata", (ev) => {
      this._resetProgress();
      this.duration = convertTimeMMSS(this.player.duration);
    });

    this.player.addEventListener("timeupdate", this._onTimeUpdate);

    this.$eventBus.$on("remove-record", () => {
      this._resetProgress();
    });
  },
  computed: {
    audioSource() {
      const url = this.src || this.record.url;
      if (url) {
        return url;
      } else {
        this._resetProgress();
      }
    },
    playBtnIcon() {
      return this.isPlaying ? "pause" : "play";
    },
    playerUniqId() {
      return `audio-player${this._uid}`;
    },
  },
  methods: {
    playback() {
      if (!this.hasRecord) {
        return;
      }
      if (!this.audioSource) {
        return;
      }

      if (this.isPlaying) {
        this.player.pause();
      } else {
        setTimeout(() => {
          this.player.play();
        }, 0);
      }

      this.isPlaying = !this.isPlaying;
    },
    _resetProgress() {
      if (this.isPlaying) {
        this.player.pause();
      }

      this.duration = convertTimeMMSS(0);
      this.playedTime = convertTimeMMSS(0);
      this.progress = 0;
      this.isPlaying = false;
    },
    _onTimeUpdate() {
      this.playedTime = convertTimeMMSS(this.player.currentTime);
      this.progress = (this.player.currentTime / this.player.duration) * 100;
    },
    _onUpdateProgress(pos) {
      if (pos) {
        this.player.currentTime = pos * this.player.duration;
      }
    },
    _onChangeVolume(val) {
      if (val) {
        this.player.volume = val;
      }
    },
  },
};
</script>
