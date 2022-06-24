<template>
  <span class="improve-reading-action">
    <a
      :class="{ 'icon-retry-speak': !isRecording, 'icon-stop-speak': isRecording , 'audio-btn-disabled': !showRecorder }"
      href="#"
    ></a>
    <div v-if="isRecording" class="loading-container">
      <div v-loading="connecting">
        <mic-indicator :level="micLevel" skin="frame" />
      </div>
    </div>
  </span>
</template>

<script>
export default {
  props: {
    micLevel: Number,
    isRecording: Boolean,
    showRecorder: Boolean
  },
  data() {
    return {
      active: true,
      connecting: false, 
    };
  },
};
</script>
