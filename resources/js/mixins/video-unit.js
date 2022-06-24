export default {
    data() {
        return {
            video: null,
            preview: null,
            sentence: null,
        };
    },
    created() {
        this.subtitles = [];
    },
    mounted() {
        this.video = this.unit.listeningVideo;
        this.preview = this.unit.preview;
        this.subtitles = [...this.unit.subtitles];
        this.$nextTick(() => {
            this.$refs.player.play();
        });
    },
    methods: {
        progressChanged(progress) {
            if (this.subtitles.length > 0 && this.subtitles[0].time <= progress) {
                this.sentence = this.subtitles.shift();
            }
        },
    },
};