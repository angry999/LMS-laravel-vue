<template>
    <select-translation
        v-if="translations"
        :unit="unit"
        @back="hideTranslationDialog" />
    <div
        v-else
        class="full-height">
        <titlebar>
            {{ unit.title }}
            <a
                slot="right"
                href="#"
                class="next text-link icon-right"
                @click.prevent="next">
                {{ __('Next') }} <span class="icon-32 icon-next"></span>
            </a>
            <div
                slot="left"
                class="btn-set">
                <router-link
                    :to="routeTo('unit-menu', unit.id)"
                    class="back">
                    <span class="icon-32 icon-back"></span>
                </router-link>
                <a
                    href="#"
                    class="select-translation"
                    @click.prevent="selectTranslation">
                    <flag-icon
                        :country="flag"
                        size="normal"/>
                </a>
            </div>
        </titlebar>

        <div class="dialog-review container scrollable">
            <scrollbar>
                <div class="sentences-list">
                    <dialog-sentence
                        v-for="cue in sentences"
                        :unit="unit"
                        :sentence-id="cue.sentenceId"
                        :text="cue.text"
                        :speaker="cue.actor"
                        :result="cue.result"
                        :key="cue.sentenceId"
                        :inverse-parts="inverseParts"
                        @clickReference="showVocabulary" />
                </div>
            </scrollbar>
            <vocabulary-word
                :reference="reference"
                @closed="reference = null" />
        </div>

        <div class="legend">
            <div>
                <div>
                    <div>
                        <span class="legend-color legend-color-red">red</span>
                    </div>
                    <div>
                        You can do better. Try again.
                    </div>
                </div>
                <div>
                    <div>
                        <span class="legend-color legend-color-black">black</span>
                    </div>
                    <div>
                        OK.
                    </div>
                </div>
                <div>
                    <div>
                        <span class="legend-color legend-color-green">green</span>
                    </div>
                    <div>
                        Perfect!
                    </div>
                </div>
            </div>

            <br><br><br>

            <div style="display: table">
                <div style="display:table-cell;">
                    <div class="stars size-tiny"><span class="v-1"></span></div>
                </div>
                <div style="display:table-cell; padding-left: 1em">
                    Try again.
                </div>
            </div>
            <div style="display: table">
                <div style="display:table-cell;">
                    <div class="stars size-tiny"><span class="v-2"></span></div>
                </div>
                <div style="display:table-cell; padding-left: 1em">
                    Good, but you can still improve.
                </div>
            </div>
            <div style="display: table">
                <div style="display:table-cell;">
                    <div class="stars size-tiny"><span class="v-3"></span></div>
                </div>
                <div style="display:table-cell; padding-left: 1em">
                    Nice job!
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import translation from '../../../../services/translation';
import UnitMixin from '../../../../mixins/unit';

const FLAG_EARTH = 'earth';

export default {
    mixins: [
        UnitMixin,
    ],
    props: {
        sentences: Array,
        inverseParts: Boolean,
    },
    data() {
        return {
            reference: null,
            translations: false,
            flag: FLAG_EARTH,
        };
    },
    mounted() {
        this.flag = translation.current || FLAG_EARTH;
        this.$nextTick(() => {
            for (let sentence of this.sentences) {
                if (sentence.isReader && !this.inverseParts || !sentence.isReader && this.inverseParts) {
                    eventHub.$emit('toggleActions', sentence.sentenceId);
                    break;
                }
            }
        });
    },
    methods: {
        showVocabulary(reference) {
            this.reference = reference;
        },
        next() {
            this.$emit('next')
        },
        selectTranslation() {
            this.translations = true;
        },
        hideTranslationDialog() {
            this.translations = false;
            this.flag = translation.current || FLAG_EARTH;
        },
    },
};
</script>
