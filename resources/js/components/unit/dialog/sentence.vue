<template>
    <div :class="`dialog-sentence speaker-${speakerNumber}`">
        <div 
            :style="iconStyle" 
            class="speaker-image"></div>
        <template v-if="result">
            <sentence-score 
                :score="score" 
                size="tiny" />
            <div class="spike shifted"></div>
            <div 
                :class="{'toggle-actions': true, expanded: showActions}" 
                @click="toggleActions"></div>
        </template>
        <template v-else>
            <div class="spike"></div>
        </template>
        <div 
            :class="{text: true, 'with-actions': showActions && result}"
            @click="displayActions">
            <div class="original">
                <template v-for="token in tokens">
                    <span 
                        v-if="token.isWord" 
                        :class="wordClass(token)" 
                        @click.prevent="wordClick(token)">{{ token.text }}</span>
                    <template v-else>{{ token.text }}</template>
                </template>
            </div>
            <div 
                v-if="result"
                class="updated-score">
                <sentence-score
                    v-if="showScoreUpdate"
                    :score="score" 
                    animated />
            </div>
            <div :class="{translation: true, disabled: !unit.hasTranslations}">
                {{ unit.getTranslation(sentenceId, '...') }}
            </div>
            <review-actions 
                v-if="result && showActions" 
                :result="result" 
                :unit="unit" 
                @scoreUpdated="scoreUpdated" />
        </div>
    </div>
</template>

<script>
import Tokenizer from '../../../tools/tokenizer';

const SPEAKER = 1;    
const NATIVE_SPEAKER = 2;
const SCORE_TIMEOUT = 2000;
const PLACEHOLDER_IMAGE = '/img/speaker-placeholder.png';
    
export default {
    props: {
        unit: Object,
        sentenceId: Number,
        text: String,
        speaker: Number,
        result: Object,
        inverseParts: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            tokens: [],
            showActions: false,
            score: 0,
            showScoreUpdate: false,
        };
    },
    computed: {
        iconStyle() {
            if (this.inverseParts) {
                if (this.speaker === SPEAKER) {
                    return {};
                }
                return {
                    backgroundImage: `url(${this.unit.nativeSpeakerImage}),url(${PLACEHOLDER_IMAGE})`,
                };
            }
            if (this.speaker === NATIVE_SPEAKER) {
                return {};
            }
            return {
                backgroundImage: `url(${this.unit.speakerImage}),url(${PLACEHOLDER_IMAGE})`,
            };
        },
        speakerNumber() {
            if (this.inverseParts) {
                return this.speaker === NATIVE_SPEAKER ? SPEAKER : NATIVE_SPEAKER;
            }
            return this.speaker;
        },
    },
    created() {
        this.tokenizer = null;
        this.scoreTimer = null;
    },
    mounted() {
        this.tokenizer = new Tokenizer(this.text);
        this.checkVocabulary();
        this.updateResults(this.result);
        this.tokens = [...this.tokenizer];
        eventHub.$on('toggleActions', (sentenceId) => {
            this.showActions = sentenceId === this.sentenceId;
        });
    },
    beforeDestroy() {
        clearTimeout(this.scoreTimer);
    },
    methods: {
        checkVocabulary() {
            let references = this.unit.vocabulary.getReferences(this.sentenceId);
            references.forEach((reference) => {
                reference.indices.forEach((index) => {
                    let token = this.tokenizer.word(index);
                    if (token) {
                        token.setProp('reference', reference);
                    }
                });
            });
        },
        updateResults(result) {
            if (result) {
                this.result.sentence.result = result;
                this.score = result.score;
                result.words.forEach((word, index) => {
                    let token = this.tokenizer.find(word.word, index);
                    if (token) {
                        token.setProp('score', word.score);
                    }
                });
            }
        },
        wordClass(word) {
            return {
                reference: word.getProp('reference') !== undefined,
                'bad-score': this.isBadScore(word.getProp('score')),
                'good-score': this.isGoodScore(word.getProp('score')),
            };
        },
        wordClick(word) {
            let reference = word.getProp('reference');
            if (reference) {
                this.$emit('clickReference', reference);
            }
        },
        isBadScore(score) {
            return appConfig.scores.bad.indexOf(Number(score)) !== -1;
        },
        isGoodScore(score) {
            return appConfig.scores.good.indexOf(Number(score)) !== -1;
        },
        toggleActions() {
            if (!this.showActions) {
                eventHub.$emit('toggleActions', this.sentenceId);
            } else {
                this.showActions = false;
            }
        },
        displayActions() {
            if (!this.showActions) {
                eventHub.$emit('toggleActions', this.sentenceId);
            }
        },
        scoreUpdated(result) {
            this.updateResults(result);
            this.showScoreUpdate = true;
            this.scoreTimer = setTimeout(() => {
                this.showScoreUpdate = false;
            }, SCORE_TIMEOUT);
        },
    },
};
</script>