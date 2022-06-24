<template>
  <div class="audio-recorer-wrpper">
    <div class="speeching-actions">
      <el-tooltip content="Play Sample" placement="top">
        <play-sentence-speeching-action
          :sentence="result.sentence"
          :hasRecord="hasRecord"
          :showRecorder="showRecorder"
          :example_answer_video="example_answer_video"
        />
      </el-tooltip>

      <el-tooltip content="Speak Again" placement="top">
        <improve-reading-speeching-action
          :showRecorder="showRecorder"
          :isRecording="isRecording"
          :micLevel="volume"
          @click.native="toggleRecorder"
        />
      </el-tooltip>
      <div v-if="isRecording" class="ar-recorder__duration" @click="toggleRecorder">{{ recordedTime }}</div>

      <el-tooltip content="My Recording" placement="top">
        <audio-player
          :record="selected"
          :hasRecord="hasRecord"
          :showRecorder="showRecorder"
        />
      </el-tooltip>
    </div>
  </div>
</template>

<script>
import AudioPlayer from "./player";
import IconButton from "./icon-button";
import Recorder from "../../lib/recorder";
import UploaderPropsMixin from "../../mixins/uploader-props";
import { convertTimeMMSS } from "../../lib/utils";

export default {
  mixins: [UploaderPropsMixin],
  props: {
    attempts: { type: Number },
    time: { type: Number },

    bitRate: { type: Number, default: 128 },
    sampleRate: { type: Number, default: 44100 },

    showDownloadButton: { type: Boolean, default: true },
    showUploadButton: { type: Boolean, default: true },

    micFailed: { type: Function },
    beforeRecording: { type: Function },
    pauseRecording: { type: Function },
    afterRecording: { type: Function },
    failedUpload: { type: Function },
    beforeUpload: { type: Function },
    successfulUpload: { type: Function },
    selectRecord: { type: Function },

    result: Object,
    unit: Object,
    showRecorder: Boolean,
    hasRecord: Boolean,
    example_answer_video:String
  },
  data() {
    return {
      isUploading: false,
      recorder: this._initRecorder(),
      recordList: [],
      selected: {},
      uploadStatus: null,
    };
  }, 
  components: {
    AudioPlayer,
    IconButton,
  },
  mounted() {
    this.$eventBus.$on("start-upload", () => {
      this.isUploading = true;
      this.beforeUpload && this.beforeUpload("before upload");
    });

    this.$eventBus.$on("end-upload", (msg) => {
      this.isUploading = false;

      if (msg.status === "success") {
        this.successfulUpload && this.successfulUpload(msg.response);
      } else {
        this.failedUpload && this.failedUpload(msg.response);
      }
    });
  },
  beforeDestroy() {
    this.stopRecorder();
  },
  methods: {
    toggleRecorder() {
      if (!this.showRecorder) return;
      if (this.attempts && this.recorder.records.length >= this.attempts) {
        return;
      }

      if (!this.isRecording) {
        this.recorder.start();
      } else {
        this.stopRecorder();
      }
    },
    stopRecorder() {
      if (!this.isRecording) {
        return;
      }

      this.recorder.stop();
      this.recordList = this.recorder.recordList();

      this.selected = this.recordList[this.recordList.length - 1];
      this.selectRecord && this.selectRecord(this.selected);
      this.hasRecord = true;
    },
    removeRecord(idx) {
      this.recordList.splice(idx, 1);
      this.$set(this.selected, "url", null);
      this.$eventBus.$emit("remove-record");
    },
    choiceRecord(record) {
      if (this.selected === record) {
        return;
      }
      this.selected = record;
      this.selectRecord && this.selectRecord(record);
    },
    _initRecorder() {
      return new Recorder({
        beforeRecording: this.beforeRecording,
        afterRecording: this.afterRecording,
        pauseRecording: this.pauseRecording,
        micFailed: this.micFailed,
        bitRate: this.bitRate,
        sampleRate: this.sampleRate,
        format: this.format,
      });
    },
  },
  computed: {
    attemptsLeft() {
      return this.attempts - this.recordList.length;
    },
    iconButtonType() {
      return this.isRecording && this.isPause
        ? "mic"
        : this.isRecording
        ? "pause"
        : "mic";
    },
    isPause() {
      return this.recorder.isPause;
    },
    isRecording() {
      return this.recorder.isRecording;
    },
    recordedTime() {
      if (this.time && this.recorder.duration >= this.time) {
        this.stopRecorder();
      }
      return this.time - parseInt(convertTimeMMSS(this.recorder.duration));
    },
    volume() {
      return parseFloat(this.recorder.volume) * 200;
    },
  },
};
</script>
