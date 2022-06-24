<template>
    <div class="sentence-result">
        <stars 
            :score="stars" 
            :size="size" />
    </div>
</template>

<script>
import ScoreMixin from '../../../mixins/score';

const GROW_TIMEOUT = 300;
    
export default {
    mixins: [
        ScoreMixin,
    ],
    props: {
        score: {
            type: Number,
            default: 0,
        },
        size: {
            type: String,
            default: 'normal',
        },
        animated: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            stars: 0,
        };
    },
    watch: {
        score(value) {
            this.init();
        }
    },
    created() {
        this.timer = null;
        this.max = 0;
    },
    mounted() {
        this.init();
    },
    beforeDestroy() {
        clearTimeout(this.timer);
    },
    methods: {
        init() {
            if (this.animated) {
                this.max = this.calcStars(this.score);
                this.stars = 0;
                clearTimeout(this.timer);
                this.grow();
            } else {
                this.stars = this.calcStars(this.score);
            }
        },
        grow() {
            this.stars++;
            if (this.stars < this.max) {
                this.timer = setTimeout(() => {
                    this.grow();
                }, GROW_TIMEOUT);
            }
        },
    },
};
</script>