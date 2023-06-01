<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class pager extends markup {

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

    function __construct($min = 1, $max = 1, $name = 'page', $id = 0, $attributes = [], $weight = 0) {
        $this->min  = $min;
        $this->max  = $max;
        $this->name = $name;
        $this->id   = $id;
        parent::__construct(null, $attributes, [], $weight);
    }

    function init() {
        if ($this->cur === null) {
            $this->cur = request::value_get($this->name_get(), 0, '_GET');
            if ($this->cur === ''                                                  ) {$this->cur = $this->min;}
            if ($this->cur !== '' && (string)$this->cur !== (string)(int)$this->cur) {$this->cur = $this->min; $this->error_code |= static::ERR_CODE_CUR_NO_INT;}
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

    function name_get($is_optimized = true) {
        if (!$is_optimized)
             return             $this->name.$this->id;
        else return $this->id ? $this->name.$this->id :
                                $this->name;
    }

    function url_page_max_get() {
        $this->init();
        $pager_name               = $this->name_get();
        $pager_name_not_optimized = $this->name_get(false);
        $url = clone url::get_current();
        $url->query_arg_delete($pager_name);
        $url->query_arg_delete($pager_name_not_optimized);
        if ($this->max > 1) $url->query_arg_insert($pager_name, $this->max);
        return $url;
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
            $pager_name               = $this->name_get();
            $pager_name_not_optimized = $this->name_get(false);
            $url = clone url::get_current();
            $url->query_arg_delete($pager_name);
            $url->query_arg_delete($pager_name_not_optimized);

            # ─────────────────────────────────────────────────────────────────────
            # min part
            # ─────────────────────────────────────────────────────────────────────

            if ($this->max - $this->min > 0) {
                if ($this->cur === $this->min)
                     $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $this->min]), 'href' => $url->tiny_get(), 'aria-current' => 'true'], $this->min));
                else $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $this->min]), 'href' => $url->tiny_get()                          ], $this->min));
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
                    $this->child_insert(new text('…'));
                    for ($j = 1; $j < 4; $j++) {
                        $c_i = $this->min + (int)(($a - $this->min) / 4 * $j);
                        $url->query_arg_insert($pager_name, $c_i);
                        $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $c_i]), 'href' => $url->tiny_get()], $c_i));
                    }
                }

                # central links part
                if ($a > $this->min + 1) {
                    $this->child_insert(new text('…'));
                }
                for ($i = $a; $i <= $b; $i++) {
                    if ($i > $this->min && $i < $this->max) {
                        $url->query_arg_insert($pager_name, $i);
                        if ($this->cur === $i)
                             $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $i]), 'href' => $url->tiny_get(), 'aria-current' => 'true'], $i));
                        else $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $i]), 'href' => $url->tiny_get()                          ], $i));
                    }
                }
                if ($b < $this->max - 1) {
                    $this->child_insert(new text('…'));
                }

                # r-shoulder part
                if ($b < $this->max - 10) {
                    for ($j = 1; $j < 4; $j++) {
                        $c_i = $b + (int)(($this->max - $b) / 4 * $j);
                        $url->query_arg_insert($pager_name, $c_i);
                        $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $c_i]), 'href' => $url->tiny_get()], $c_i));
                    }
                    $this->child_insert(new text('…'));
                }
            }

            # ─────────────────────────────────────────────────────────────────────
            # max part
            # ─────────────────────────────────────────────────────────────────────

            if ($this->max - $this->min > 0) {
                $url->query_arg_insert($pager_name, $this->max);
                if ($this->cur === $this->max)
                     $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $this->max]), 'href' => $url->tiny_get(), 'aria-current' => 'true'], $this->max));
                else $this->child_insert(new markup('a', ['title' => new text('go to page #%%_number', ['number' => $this->max]), 'href' => $url->tiny_get()                          ], $this->max));
            }

            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

}
