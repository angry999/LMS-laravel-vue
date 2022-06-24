import store from '../store';

const LANGUAGE_NAMES = {
    'es': 'español',
    'ja': '日本語',
    'el': 'Ελληνικά',
    'pt': 'português',
    'ru': 'русский',
    'zh-hans': '中文',
    'vi': 'tiếng Việt',
    'ar': 'العَرَبِيَّة‎',
    'id': 'bahasa Indonesia',
    'ro': 'română',
    'ko': '한국어',
    'th': 'ไทย',
    'hi': 'हिन्दी',
    'tr': 'Türkçe',
    'he': 'עברית',
    'it': 'Italiano',
    'fr': 'Française',
};

const FLAG_EARTH = 'earth';

class Translation
{
    change(lang, unitId) {
        return axios.post('/api/select-translation', {
            lang,
            unitId,
        })
            .then((response) => {
                store.commit('changeTranslation', lang);
                return Promise.resolve(response.data);
            });
    }

    get current() {
        return store.state.user.desiredLanguage;
    }

    get languageNames () {
        return LANGUAGE_NAMES;
    }

    get availableLanguages() {
        return store.state.user.availableLanguages;
    }

    get earthFlag() {
        return FLAG_EARTH;
    }
}

export default new Translation();
