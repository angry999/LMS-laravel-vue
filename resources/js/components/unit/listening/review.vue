<template>
    <select-translation
        v-if="translations"
        :unit="unit"
        @back="hideTranslationDialog" />
    <div
        v-else
        class="reading-review full-height">
        <titlebar>
            {{ unit.title }}
            <router-link 
                slot="right" 
                :to="routeTo('unit-menu', unit.id)" 
                class="next text-link icon-right">
                {{ __('Next') }} <span class="icon-32 icon-next"></span>
            </router-link>
            <a
                slot="left"
                href="#"
                class="select-translation"
                @click.prevent="selectTranslation">
                <flag-icon 
                    :country="flag"
                    size="normal"/>
            </a>
        </titlebar>
        <div class="dialog-review container scrollable">
            <scrollbar>
                <div class="sentences-list">
                    <dialog-sentence 
                        v-for="subtitle in unit.subtitles" 
                        :unit="unit" 
                        :sentence-id="subtitle.sentence_id" 
                        :text="subtitle.text" 
                        :speaker="subtitle.speaker" 
                        :key="subtitle.sentence_id" 
                        @clickReference="showVocabulary" />
                </div>
            </scrollbar>
            <vocabulary-word 
                :reference="reference" 
                @closed="reference = null" />
        </div>
    </div>
</template>

<script>
import UnitMixin from '../../../mixins/unit';
import translation from '../../../services/translation';

export default {
    mixins: [
        UnitMixin,
    ],
    data() {
        return {
            reference: null,
            translations: false,
            flag: translation.earthFlag,
        };
    },
    mounted() {
        this.flag = translation.current || translation.earthFlag;
    },
    methods: {
        showVocabulary(reference) {
            this.reference = reference;
        },
        selectTranslation() {
            this.translations = true;
        },
        hideTranslationDialog() {
            this.translations = false;
            this.flag = translation.current || translation.earthFlag;
        },
    },
};
</script>