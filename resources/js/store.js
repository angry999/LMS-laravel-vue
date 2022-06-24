import Vue from 'vue';
import Vuex from 'vuex';
import user from './models/user';

Vue.use(Vuex);

const state = {
    user,
};

const mutations = {
    login(state, data) {
        state.user.login(data);
    },
    logout(state) {
        state.user.logout();
    },
    updateName(state, name) {
        state.user.updateName(name);
    },
    updateOptions(state, opts) {
        state.user.updateOptions(opts);
    },
    changeTranslation(state, lang) {
        state.user.desiredLanguage = lang;
    },
    setIsPremium(state, data) {
        state.user.premium = data;
    },
};

const getters = {
    user: (state) => {
        return state.user;
    },
}

export default new Vuex.Store({
    state,
    mutations,
    getters
});
