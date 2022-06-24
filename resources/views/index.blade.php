<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <link type="text/css" rel="stylesheet" href="/css/app.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>
    <body>
        <div id="app">
            <div v-bind:class="$route.name + '-component view-wrapper'">
                <router-view :key="$route.fullPath"></router-view>
            </div>
            <side-menu />
        </div>
    </body>

    <script src="/js/firebase/firebase-app.js"></script>
    <script src="/js/firebase/firebase-analytics.js"></script>

    <script>
        firebase.initializeApp({
            apiKey: "AIzaSyD7lxoNOcBwcgmBYu-Ygj2KblF_AXBmcME",
            authDomain: "lese-552a0.firebaseapp.com",
            databaseURL: "https://lese-552a0.firebaseio.com",
            projectId: "lese-552a0",
            storageBucket: "lese-552a0.appspot.com",
            messagingSenderId: "528341281661",
            appId: "1:528341281661:web:953ea6f05f54e5337a7a14",
            measurementId: "G-TQM4SJRNG9"
        });

        window.appConfig = {!! json_encode([
            'cdnUrls' => [
                'unit' => Cdn::getFolderUrl(App\Unit::FOLDER_UNITS),
                'translations' => Cdn::getFolderUrl(App\Unit::FOLDER_TRANSLATIONS),
            ],
            'scores' => config('app.scores'),
        ]) !!};
    </script>
    <script src="/js/spapi-all.js"></script>
    <script src="/js/app.js"></script>
</html>
