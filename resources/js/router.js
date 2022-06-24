import Vue from 'vue';
import VueRouter from 'vue-router';
import './components';
import store from './store';

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [{
            path: '/',
            component: Vue.component('home'),
            name: 'home',
            meta: { guarded: true },
        },
        {
            path: '/units/:categoryName',
            component: Vue.component('catalogue-units'),
            name: 'units',
            props: true,
            meta: { guarded: true },
        },
        {
            path: '/units/:categoryName/unit/:unitId',
            component: Vue.component('unit'),
            name: 'unit',
            props: true,
            meta: { guarded: true },
            children: [{
                    path: 'menu',
                    component: Vue.component('unit-menu'),
                    name: 'unit-menu',
                },
                {
                    path: 'listening',
                    component: Vue.component('unit-listening'),
                    name: 'unit-listening',
                },
                {
                    path: 'dialog-review',
                    component: Vue.component('listening-review'),
                    name: 'dialog-review',
                },
                {
                    path: 'reading/:activityNumber',
                    component: Vue.component('unit-reading'),
                    name: 'unit-reading',
                    props: true,
                },
                {
                    path: 'speeching/:activityNumber',
                    component: Vue.component('unit-speeching'),
                    name: 'unit-speeching',
                    props: true,
                },
                {
                    path: 'quiz',
                    component: Vue.component('unit-quiz'),
                    name: 'unit-quiz',
                },
                {
                    path: 'pronounciation',
                    component: Vue.component('unit-pronounciation'),
                    name: 'unit-pronounciation',
                },
                {
                    path: 'guidedspeech',
                    component: Vue.component('unit-guidedspeech'),
                    name: 'unit-guidedspeech',
                },
                {
                    path: 'result',
                    component: Vue.component('unit-result'),
                    name: 'unit-result',
                },
            ],
        },
        {
            path: '/login',
            component: Vue.component('login'),
            name: 'login',
        },
        {
            path: '/forgot-password',
            component: Vue.component('forgot-password'),
            name: 'forgot-password',
        },
        {
            path: '/about',
            component: Vue.component('about'),
            name: 'about',
        },
        {
            path: '/profile',
            component: Vue.component('profile'),
            name: 'profile',
        },
    ],
});

router.beforeEach((to, from, next) => {
    if (to.meta && to.meta.guarded && store.state.user.auth === false) {
        next({ name: 'login' });
    } else {
        next();
    }
});

export default router;