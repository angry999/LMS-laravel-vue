<template>
  <div v-loading.fullscreen.lock="submitting" class="unit-summary">
    <flat-unit-progress :progress="activityIndex" :total="unit.activitiesTotal" />
    <div class="actions">
      <el-button type="primary" @click="next">
        {{ __("Next") }}
      </el-button>
      <el-button type="primary" @click="retry">
        <span class="icon-32 icon-repeat"></span>
        {{ __("Retry") }}
      </el-button>
      <el-button type="primary" @click="next">
        {{ __("Share") }}
      </el-button>
    </div>
  </div>
</template>

<script>
import UnitMixin from "../../../../mixins/unit";
import ReadingReport from "../../../../models/reading-report";
import sfx from "../../../../tools/sfx";

const IMPROVE_THRESHOLD = 2;

export default {
  mixins: [UnitMixin],
  props: {
    sentences: Array,
    activityIndex: Number,
  },
  data() {
    return {
      totalScore: 0,
      highScore: 0,
      canBeImproved: 0,
      submitting: false,
    };
  },
  mounted() {
    sfx.play("dialog-completed");
    this.init();
  },
  methods: {
    init() {
      let sum = 0;
      let count = 0;
      let activity = this.unit.getActivity(this.activityIndex);
      this.canBeImproved = 0;
      this.totalScore = 0;
      this.highScore = activity.score;
      this.sentences.forEach((sentence) => {
        if (sentence.result) {
          sum += sentence.result.score;
          count++;
          if (sentence.result.score <= IMPROVE_THRESHOLD) {
            this.canBeImproved++;
          }
        }
      });
      if (count > 0) {
        this.totalScore = Math.max(1, Math.round(sum / count));
        if (this.totalScore > this.highScore) {
          this.highScore = this.totalScore;
        }
      }
    },
    next() {
      let report = new ReadingReport(this.unit, this.activityIndex);
      let sentenceId = 0;
      this.sentences.forEach((sentence) => {
        if (sentenceId != sentence.sentenceId) {
          report.addResult(sentence.nodeId, sentence.sentenceId, 5);
          sentenceId = sentence.sentenceId;
        }
        // if (sentence.result) {
        //     report.addResult(sentence.nodeId, sentence.sentenceId, sentence.result.score);
        // }
      });
      this.submitting = true;
      report
        .submit()
        .then((progress) => {
          this.submitting = false;
          this.unit.progress = progress;
          if (
            !progress.has_quiz &&
            progress.activities_completed >= progress.activities_total
          ) {
            this.$router.push(this.routeTo("unit-result", this.unit.id));
          } else {
            this.$router.push(this.routeTo("unit-guidedspeech", this.unit.id));
          }
        })
        .catch(() => {
          this.submitting = false;
        });
    },
    improve() {
      this.$emit("improve");
    },
    retry() {
      eventHub.$emit("forceReset");
    },
  },
};
</script>
