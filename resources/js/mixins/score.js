export default {
    methods: {
        calcStars(score) {
            if (appConfig.scores.bad.indexOf(Number(score)) !== -1) {
                return 1;
            }
            if (appConfig.scores.moderate.indexOf(Number(score)) !== -1) {
                return 2;
            }
            if (appConfig.scores.good.indexOf(Number(score)) !== -1) {
                return 3;
            }
            return 0;
        },
    },
};