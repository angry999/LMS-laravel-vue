<template>
    <span class="improve-reading-action">
        <a
            :class="{'icon-32': true, 'icon-retry-speak': true, ready: ready}"
            href="#"
            @click.prevent="activate"></a>
        <div
            v-if="active"
            class="loading-container">
            <div
                v-loading="connecting"
                class="overlay">
                <mic-indicator
                    :level="micLevel"
                    skin="frame" />
            </div>
        </div>
        <!--<retry-dialog
            :visible="showRetry"
            @retry="retry"
            @skip="skip" />-->
    </span>
</template>

<script>
import asr from '../../../../../services/asr';

export default {
    props: {
        sentence: Object,
        unit: Object
    },
    data() {
        return {
            micLevel: 0,
            active: false,
            connecting: true,
            ready: false,
            showRetry: false,
        };
    },
    beforeDestroy() {
        this.suspend();
    },
    methods: {
        activate() {
            firebase.analytics().logEvent('Dialog', {activity: 'review_start_recording', location : this.unit.title});

            if (!this.active) {
                this.active = true;
                asr.ready(() => {
                    this.connecting = false;
                    this.ready = true;
                })
                    .error((error) => {
                        this.$error(this.__('Something went wrong. Please try again later.'));
                    })
                    .success((data) => {
                        this.success(data);
                    })
                    .failure((error) => {
                        this.failure();
                    })
                    .micLevelChanged((value) => {
                        this.micLevel = value;
                    }).initialized(() => {
                        asr.start(this.sentence);
                    })
                    .initIfNotInitialized();
            }
        },
        success(result) {
            this.suspend();
            this.$emit('scoreUpdated', result);
        },
        failure() {
            this.showRetry = true;
        },
        retry() {
            this.connecting = true;
            this.ready = false;
            this.showRetry = false;
            asr.start(this.sentence);
        },
        skip() {
            this.showRetry = false;
            this.suspend();
        },
        suspend() {
            this.active = false;
            this.connecting = true;
            this.ready = false;
            asr.unbindAll();
            if (asr.active) {
                asr.shutdown();
            }
        },
    },
    watch: {
        showRetry() {
            if(this.showRetry) {
                this.$notify({
                    type : 'warning',
                    showClose : true,
                    message: this.__('Please say again'),
                    customClass: 'alert-warning',
                    duration : 7000
                });
                this.skip();
            }
        }
    }
};
</script>
