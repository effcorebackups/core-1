<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class canvas_svg extends node_simple {

    public $template = 'canvas_svg';
    public $w;
    public $h;
    public $color_bg;
    public $scale;
    public $canvas = [];

    function __construct($w = 10, $h = 10, $scale = 1, $color_bg = 'white', $weight = 0) {
        if ($w       ) $this->w        = $w;
        if ($h       ) $this->h        = $h;
        if ($scale   ) $this->scale    = $scale;
        if ($color_bg) $this->color_bg = $color_bg;
        parent::__construct([], $weight);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function pixel_get($x, $y) {
        return $this->canvas[$y][$x] ?? null;
    }

    function pixel_set($x, $y, $color = '#000000') {
        $this->canvas[$y][$x] = $color;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function matrix_get($x = 0, $y = 0, $w = null, $h = null) {
        $matrix = [];
        for ($c_y = 0; $c_y < ($h ?: $this->h); $c_y++) {
        for ($c_x = 0; $c_x < ($w ?: $this->w); $c_x++) {
            $matrix[$c_y][$c_x] = $this->canvas[$c_y + $y][$c_x + $x] ?? null; }}
        return $matrix;
    }

    function matrix_set($matrix, $x = 0, $y = 0) {
        for ($c_y = 0; $c_y < count($matrix);       $c_y++) {
        for ($c_x = 0; $c_x < count($matrix[$c_y]); $c_x++) {
            $this->pixel_set($c_x + $x, $c_y + $y, $matrix[$c_y][$c_x]);
        }}
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function color_mask_get($color = '#000000') {
        $binstr = '';
        for ($c_y = 0; $c_y < $this->h; $c_y++) {
        for ($c_x = 0; $c_x < $this->w; $c_x++) {
            $binstr.= isset($this->canvas[$c_y][$c_x]) &&
                            $this->canvas[$c_y][$c_x] === $color ? '1' : '0'; }}
        return $binstr;
    }

    function color_mask_set($binstr, $color = '#000000') {
        $matrix = [];
        for ($c_y = 0; $c_y < $this->h; $c_y++) {
        for ($c_x = 0; $c_x < $this->w; $c_x++) {
            $matrix[$c_y][$c_x] = $binstr[$c_x + ($c_y * $this->w)] === '1' ? $color : null; }}
        $this->matrix_set($matrix);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function glyph_set($glyph, $x, $y) {
        $rows = array_reverse(explode('|', $glyph));
        for ($c_y = 0; $c_y < count ($rows);       $c_y++) {
        for ($c_x = 0; $c_x < strlen($rows[$c_y]); $c_x++) {
            $c_color = $rows[$c_y][$c_x] === 'X' ? '#000000' : null;
            if ($c_color) {
                $this->pixel_set($c_x + $x, $c_y + $y, $c_color);
            }
        }}
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function fill($color, $x = 0, $y = 0, $w = null, $h = null, $random = 0) {
        for ($c_y = 0; $c_y < ($h ?: $this->h); $c_y++) {
        for ($c_x = 0; $c_x < ($w ?: $this->w); $c_x++) {
            if (!$random || ($random && random_int(0, 10) / 10 > $random)) {
                $this->pixel_set($c_x + $x, $c_y + $y, $color);
            }
        }}
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function render() {
        return (template::make_new(template::pick_name($this->template), [
            'color_bg' => $this->color_bg,
            'width'    => $this->scale * $this->w,
            'height'   => $this->scale * $this->h,
            'canvas'   => $this->render_canvas($this->canvas)
        ]))->render();
    }

    function render_canvas($canvas) {
        $result = [];
        for ($c_y = 0; $c_y < $this->h; $c_y++) {
        for ($c_x = 0; $c_x < $this->w; $c_x++) {
            if (isset($this->canvas[$c_y][$c_x])) {
                $result[] = (new markup_xml_simple('rect', [
                    'style'  => 'fill:'.$this->canvas[$c_y][$c_x],
                    'x'      => 1 * $this->scale * $c_x,
                    'y'      => 1 * $this->scale * $c_y,
                    'width'  => 1 * $this->scale,
                    'height' => 1 * $this->scale
                ]))->render();
            }
        }}
        return implode(NL, $result);
    }

}
