<template>
    <div
        v-loading="connecting"
        :element-loading-text="__('Connecting...')"
        class="unit-reading-container">
        <choose-part
            :visible="isChoice"
            :unit="unit"
            @choice="takePart"/>
        <ready-double
            v-if="isReadyDouble"
            @start="startReading"/>
        <template v-else-if="isReading || isCountdown">
            <titlebar :back="routeTo('unit-menu', unit.id)">{{ unit.title }}</titlebar>
            <div class="video-player-wrapper">
                <video-player
                    ref="player"
                    :video="video"
                    :audio="audio"
                    :preview="preview"
                    :hide-loader="hideVideoLoader"
                    @ended="videoEnded"/>
                <sentence-score
                    v-if="sentenceScore !== null"
                    :score="sentenceScore"
                    size="larger"
                    animated />
            </div>
            <subtitle
                :text="speakerSentence"
                speaker="1" />
            <subtitle
                :text="readerSentence"
                :level="micLevel"
                speaker="2"
                indicator />
            <reading-countdown
                v-if="isCountdown"
                @ended="startReading" />
        </template>
        <template v-else-if="isReview">
            <reading-review
                :unit="unit"
                :sentences="sentences"
                :inverse-parts="inverseParts"
                @next="showSummary" />
        </template>
        <template v-else-if="isSummary">
            <reading-summary
                :unit="unit"
                :sentences="sentences"
                :activity-index="activityIndex"
                @improve="showReview" />
        </template>
        <retry-dialog
            :visible="showRetry"
            @retry="retry"
            @skip="skip" />
    </div>
</template>

<script>
import UnitMixin from '../../../mixins/unit';
import asr from '../../../services/asr';
import ImageLoader from '../../../tools/image-loader';
import {MultiCue} from '../../../models/unit-activity';
import { mapGetters } from "vuex";

const MAX_RETRIES = 1;
const RESULT_TIMEOUT = 3000;
const STAGE_INIT = 0;
const STAGE_CHOICE = 1;
const STAGE_READY_DOUBLE = 2;
const STAGE_COUNTDOWN = 3;
const STAGE_READING = 4;
const STAGE_REVIEW = 5;
const STAGE_SUMMARY = 6;

