<template>
    <div class="category-details">
        <div class="details">
            <div class="primary">
                <div 
                    :style="{backgroundImage: `url(${category.icon})`}" 
                    class="icon"></div>
                <div 
                    :style="{color: `#${category.color}`}" 
                    class="title">{{ category.title }}</div>
            </div>
            <div class="secondary">
                <div class="level">
                    <difficulty 
                        :start-level="category.startLevel" 
                        :end-level="category.endLevel" />
                </div>
                <div class="sub-title">{{ category.title }} {{ __('Lessons') }}</div>
            </div>
        </div>
        <progress-meter 
            :value="category.progress"
            :max="100" />
    </div>
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