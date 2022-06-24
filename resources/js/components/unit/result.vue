<template>
    <div class="unit-result-container full-height">
        <titlebar :back="routeTo('units')">{{ unit.title }}</titlebar>
        <div class="result-body full-height">
            <div class="stars">
                <div class="stars-wrap">
                    <stars 
                        :score="calcStars(score)" 
                        size="largest" />
                </div>
                <div class="congrats">
                    <span class="icon-32 icon-speaking"></span> 
                    <span>{{ __('Good job') }}</span>
                </div>
                <a 
                    href="#" 
                    class="share"></a>
            </div>
            <div class="title">
                <span class="icon-32 icon-check"></span> {{ unit.title }}
            </div>
            <a 
                href="#" 
                class="next"
                @click.prevent="next">
                <div class="label">
                    <span>{{ __('Next level') }}</span>
                    <span class="icon-32 icon-arrow"></span>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
import UnitMixin from '../../mixins/unit';
import ScoreMixin from '../../mixins/score';
import sfx from '../../tools/sfx';
    
export default {
    mixins: [
        UnitMixin,
        ScoreMixin,
    ],
    data() {
        return {
            score: 0,
        };
    },
    mounted() {
        sfx.play('level-completed');
        this.score = this.unit.score;
    },
    methods: {
        next() {
            this.$router.push(this.routeTo('units'));
        },
    },
};
</script>