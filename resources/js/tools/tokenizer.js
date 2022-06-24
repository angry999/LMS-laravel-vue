import {ArrayIterator} from './iterator';

const SEPARATOR = new RegExp('([ ,.?!:;]+)');

class Token
{
    constructor(text, isWord) {
        this._text = text;
        this._isWord = isWord;
        this._props = {};
    }
    
    get text() {
        return this._text;
    }
    
    get isWord() {
        return this._isWord;
    }
    
    getProp(prop) {
        return this._props[prop];
    }
    
    setProp(prop, value)
    {
        this._props[prop] = value;
    }
}

class Tokenizer
{
    constructor(string) {
        this._tokens = this.split(string);
        this._words = this._tokens.filter((token) => token.isWord);
    }
    
    split(string) {
        return string.split(SEPARATOR).filter((w) => w.length !== 0).map((token) => {
            return new Token(token, !SEPARATOR.test(token));
        });
    }
    
    item(i) {
        return this._tokens[i];
    }
    
    word(i) {
        return this._words[i];
    }
    
    find(word, fromIndex = 0) {
        return lodash.find(this._words, (w) => w.text.toLowerCase() === word.toLowerCase(), fromIndex);
    }
    
    [Symbol.iterator]() {
        return ArrayIterator.make(this._tokens);
    }
}

export default Tokenizer;