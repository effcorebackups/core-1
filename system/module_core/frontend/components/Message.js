
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

export default class Message {

    static messages_block = null;
    static messages_group = null;
    static messages_lists = {
        'ok'     : null,
        'warning': null,
        'error'  : null,
        'notice' : null
    };

    static template_list(type) {
        let list = document.createElement('ul');
        list.setAttribute('data-type', type);
        return list;
    }

    static template_list_item(text) {
        let item = document.createElement('li');
        let item_text = document.createElement('p');
        item_text.innerHTML = text;
        item.append(item_text);
        return item;
    }

    //////////////////////////////////////////////////////////////////

    static init_messages_block() {
        if (this.messages_block === null) {
            this.messages_block = document.querySelector(
                '[data-area][data-id="messages"] [data-id="block__messages"] [data-block-content]'
            );
        }
    }

    static init_messages_group() {
        if (this.messages_group === null) {
            this.messages_group = this.messages_block.querySelector('x-messages');
            if (this.messages_group === null) {
                this.messages_group = document.createElement('x-messages');
                this.messages_block.append(
                    this.messages_group
                );
            }
        }
    }

    static init_messages_list(type) {
        if (this.messages_lists[type] === null) {
            this.messages_lists[type] = this.messages_group.querySelector(`ul[data-type="${type}"]`);
            if (this.messages_lists[type] === null) {
                this.messages_lists[type] = this.template_list(type);
                this.messages_group.append(
                    this.messages_lists[type]
                );
            }
        }
    }

    //////////////////////////////////////////////////////////////////

    static add(text, type = 'ok') {
        this.init_messages_block();
        if (this.messages_block === null) {
            console.log('Cannot find "messages_block"!');
            return;
        }
        this.init_messages_group();
        this.init_messages_list(type);
        this.messages_lists[type].append(
            this.template_list_item(text)
        );
    }

}
