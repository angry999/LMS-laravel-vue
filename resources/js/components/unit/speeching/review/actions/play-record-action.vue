<template>
    <span :class="{'play-record-action': true, 'playing': ready && playing}">
        <a 
            href="#" 
            class="icon-replay-audio"
            @click.prevent="playStop"></a>
        <div 
            v-if="loading"
            class="loading-container">
            <div 
                v-loading="loading"
                class="overlay">
            </div>
        </div>
    </span>
</template>

<script>
import asr from '../../../../../services/asr';

const NO_ERROR = 0;
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
    beforeDestroy() {
        asr.unbindAll();
    },
    methods: {
        playStop() {
            if (this.playing) {
                this.playing = false;
                if (this.ready) {
                    this.player.pause();
                }
            } else {
                this.playing = true;
                this.setupPlayer().then(() => {
                    if (this.playing) {
                        this.player.currentTime = 0;
                        this.player.play();
                        this.player.onended = () => {
                            this.playing = false;
                        }
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
                asr.initialized(() => {
                    asr.getRecordingUrl(this.sentence.recordingId, (result) => {
                        if (result.err == NO_ERROR) {
                            this.player = this.createAudioPlayer(result.data);
                            if (this.player.readyState == HAVE_ENOUGH_DATA) {
                                this.loading = false;
                                this.ready = true;
                                resolve(this.player);
                            } else {
                                this.player.oncanplaythrough = () => {
                                    this.loading = false;
                                    this.ready = true;
                                    resolve(this.player);
                                }
                            }
                        } else {
                            reject(result.err); 
                        }
                    });
                })
                    .initIfNotInitialized();
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