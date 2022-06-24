<template>
    <span 
        :class="`flag-icon size-${size}`"
        :style="{backgroundImage: `url(${iconUrl})`}"></span>
</template>

<script>
const SIZES = {
    small: '/img/flags/48x32/',
    normal: '/img/flags/64x42/',
    large: '/img/flags/96x64/',
};

const ALIASES = {
    en: 'earth',
};

export default {
    props: {
        country: String,
        size: {
            type: String,
            default: 'normal',
        },
    },
    computed: {
        iconUrl() {
            return [
                this.getSizePath(),
                this.getFileName(),
                '.png'
            ].join('');
        },
    },
    methods: {
        getSizePath() {
            return (this.size in SIZES) ? SIZES[this.size] : SIZES['normal'];
        },
        getFileName() {
            let code = this.country.toLowerCase();
            return (code in ALIASES) ? ALIASES[code] : code;
        },
    },
};
</script>