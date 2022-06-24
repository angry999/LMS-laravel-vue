<template>
    <div :class="{'speaker': true, 'playing': ready && playing}">
        <a 
            href="#"
            class="icon-32 icon-sound"
            @click.prevent="play()"></a>
        <div 
            v-if="loading"
            class="loading-container">
            <div 
                v-loading="loading"
                class="overlay">
            </div>
        </div>
    </div>
</template>

<script>
const HAVE_ENOUGH_DATA = 4;

export default {
    props: {
        sentence: Object,
    },
    data() {
        return {
            playing: false,
            ready: false,
            loading: false,
        };
    },
    created() {
        this.player = null;
    },
    methods: {
        play() {
            if (!this.playing) {
                this.playing = true;
                this.setupPlayer().then(() => {
                    this.player.currentTime = 0;
                    this.player.play();
                    this.player.onended = () => {
                        this.playing = false;
                    }
                });
            }
        },
        setupPlayer() {
            if (this.player) {
                return Promise.resolve(this.player);
            }
            this.loading = true;
            return new Promise((resolve, reject) => {
                this.player = this.createAudioPlayer(this.sentence.video || this.sentence.audio);
                if (this.player.readyState == HAVE_ENOUGH_DATA) {
                    resolve(this.player);
                } else {
                    this.player.oncanplaythrough = () => {
                        resolve(this.player);
                    }
                }
            }).then(() => {
                this.loading = false;
                this.ready = true;
            });
        },
        createAudioPlayer(url) {
            let player = document.createElement('audio');
            player.src = url;
            player.load();
            return player;
        },
    },
};
</script>