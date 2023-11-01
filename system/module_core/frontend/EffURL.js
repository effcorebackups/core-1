
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

// ─────────────────────────────────────────────────────────────────────
// EffURL class
// ─────────────────────────────────────────────────────────────────────

class EffURL {

    constructor(url, options = {}) {
        if (options['completion'] === undefined) options['completion'] = true;
        if (options['extra']      === undefined) options['extra'] = '\\p{Ll}\\p{Lo}\\p{Lt}\\p{Lu}';
        this.raw = url;
        this.pattern = new RegExp('^(?:([a-zA-Z]+)://|)' +                                                      // protocol
                                      '([' + options['extra'] + 'a-zA-Z0-9\\-\\.]{2,200}(?::([0-9]{1,5})|)|)' + // domain + port
                                     '(/[' + options['extra'] + '\\x21-\\x22\\x24-\\x3e\\x40-\\x7e]*|)' +       // path
                                '(?:\\?([' + options['extra'] + '\\x21-\\x22\\x24-\\x7e]*)|)' +                 // query
                                  '(?:#([' + options['extra'] + '\\x21-\\x7e]*)|)$', 'u');                      // anchor
        this.parse = url.match(this.pattern)?.map((value) => value === undefined ? '' : value);
        let [, protocol, domain, port, path, query, anchor] = this.parse ?? [, '', '', '', '', '', '', ''];

        // matrix check
        if ( (!protocol.length && !domain.length &&  path.length && !query.length && !anchor.length) ||  //                  /path
             (!protocol.length && !domain.length &&  path.length &&  query.length && !anchor.length) ||  //                  /path?query
             (!protocol.length && !domain.length &&  path.length && !query.length &&  anchor.length) ||  //                  /path#anchor
             (!protocol.length && !domain.length &&  path.length &&  query.length &&  anchor.length) ||  //                  /path?query#anchor
             (!protocol.length &&  domain.length && !path.length && !query.length && !anchor.length) ||  //            domain
             (!protocol.length &&  domain.length &&  path.length && !query.length && !anchor.length) ||  //            domain/path
             (!protocol.length &&  domain.length &&  path.length &&  query.length && !anchor.length) ||  //            domain/path?query
             (!protocol.length &&  domain.length &&  path.length && !query.length &&  anchor.length) ||  //            domain/path?#anchor
             (!protocol.length &&  domain.length &&  path.length &&  query.length &&  anchor.length) ||  //            domain/path?query#anchor
             ( protocol.length &&  domain.length && !path.length && !query.length && !anchor.length) ||  // protocol://domain
             ( protocol.length &&  domain.length &&  path.length && !query.length && !anchor.length) ||  // protocol://domain/path
             ( protocol.length &&  domain.length &&  path.length &&  query.length && !anchor.length) ||  // protocol://domain/path?query
             ( protocol.length &&  domain.length &&  path.length && !query.length &&  anchor.length) ||  // protocol://domain/path?#anchor
             ( protocol.length &&  domain.length &&  path.length &&  query.length &&  anchor.length) ) { // protocol://domain/path?query#anchor
            this.protocol  = protocol;
            this.domain    = domain;
            this.port      = port;
            this.path      = path;
            this.query     = query;
            this.anchor    = anchor;
            this.has_error = false;
            if (options['completion'] === true && this.protocol === '') this.protocol = window.location.protocol.replace(/[:]*$/g, '');
            if (options['completion'] === true && this.domain   === '') this.domain   = window.location.hostname;
            if (options['completion'] === true && this.path     === '') this.path     = '/';
        } else {
            this.has_error = true;
        }
    }

    relativeGet() {
        if (!this.has_error) {
            let result = this.path;
            if (this.query ) result+= '?' + this.query;
            if (this.anchor) result+= '#' + this.anchor;
            return result;
        }
    }

