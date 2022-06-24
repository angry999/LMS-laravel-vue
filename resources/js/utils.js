import Vue from 'vue';
import axios from 'axios';
import lodash from 'lodash';
import store from './store';
import router from './router';
import {Notification} from 'element-ui';

const URL_PROTO_REGEXP = new RegExp('^https?:');
let currentLang = {};

const translate = (...args) => {
    let key = args[0], category = 'default', subs = null;
    
    if (args.length === 2) {
        if (_.isObject(args[1])) {
            subs = args[1];
        } else {
            category = args[1];
        }
    } else if (args.length === 3) {
        category = args[1];
        subs = args[2];
    }
    
    let string = _.get(currentLang, `${category}.${key}`, key);
    
    if (subs !== null) {
        for (let key in subs) {
            string = string.replace(`:${key}`, subs[key]);
        }
    }
    
    return string;
};

window.eventHub = new Vue();

Vue.prototype.__ = translate;

const info = Vue.prototype.$info = (message, options = {}) => {
    Notification({
        message,
        title: translate('Info'),
        type: 'info',
        customClass: 'alert-info',
        ...options,
    });
}

const error = Vue.prototype.$error = (message, options = {}) => {
    Notification({
        message,
        title: translate('Error'),
        type: 'error',
        customClass: 'alert-error',
        duration: 10000,
        ...options,
    });
}

const invalid = (errors) => {
    error([
        '<p>',
        translate('Your input has errors:'),
        '</p><ul>',
        Object.keys(errors).map((key) => {
            return errors[key].map((error) => {
                return [
                    '<li>',
                    translate(error),
                    '</li>'
                ].join('');
            });
        }).join(''),
        '</ul>'
    ].join(''), {
        dangerouslyUseHTMLString: true,
    });
}

window.fix_protocol = (url) => {
    return url.replace(URL_PROTO_REGEXP, location.protocol);
}

window.lodash = lodash;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
window.axios.interceptors.response.use(undefined, (err) => {
    if(err.response && err.response.status === 401) {
        store.commit('logout');
        router.push({name: 'login'});
    } else if(err.response && err.response.status === 422) {
        invalid(err.response.data.errors);
    } else {
        error(translate('Something went wrong. Please try again later.'));
    }
    return Promise.reject(err);
});