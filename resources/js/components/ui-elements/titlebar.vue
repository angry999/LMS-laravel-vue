<template>
    <div class="titlebar">
        <div class="container">
            <div class="prefix">
                <slot name="left">
                    <router-link 
                        v-if="back !== null" 
                        :to="back" 
                        class="back">
                        <span class="icon-32 icon-back"></span>
                    </router-link>
                </slot>
            </div>
            <div class="title">
                <slot></slot>
            </div>
            <div class="postfix">
                <slot name="right"></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        back: {
            type: Object,
            default: () => null,
        },
    },
};
</script>