<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Pager extends Markup {

    const ERR_CODE_OK         = 0b0000;
    const ERR_CODE_CUR_NO_INT = 0b0001;
    const ERR_CODE_CUR_LT_MIN = 0b0010;
    const ERR_CODE_CUR_GT_MAX = 0b0100;
    const ERR_CODE_MAX_LT_MIN = 0b1000;

    public $tag_name = 'nav';
    public $attributes = ['aria-label' => 'pager'];
    public $error_code = self::ERR_CODE_OK;

    public $cur;
    public $min = 1;
    public $max = 1;
    public $name = 'page';
    public $id = 0;

    function __construct($min = 1, $max = 1, $name = 'page', $id = 0, $attributes = [], $weight = +0) {
        $this->min  = $min;
        $this->max  = $max;
        $this->name = $name;
        $this->id   = $id;
        parent::__construct(null, $attributes, [], $weight);
    }

    function init() {
        if ($this->cur === null) {
            $url_args = Request::values_get($this->name, '_GET', [], false);
            $this->cur = array_key_exists($this->id, $url_args) ? $url_args[$this->id] : '';
            if ($this->cur === ''                                           ) {$this->cur = $this->min;}
            if ($this->cur !== '' && !Security::validate_str_int($this->cur)) {$this->cur = $this->min; $this->error_code |= static::ERR_CODE_CUR_NO_INT;}
            $this->min = (int)$this->min;
            $this->max = (int)$this->max;
            $this->cur = (int)$this->cur;
            if ($this->max < $this->min) {$this->max = $this->min; $this->error_code |= static::ERR_CODE_MAX_LT_MIN;}
            if ($this->cur < $this->min) {$this->cur = $this->min; $this->error_code |= static::ERR_CODE_CUR_LT_MIN;}
            if ($this->cur > $this->max) {$this->cur = $this->max; $this->error_code |= static::ERR_CODE_CUR_GT_MAX;}
        }
    }

    function error_code_get() {
        $this->init();
        return $this->error_code;
    }

    # ─────────────────────────────────────────────────────────────────────
    # the dynamic of the pager center part:
    # ═════════════════════════════════════════════════════════════════════
    #
    #
    #         cur = 3
    #             ◍
    #           ┌┐┼──────┐
    #           └┘┴──────┘
    #           ┝┿┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷
    #    min = 1││       │B_min = min + 1 + 8 = 10
    #            │
    #            │A_min = min + 1 = 2
    #
    #
    #             cur = 7
    #                 ◍
    #           ┌┐┌───┼───┐
    #           └┘└───┴───┘
    #           ┝┷┿┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷
    #    min = 1│ │       │B_min = cur + 4 = 11
    #             │
    #             │A_min = cur - 4 = 3
    #
    #
    #                                                      cur = 98
    #                                                          ◍
    #                                                   ┌──────┼┌┐
    #                                                   └──────┴└┘
    #           ┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┿┥
    #                           A_max = max - 8 - 1 = 91│       ││max = 100
    #                                                           │
    #                                       B_max = max - 1 = 99│
    #
    #
    #                                                  cur = 94
    #                                                      ◍
    #                                                  ┌───┼───┐┌┐
    #                                                  └───┴───┘└┘
    #           ┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┿┷┥
    #                              A_max = cur - 4 = 90│       │ │max = 100
    #                                                          │
    #                                      B_max = cur + 4 = 98│
    #
    #
    # ─────────────────────────────────────────────────────────────────────

    function build() {
        if (!$this->is_builded) {
            $this->init();

            # ─────────────────────────────────────────────────────────────────────
            # min part
            # ─────────────────────────────────────────────────────────────────────

            if ($this->max - $this->min > 0) {
                if ($this->cur === $this->min)
                     $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $this->min]), 'href' => static::url_get($this->name, $this->id), 'aria-current' => 'true'], $this->min));
                else $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $this->min]), 'href' => static::url_get($this->name, $this->id)                          ], $this->min));
            }

            # ─────────────────────────────────────────────────────────────────────
            # central part
            # ─────────────────────────────────────────────────────────────────────

            if ($this->max - $this->min > 1) {
                $a_min = $this->cur - $this->min < 6 ? $this->min + 1 : $this->cur - 4;
                $b_min = $this->cur - $this->min < 6 ? $this->min + 9 : $this->cur + 4;
                $a_max = $this->max - $this->cur < 6 ? $this->max - 9 : $this->cur - 4;
                $b_max = $this->max - $this->cur < 6 ? $this->max - 1 : $this->cur + 4;
                $a     = $this->cur - $this->min < 6 ? max($a_min, $a_max) : min($a_min, $a_max);
                $b     = $this->cur - $this->min < 6 ? max($b_min, $b_max) : min($b_min, $b_max);

                # l-shoulder part
                if ($a > $this->min + 10) {
                    $this->child_insert(new Text('…'));
                    for ($j = 1; $j < 4; $j++) {
                        $c_i = $this->min + (int)(($a - $this->min) / 4 * $j);
                        $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $c_i]), 'href' => static::url_get($this->name, $this->id, $c_i)], $c_i));
                    }
                }

                # central links part
                if ($a > $this->min + 1) {
                    $this->child_insert(new Text('…'));
                }
                for ($i = $a; $i <= $b; $i++) {
                    if ($i > $this->min && $i < $this->max) {
                        if ($this->cur === $i)
                             $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $i]), 'href' => static::url_get($this->name, $this->id, $i), 'aria-current' => 'true'], $i));
                        else $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $i]), 'href' => static::url_get($this->name, $this->id, $i)                          ], $i));
                    }
                }
                if ($b < $this->max - 1) {
                    $this->child_insert(new Text('…'));
                }

                # r-shoulder part
                if ($b < $this->max - 10) {
                    for ($j = 1; $j < 4; $j++) {
                        $c_i = $b + (int)(($this->max - $b) / 4 * $j);
                        $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $c_i]), 'href' => static::url_get($this->name, $this->id, $c_i)], $c_i));
                    }
                    $this->child_insert(new Text('…'));
                }
            }

            # ─────────────────────────────────────────────────────────────────────
            # max part
            # ─────────────────────────────────────────────────────────────────────

            if ($this->max - $this->min > 0) {
                if ($this->cur === $this->max)
                     $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $this->max]), 'href' => static::url_get($this->name, $this->id, $this->max), 'aria-current' => 'true'], $this->max));
                else $this->child_insert(new Markup('a', ['title' => new Text('go to page #%%_number', ['number' => $this->max]), 'href' => static::url_get($this->name, $this->id, $this->max)                          ], $this->max));
            }

            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function url_get($name, $id, $number = 1) {
        $url = clone URL::get_current();
        $url->query_arg_delete($name);
        $url_args = Request::values_get($name, '_GET', [], false);
        unset($url_args[$id]);
        if ($number !== 1)
            $url_args[$id] = $number;
        ksort($url_args);
        # optimization for page #1: page[x]=1 & page[y]=100 & page[z]=1 → page[y]=100
        foreach ($url_args as $c_id => $c_number)
            if ($c_number === 1)
                unset($url_args[$c_id]);
        # sanitization: only numeric pairs (int|str_int) => (int|str_int) is available
        foreach ($url_args as $c_id => $c_number)
            if (!(Security::validate_str_int($c_id) && Security::validate_str_int($c_number)))
                unset($url_args[$c_id]);
        # optimization for single pager with id = 0: page[0]=1 → page=1, … , page[0]=N → page=N
        if (count($url_args) === 1 && key($url_args) === 0)
            $url_args = reset($url_args);
        $url->query_arg_insert($name, $url_args);
        return $url->relative_get();
    }

}
