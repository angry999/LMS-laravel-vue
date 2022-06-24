<template>
    <router-link 
        :to="{name: 'units', params: {categoryName: category.slug}}" 
        class="category">
        <preview 
            :image="category.icon" 
            class-name="icon"></preview>
        <div class="title">
            <h3 :style="{color: `#${category.color}`}">{{ category.title }}</h3>
            <p>{{ category.description }}</p>
        </div>
        <progress-meter 
            :value="category.progress"
            :max="100" 
            :color="category.color" />
    </router-link>
</template>

<script>
export default {
    props: {
        category: {
            type: Object,
        },
    },
};
</script>