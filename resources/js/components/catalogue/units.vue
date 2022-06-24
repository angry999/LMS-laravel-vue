<template>
    <div
        v-loading.fullscreen.lock="loading"
        class="units-component standard-layout container scrollable has-titlebar">
        <titlebar :back="{name: 'home'}">{{ __('Lessons') }}</titlebar>
        <scrollbar ref="scrollbar">
            <div class="units-container">
                <units-header
                    v-if="category !== null"
                    :category="category"></units-header>
                <div
                    v-if="category !== null"
                    class="units-list">
                    <catalogue-unit
                        v-for="(unit, number) in category.units"
                        :unit="unit"
                        :category="category"
                        :key="unit.id"
                        :number="number + 1"/>
                </div>
            </div>
        </scrollbar>
    </div>
</template>

<script>
import catalogue from '../../services/catalogue';

export default {
    props: {
        categoryName: String,
    },
    data() {
        return {
            category: null,
            loading: false,
        };
    },
    mounted() {
        this.load();
        firebase.analytics().logEvent('Category', {location: this.categoryName});
    },
    methods: {
        load() {
            this.loading = true;
            catalogue.getCategory(this.categoryName).then((response) => {
                this.category = response;
                this.loading = false;
                this.$nextTick(() => {
                    this.$refs.scrollbar.calculateSize();
                });
            })
                .catch((error) => {
                    this.loading = false;
                });
        },
    },
};
</script>
