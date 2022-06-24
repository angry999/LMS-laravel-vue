<template>
    <span class="activity-wrap">
        <div
            v-if="activity.locked"
            :class="['activity', 'locked', activity.activityType]">
            <template v-if="isReading">
                <div class="body">
                    <span class="number">{{ activity.number }}</span>
                    <div class="icon-32 icon-lock"></div>
                </div>
            </template>
            <template v-if="isQuiz">
                <div class="body">
                    <span class="number">{{ activity.number }}</span>
                    <div class="inner">
                        <div class="icon-48 icon-timer"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-32 icon-lock"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-quiz"></div>
                    </div>
                </div>
            </template>
        </div>
        <router-link 
            v-else
            :to="link" 
            :class="['activity', activity.activityType, activity.completed ? 'completed' : 'pending']">
            <template v-if="isReading">
                <div class="body">
                    <span class="number">{{ activity.number }}</span>
                    <stars 
                        :score="calcStars(activity.score)" 
                        allow-zero />
                </div>
                <div
                    v-if="activity.isDouble"
                    class="icon-32 icon-2x"></div>
                <div
                    v-else
                    class="icon-32 icon-speaking"></div>
            </template>
            <template v-if="isQuiz">
                <div class="body">
                    <span class="number">{{ activity.number }}</span>
                    <div 
                        v-if="activity.completed"
                        class="inner">
                        <div class="qiuz-passed"></div>
                    </div>
                    <div 
                        v-else
                        class="inner">
                        <div class="icon-48 icon-timer"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-dots"></div>
                        <div class="icon-48 icon-quiz"></div>
                    </div>
                </div>
            </template>
        </router-link>
    </span>
</template>

<script>
import UnitMixin from '../../mixins/unit';
import {READING, QUIZ} from '../../models/unit-activity';
import ScoreMixin from '../../mixins/score';

export default {
    mixins: [
        UnitMixin,
        ScoreMixin,
    ],
    props: {
        activity: Object,
    },
    computed: {
        link() {
            if (this.isReading) {
                return this.routeTo('unit-reading', this.unit.id, {activityNumber: this.activity.number});
            }
            if (this.isQuiz) {
                return this.routeTo('unit-quiz', this.unit.id);
            }
            return {name: 'home'};
        },
        isReading() {
            return this.activity.activityType == READING;
        },
        isQuiz() {
            return this.activity.activityType == QUIZ;
        },
    },
};
</script>