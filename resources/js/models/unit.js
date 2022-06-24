import { Reading, Speech, Quiz, Cue } from './unit-activity';
import Vocabulary from './vocabulary';
import { UnitProgress } from './progress';
import catalogue from '../services/catalogue';

const CONTENT_BASE_URL = appConfig.cdnUrls.unit;

let cache = {};

class Unit {
    constructor(category, id) {
        this._category = category;
        this._id = id;
        this._data = null;
        this._vocabulary = null;
        this._activities = null;
        this._progress = null;
    }

    static invalidateCache() {
        cache = {};
    }

    getUnitData() {
        if (this.cacheKey in cache) {
            return Promise.resolve(cache[this.cacheKey]);
        } else {
            return catalogue.getCategory(this._category).then((category) => {
                let unit = category.getUnit(this._id);
                let url = unit.isPronounciation ?
                    `/api/unit/${this._id}/pronounciation` :
                    unit.isGuidedSpeech ?
                    `/api/unit/${this._id}/guidedspeech` :
                    `/api/unit/${this._id}`;
                return axios.get(url).then((response) => {
                    return Promise.resolve(cache[this.cacheKey] = {
                        ...unit.meta,
                        ...response.data.unit
                    });
                });
            });
        }
    }

    load() {
        if (this._data === null) {
            return this.getUnitData().then((data) => {
                this.init(data);
                return this;
            });
        } else {
            return Promise.resolve(this);
        }
    }

    init(data) {
        this._data = data;
        if (data.vocabulary !== undefined) {
            this._vocabulary = new Vocabulary(this, data.vocabulary.words);
        }
        if (this._data.dialog !== undefined || this._data.dialogs !== undefined) {
            this._activities = lodash.range(1, data.activities_count + 1).map((number) => {
                return new Reading(this, number);
            });
        } else {
            this._activities = [];
        }

        if (this._data.guidedspeeches !== undefined) {
            this._guidedspeeches = lodash.range(1, data.guidedspeeches_count + 1).map((number) => {
                return new Speech(this, number + data.activities_count);
            });

            if (this._data.quiz !== undefined) {
                this._guidedspeeches.push(new Quiz(this, ((data.activities_count + data.guidedspeeches_count) || 0) + 1));
            }
        } else {
            if (this._data.quiz !== undefined) {
                this._activities.push(new Quiz(this, ((data.activities_count + data.guidedspeeches_count) || 0) + 1));
            }
        }
        if (this._data.progress !== undefined) {
            this._progress = new UnitProgress(this._data.progress);
        }
    }

    getContentUrl(file) {
        return [fix_protocol(CONTENT_BASE_URL), this._id, file].join('/');
    }

    getActivity(number) {
        if (number < this._data.activities_count + 1)
            return lodash.find(this._activities, (item) => item.number == number);
        else
            return lodash.find(this._guidedspeeches, (item) => item.number == number);
    }

    get cacheKey() {
        return `u${this._id}`;
    }

    get id() {
        return this._id;
    }

    get title() {
        return this._data.name;
    }

    get preview() {
        return fix_protocol(this._data.thumbnail_image_url);
    }

    get level() {
        return this._data.level;
    }

    get listeningVideo() {
        return this.getContentUrl(this._data.listening.video);
    }

    get speakerImage() {
        return this.getContentUrl('speaker_image.jpg');
    }

    get nativeSpeakerImage() {
        return this.getContentUrl('native_speaker_image.jpg');
    }

    get activities() {
        return this._activities;
    }

    get guidedspeeches() {
        return this._guidedspeeches;
    }

    get subtitles() {
        return this._data.listening.subtitles;
    }

    get vocabulary() {
        return this._vocabulary;
    }

    getDialog(index = 0) {
        if (this._data.dialogs !== undefined) {
            return this._data.dialogs[index].tree;
        }
        return this._data.dialog.tree;
    }

    getSpeech(index = 0) {
        if (this._data.guidedspeeches !== undefined) {
            return this._data.guidedspeeches[index].guidedspeeches;
        }
        return this._data.guidedspeeches[0].guidedspeeches;
    }

    get isBranching() {
        return this._data.dialog !== undefined && this._data.dialog.tree !== undefined;
    }

    get branchingTree() {
        if (this._data.unit_type == 'GuidedSpeech')
            return this._data.guidedspeeches[0].guidedspeeches;
        else
            return this._data.dialog.tree;
    }

    get hasQuiz() {
        return this._data.quiz !== undefined;
    }

    get quiz() {
        return this._data.quiz.questions;
    }

    get progress() {
        return this._progress;
    }

    set progress(value) {
        this._data.progress = value;
        this._progress = new UnitProgress(this._data.progress);
        catalogue.updateProgress(this, value);
        return this._progress;
    }

    get activitiesCompleted() {
        return this._data.progress ? this._data.progress.activities_completed : 0;
    }

    get quizPassed() {
        return this._data.progress ? this._data.progress.quiz_score > 0 : false;
    }

    get activitiesTotal() {
        return this._data.activities_count;
    }

    get score() {
        return this._data.progress.best_score;
    }

    getActivityScore(number) {
        return this._progress ? this._progress.getActivityProgress(number, (p) => p.score, 0) : 0;
    }

    get hasTranslations() {
        return this._data.translations !== undefined;
    }

    get translations() {
        return this._data.translations;
    }

    get showSides() {
        return this._data.showSides;
    }

    set translations(value) {
        if (value) {
            this._data.translations = value;
        } else {
            delete this._data.translations;
        }
        return value;
    }

    getTranslation(stringId, defaultValue = '') {
        if (this._data.translations !== undefined && (stringId in this._data.translations)) {
            return this._data.translations[stringId];
        }
        return defaultValue;
    }

    getDialogSencence(sentenceId) {
        if (this._data.dialogs !== undefined) {
            for (let dialog of this._data.dialogs) {
                let finder = new SentenceFinder(dialog.tree, sentenceId);
                if (finder.sentence) {
                    return new Cue(this, 1, finder.sentence);
                }
            }
        } else {
            let finder = new SentenceFinder(this._data.dialog.tree, sentenceId);
            if (finder.sentence) {
                return new Cue(this, 1, finder.sentence);
            }
        }
        return null;
    }
}

class SentenceFinder {
    constructor(tree, sentenceId) {
        this._sentenceId = sentenceId;
        this._sentence = null;
        this.find(tree);
    }

    find(tree) {
        if (tree.sentence_id == this._sentenceId) {
            this._sentence = tree;
            return;
        }
        if (tree.options) {
            for (let option of tree.options) {
                if (option.sentence_id == this._sentenceId) {
                    this._sentence = option;
                    return;
                }
                if (option.next) {
                    let result = this.find(option.next);
                    if (result) {
                        this._sentence = result;
                        return;
                    }
                }
            }
        }
    }

    get sentence() {
        return this._sentence;
    }
}

export default Unit;