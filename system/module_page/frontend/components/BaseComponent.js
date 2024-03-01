
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

export default class BaseComponent {

    constructor() {
        this.pool = {};
    }

    Markup(tag_name = 'div', attributes = {}, children = null, ignore_pool = false) {
        let element = document.createElement(tag_name);
        for (let c_key in attributes)
            if (!c_key.startsWith('on'))
                 element.setAttribute    (c_key,                          attributes[c_key]            );
            else element.addEventListener(c_key.substring(2), () => {this[attributes[c_key]](element);});
        if (children !== null) {
            if (Array.isArray(children))
                for (let c_key in children)
                    element.append(children[c_key]);
            else    element.append(children); }
        if (!ignore_pool) {
            if (!this.pool[tag_name])
                 this.pool[tag_name] = [];
            this.pool[tag_name].push(element); }
        return element;
    }

    template_get() {
        return null;
    }

}
