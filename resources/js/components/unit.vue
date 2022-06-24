<template>
    <div
        v-loading.fullscreen.lock="loading"
        class="unit-component standard-layout container has-titlebar full-height">
        <router-view
            v-if="unit && !forceReset"
            :unit="unit"></router-view>
    </div>
</template>

<script>
import Unit from '../models/unit';

export default {
    props: {
        categoryName: String,
        unitId: String,
    },
    data() {
        return {
            unit: null,
            loading: false,
            forceReset: false,
        };
    },
    mounted() {
        this.load();
        eventHub.$on('forceReset', () => {
            this.forceReset = true;
            this.$nextTick(() => {
                this.forceReset = false;
            });
        });
        firebase.analytics().logEvent('Lesson', {location: this.categoryName, premium: this.$store.state.user.premium});
    },
    methods: {
        load() {
            this.loading = true;
            (new Unit(this.categoryName, this.unitId)).load().then((unit) => {
                this.unit = unit;
                this.loading = false;
            })
                .catch((error) => {
                    this.loading = false;
                });
        },
    },
};
</script>
