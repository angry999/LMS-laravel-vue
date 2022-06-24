<template>
    <div
        v-loading.fullscreen.lock="submitting"
        class="select-translation-dialog full-height">
        <titlebar>
            {{ __('Translation') }}
            <a
                slot="left"
                href="#"
                class="back"
                @click.prevent="getBack">
                <span class="icon-32 icon-back"></span>
            </a>
        </titlebar>
        <div class="wrapper full-height">
            <div class="head">
                <template v-if="translateTo !== ''">
                    {{ __('Translate to') }}: {{ language(translateTo) }}
                </template>
                <template v-else>
                    {{ __('No translation') }}
                </template>
            </div>
            <div class="body">
                <scrollbar>
                    <div class="options-list">
                        <div 
                            v-for="option in options"
                            class="option">
                            <el-button
                                @click="changeTranslation(option)">
                                {{ language(option) }}
                                <flag-icon 
                                    :country="option"
                                    size="large"/>
                            </el-button>
                        </div>
                    </div>
                </scrollbar>
            </div>
        </div>
    </div>
</template>

<script>
import translation from '../services/translation';
import Unit from '../models/unit';

const EN = 'en';
const LANGUAGE_NAMES = translation.languageNames;

export default {
    props: {
        unit: {
            type: Object,
            default: () => null,
        },
    },
    data() {
        return {
            translateTo: '',
            options: [],
            submitting: false,
        };
    },
    mounted() {
        this.translateTo = translation.current;
        this.options = translation.availableLanguages;
    },
    methods: {
        getBack() {
            this.$emit('back');
        },
        language(code) {
            let c = code.toLowerCase();
            if (c === EN) {
                return this.__('No translation');
            }
            return (c in LANGUAGE_NAMES) ? LANGUAGE_NAMES[c] : c;
        },
        changeTranslation(to) {
            let c = to !== EN ? to : '';
            if (this.translateTo === c) {
                this.getBack();
            } else {
                this.submitting = true;
                translation.change(c, this.unit ? this.unit.id : null).then((response) => {
                    this.submitting = false;
                    Unit.invalidateCache();
                    if (this.unit) {
                        this.unit.translations = response.translations;
                    }
                    this.getBack();
                }).catch((e) => {
                    this.submitting = false;
                });
            }
        },
    },
};
</script>