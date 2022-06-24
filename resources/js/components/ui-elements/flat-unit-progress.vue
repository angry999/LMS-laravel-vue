<template>
    <div class="flat-unit-progress">
        <div class="bars">
            <div
                v-for="n in total"
                :class="{filled: progress >= n}">
            </div>
        </div>
        <div class="numbers">{{ progress }} / {{ total }}</div>
    </div>
</template>

<script>
export default {
    props: {
        progress: Number,
        total: {
            type: Number,
            default: 7,
        },
    },
};
</script>

