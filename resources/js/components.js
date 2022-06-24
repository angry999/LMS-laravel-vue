import Vue from 'vue';

import {
    Input,
    Button,
    Notification,
    Loading,
    Dialog,
    Tooltip
} from 'element-ui';
import AudioRecorder from './components/audio-recorder/recorder.vue'

Vue.use(Input);
Vue.use(Button);
Vue.use(Dialog);
Vue.use(Tooltip);
Vue.use(Loading.directive);

Vue.component('scrollbar', require('./components/ui-elements/scrollbar.vue'));
Vue.component('side-menu', require('./components/side-menu.vue'));
Vue.component('progress-meter', require('./components/ui-elements/progress.vue'));
Vue.component('unit-progress', require('./components/ui-elements/unit-progress.vue'));
Vue.component('flat-unit-progress', require('./components/ui-elements/flat-unit-progress.vue'));
Vue.component('difficulty', require('./components/ui-elements/difficulty.vue'));
Vue.component('titlebar', require('./components/ui-elements/titlebar.vue'));
Vue.component('userbar', require('./components/userbar.vue'));
Vue.component('stars', require('./components/ui-elements/stars.vue'));
Vue.component('check', require('./components/ui-elements/check.vue'));
Vue.component('preview', require('./components/ui-elements/preview.vue'));
Vue.component('copyright', require('./components/ui-elements/copyright.vue'));
Vue.component('mic-indicator', require('./components/ui-elements/mic-indicator.vue'));
Vue.component('arc-timer', require('./components/ui-elements/arc-timer.vue'));
Vue.component('flag-icon', require('./components/ui-elements/flag-icon.vue'));

Vue.component('home', require('./components/home.vue'));
Vue.component('login', require('./components/login.vue'));
Vue.component('forgot-password', require('./components/forgot-password.vue'));
Vue.component('about', require('./components/about.vue'));
Vue.component('catalogue', require('./components/catalogue.vue'));
Vue.component('catalogue-category', require('./components/catalogue/category.vue'));
Vue.component('catalogue-units', require('./components/catalogue/units.vue'));
Vue.component('catalogue-unit', require('./components/catalogue/unit.vue'));
Vue.component('units-header', require('./components/catalogue/units-header.vue'));
Vue.component('unit', require('./components/unit.vue'));
Vue.component('unit-menu', require('./components/unit/menu.vue'));
Vue.component('unit-guidedspeech', require('./components/unit/guidedspeech.vue'));
Vue.component('unit-listening', require('./components/unit/listening/listening.vue'));
Vue.component('listening-review', require('./components/unit/listening/review.vue'));
Vue.component('unit-activity', require('./components/unit/activity.vue'));
Vue.component('unit-activity-speech', require('./components/unit/activity-speech.vue'));
Vue.component('video-player', require('./components/unit/video-player.vue'));
Vue.component('video-player-speech', require('./components/unit/speeching/video-player.vue'));
Vue.component('speeching-summary', require('./components/unit/speeching/review/summary.vue'));
Vue.component('review-speeching-actions', require('./components/unit/speeching/review/review-actions.vue'));
Vue.component('play-sentence-speeching-action', require('./components/unit/speeching/review/actions/play-sentence-action.vue'));
Vue.component('improve-reading-speeching-action', require('./components/unit/speeching/review/actions/improve-reading-action.vue'));
Vue.component('play-record-speeching-action', require('./components/unit/speeching/review/actions/play-record-action.vue'));

Vue.component('subtitle', require('./components/unit/subtitle.vue'));
Vue.component('dialog-sentence', require('./components/unit/dialog/sentence.vue'));
Vue.component('vocabulary-word', require('./components/unit/vocabulary/word.vue'));
Vue.component('vocabulary-speaker', require('./components/unit/vocabulary/speaker.vue'));
Vue.component('unit-reading', require('./components/unit/reading/reading.vue'));
Vue.component('retry-dialog', require('./components/unit/reading/retry-dialog.vue'));
Vue.component('choose-part', require('./components/unit/reading/choose-part.vue'));
Vue.component('ready-double', require('./components/unit/reading/ready-double.vue'));
Vue.component('reading-countdown', require('./components/unit/reading/countdown.vue'));
Vue.component('sentence-score', require('./components/unit/reading/sentence-score.vue'));
Vue.component('reading-review', require('./components/unit/reading/review/review.vue'));
Vue.component('reading-summary', require('./components/unit/reading/review/summary.vue'));
Vue.component('review-actions', require('./components/unit/reading/review/review-actions.vue'));
Vue.component('play-sentence-action', require('./components/unit/reading/review/actions/play-sentence-action.vue'));
Vue.component('improve-reading-action', require('./components/unit/reading/review/actions/improve-reading-action.vue'));
Vue.component('play-record-action', require('./components/unit/reading/review/actions/play-record-action.vue'));

Vue.component('unit-speeching', require('./components/unit/speeching/speeching.vue'));

Vue.component('unit-quiz', require('./components/unit/quiz/quiz.vue'));
Vue.component('quiz-progress', require('./components/unit/quiz/progress.vue'));
Vue.component('quiz-question', require('./components/unit/quiz/question.vue'));
Vue.component('quiz-start', require('./components/unit/quiz/start.vue'));
Vue.component('quiz-pause', require('./components/unit/quiz/pause.vue'));
Vue.component('quiz-summary', require('./components/unit/quiz/summary.vue'));
Vue.component('unit-pronounciation', require('./components/unit/pronounciation/pronounciation.vue'));
Vue.component('unit-result', require('./components/unit/result.vue'));
Vue.component('select-translation', require('./components/select-translation.vue'));
Vue.component('profile', require('./components/profile.vue'));

Vue.component('audio-recorder', AudioRecorder)

Vue.prototype.$notify = Notification;
Vue.prototype.$loading = Loading.service;