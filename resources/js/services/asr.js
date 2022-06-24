import store from '../store';

// const ENDPOINT = 'asr-tpk.speakingpal.com';
const ENDPOINT = 'asr10.speakingpal.com';

const NO_ERROR = 0;
const DISCONNECT = 3;
const NOT_RECOGNIZED = 5;
const REC_LEVEL_TOO_LOW = 16;
const RECOGNIZED = 21;

const REC_LEVEL_MULTIPLIER = 200;

const RR_PATTERNS = {
    INTERPRETATION: [new RegExp('<interpretation grammar=".SENTENCE" score="([0-9]+)"( time="([0-9,]+)")?[ ]*>'), {score: 1, time: 3}],
    INSTANCE: [new RegExp('<instance>(.+)</instance>'), {instance: 1}],
    INPUTS: new RegExp('<input.+?</input>', 'g'),
    INPUT: [new RegExp('<input mode="speech" score="([0-9]+)">(.+?)</input>'), {score: 1, word: 2}],
}

class RecognitionResult
{
    constructor(data) {
        this._instance = null;
        this._sentence = null;
        this._score = 0;
        this._time = 0;
        this._words = []
        if (data) {
            this.parse(data);
        }
    }

    parse(data) {
        let header = this.extractObject(data, RR_PATTERNS.INTERPRETATION);
        if (header) {
            this._score = parseInt(header.score);
            this._time = parseInt(header.time);
            this._instance = this.extractObject(data, RR_PATTERNS.INSTANCE).instance;
            for (let input of this.extractArray(data, RR_PATTERNS.INPUTS)) {
                let word = this.extractObject(input, RR_PATTERNS.INPUT);
                this._words.push({
                    word: word.word,
                    score: parseInt(word.score),
                });
            }
        }
    }

    extractArray(text, regexp) {
        let matches = text.match(regexp);
        return matches || [];
    }

    extractObject(text, pattern) {
        let result = {};
        let regexp = pattern[0];
        let map = pattern[1];
        let matches = text.match(regexp);
        if (matches) {
            for (let attr in map) {
                result[attr] = matches[map[attr]];
            }
        }
        return result;
    }

    get instance() {
        return this._instance;
    }

    get score() {
        return this._score;
    }

    get time() {
        return this._time;
    }

    get words() {
        return this._words;
    }

    get sentence() {
        return this._sentence;
    }

    set sentence(value) {
        this._sentence = value;
        return value;
    }
}

class ASR
{
    constructor() {
        this._instance = new SpApi();
        this._initialized = false;
        this._active = false;
        this._prepared = false;
        this._micLevel = 0;
        this._oninitialized = null;
        this._onready = null;
        this._onerror = null;
        this._ondisconnect = null;
        this._onsuccess = null;
        this._onfailure = null;
        this._onlevelchange = null;
    }

    reinstantiate() {
        this._instance.destroy();
        this._instance = new SpApi();
        this._initialized = false;
        this._active = false;
        this._prepared = false;
        return this;
    }

    init() {
        if (!this._initialized) {
            this._instance.initialize(store.state.user.id, store.state.user.apiToken, (data) => {
                if (data.err == NO_ERROR) {
                    this._initialized = true;
                    if (this._oninitialized) {
                        this._oninitialized();
                    }
                } else if (this._onerror) {
                    this._onerror(data.err);
                }
            }, () => {}, ENDPOINT);
        } else if (this._oninitialized) {
            this._oninitialized();
        }
    }

    initIfNotInitialized() {
        if (!this._initialized) {
            this.init();
        }
    }

    checkinit() {
        if (!this._initialized) {
            throw 'SP API is not initialized';
        }
    }

    prepareIfNotPrepared(callback) {
        this.checkinit();
        if (!this._prepared) {
            this._instance.prepare((data) => {
                if (data.err == NO_ERROR) {
                    this._prepared = true;
                    callback();
                } else if (data.err == DISCONNECT) {
                    if (this._ondisconnect) {
                        this._ondisconnect();
                    }
                } else {
                    if (this._onerror) {
                        this._onerror(data.err);
                    }
                }
            });
        } else {
            callback();
        }
    }

    start(sentence) {
        this.checkinit();
        this.prepareIfNotPrepared(() => {
            sentence.generateRecordingId(store.state.user);
            this._instance.recognize(sentence.grammar, sentence.recordingId, (data) => {
                this._micLevel = data * REC_LEVEL_MULTIPLIER;
                if (this._onlevelchange) {
                    this._onlevelchange(this._micLevel);
                }
            }, (data) => {
                if (this._active) {
                    if ([NO_ERROR, RECOGNIZED].indexOf(data.err) !== -1) {
                        if (this._onsuccess) {
                            this._onsuccess(new RecognitionResult(data.data));
                        }
                    } else if ([NOT_RECOGNIZED, REC_LEVEL_TOO_LOW].indexOf(data.err) !== -1) {
                        if (this._onfailure) {
                            this._onfailure(data.err);
                        }
                    } else if (this._onerror) {
                        this._onerror(data.err);
                    }
                }
                if (this._onlevelchange) {
                    this._onlevelchange(0);
                }
                this._prepared = false;
                this._active = false;
            }, () => {
                this._active = true;
                if (this._onready) {
                    this._onready();
                }
            });
        });
        return this;
    }

    shutdown() {
        this.checkinit();
        this._active = false;
        this._prepared = false;
        this._instance.stop();
        return this;
    }

    destroy() {
        this.unbindAll();
        this._active = false;
        this._prepared = false;
        this._initialized = false;
        this._instance.destroy();
        return this;
    }

    getRecordingUrl(recordingId, callback) {
        this.checkinit();
        this._instance.getRecordingUrl(recordingId, callback);
    }

    initialized(callback) {
        this._oninitialized = callback;
        if (this._initialized && callback) {
            callback();
        }
        return this;
    }

    ready(callback) {
        this._onready = callback;
        if (this._active && callback) {
            callback();
        }
        return this;
    }

    error(callback) {
        this._onerror = callback;
        return this;
    }

    disconnected(callback) {
        this._ondisconnect = callback;
        return this;
    }

    success(callback) {
        this._onsuccess = callback;
        return this;
    }

    failure(callback) {
        this._onfailure = callback;
        return this;
    }

    micLevelChanged(callback) {
        this._onlevelchange = callback;
        return this;
    }

    unbindAll() {
        this._oninitialized = null;
        this._onready = null;
        this._onerror = null;
        this._ondisconnect = null;
        this._onsuccess = null;
        this._onfailure = null;
        this._onlevelchange = null;
    }

    emptyResult() {
        return new RecognitionResult(false);
    }

    get micLevel() {
        return this._micLevel;
    }

    get active() {
        return this._active;
    }
}

export default new ASR();
