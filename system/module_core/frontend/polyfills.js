
/* ───────────────────────────────────────────────────────────────────── */
/* polyfills                                                             */
/* ───────────────────────────────────────────────────────────────────── */

if (!Node.prototype.hasOwnProperty('prepend')) {
    Object.defineProperty(Node.prototype, 'prepend', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function () {
            for (var i = arguments.length - 1; i >= 0; i--) {
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
            for (var i = 0; i < arguments.length; i++) {
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

/* ───────────────────────────────────────────────────────────────────── */
/* additions                                                             */
/* ───────────────────────────────────────────────────────────────────── */

if (!Node.prototype.hasOwnProperty('querySelector__withHandler')) {
    Object.defineProperty(Node.prototype, 'querySelector__withHandler', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function (query, handler) {
            var result = this.querySelector(query);
            if (result instanceof Node) {
                handler(result);
            }
        }
    });
}

if (!Document.prototype.hasOwnProperty('createElement__withAttributes')) {
    Object.defineProperty(Document.prototype, 'createElement__withAttributes', {
        configurable: true,
        enumerable  : true,
        writable    : true,
        value: function (tag_name, attributes, options) {
            var node = document.createElement(tag_name, options);
            if (Effcore.getType(attributes) === 'Object')
                for (var c_key in attributes)
                    node.setAttribute(c_key, attributes[c_key]);
            return node;
        }
    });
}
