<template>
    <div 
        v-loading.fullscreen.lock="loading"
        class="standard-layout has-titlebar container full-height">
        <select-translation
            v-if="translations"
            @back="hideTranslationDialog" />
        <div 
            v-else
            class="profile-container full-height">
            <titlebar :back="{name: 'home'}">{{ __('My profile') }}</titlebar>
            <div class="wrapper full-height">
                <div class="header">
                    <span class="avatar"></span>
                    <div class="username">{{ username }}</div>
                </div>
                <div class="body">
                    <div 
                        v-if="!loading"
                        class="scores">
                        <div class="sentences">
                            <stars 
                                :score="sentencesScore" 
                                allow-zero />
                            <small>{{ sentencesNumber }} {{ __('Sentences') }}</small>
                        </div>
                        <div class="quiz">
                            <div>{{ quizScore }}%</div>
                            <small>{{ quizNumber }} {{ __('Quizzes') }}</small>
                        </div>
                        <div class="coverage">
                            <div>{{ coverage }}%</div>
                            <small>{{ __('Coverage') }}</small>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <el-button
                        @click="selectTranslation">
                        {{ __('Translation') }}
                        <flag-icon 
                            :country="flag"
                            size="large" />
                    </el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import translation from '../services/translation';
import user from '../models/user';
import catalogue from '../services/catalogue';

const FLAG_EARTH = 'earth';

export default {
    data() {
        return {
            loading: false,
            sentencesScore: 0,
            sentencesNumber: 0,
            quizScore: 0,
            quizNumber: 0,
            coverage: 0,
            flag: FLAG_EARTH,
            translations: false,
        };
    },
    computed: {
        username() {
            return this.$store.state.user.name;
        }
    },
    mounted() {
        this.flag = translation.current || FLAG_EARTH;
        this.load();
    },
    methods: {
        load() {
            this.loading = true;
            catalogue.getTotalSize().then((size) => {
                return user.getOverallProgress().then((progress) => {
                    this.loading = false;
                    this.sentencesScore = progress.speakingScore;
                    this.sentencesNumber = progress.sentences;
                    this.quizScore = progress.quizScore;
                    this.quizNumber = progress.quizzes;
                    this.coverage = Math.floor(100 * progress.units / size);
                })
            })
                .catch(() => {
                    this.loading = false;
                });
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