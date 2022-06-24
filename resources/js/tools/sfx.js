const DIR = '/audio/';
const AUDIO = [
    'dialog-completed',
    'level-completed',
    'quiz-completed',
    'quiz-correct-answer',
    'quiz-failed',
    'quiz-intro',
    'quiz-timer',
    'quiz-wrong-answer',
];

class Sfx
{
    constructor(name) {
        this.playing = false;
        this.loop = false;
        this.player = document.createElement('audio');
        this.player.src = DIR + name + '.mp3';
        this.player.load();
        this.player.onended = () => {
            this.playing = false;
            if (this.loop) {
                this.play(true);
            }
        }
    }
    
    play(loop = false) {
        this.loop = loop;
        this.player.currentTime = 0;
        if (!this.playing) {
            this.playing = true;
            this.player.play();
        }
    }
    
    mute() {
        if (this.playing) {
            this.playing = false;
            this.player.pause();
        }
    }
}

class Mixer
{
    constructor() {
        this.sfxmap = {};
        this.createSfx();
    }
    
    createSfx() {
        AUDIO.forEach((name) => {
            this.sfxmap[name] = new Sfx(name);
        });
    }
    
    play(name, loop = false, muteOthers = true) {
        if (muteOthers) {
            this.muteAll(name);
        }
        this.sfxmap[name].play(loop);
    }
    
    mute(name) {
        this.sfxmap[name].mute();
    }
    
    muteAll(except = null) {
        AUDIO.forEach((name) => {
            if (name !== except) {
                this.mute(name);
            }
        });
    }
}

export default new Mixer();