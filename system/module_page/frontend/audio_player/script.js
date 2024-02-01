
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    Element.prototype.process__defaultAudioPlayer = function () {
        let c_audio        = this;
        let c_player       = document.createElement('x-audio-player');
        let c_button_play  = document.createElement__withAttributes('button', {'type' : 'button'});
        let c_timeline     = document.createElement('x-timeline');
        let c_trackpos     = document.createElement('x-track-position');
        let c_time         = document.createElement('x-time');
        let c_time_elpsd   = document.createElement('x-time-elapsed');
        let c_time_total   = document.createElement('x-time-total');
        let c_timerId      = null;
        let c_is_init      = null;
        let on_updateTimeInfo = function () {
            if (!isNaN(c_audio.duration)) {
                let time_cur =     Math.floor(c_audio.currentTime);
                let time_ttl =     Math.floor(c_audio.duration);
                let h_cur =        Math.floor(time_cur / 3600);
                let h_ttl =        Math.floor(time_ttl / 3600);
                let m_cur = ('0' + Math.floor(time_cur / 60 % 60)).slice(-2);
                let m_ttl = ('0' + Math.floor(time_ttl / 60 % 60)).slice(-2);
                let s_cur = ('0' + Math.floor(time_cur      % 60)).slice(-2);
                let s_ttl = ('0' + Math.floor(time_ttl      % 60)).slice(-2);
                c_trackpos.style.width = Math.floor(c_audio.currentTime / c_audio.duration * 100) + '%';
                c_time_elpsd.innerText = h_cur + ':' + m_cur + ':' + s_cur;
                c_time_total.innerText = h_ttl + ':' + m_ttl + ':' + s_ttl;
                if (!c_is_init) {
                    c_is_init = true;
                    c_player.setAttribute('data-is-loadedmetadata', '');
                    c_timeline.addEventListener('click', function (event) {
                        c_audio.currentTime = c_audio.duration * (event.offsetX / c_timeline.clientWidth);
                    });
                }
            }
        }
        c_player.append(c_button_play, c_timeline, c_time);
        c_timeline.append(c_trackpos);
        c_time.append(c_time_elpsd, c_time_total);
        c_audio.parentNode.insertBefore(c_player, c_audio.nextSibling);
        c_audio.setAttribute('data-player-audio-default-is-processed', '');
        c_audio.controls = false;
        c_time_elpsd.innerText = '‐ : ‐ ‐';
        c_time_total.innerText = '‐ : ‐ ‐';
        c_button_play.value = 'play';

        // bind events
        c_audio.addEventListener('loadedmetadata', on_updateTimeInfo);
        c_audio.addEventListener('timeupdate' ,    on_updateTimeInfo);
        c_audio.addEventListener('play'       , function () {c_player.   setAttribute('data-is-playing', '');});
        c_audio.addEventListener('pause'      , function () {c_player.removeAttribute('data-is-playing');});
        c_audio.addEventListener('ended'      , function () {c_player.removeAttribute('data-is-playing');});
        c_button_play.addEventListener('click', function () {
            if (c_audio.paused) c_audio.play ();
            else                c_audio.pause();
        });
        c_audio.addEventListener('progress', function () {
            clearTimeout(c_timerId);
            c_player.setAttribute('data-is-progressing', '');
            c_timerId = setTimeout(function () {
                c_player.removeAttribute('data-is-progressing');
            }, 1000);
        });
    };

    document.querySelectorAll('audio[data-player-name="default"]:not([data-player-audio-default-is-processed])').forEach(function (c_audio) {
        c_audio.process__defaultAudioPlayer();
    });

});
