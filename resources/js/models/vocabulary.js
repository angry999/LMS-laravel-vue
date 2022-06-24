import {ArrayIterator} from '../tools/iterator';

class WordReference
{
    constructor(word, data) {
        this._word = word;
        this._data = data;
    }
    
    get word() {
        return this._word;
    }
    
    get sentenceId() {
        return this._data.sentence_id;
    }
    
    get text() {
        return this._data.text;
    }
    
    get indices() {
        return this._data.indices;
    }
    
    get translation() {
        return this._word.vocabulary.unit.getTranslation(this.sentenceId);
    }
    
    get sentence() {
        return this._word.vocabulary.unit.getDialogSencence(this.sentenceId);
    }
}

class Word
{
    constructor(vocabulary, data) {
        this._vocabulary = vocabulary;
        this._data = data;
        this._references = data.references.map((ref) => new WordReference(this, ref));
    }
    
    getReference(sentenceId) {
        return lodash.find(this._references, (ref) => ref.sentenceId === sentenceId);
    }
    
    get id() {
        return this._data.id;
    }
    
    get description() {
        return this._data.description;
    }
    
    get references() {
        return this._references;
    }
    
    get text() {
        return this._data.text;
    }
    
    get image() {
        return this._data.image ? this._vocabulary.unit.getContentUrl(this._data.image) : false;
    }
    
    get vocabulary() {
        return this._vocabulary;
    }
}

class Vocabulary
{
    constructor(unit, words) {
        this._unit = unit;
        this._words = words.map((word) => new Word(this, word));
    }
    
    get unit() {
        return this._unit;
    }
    
    getReferences(sentenceId) {
        let references = [];
        this._words.forEach((word) => {
            let reference = word.getReference(sentenceId);
            if (reference) {
                references.push(reference);
            }
        });
        return references;
    }
    
    [Symbol.iterator]() {
        return ArrayIterator.make(this._words);
    }
}

export default Vocabulary;