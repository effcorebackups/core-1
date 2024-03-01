
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import BaseComponent from '../BaseComponent.js';

export default class GalleryComponent extends BaseComponent {

    constructor(data = [], on_change = null) {
        super();
        this.data = data;
        this.on_change = on_change;
        this.position_min = 0;
        this.position_cur = 0;
        this.position_max = data.length - 1;
        this.state = 'closed';
        this.template = this.template_get();
        document.addEventListener('keydown',
            () => {this.on_keydown();}
        );
    }

    mount(parent) {
        return parent.append(
            this.template
        );
    }

    show(cur = 0) {
        this.position_cur = cur;
        this.state = 'opened';
        this.template_update(null);
    }

    //////////////////////////////////////////////////////////////////

    template_get() {
        return this.Markup('x-gallery-player', {'aria-hidden': 'true'}, [
            this.Markup('x-thumbnails', {}, this.data.map((item, num) =>
                this.Markup('x-thumbnail', {'data-num': num, 'data-type': item.type, 'onclick': 'on_click_thumbnail'},
                    this.Markup('img', {'src': item['thumbnail'], 'ondragstart': 'on_drag_thumbnail'}) ))),
            this.Markup('x-button-c', {'onclick': 'on_click_close'}),
            this.Markup('x-viewing-part', {}, [
                this.Markup('x-button-l', {'onclick': 'on_click_previous'}),
                this.Markup('x-viewing-area', {}, this.Markup('x-centerer')),
                this.Markup('x-button-r', {'onclick': 'on_click_next'})
            ])
        ]);
    }

    template_update(sender = null) {
        if (this.state === 'opened') {
            document.body.setAttribute('data-is-active-gallery-player', 'true');
            this.template.removeAttribute('aria-hidden');
            let [min, cur, max] = [this.position_min, this.position_cur, this.position_max];
            this.pool['x-thumbnail'].forEach(c_item => {c_item.removeAttribute('aria-selected');});
            this.pool['x-thumbnail'][cur].setAttribute('aria-selected', 'true');
            if (sender?.tagName.toLowerCase() !== 'x-thumbnail') this.pool['x-thumbnails'][0].scrollLeft = this.pool['x-thumbnail'][cur].offsetLeft - (this.pool['x-thumbnails'][0].clientWidth / 2) + (this.pool['x-thumbnail'][cur].clientWidth / 2) - 24;
            if (cur === min) this.pool['x-button-l'][0].setAttribute('data-is-blocked', '');
            if (cur !== min) this.pool['x-button-l'][0].removeAttribute('data-is-blocked');
            if (cur === max) this.pool['x-button-r'][0].setAttribute('data-is-blocked', '');
            if (cur !== max) this.pool['x-button-r'][0].removeAttribute('data-is-blocked');
            this.pool['x-centerer'][0].innerHTML = '';
            if (this.data[cur]['type']             && this.data[cur]['picture']) this.pool['x-centerer'][0].append(this.Markup( 'img',  this.data[cur]['picture'], null, true));
            if (this.data[cur]['type'] === 'audio' && this.data[cur][ 'audio' ]) this.pool['x-centerer'][0].append(this.Markup('audio', this.data[cur][ 'audio' ], null, true));
            if (this.data[cur]['type'] === 'video' && this.data[cur][ 'video' ]) this.pool['x-centerer'][0].append(this.Markup('video', this.data[cur][ 'video' ], null, true));
            if (this.on_change) {
                this.on_change.call(this, this.data[cur]['type'], this.pool['x-centerer'][0]);
            }
        }
        if (this.state === 'closed') {
            this.pool['x-centerer'][0].innerHTML = '';
            document.body.removeAttribute('data-is-active-gallery-player');
            this.template.setAttribute('aria-hidden', 'true');
            return;
        }
    }

    //////////////////////////////////////////////////////////////////

    on_drag_thumbnail() {
        event.preventDefault();
    }

    on_click_thumbnail(node) {
        let cur = node.getAttribute('data-num');
        this.position_cur = parseInt(cur);
        this.template_update(node);
    }

    on_click_previous(node = null) {
        if (this.position_cur > this.position_min) {
            this.position_cur--;
            this.template_update(node);
        }
    }

    on_click_next(node = null) {
        if (this.position_cur < this.position_max) {
            this.position_cur++;
            this.template_update(node);
        }
    }

    on_click_close(node = null) {
        this.state = 'closed';
        this.template_update(node);
    }

    on_keydown() {
        if (this.state === 'opened') {
            if (event.code === 'Escape'    ) {event.preventDefault(); this.on_click_close   ();}
            if (event.code === 'ArrowLeft' ) {event.preventDefault(); this.on_click_previous();}
            if (event.code === 'ArrowRight') {event.preventDefault(); this.on_click_next    ();}
        }
    }

}
