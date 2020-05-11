document.addEventListener('DOMContentLoaded', function(){

/* this code activate hover state on ios devices */

  document.addEventListener('touchstart', function(){}, false);

/* audio */

  document.effQuerySelectorAll('audio[data-player-name="default"]').forEach(function(c_audio){
    var c_player      = document.createElement('x-audio-player');
    var c_button_play = document.createElement('button');
    var c_timeline    = document.createElement('x-timeline');
    var c_trackpos    = document.createElement('x-track-position');
    var c_time        = document.createElement('x-time');
    var c_time_elpsd  = document.createElement('x-time-elapsed');
    var c_time_total  = document.createElement('x-time-total');
    c_player.append(c_button_play, c_timeline, c_time);
    c_timeline.append(c_trackpos);
    c_time.append(c_time_elpsd, c_time_total);
    c_audio.parentNode.insertBefore(c_player, c_audio);
    c_audio.controls = false;
    c_time_elpsd.innerText = '‐ : ‐ ‐';
    c_time_total.innerText = '‐ : ‐ ‐';
    c_button_play.value = 'play';
    c_button_play.addEventListener('click', function(){
      if (c_audio.paused) c_audio.play ();
      else                c_audio.pause();
    }, false);
    c_timeline.addEventListener('click', function(event){
      var timelineX = event.clientX + document.documentElement.scrollLeft - c_timeline.offsetLeft;
      c_audio.currentTime = c_audio.duration * (timelineX / c_timeline.clientWidth);
    }, false);
    c_audio.addEventListener('play',           function(){c_player.   setAttribute('data-is-playing', true);}, false);
    c_audio.addEventListener('pause',          function(){c_player.removeAttribute('data-is-playing');      }, false);
    c_audio.addEventListener('timeupdate',     function(){updateTimeInfo();}, false);
    c_audio.addEventListener('loadedmetadata', function(){updateTimeInfo();}, false);
    var updateTimeInfo = function(){
      if (c_audio.duration) {
        var time_cur =     Math.floor(c_audio.currentTime);
        var time_ttl =     Math.floor(c_audio.duration);
        var h_cur =        Math.floor(time_cur /    3600);
        var h_ttl =        Math.floor(time_ttl /    3600);
        var m_cur = ('0' + Math.floor(time_cur / 60 % 60)).slice(-2);
        var m_ttl = ('0' + Math.floor(time_ttl / 60 % 60)).slice(-2);
        var s_cur = ('0' + Math.floor(time_cur      % 60)).slice(-2);
        var s_ttl = ('0' + Math.floor(time_ttl      % 60)).slice(-2);
        c_trackpos.style.width = Math.floor(c_audio.currentTime / c_audio.duration * 100) + '%';
        c_time_elpsd.innerText = h_cur + ':' + m_cur + ':' + s_cur;
        c_time_total.innerText = h_ttl + ':' + m_ttl + ':' + s_ttl;
      }
    }
  });

});