
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    Element.prototype.process__defaultGalleryPlayer = function () {
        let c_gallery              = this;
        let c_player               = document.createElement__withAttributes('x-gallery-player', {'aria-hidden' : 'true'});
        let c_player_thumbnails    = document.createElement('x-thumbnails');
        let c_player_button_l      = document.createElement('x-button-l');
        let c_player_button_r      = document.createElement('x-button-r');
        let c_player_button_c      = document.createElement('x-button-c');
        let c_player_viewing_part  = document.createElement('x-viewing-part');
        let c_player_viewing_area  = document.createElement('x-viewing-area');
        let player_show            = function () {c_player.removeAttribute('aria-hidden'); document.body.setAttribute('data-is-active-gallery-player', 'true');}
        let player_hide            = function () {c_player.setAttribute('aria-hidden', 'true'); document.body.removeAttribute('data-is-active-gallery-player'); viewing_area_clear();}
        let player_move_L          = function () {c_player_thumbnails.querySelector__withHandler('x-thumbnail[aria-selected="true"]',         function (c_selected) { if (c_selected.previousSibling) {c_selected.previousSibling.click(); thumbnails_centration(); button_L_set_state(); button_R_set_state();} })}
        let player_move_R          = function () {c_player_thumbnails.querySelector__withHandler('x-thumbnail[aria-selected="true"]',         function (c_selected) { if (c_selected.nextSibling    ) {c_selected.nextSibling    .click(); thumbnails_centration(); button_L_set_state(); button_R_set_state();} })}
        let button_L_set_state     = function () {c_player_thumbnails.querySelector__withHandler('x-thumbnail[aria-selected="true"]',         function (c_selected) { if (c_selected.previousSibling) c_player_button_l.removeAttribute('data-is-blocked'); else c_player_button_l.setAttribute('data-is-blocked', ''); })}
        let button_R_set_state     = function () {c_player_thumbnails.querySelector__withHandler('x-thumbnail[aria-selected="true"]',         function (c_selected) { if (c_selected.nextSibling    ) c_player_button_r.removeAttribute('data-is-blocked'); else c_player_button_r.setAttribute('data-is-blocked', ''); })}
        let thumbnails_centration  = function () {c_player_thumbnails.querySelector__withHandler('x-thumbnail[aria-selected="true"]',         function (c_selected) { c_player_thumbnails.scrollLeft = c_selected.offsetLeft - (c_player_thumbnails.clientWidth / 2) + (c_selected.clientWidth / 2) + 3; })}
        let thumbnails_reset_state = function () {c_player_thumbnails.querySelectorAll          ('x-thumbnail[aria-selected="true"]').forEach(function (c_selected) { c_selected.removeAttribute('aria-selected'); });}
        let viewing_area_clear     = function () {c_player_viewing_area.innerHTML = '';}
        c_gallery.prepend(c_player);
        c_gallery.setAttribute('data-player-is-processed', 'true');
        c_player_viewing_part.append(c_player_button_l, c_player_viewing_area, c_player_button_r);
        c_player.append(c_player_thumbnails, c_player_button_c, c_player_viewing_part);

        // bind events
        c_player_button_c.addEventListener('click', player_hide);
        c_player_button_l.addEventListener('click', player_move_L);
        c_player_button_r.addEventListener('click', player_move_R);
        document.addEventListener('keydown', function (event) {
            if (c_player.getAttribute('aria-hidden') !== 'true') {
                if (event.code === 'ArrowLeft' ) player_move_L();
                if (event.code === 'ArrowRight') player_move_R();
                if (event.code === 'Escape'    ) {
                    event.preventDefault();
                    player_hide();
                }
            }
        });

        // process of each gallery item
        c_gallery.querySelectorAll('x-item').forEach(function (c_item) {
            let c_thumbnail = document.createElement__withAttributes('x-thumbnail', {'data-type' : c_item.getAttribute('data-type'), 'data-num' : c_item.getAttribute('data-num')});
            switch (c_item.getAttribute('data-type')) {
                case 'picture':
                    var c_image = c_item.getElementsByTagName('img')[0];
                    var c_thumbnail_img_src = (new EffURL(c_image.getAttribute('src')).queryArgDelete('thumb').queryArgInsert('thumb', 'small')).relativeGet();
                    var c_preview_a_img_src = (new EffURL(c_image.getAttribute('src')).queryArgDelete('thumb').queryArgInsert('thumb', 'big'  )).relativeGet();
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
                    var c_thumbnail_img_src = c_audio.getAttribute('data-cover-thumbnail') ? c_audio.getAttribute('data-cover-thumbnail') : '/' + Effcore.getToken('thumbnail_path_cover_default');
                    var c_thumbnail_img = document.createElement__withAttributes('img', {'src' : c_thumbnail_img_src});
                    c_thumbnail.setAttribute('data-preview-area-content', JSON.stringify(c_audio.outerHTML).replace(/^"/, '').replace(/"$/, ''));
                    c_thumbnail.append(c_thumbnail_img);
                    c_player_thumbnails.append(c_thumbnail);
                    break;
            }

            // when click on item in gallery
            c_item.addEventListener('click', function (event) {
                event.stopPropagation();
                event.preventDefault();
                player_show();
                c_player_thumbnails.querySelector__withHandler('x-thumbnail[data-num="' + this.getAttribute('data-num') + '"]', function (c_selected) {
                    c_selected.click();
                    thumbnails_centration();
                });
            }, true);

            // when click on thumbnail in player
            c_thumbnail.addEventListener('click', function () {
                thumbnails_reset_state();
                c_thumbnail.setAttribute('aria-selected', 'true');
                viewing_area_clear();
                if (c_thumbnail.getAttribute('data-preview-area-content')) {
                    c_player_viewing_area.innerHTML = JSON.parse('"' + c_thumbnail.getAttribute('data-preview-area-content') + '"');
                    if (c_thumbnail.getAttribute('data-type') === 'audio') {
                        c_player_viewing_area.querySelectorAll('audio[data-player-name="default"]').forEach(function (c_player_viewing_area_audio) {
                            c_player_viewing_area_audio.process__defaultAudioPlayer();
                        });
                    }
                }
                button_L_set_state();
                button_R_set_state();
            });
        });
    };

    document.querySelectorAll('x-galleries-group[data-player-name="default"] x-gallery').forEach(function (c_gallery) {
        c_gallery.process__defaultGalleryPlayer();
    });

});