    absoluteGet() {
        if (!this.has_error) {
            let result = this.protocol + '://' + this.domain + this.path;
            if (this.query ) result+= '?' + this.query;
            if (this.anchor) result+= '#' + this.anchor;
            return result.replace(/[/]*$/g, '');
        }
    }

    queryArgSelect(name       ) {if (this.has_error) return; let args = EffURL.parseQuery(this.query); return args[name] ?? null;}
    queryArgInsert(name, value) {if (this.has_error) return; let args = EffURL.parseQuery(this.query);        args[name] = value; this.query = EffURL.buildQuery(args); return this;}
    queryArgDelete(name       ) {if (this.has_error) return; let args = EffURL.parseQuery(this.query); delete args[name];         this.query = EffURL.buildQuery(args); return this;}

    ///////////////////////////
    /// static declarations ///
    ///////////////////////////

    static parseQuery(value) {
        let result = {};
        value.split('&').forEach((c_param_raw) => {
            if (c_param_raw.length) {
                let [, c_key_raw, c_value_raw] = c_param_raw.match(/^([^=]+)[=]{0,1}(.*)$/i) ?? [, '', ''];
                let [, c_key_group_raw, c_groups_raw] = c_key_raw.match(/^([a-z0-9\_\-]+)(\[.*\]|)$/i) ?? [, '', ''];
                if (c_groups_raw.length) {
                    let c_groups = c_groups_raw.matchAll(/\[([a-z0-9\_\-]*)\]/gi);
                    let c_group_indexes = [];
                    for (const [, c_group_index] of c_groups) {
                        c_group_indexes.push(
                            decodeURIComponent(c_group_index)
                        );
                    }
                    let c_pointer;
                    let c_key_group = decodeURIComponent(c_key_group_raw);
                    if (result[c_key_group] === undefined                                                     ) c_pointer = result[c_key_group] = {};
                    if (result[c_key_group] !== undefined && Effcore.getType(result[c_key_group]) === 'String') c_pointer = result[c_key_group] = {};
                    if (result[c_key_group] !== undefined && Effcore.getType(result[c_key_group]) !== 'String') c_pointer = result[c_key_group];
                    for (let i = 0; i < c_group_indexes.length; i++) {
                        let is_last = !(i < c_group_indexes.length - 1);
                        let c_group_index = c_group_indexes[i];
                        let c_group_index_max = c_pointer.maxIndex() + 1;
                        if (!is_last && c_group_index === '' && c_pointer[c_group_index] === undefined) c_pointer[c_group_index_max] = {};
                        if (!is_last && c_group_index !== '' && c_pointer[c_group_index] === undefined) c_pointer[c_group_index    ] = {};
                        if ( is_last && c_group_index === '') c_pointer[c_group_index_max] = decodeURIComponent(c_value_raw);
                        if ( is_last && c_group_index !== '') c_pointer[c_group_index    ] = decodeURIComponent(c_value_raw);
                        if (!is_last && c_group_index === '') c_pointer = c_pointer[c_group_index_max];
                        if (!is_last && c_group_index !== '') c_pointer = c_pointer[c_group_index    ];
                    }
                } else if(c_key_raw.length) {
                    result[decodeURIComponent( c_key_raw )] =
                           decodeURIComponent(c_value_raw);
                }
            }
        });
        return result;
    }

    static buildQuery(data, path = null) {
        let result = {};
        let c_path;
        for (let c_key in data) {
            c_path = path ? path + '[' + encodeURIComponent(c_key) + ']' :
                                         encodeURIComponent(c_key);
            if (Effcore.getType(data[c_key]) === 'Object') Object.assign(result, this.buildQuery(data[c_key], c_path));
            if (Effcore.getType(data[c_key]) !== 'Object') result[c_path] = encodeURIComponent(data[c_key]);
        }
        if (path !== null) return result;
        if (path === null) {
            let final_result = [];
            for (let c_key in result)
                   final_result.push(c_key + '=' + result[c_key]);
            return final_result.join('&');
        }
    }

}
