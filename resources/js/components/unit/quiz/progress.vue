<template>
    <div class="quiz-progress">
        <div class="line">
            <div
                v-for="i in questions"
                :class="dotClass(i)"
                class="dot">
                <span></span>
                <div 
                    v-if="i === results.length + 1"
                    class="current">
                    <div class="wrap">
                        {{ i }}
                        <arc-timer 
                            ref="timer"
                            :key="0"
                            :time="time"
                            color="#FF4D19"
                            @timeout="timeOut" />
                    </div>
                </div>
            </div>
        </div>
        <div class="cap">
            <span class="icon-48 icon-quiz"></span>
        </div>
    </div>
</template>

<script>
import sfx from '../../../tools/sfx';

export default {
    props: {
        results: Array,
        questions: Number,
        time: Number,
        paused: Boolean,
    },
    watch: {
        results(val) {
            if (val.length < this.questions) {
                this.startTimer();
            }
        },
        paused(val) {
            if (val) {
                this.pause();
            } else {
                this.resume();
            }
        },
    },
    mounted() {
        if (this.results.length < this.questions) {
            this.startTimer();
        }
    },
    beforeDestroy() {
        sfx.mute('quiz-timer');
    },
    methods: {
        dotClass(i) {
            if (i <= this.results.length) {
                return this.results[i - 1] ? 'correct' : 'incorrect';
            }
            return 'pending';
        },
        getTimer() {
            return this.$refs.timer[0];
        },
        startTimer() {
            this.$nextTick(() => {
                sfx.play('quiz-timer', true);
                this.getTimer().restart();
            });
        },
        pause() {
            sfx.mute('quiz-timer');
            this.getTimer().pause();
        },
        resume() {
            sfx.play('quiz-timer', true);
            this.getTimer().start();
        },
        timeOut() {
            sfx.mute('quiz-timer');
            this.$emit('timeout');
        },
    },
};
</script>