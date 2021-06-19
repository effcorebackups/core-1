document.addEventListener('DOMContentLoaded', function(){

  /* ───────────────────────────────────────────────────────────────────── */
  /* this code activate hover state on iOS devices                         */
  /* ───────────────────────────────────────────────────────────────────── */

  document.addEventListener('touchstart', function(){});

  /* ───────────────────────────────────────────────────────────────────── */
  /* audio player                                                          */
  /* ───────────────────────────────────────────────────────────────────── */

  Element.prototype.process__defaultAudioPlayer = function(){
    var c_audio        = this;
    var c_player       = document.createElement('x-audio-player');
    var c_button_play  = document.createElement__withAttributes('button', {'type' : 'button'});
    var c_timeline     = document.createElement('x-timeline');
    var c_trackpos     = document.createElement('x-track-position');
    var c_time         = document.createElement('x-time');
    var c_time_elpsd   = document.createElement('x-time-elapsed');
    var c_time_total   = document.createElement('x-time-total');
    var c_timerId      = null;
    var c_is_init      = null;
    var on_updateTimeInfo = function(){
      if (!isNaN(c_audio.duration)) {
        var time_cur =     Math.floor(c_audio.currentTime);
        var time_ttl =     Math.floor(c_audio.duration);
        var h_cur =        Math.floor(time_cur / 3600);
        var h_ttl =        Math.floor(time_ttl / 3600);
        var m_cur = ('0' + Math.floor(time_cur / 60 % 60)).slice(-2);
        var m_ttl = ('0' + Math.floor(time_ttl / 60 % 60)).slice(-2);
        var s_cur = ('0' + Math.floor(time_cur      % 60)).slice(-2);
        var s_ttl = ('0' + Math.floor(time_ttl      % 60)).slice(-2);
        c_trackpos.style.width = Math.floor(c_audio.currentTime / c_audio.duration * 100) + '%';
        c_time_elpsd.innerText = h_cur + ':' + m_cur + ':' + s_cur;
        c_time_total.innerText = h_ttl + ':' + m_ttl + ':' + s_ttl;
        if (!c_is_init) {
          c_is_init = true;
          c_player.setAttribute('data-is-loadedmetadata', '');
          c_timeline.addEventListener('click', function(event){
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
 /* bind events */
    c_audio.addEventListener('loadedmetadata', on_updateTimeInfo);
    c_audio.addEventListener('timeupdate',     on_updateTimeInfo);
    c_audio.addEventListener('play',        function(){c_player.   setAttribute('data-is-playing', '');});
    c_audio.addEventListener('pause',       function(){c_player.removeAttribute('data-is-playing');});
    c_audio.addEventListener('ended',       function(){c_player.removeAttribute('data-is-playing'); /* IE fix → */ c_audio.pause();});
    c_button_play.addEventListener('click', function(){
      if (c_audio.paused) c_audio.play ();
      else                c_audio.pause();
    });
    c_audio.addEventListener('progress', function(){
      clearTimeout(c_timerId);
      c_player.setAttribute('data-is-progressing', '');
      c_timerId = setTimeout(function(){
        c_player.removeAttribute('data-is-progressing');
      }, 1000);
    });
  };

  document.querySelectorAll__notNull('audio[data-player-name="default"]:not([data-player-audio-default-is-processed])').forEach(function(c_audio){
    c_audio.process__defaultAudioPlayer();
  });

  /* ───────────────────────────────────────────────────────────────────── */
  /* gallery player                                                        */
  /* ───────────────────────────────────────────────────────────────────── */

  document.querySelectorAll__notNull('x-gallery[data-player-name="default"]').forEach(function(c_gallery){
    var c_player              = document.createElement__withAttributes('x-gallery-player', {'aria-hidden' : 'true'});
    var c_player_thumbnails   = document.createElement('x-thumbnails');
    var c_player_button_l     = document.createElement('x-button-l');
    var c_player_button_r     = document.createElement('x-button-r');
    var c_player_button_c     = document.createElement('x-button-c');
    var c_player_viewing_area = document.createElement('x-viewing-area');
    var clear_viewing_area    =                 function(){c_player_viewing_area.innerHTML = '';}
    var on_setButtonLState    =                 function(){c_player_thumbnails.querySelector__notNull('x-thumbnail[aria-selected="true"]').forFirst__(function(c_selected){ if (c_selected.previousSibling) c_player_button_l.removeAttribute('data-is-blocked'); else c_player_button_l.setAttribute('data-is-blocked', 'true'); })}
    var on_setButtonRState    =                 function(){c_player_thumbnails.querySelector__notNull('x-thumbnail[aria-selected="true"]').forFirst__(function(c_selected){ if (c_selected.nextSibling    ) c_player_button_r.removeAttribute('data-is-blocked'); else c_player_button_r.setAttribute('data-is-blocked', 'true'); })}
    var on_click_L            =                 function(){c_player_thumbnails.querySelector__notNull('x-thumbnail[aria-selected="true"]').forFirst__(function(c_selected){ if (c_selected.previousSibling) {c_selected.previousSibling.click(); c_player_thumbnails.scrollLeft = c_selected.previousSibling.offsetLeft - (c_player_thumbnails.clientWidth / 2) + (c_selected.previousSibling.clientWidth / 2) + 3; on_setButtonLState(); on_setButtonRState();} })}
    var on_click_R            =                 function(){c_player_thumbnails.querySelector__notNull('x-thumbnail[aria-selected="true"]').forFirst__(function(c_selected){ if (c_selected.nextSibling    ) {c_selected.nextSibling    .click(); c_player_thumbnails.scrollLeft = c_selected.nextSibling    .offsetLeft - (c_player_thumbnails.clientWidth / 2) + (c_selected.nextSibling    .clientWidth / 2) + 3; on_setButtonLState(); on_setButtonRState();} })}
    var on_click_C            =                 function(){clear_viewing_area(); c_player.setAttribute('aria-hidden', 'true'); document.body.removeAttribute('data-is-active-gallery-player');}
    c_gallery.prepend(c_player);
    c_gallery.setAttribute('data-player-is-processed', true);
    c_player.append(
      c_player_thumbnails,
      c_player_button_l,
      c_player_button_r,
      c_player_button_c,
      c_player_viewing_area);
 /* bind events */
    c_player_button_l.addEventListener('click', function(){on_click_L();});
    c_player_button_r.addEventListener('click', function(){on_click_R();});
    c_player_button_c.addEventListener('click', function(){on_click_C();});
    document.addEventListener('keydown', function(event){if (event.keyCode === 27) on_click_C();});
 /* process each gallery item */
    c_gallery.querySelectorAll__notNull('x-item').forEach(function(c_item){
      var c_thumbnail = document.createElement__withAttributes('x-thumbnail', {'data-type' : c_item.getAttribute('data-type'), 'data-num' : c_item.getAttribute('data-num')});
      switch (c_item.getAttribute('data-type')) {
        case 'picture':
          var c_image = c_item.getElementsByTagName('img')[0];
          var c_thumbnail_img_src = (new EffURL(c_image.getAttribute('src')).queryArgDelete('thumb').queryArgInsert('thumb', 'small')).tinyGet();
          var c_preview_a_img_src = (new EffURL(c_image.getAttribute('src')).queryArgDelete('thumb').queryArgInsert('thumb', 'big'  )).tinyGet();
          var c_thumbnail_img = document.createElement__withAttributes('img', {'src' : c_thumbnail_img_src});
          var c_preview_a_img = document.createElement__withAttributes('img', {'src' : c_preview_a_img_src});
          c_thumbnail.setAttribute('data-preview-area-content', JSON.stringify(c_preview_a_img.outerHTML).replace(/^"/, '').replace(/"$/, ''));
          c_thumbnail.append(c_thumbnail_img);
          c_player_thumbnails.append(c_thumbnail);
          break;
        case 'video':
          var c_video = c_item.getElementsByTagName('video')[0];
          var c_thumbnail_img_src = c_video.getAttribute('poster');
          var c_thumbnail_img = document.createElement__withAttributes('img', {'src' : c_thumbnail_img_src});
          c_thumbnail.setAttribute('data-preview-area-content', JSON.stringify(c_video.outerHTML).replace(/^"/, '').replace(/"$/, ''));
          c_thumbnail.append(c_thumbnail_img);
          c_player_thumbnails.append(c_thumbnail);
          break;
        case 'audio':
          var c_audio = c_item.getElementsByTagName('audio')[0];
          var c_thumbnail_img_src = c_audio.getAttribute('data-cover-thumbnail') ? c_audio.getAttribute('data-cover-thumbnail') : '/' + effcore.tokens['thumbnail_path_cover_default'];
          var c_thumbnail_img = document.createElement__withAttributes('img', {'src' : c_thumbnail_img_src});
          c_thumbnail.setAttribute('data-preview-area-content', JSON.stringify(c_audio.outerHTML).replace(/^"/, '').replace(/"$/, ''));
          c_thumbnail.append(c_thumbnail_img);
          c_player_thumbnails.append(c_thumbnail);
          break;
      }
   /* when click on item in gallery */
      c_item.addEventListener('click', function(event){
        event.stopPropagation();
        event.preventDefault();
        c_player.removeAttribute('aria-hidden');
        document.body.setAttribute('data-is-active-gallery-player', 'true');
        c_player_thumbnails.querySelector__notNull('x-thumbnail[data-num="' + this.getAttribute('data-num') + '"]').forFirst__(function(c_selected){
          c_selected.click(); c_player_thumbnails.scrollLeft = c_selected.offsetLeft - (c_player_thumbnails.clientWidth / 2) + (c_selected.clientWidth / 2) + 3;
        });
      }, true);
    /* when click on thumbnail in player */
      c_thumbnail.addEventListener('click', function(){
        c_player_thumbnails.querySelectorAll__notNull('[aria-selected="true"]').forEach(function(c_selected){c_selected.removeAttribute('aria-selected');});
        c_thumbnail.setAttribute('aria-selected', 'true');
        clear_viewing_area();
        if (c_thumbnail.getAttribute('data-preview-area-content')) {
          c_player_viewing_area.innerHTML = '<x-centrator-wrapper><x-centrator>' + JSON.parse('"' + c_thumbnail.getAttribute('data-preview-area-content') + '"') + '</x-centrator></x-centrator-wrapper>';
          if (c_thumbnail.getAttribute('data-type') === 'audio') {
            c_player_viewing_area.querySelectorAll__notNull('audio[data-player-name="default"]').forEach(function(c_player_viewing_area_audio){
              c_player_viewing_area_audio.process__defaultAudioPlayer();
            });
          }
        }
        on_setButtonLState();
        on_setButtonRState();
      });
    });
  });

});