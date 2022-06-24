<template>
    <router-link 
        :to="unitRoute" 
        class="unit">
        <preview :image="unit.preview"></preview>
        <div class="details">
            <div class="title">{{ unit.title }}</div>
            <!-- div class="sub-title">{{ unit.level }}</div -->
            <div class="score">
                <stars :score="calcStars(unit.score)" />
            </div>
        </div>
        <unit-progress 
            :value="unit.progress"
            :max="100">
            {{ number }}
        </unit-progress>
    </router-link>
</template>

<script>
import UnitMixin from '../../mixins/unit';
import ScoreMixin from '../../mixins/score';

export default {
    mixins: [
        UnitMixin,
        ScoreMixin,
    ],
    props: {
        number: {
            type: Number,
        },
        category: {
            type: Object,
        },
    },
    computed: {
        unitRoute() {
            if (this.unit.isSpeaking) {
                return this.routeTo('unit-menu', this.unit.id, {categoryName: this.category.slug});
            }
            if (this.unit.isPronounciation) {
                return this.routeTo('unit-pronounciation', this.unit.id, {categoryName: this.category.slug});
            }
            if (this.unit.isGuidedSpeech) {
                return this.routeTo('unit-guidedspeech', this.unit.id, {categoryName: this.category.slug});
            }

            return {name: 'home'};
        }
    },
};
</script>