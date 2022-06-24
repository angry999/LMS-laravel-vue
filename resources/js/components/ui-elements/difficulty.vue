<template>
    <div class="difficulty">
        <div 
            v-for="lvl in levels" 
            :class="`level-${lvl}`"></div>
        <div :class="`arrow-${level}`"></div>
    </div>
</template>

<script>
export default {
    props: {
        startLevel: Number,
        endLevel: Number,
    },
    data() {
        return {
            levels: [],
            level: '1',
        };
    },
    mounted() {
        lodash.range(1, 7).forEach((level) => {
            if (level <= this.endLevel) {
                this.levels.push(level);
            }
        });
        this.level = ((this.startLevel + this.endLevel) / 2).toFixed(1).replace('.', '-');
    },
};
</script>