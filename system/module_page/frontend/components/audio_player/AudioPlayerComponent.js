
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import BaseComponent from '../BaseComponent.js';

export default class AudioPlayerComponent extends BaseComponent {

    constructor(controller) {
        super();
        this.is_load_metadata = false;
        this.controller = controller;
        controller.addEventListener('loadedmetadata', () => {this.template_update();});
        controller.addEventListener('timeupdate'    , () => {this.template_update();});
        controller.addEventListener('play'          , () => {this.template_update();});
        controller.addEventListener('pause'         , () => {this.template_update();});
        controller.addEventListener('ended'         , () => {this.template_update();});
        controller.addEventListener('progress'      , () => {this.template_update_on_progress();});
        controller.controls = false;
        this.template = this.template_get();
    }

    mount() {
        return this.controller.parentNode.insertBefore(this.template, this.controller);
    }

    //////////////////////////////////////////////////////////////////

    template_get() {
        return this.Markup('x-audio-player', {}, [
            this.Markup('button', {'type' : 'button', 'value': 'play', 'onclick': 'on_click_play'}),
            this.Markup('x-timeline', {'onclick': 'on_click_timeline'},
                this.Markup('x-track-position')),
            this.Markup('x-time', {}, [
                this.Markup('x-time-elapsed', {}, '-:--:--'),
                this.Markup('x-time-total'  , {}, '-:--:--')
            ])
        ]);
    }

    template_update() {
        if (!isNaN(this.controller.duration)) {
            let time_cur =        Math.floor(this.controller.currentTime);
            let time_tot =        Math.floor(this.controller.duration);
            let h_cur    =        Math.floor(time_cur / 3600);
            let h_tot    =        Math.floor(time_tot / 3600);
            let m_cur    = ('0' + Math.floor(time_cur / 60 % 60)).slice(-2);
            let m_tot    = ('0' + Math.floor(time_tot / 60 % 60)).slice(-2);
            let s_cur    = ('0' + Math.floor(time_cur      % 60)).slice(-2);
            let s_tot    = ('0' + Math.floor(time_tot      % 60)).slice(-2);
            this.pool[ 'x-time-elapsed' ][0].innerText = `${h_cur}:${m_cur}:${s_cur}`;
            this.pool[  'x-time-total'  ][0].innerText = `${h_tot}:${m_tot}:${s_tot}`;
            this.pool['x-track-position'][0].style.width = Math.floor(this.controller.currentTime / this.controller.duration * 100) + '%';
            if (this.is_load_metadata === false) {
                this.template.setAttribute('data-is-loadedmetadata', '');
                this.is_load_metadata = true;
            }
        }
        if (this.controller.paused) this.template.removeAttribute('data-is-playing');
        else this.template.setAttribute('data-is-playing', '');
    }

    template_update_on_progress() {
        this.template.setAttribute('data-is-progressing', '');
        setTimeout(() => {this.template.removeAttribute('data-is-progressing');}, 1000);
    }

    //////////////////////////////////////////////////////////////////

    on_click_play() {
        if (this.controller.paused) this.controller.play ();
        else                        this.controller.pause();
    }

    on_click_timeline() {
        this.controller.currentTime = this.controller.duration * (event.offsetX / this.pool['x-timeline'][0].clientWidth);
    }

}
