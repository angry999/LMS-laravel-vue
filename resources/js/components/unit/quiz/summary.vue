<template>
    <div 
        v-loading.fullscreen.lock="submitting"
        class="quiz-completed">
        <quiz-progress 
            :results="results"
            :questions="questions" />
        <div class="congrats">
            <progress-meter 
                :value="score" 
                :max="questions"
                size="large" />
            <div 
                v-if="quizPassed" 
                class="text quiz-passed">
                {{ __('Congratulations!') }}
            </div>
            <div 
                v-else 
                class="text quiz-failed">
                {{ __('Woops! Try again.') }}
            </div>
        </div>
        <div class="actions">
            <el-button
                v-if="quizPassed" 
                type="success"
                @click="next">
                <span class="icon-48 icon-quiz"></span>
                {{ __('Next') }}
            </el-button>
            <el-button
                type="success"
                @click="restart">
                <span class="icon-32 icon-repeat"></span>
                {{ __('Restart') }}
            </el-button>
        </div>
    </div>
</template>

<script>
import UnitMixin from '../../../mixins/unit';
import QuizReport from '../../../models/quiz-report';
import sfx from '../../../tools/sfx';

const PASS_THRESHOLD = 1;

export default {
    mixins: [
        UnitMixin,
    ],
    props: {
        results: Array,
        questions: Number,
    },
    data() {
        return {
            submitting: false,
        };
    },
    computed: {
        score() {
            return this.results.filter((v) => v).length;
        },
        quizPassed() {
            return this.score >= PASS_THRESHOLD;
        },
    },
    mounted() {
        if (this.quizPassed) {
            sfx.play('quiz-completed');
        } else {
            sfx.play('quiz-failed');
        }
    },
    methods: {
        restart() {
            this.$emit('restart');
        },
        next() {
            let report = new QuizReport(this.unit, this.results);
            this.submitting = true;
            report.submit().then((progress) => {
                this.submitting = false;
                this.unit.progress = progress;
                this.$router.push(this.routeTo('unit-result', this.unit.id));
            })
                .catch(() => {
                    this.submitting = false;
                });
        },
    },
};
</script>