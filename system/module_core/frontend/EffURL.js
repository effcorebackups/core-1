
'use strict';

/* ───────────────────────────────────────────────────────────────────── */
/* EffURL class                                                          */
/* ───────────────────────────────────────────────────────────────────── */

class EffURL {

    constructor(url, non_completion) {
        this.raw = url;
        this.pattern = new RegExp('^(?:([a-zA-Z]+)://|)' +                                /* protocol */
                                      '([a-zA-Z0-9\\-\\.]{2,200}(?:\\:([0-9]{1,5})|)|)' + /* domain + port */
                                     '(/[\\x21-\\x22\\x24-\\x3e\\x40-\\x7e]*|)' +         /* path */
                                '(?:\\?([\\x21-\\x22\\x24-\\x7e]*)|)' +                   /* query */
                                '(?:\\#([\\x21-\\x7e]*)|)$');                             /* anchor */

        var parse = this.parse = url.match(this.pattern);
        var protocol = parse !== null && parse[1] !== undefined ? parse[1] : '';
        var domain   = parse !== null && parse[2] !== undefined ? parse[2] : '';
        var port     = parse !== null && parse[3] !== undefined ? parse[3] : '';
        var path     = parse !== null && parse[4] !== undefined ? parse[4] : '';
        var query    = parse !== null && parse[5] !== undefined ? parse[5] : '';
        var anchor   = parse !== null && parse[6] !== undefined ? parse[6] : '';

        /* matrix check */
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
            if (!(non_completion === true) && this.protocol === '') this.protocol = window.location.protocol.replace(/[:]*$/g, '');
            if (!(non_completion === true) && this.domain   === '') this.domain   = window.location.hostname;
            if (!(non_completion === true) && this.path     === '') this.path     = '/';
        } else {
            this.has_error = true;
        }
    }

    tinyGet() {
        if (!this.has_error) {
            var result = this.path;
            if (this.query ) result+= '?' + this.query;
            if (this.anchor) result+= '#' + this.anchor;
            return result;
        }
    }

    fullGet() {
        if (!this.has_error) {
            var result = this.protocol + '://' + this.domain + this.path;
            if (this.query ) result+= '?' + this.query;
            if (this.anchor) result+= '#' + this.anchor;
            return result.replace(/[/]*$/g, '');
        }
    }

    queryArgSelect(name       ) {if (this.has_error) return; var args = EffURL.parseUrlQuery(this.query); return args[name] ? args[name] : null;}
    queryArgInsert(name, value) {if (this.has_error) return; var args = EffURL.parseUrlQuery(this.query);        args[name] = value; this.query = EffURL.buildUrlQuery(args); return this;}
    queryArgDelete(name       ) {if (this.has_error) return; var args = EffURL.parseUrlQuery(this.query); delete args[name];         this.query = EffURL.buildUrlQuery(args); return this;}

    /* ─────────────────── */
    /* static declarations */
    /* ─────────────────── */

    static parseUrlQuery(value) {
        var result = {}, counters = {};
        value.split('&').forEach(function (c_param_raw) {
            var c_param = c_param_raw.split('=');
            if (c_param[0].length) {
                var c_key = c_param.shift(), c_val = c_param.join('='), c_analyze = c_key.match(/([a-z0-9\_\-]+)(?:\[([a-z0-9\_\-]*)\]|)$/i);
                if (c_analyze[2] !== undefined) {
                    var c_sub_key = decodeURIComponent(c_analyze[1]);
                    var c_sub_idx = decodeURIComponent(c_analyze[2]);
                    var c_sub_val = decodeURIComponent(c_val);
                    if (c_sub_idx === '' && counters[c_sub_key] === undefined) counters[c_sub_key] = 0;
                    if (c_sub_idx === '' && counters[c_sub_key] !== undefined) c_sub_idx = counters[c_sub_key]++;
                    if (result[c_sub_key] === undefined)
                        result[c_sub_key] = {};
                    result[c_sub_key][c_sub_idx] = c_sub_val;
                } else {
                    result[decodeURIComponent(c_key)] =
                           decodeURIComponent(c_val);
                }
            }
        });
        return result;
    }

    static buildUrlQuery(params) {
        var result = [];
        for (var c_key in params)
            if (Effcore.getType(params[c_key]) === 'Object')
                for (var c_sub_key in params[c_key])
                    result.push(encodeURIComponent(c_key) + '[' + c_sub_key + ']=' + encodeURIComponent(params[c_key][c_sub_key]));
            else    result.push(encodeURIComponent(c_key) +                    '=' + encodeURIComponent(params[c_key]));
        return result.join('&');
    }

}
