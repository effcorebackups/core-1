
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import AudioPlayerComponent from './AudioPlayerComponent.js';

document.addEventListener('DOMContentLoaded', () => {

    Element.prototype.process__defaultAudioPlayer = function () {
        (new AudioPlayerComponent(this)).mount();
        this.setAttribute('data-player-audio-default-is-processed', '');
    }

    document.querySelectorAll('audio[data-player-name="default"]:not([data-player-audio-default-is-processed])').forEach((c_audio) => {
        if (c_audio.process__defaultAudioPlayer) {
            c_audio.process__defaultAudioPlayer();
        }
    });

});
