import Vue from 'vue';
import router from './router';
import store from './store';
import './utils';

import * as firebase from "firebase/app";
import "firebase/auth";
import "firebase/firestore";

const app = new Vue({
    el: '#app',
    router,
    store
});
