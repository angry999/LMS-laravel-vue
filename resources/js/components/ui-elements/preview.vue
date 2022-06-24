<template>
    <div 
        :class="className" 
        :style="{backgroundImage: `url(${image})`}">
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        className: {
            type: String,
            default: 'preview'
        },
        image: String,
    },
};
</script>