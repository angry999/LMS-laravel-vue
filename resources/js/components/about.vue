<template>
    <div class="about-component standard-layout container full-height">
        <titlebar :back="{name: 'home'}">{{ __('About') }}</titlebar>
        <div class="about-text container scrollable">
            <vue-scrollbar>
                <div class="about-text-inner">
                    <p class="logo">
                        {{ __('SpeakingPal') }}
                    </p>
                    <p>
                        {{ __('SpeakingPal is an application that helps you learn how to speak English clearly.') }}
                    </p>
                    <p>
                        {{ __('Prepared by pedagogical and linguistic experts, it is a fun and easy way to practice and improve your English speaking skills.') }}
                        {{ __('Founded by an experienced multidisciplinary team, SpeakingPal brings together experts in the fields of software, management, linguistics, automatic speech recognition and English as a Foreign Language (EFL).') }}
                    </p>
                    <p>
                        {{ __('See SpeakingPal\'s other English learning products at:') }}
                        <a 
                            href="https://www.speakingpal.com/products" 
                            target="_blank">www.speakingpal.com/products</a>
                    </p>
                    <p class="footer">
                        <copyright from="2009">{{ __('SpeakingPal. All rights reserved.') }}</copyright>
                    </p>
                </div>
            </vue-scrollbar>
        </div>
    </div>
</template>