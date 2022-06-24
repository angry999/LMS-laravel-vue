const TYPE_SPEAKING = 'SpeakV3';
const TYPE_PRONOUNCIATION = 'Pronounciation';
const TYPE_GUIDEDSPEECH = 'GuidedSpeech';

class CatalogueUnit {
    constructor(catalogue, data) {
        this._catalogue = catalogue;
        this._data = data;
    }

    get id() {
        return this._data.id;
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

    get score() {
        return this._catalogue.getProgress().getUnitProgress(this, (p) => p.score, 0);
    }

    get progress() {
        return this._catalogue.getProgress().getUnitProgress(this, (p) => p.progress, 0);
    }

    get meta() {
        return this._data;
    }

    get isSpeaking() {
        return this._data.unit_type === TYPE_SPEAKING;
    }

    get isGuidedSpeech() {
        return this._data.unit_type === TYPE_GUIDEDSPEECH;
    }
}

export default CatalogueUnit;