export default {
    mixins: [
        UnitMixin,
    ],
    props: {
        activityNumber: [
            String,
            Number,
        ],
    },
    data() {
        return {
            activity: null,
            video: null,
            audio: null,
            preview: null,
            speakerSentence: null,
            readerSentence: null,
            micLevel: 0,
            connecting: true,
            hideVideoLoader: true,
            showRetry: false,
            sentenceScore: null,
            sentences: [],
            stage: STAGE_INIT,
            inverseParts: false,
        };
    },
    computed: {
        isChoice() {

            if(!this.unit.showSides) {
                return this.startReading();
            }

            return this.stage === STAGE_CHOICE;
        },
        isReadyDouble() {
            return this.stage === STAGE_READY_DOUBLE;
        },
        isCountdown() {
            return this.stage === STAGE_COUNTDOWN;
        },
        isReading() {
            return this.stage === STAGE_READING;
        },
        isReview() {
            return this.stage === STAGE_REVIEW;
        },
        isSummary() {
            return this.stage === STAGE_SUMMARY;
        },
        activityIndex() {
            return parseInt(this.activityNumber);
        },
        ...mapGetters({
            user : 'user'
        }),
    },
    created() {
        this.iterations = null;
        this.currentReaderCue = null;
        this.currentSpeakerCue = null;
        this.retries = 0;
        this.resultTimer = null;
        this.switchTo = 0;
        this.story = [];

        firebase.analytics().logEvent('Dialog', {activity: 'start', location : this.unit.title, premium: this.user.premium});
    },
    mounted() {
        this.activity = this.unit.getActivity(this.activityNumber);
        if (!this.activity || this.activity.locked) {
            this.$router.push(this.routeTo('unit-menu', this.unit.id));
        } else {
            this.iterations = this.activity.makeIterator();
            this.preview = this.unit.preview;
            this.preloadSnapshots().then(() => {
                this.setupAsr();
            });
        }
    },
    beforeDestroy() {
        asr.destroy();
        clearTimeout(this.resultTimer);
    },
    methods: {
        preloadSnapshots() {
            return new Promise((resolve) => {
                let loader = new ImageLoader();
                for (let cue of this.activity.all) {
                    if (cue.startSnapshot) {
                        loader.enqueue(cue.startSnapshot);
                    }
                    if (cue.endSnapshot) {
                        loader.enqueue(cue.endSnapshot);
                    }
                }
                loader.completed(resolve);
            });
        },
        setupAsr() {
            try {
                asr.initialized(() => {
                    this.connecting = false;
                    if (this.activity.isDouble) {
                        this.stage = STAGE_READY_DOUBLE;
                    } else {
                        this.stage = STAGE_CHOICE;
                    }
                })
                    .ready(() => {
                        this.readerSentence = this.currentReaderCue.text;
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
                    })
                    .reinstantiate()
                    .init();
            } catch (e) {
                this.$error(this.__('Something went wrong. Please try again later.'));
            }
        },
        takePart(inversed) {
            this.inverseParts = inversed;
            if (inversed) {
                this.showCountdown();
            } else {
                this.startReading();
            }

            firebase.analytics().logEvent('Dialog', {activity: 'start_recording', location : this.unit.title});
        },
        startReading() {
            this.stage = STAGE_READING;
            this.nextIteration();
        },
        nextIteration(promote = false) {
            if (promote) {
                this.iterations.next(this.switchTo);
                this.switchTo = 0;
            }
            if (this.iterations.hasMore) {
                let cue = this.iterations.current;
                if (lodash.isArray(cue)) {
                    cue = new MultiCue(cue);
                }
                this.showCue(cue);
            } else {
                this.complete();
            }
        },
        showCue(cue) {
            if (this.inverseParts ? cue.isReader : cue.isSpeaker) {
                this.currentSpeakerCue = cue;
                this.video = cue.video;
                this.audio = cue.audio;
                this.preview = cue.startSnapshot;
                this.readerSentence = null;
                this.speakerSentence = null;
                this.$nextTick(() => {
                    this.$refs.player.play(() => {
                        this.speakerSentence = cue.text;
                        if (this.video) {
                            this.preview = cue.endSnapshot;
                        }
                    });
                });
            } else {
                this.currentReaderCue = cue;
                asr.start(cue);
            }
        },
        videoEnded() {
            this.story.push(this.currentSpeakerCue);
            this.video = null;
            this.audio = null;
            this.nextIteration(true);
        },
        success(result) {
            this.retries = 0;
            if (this.currentReaderCue instanceof MultiCue) {
                this.currentReaderCue = this.detectChoice(result);
            }
            this.currentReaderCue.result = result;
            this.story.push(this.currentReaderCue);
            this.displayResult(result, this.currentReaderCue.sentenceId, () => {
                this.nextIteration(true);
            });

            firebase.analytics().logEvent('Dialog', {activity: 'recognized_recording', location : this.unit.title});
        },
        detectChoice(result) {
            let choice = this.currentReaderCue.findCue(result.instance);
            if (choice >= 0) {
                this.switchTo = choice;
            } else {
                this.switchTo = 0;
            }
            return this.currentReaderCue.getCue(this.switchTo);
        },
        failure() {
            if (this.retries >= MAX_RETRIES) {
                this.skip();
            } else {
                this.retries++;
                this.showRetry = true;
            }
        },
        retry() {
            this.showRetry = false;
            this.readerSentence = null;
            if (this.inverseParts) {
                this.showCountdown();
            } else {
                this.iterations.backward();
                this.story.pop();
                this.nextIteration();
            }
        },
        skip() {
            if (this.currentReaderCue instanceof MultiCue) {
                this.currentReaderCue = this.currentReaderCue.getCue();
            }
            this.currentReaderCue.result = asr.emptyResult();
            this.showRetry = false;
            this.retries = 0;
            this.story.push(this.currentReaderCue);
            this.nextIteration(true);
        },
        displayResult(result, sentenceId, then) {
            this.sentenceScore = result.score;
            this.resultTimer = setTimeout(() => {
                this.sentenceScore = null;
                then();
            }, RESULT_TIMEOUT);

            firebase.analytics().logEvent('Dialog', {activity: 'scored', location : this.unit.title});
        },
        complete() {
            asr.unbindAll();
            this.iterations.rewind();
            this.sentences = [...this.story];
            this.showReview();

            firebase.analytics().logEvent('Dialog', {activity: 'finished_recording', location : this.unit.title});
        },
        showReview() {
            this.stage = STAGE_REVIEW;
        },
        showSummary() {
            this.stage = STAGE_SUMMARY;
        },
        showCountdown() {
            this.stage = STAGE_COUNTDOWN;
        },
    },
};
</script>
