
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

// ─────────────────────────────────────────────────────────────────────
// polyfills
// ─────────────────────────────────────────────────────────────────────

if (!Node.prototype.hasOwnProperty('prepend')) {
    Object.defineProperty(Node.prototype, 'prepend', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function () {
            for (let i = arguments.length - 1; i >= 0; i--) {
                if (arguments[i] instanceof Node)
                     this.insertBefore(                               arguments[i],   this.firstChild);
                else this.insertBefore(document.createTextNode(String(arguments[i])), this.firstChild);
            }
        }
    });
}

if (!Node.prototype.hasOwnProperty('append')) {
    Object.defineProperty(Node.prototype, 'append', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function () {
            for (let i = 0; i < arguments.length; i++) {
                if (arguments[i] instanceof Node)
                     this.appendChild(                               arguments[i]  );
                else this.appendChild(document.createTextNode(String(arguments[i])));
            }
        }
    });
}

if (!NodeList.prototype.hasOwnProperty('forEach')) {
    Object.defineProperty(NodeList.prototype, 'forEach', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: Array.prototype.forEach
    });
}

if (!KeyboardEvent.prototype.hasOwnProperty('code')) {
    Object.defineProperty(KeyboardEvent.prototype, 'code', {
        configurable: true,
        enumerable  : true,
        get: function () {
            switch (this.keyCode) {
                case 27: return 'Escape';
                case 13: return 'Enter';
                case 37: return 'ArrowLeft';
                case 38: return 'ArrowUp';
                case 39: return 'ArrowRight';
                case 40: return 'ArrowDown';
            }
        }
    });
}

// ─────────────────────────────────────────────────────────────────────
// additions
// ─────────────────────────────────────────────────────────────────────

if (!Object.prototype.hasOwnProperty('maxIndex')) {
    Object.defineProperty(Object.prototype, 'maxIndex', {
        value: function () {
            return Object.keys(this)
                .filter((value) => /^[0-9]+$/.test(value))
                .map((value) => parseInt(value))
                .reduce((a, b) => Math.max(a, b), -1);
        }
    });
}

if (!Node.prototype.hasOwnProperty('querySelectorWithProcessing')) {
    Object.defineProperty(Node.prototype, 'querySelectorWithProcessing', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function (query, handler) {
            let result = this.querySelector(query);
            if (result instanceof Node) {
                handler(result);
            }
        }
    });
}
