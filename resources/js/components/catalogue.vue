<template>
  <div
    v-loading.fullscreen.lock="loading"
    class="catalogue-component standard-layout container scrollable"
  >
    <scrollbar ref="scrollbar">
      <div class="category-list">
        <catalogue-category
          v-for="category in categories"
          :category="category"
          :key="category.id"
        />
      </div>
    </scrollbar>
  </div>
</template>

<script>
import catalogue from "../services/catalogue";

export default {
  data() {
    return {
      categories: [],
      loading: false,
    };
  },
  mounted() {
    this.load();
  },
  methods: {
    load() {
      this.loading = true;
      catalogue
        .getCategories()
        .then((response) => {
          this.categories = response;
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
