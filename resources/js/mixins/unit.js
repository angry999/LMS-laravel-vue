export default {
    props: {
        unit: Object,
    },
    methods: {
        routeTo(name, unitId = null, params = {}) {
            let route = {name, params: {...params}};
            if (unitId) {
                route.params.unitId = unitId;
            }
            if (this.$route.params.categoryName) {
                route.params.categoryName = this.$route.params.categoryName;
            }
            return route;
        },
    },
};