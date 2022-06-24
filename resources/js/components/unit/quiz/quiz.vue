<template>
    <div class="unit-quiz-container">
        <titlebar :back="routeTo('unit-menu', unit.id)">
            {{ unit.title }}
            <template
                v-if="isQuiz"
                slot="right">
                <a
                    v-if="paused"
                    href="#"
                    class="icon-32 icon-play"
                    @click.prevent="resume"></a>
                <a
                    v-else
                    href="#"
                    class="icon-32 icon-pause"
                    @click.prevent="pause"></a>
            </template>
        </titlebar>
        <quiz-start
            v-if="isReadyScreen"
            @start="startQuiz" />
        <div
            v-if="isQuiz"
            class="quiz-main">
            <quiz-progress
                ref="progress"
                :results="results"
                :questions="numQuestions"
                :time="questionTime"
                :paused="paused"
                @timeout="timeout" />
            <quiz-pause
                v-if="paused"
                @resume="resume"
                @restart="restart" />
            <quiz-question
                v-if="!paused && question != null"
                ref="question"
                :question="question"
                @answer="acceptAnswer" />
        </div>
        <quiz-summary
            v-if="isSummary"
            :unit="unit"
            :results="results"
            :questions="numQuestions"
            @restart="restart" />
    </div>
</template>

<script>
import UnitMixin from '../../../mixins/unit';
import {QUIZ} from '../../../models/unit-activity';
import sfx from '../../../tools/sfx';

const STAGE_READY = 1;
const STAGE_QUIZ = 2;
const STAGE_SUMMARY = 3;
const DELAY_TIMEOUT = 2000;
const QUESTION_TIME = 20;

export default {
    mixins: [
        UnitMixin,
    ],
    data() {
        return {
            stage: STAGE_READY,
            paused: false,
            results: [],
            question: null,
            progress: 0,
            numQuestions: 10,
        };
    },
    computed: {
        isReadyScreen() {
            return this.stage === STAGE_READY;
        },
        isQuiz() {
            return this.stage === STAGE_QUIZ;
        },
        isSummary() {
            return this.stage === STAGE_SUMMARY;
        },
        questionTime() {
            return QUESTION_TIME;
        },
    },
    created() {
        this.quiz = null;
        this.waitNext = null;
        this.delayTimer = null;
    },
    mounted() {
        this.quiz = lodash.find(this.unit.activities, (activity) => activity.activityType === QUIZ);
        if (!this.quiz) {
            this.quiz = lodash.find(this.unit.guidedspeeches, (activity) => activity.activityType === QUIZ);
        }
        if (!this.quiz || this.quiz.locked) {
            this.$router.push(this.routeTo('unit-menu', this.unit.id));
        }
    },
    beforeDestroy() {
        clearTimeout(this.delayTimer);
    },
    methods: {
        startQuiz() {
            this.results = [];
            this.progress = 0;
            this.stage = STAGE_QUIZ;
            this.numQuestions = this.quiz.questions.length;
            this.showQuestion();

            firebase.analytics().logEvent('Quiz', {activity: 'start', location: this.unit.title, premium: this.$store.state.user.premium});
        },
        showQuestion() {
            this.question = this.quiz.getQuestion(this.progress).shuffle();
        },
        acceptAnswer(answer) {
            this.$refs.progress.pause();
            if (answer ? answer.isCorrect : false) {
                sfx.play('quiz-correct-answer');

                firebase.analytics().logEvent('Quiz', {activity: 'correct_answer', location: this.unit.title});
            } else {
                sfx.play('quiz-wrong-answer');

                firebase.analytics().logEvent('Quiz', {activity: 'incorrect_answer', location: this.unit.title});
            }
            this.waitNext = () => {
                this.waitNext = null;
                this.results.push(answer ? answer.isCorrect : false);
                this.progress++;
                if (this.progress >= this.numQuestions) {
                    this.complete();
                } else {
                    this.question = null;
                    this.$nextTick(() => {
                        this.showQuestion();
                    });
                }
            };
            this.delayTimer = setTimeout(this.waitNext, DELAY_TIMEOUT)
        },
        timeout() {
            this.$refs.question.reveal();
            this.acceptAnswer(false);
        },
        pause() {
            this.paused = true;
            if (this.waitNext) {
                clearTimeout(this.delayTimer);
            }
        },
        resume() {
            this.paused = false;
            if (this.waitNext) {
                this.waitNext();
            }
        },
        restart() {
            this.paused = false;
            this.startQuiz();
        },
        complete() {
            this.stage = STAGE_SUMMARY;
        },
    },
};
</script>
