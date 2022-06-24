class UserProgress
{
    constructor(data) {
        this._data = data;
    }
    
    get speakingScore() {
        return Math.round(this._data.speaking.score);
    }
    
    get sentences() {
        return this._data.speaking.sentences;
    }
    
    get quizScore() {
        return this._data.quiz.avg_score;
    }
    
    get quizzes() {
        return this._data.quiz.completed;
    }
    
    get units() {
        return this._data.units;
    }
}

class User
{
    constructor() {
        this._name = localStorage.getItem('name') || 'Guest';
        this._id = localStorage.getItem('userid');
        this._api_token = localStorage.getItem('api_token');
        try {
            this._options = JSON.parse(localStorage.getItem('options') || '{}');
        } catch (e) {
            this._options = {};
        }
    }
    
    get auth() {
        return this._id != null;
    }
    
    get name() {
        return this._name;
    }
    
    get id() {
        return this._id;
    }
    
    get apiToken() {
        return this._api_token;
    }
    
    get desiredLanguage() {
        return this.getOption('desired_language', '');
    }
    
    set desiredLanguage(value) {
        this.updateOption('desired_language', value);
        return value;
    }
    
    get availableLanguages() {
        return this.getOption('supported_languages', []);
    }
    
    getOption(key, def = undefined) {
        if (key === undefined) {
            return this._options;
        }
        return (key in this._options) ? this._options[key] : def;
    }
    
    login(data) {
        this._name = data.name;
        this._id = data.id;
        this._api_token = data.api_token;
        this._options = data.options || {};
        
        localStorage.setItem('name', this._name);
        localStorage.setItem('userid', this._id);
        localStorage.setItem('api_token', this._api_token);
        localStorage.setItem('options', JSON.stringify(this._options));
    }
    
    logout() {
        this._name = 'Guest';
        this._id = null;
        this._api_token = null;
        this._options = {};
        localStorage.removeItem('name');
        localStorage.removeItem('userid');
        localStorage.removeItem('api_token');
        localStorage.removeItem('options');
    }
    
    updateName(name) {
        this._name = name;
        localStorage.setItem('name', this._name);
    }
    
    updateOptions(options) {
        this._options = options;
        localStorage.setItem('options', JSON.stringify(this._options));
    }
    
    updateOption(name, value) {
        this._options[name] = value;
        localStorage.setItem('options', JSON.stringify(this._options));
    }
    
    getOverallProgress() {
        return axios.get('/api/overall-progress')
            .then((response) => {
                return Promise.resolve(new UserProgress(response.data.progress));
            });
    }
}

export default new User();