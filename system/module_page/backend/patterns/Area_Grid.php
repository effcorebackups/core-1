<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Area_Grid extends Area_group {

    const COL_COUNT_MIN = 2;
    const COL_COUNT_MAX = 9;
    const ROW_COUNT_MIN = 2;
    const ROW_COUNT_MAX = 9;

    public $id = 'grid';
    public $type = 'grid-custom';
    public $col_count = self::COL_COUNT_MAX;
    public $row_count = self::ROW_COUNT_MAX;

    function build() {
        if (!$this->is_builded) {
            static::validate_col_count($this->col_count);
            static::validate_row_count($this->row_count);

            $this->attribute_insert('data-id',   $this->id);
            $this->attribute_insert('data-type', $this->type);
            $this->attribute_insert('data-w',    static::value_grid_w_get($this));
            $this->attribute_insert('data-h',    static::value_grid_h_get($this));
            if ($this->manage_mode) {
                $this->attribute_insert('data-manage-mode', $this->manage_mode);
            }

            # default mode
            if ($this->manage_mode === null) {
                $this->child_insert(
                    static::generate_markup($this), 'structure'
                );
            }

            # mode on '/manage/data/content/page/*'
            if ($this->manage_mode === 'block_filling') {
                $this->child_insert(
                    static::generate_markup__block_filling($this), 'structure'
                );
            }

            # mode on '/manage/view/layouts/*'
            if ($this->manage_mode === 'customization') {
                $this->child_insert(
                    static::generate_markup__customization($this), 'customizer'
                );
                if (!Frontend::select('grid_custom__permanent__page'))
                     Frontend::insert('grid_custom__permanent__page', null, 'styles', [
                        'path' => '/system/module_page/frontend/layout-grid-custom.cssd',
                        'attributes' => [
                            'rel'   => 'stylesheet',
                            'media' => 'all'],
                        'weight' => +350], 'layout_grid_custom_style', 'page'
                     );
                if (!Frontend::select('grid_custom__page'))
                     Frontend::insert('grid_custom__page', null, 'styles', [
                        'path' => '/system/module_page/frontend/layout-grid-custom.cssv?'.
                        'col_count='.$this->col_count.'&'.
                        'row_count='.$this->row_count,
                        'attributes' => [
                            'rel'   => 'stylesheet',
                            'media' => 'all'],
                        'weight' => +300], 'layout_grid_custom_virtual_style', 'page'
                     );
            }

            $this->is_builded = true;
        }
    }

    function states_get() {
        $result = [
            'grid_w' => static::value_grid_w_get($this),
            'grid_h' => static::value_grid_h_get($this)
        ];
        $c_weight = Area_Grid::COL_COUNT_MAX *
                    Area_Grid::ROW_COUNT_MAX;
        for ($y = 1; $y <= $this->row_count; $y++) {
            for ($x = 1; $x <= $this->col_count; $x++) {
                $c_weight--;
                if (static::value_activator_get($this, $x, $y)) {
                    for ($num_w = $this->col_count; $num_w >= 1; $num_w--) if (static::value_item_scaler_w_get($this, $x, $y, $num_w)) break;
                    for ($num_h = $this->row_count; $num_h >= 1; $num_h--) if (static::value_item_scaler_h_get($this, $x, $y, $num_h)) break;
                    $result['items'][$x.'-'.$y]['w'] = $num_w - $x + 1;
                    $result['items'][$x.'-'.$y]['h'] = $num_h - $y + 1;
                    $result['items'][$x.'-'.$y]['weight'] = $c_weight;
                }
            }
        }
        return $result;
    }

    ###########################
    ### static declarations ###
    ###########################

    static function generate_attribute_data_path($length = 1) {
        $result = '';
        for ($i = 1; $i <= $length; $i++)
               $result.= $i;
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function validate_col_count(&$value) {
        if ($value < static::COL_COUNT_MIN) $value = static::COL_COUNT_MIN;
        if ($value > static::COL_COUNT_MAX) $value = static::COL_COUNT_MAX;
    }

    static function validate_row_count(&$value) {
        if ($value < static::ROW_COUNT_MIN) $value = static::ROW_COUNT_MIN;
        if ($value > static::ROW_COUNT_MAX) $value = static::ROW_COUNT_MAX;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function value_grid_w_get($grid, $ignore_form = false) {
        # value from request
        if ($ignore_form === false && Request::value_get('form_id', 0, '_POST', false) === 'layout') {
            $result = (int)Request::value_get($grid->id.'-grid_w', 0, '_POST', $grid->col_count);
            return Security::sanitize_min_max(1, $grid->col_count, $result);
        }
        # value from settings
        if (isset(         $grid->states['grid_w'] )) {
            $result = (int)$grid->states['grid_w'];
            return Security::sanitize_min_max(1, $grid->col_count, $result);
        }
        # default value
        return $grid->col_count;
    }

    static function value_grid_h_get($grid, $ignore_form = false) {
        # value from request
        if ($ignore_form === false && Request::value_get('form_id', 0, '_POST', false) === 'layout') {
            $result = (int)Request::value_get($grid->id.'-grid_h', 0, '_POST', $grid->row_count);
            return Security::sanitize_min_max(1, $grid->row_count, $result);
        }
        # value from settings
        if (isset(         $grid->states['grid_h'] )) {
            $result = (int)$grid->states['grid_h'];
            return Security::sanitize_min_max(1, $grid->row_count, $result);
        }
        # default value
        return $grid->row_count;
    }

    static function value_activator_get($grid, $x, $y, $ignore_form = false) {
        # value from request
        if ($ignore_form === false && Request::value_get('form_id', 0, '_POST', false) === 'layout') {
            $form_values = Request::values_get($grid->id.'-activator', '_POST', [], false);
            return isset($form_values[$x][$y]);
        }
        # value from settings
        if (isset($grid->states['items'][$x.'-'.$y])) {
            return true;
        }
        # default value
        return false;
    }

    static function value_item_scaler_w_get($grid, $x, $y, $num, $ignore_form = false) {
        # value from request
        if ($ignore_form === false && Request::value_get('form_id', 0, '_POST', false) === 'layout') {
            $form_values = Request::values_get($grid->id.'-item_w', '_POST', [], false);
            $value = isset($form_values[$x][$y]) ?
                      (int)$form_values[$x][$y] : 0;
            return Security::sanitize_min_max($x, $grid->col_count, $value) === $num;
        }
        # value from settings
        if (isset($grid->states['items'][$x.'-'.$y]['w'])) {
            $value = $x + (int)$grid->states['items'][$x.'-'.$y]['w'] - 1;
            return Security::sanitize_min_max($x, $grid->col_count, $value) === $num;
        }
        # default value
        return $x === $num;
    }

    static function value_item_scaler_h_get($grid, $x, $y, $num, $ignore_form = false) {
        # value from request
        if ($ignore_form === false && Request::value_get('form_id', 0, '_POST', false) === 'layout') {
            $form_values = Request::values_get($grid->id.'-item_h', '_POST', [], false);
            $value = isset($form_values[$x][$y]) ?
                      (int)$form_values[$x][$y] : 0;
            return Security::sanitize_min_max($y, $grid->row_count, $value) === $num;
        }
        # value from settings
        if (isset($grid->states['items'][$x.'-'.$y]['h'])) {
            $value = $y + (int)$grid->states['items'][$x.'-'.$y]['h'] - 1;
            return Security::sanitize_min_max($y, $grid->row_count, $value) === $num;
        }
        # default value
        return $y === $num;
    }

    # ─────────────────────────────────────────────────────────────────────
    # default mode
    # ─────────────────────────────────────────────────────────────────────

    static function generate_markup($grid) {
        $result = new Node;
        if (isset($grid->states['items'])) {
            foreach ($grid->states['items'] as $c_coords_join => $c_info) {
                $c_coords = explode('-', $c_coords_join);
                $c_x     = $c_coords[0];
                $c_y     = $c_coords[1];
                $_weight = $c_info['weight'] ?? 0;
                $c_area = new Area;
                $c_area->id = $grid->id.'_cell_'.$c_y.'_'.$c_x;
                $c_area->attribute_insert('style', 'z-index: '.$_weight);
                $c_area->attribute_insert('data-start-x', $c_x);
                $c_area->attribute_insert('data-start-y', $c_y);
                $c_area->attribute_insert('data-end-x', Security::sanitize_min_max($c_x + 1, $grid->states['grid_w'] + 1, $c_x + $c_info['w']));
                $c_area->attribute_insert('data-end-y', Security::sanitize_min_max($c_y + 1, $grid->states['grid_h'] + 1, $c_y + $c_info['h']));
                if ($c_x > $grid->states['grid_w']) $c_area->attribute_insert('aria-hidden', 'true');
                if ($c_y > $grid->states['grid_h']) $c_area->attribute_insert('aria-hidden', 'true');
                $c_area->build();
                $result->child_insert($c_area, $c_x.'-'.$c_y);
            }
        }
        return $result;
    }

    # ─────────────────────────────────────────────────────────────────────
    # mode 'block_filling'
    # ─────────────────────────────────────────────────────────────────────

    static function generate_markup__block_filling($grid) {
        $result = new Node;
        if (isset($grid->states['items'])) {
            foreach ($grid->states['items'] as $c_coords_join => $c_info) {
                $c_coords = explode('-', $c_coords_join);
                $c_x     = $c_coords[0];
                $c_y     = $c_coords[1];
                $_weight = $c_info['weight'] ?? 0;
                $c_area = new Area;
                $c_area->id = $grid->id.'_cell_'.$c_y.'_'.$c_x;
                $c_area->manage_mode_enable('block_filling');
                $c_area->attribute_insert('style', 'z-index: '.$_weight);
                $c_area->attribute_insert('data-start-x', $c_x);
                $c_area->attribute_insert('data-start-y', $c_y);
                $c_area->attribute_insert('data-end-x', Security::sanitize_min_max($c_x + 1, $grid->states['grid_w'] + 1, $c_x + $c_info['w']));
                $c_area->attribute_insert('data-end-y', Security::sanitize_min_max($c_y + 1, $grid->states['grid_h'] + 1, $c_y + $c_info['h']));
                if ($c_x > $grid->states['grid_w']) $c_area->attribute_insert('aria-hidden', 'true');
                if ($c_y > $grid->states['grid_h']) $c_area->attribute_insert('aria-hidden', 'true');
                $c_area->build();
                $result->child_insert($c_area, $c_x.'-'.$c_y);
            }
        }
        return $result;
    }

    # ─────────────────────────────────────────────────────────────────────
    # mode 'customization'
    # ─────────────────────────────────────────────────────────────────────

    static function generate_markup__customization($grid) {
        $result = new Node;
        # grid scaler-w + scaler-h
        $value_grid_w = static::value_grid_w_get($grid);
        $value_grid_h = static::value_grid_h_get($grid);
        for ($x = 1; $x <= $grid->col_count; $x++) {
            $c_scaler = new Markup_simple('input', [
                'data-type' => 'scaler-w-grid',
                'type'      => 'radio',
                'name'      => $grid->id.'-grid_w',
                'value'     => $x]);
            if ($value_grid_w === $x) $c_scaler->attribute_insert('checked', true);
            $result->child_insert($c_scaler);
        }
        for ($y = 1; $y <= $grid->row_count; $y++) {
            $c_scaler = new Markup_simple('input', [
                'data-type' => 'scaler-h-grid',
                'type'      => 'radio',
                'name'      => $grid->id.'-grid_h',
                'value'     => $y]);
            if ($value_grid_h === $y) $c_scaler->attribute_insert('checked', true);
            $result->child_insert($c_scaler);
        }
        # grid manager
        $manager = new Markup('x-grid-manager', [
            'data-grid-manager' => true,
        ]);
        for ($y = 1; $y <= $grid->row_count; $y++) {
            $manager->child_insert(new Text_simple('<!-- ///////////////// row '.$y.' ///////////////// -->'));
            for ($x = 1; $x <= $grid->col_count; $x++) {
                $c_path_x = static::generate_attribute_data_path($x);
                $c_path_y = static::generate_attribute_data_path($y);
                # item activator
                $c_activator = new Markup_simple('input', [
                    'data-type'   => 'item-activator',
                    'data-x'      => $x,
                    'data-y'      => $y,
                    'data-path-x' => $c_path_x,
                    'data-path-y' => $c_path_y,
                    'type'        => 'checkbox',
                    'name'        => $grid->id.'-activator['.$x.']['.$y.']',
                    'value'       => 'on'
                ]);
                $c_activator->attribute_insert('checked', static::value_activator_get($grid, $x, $y));
                $manager->child_insert($c_activator);
                # item scaler-w + scaler-h
                for ($num_w = 1; $num_w <= $grid->col_count; $num_w++) {
                    $c_scaler = new Markup_simple('input', [
                        'data-type'   => 'scaler-w-item',
                        'data-x'      => $x,
                        'data-y'      => $y,
                        'data-path-x' => $c_path_x,
                        'data-path-y' => $c_path_y,
                        'type'        => 'radio',
                        'name'        => $grid->id.'-item_w['.$x.']['.$y.']',
                        'value'       => $num_w]);
                    if ($num_w < $x) $c_scaler->attribute_insert('disabled', true);
                    $c_scaler->attribute_insert('checked', static::value_item_scaler_w_get($grid, $x, $y, $num_w));
                    $manager->child_insert($c_scaler);
                }
                for ($num_h = 1; $num_h <= $grid->row_count; $num_h++) {
                    $c_scaler = new Markup_simple('input', [
                        'data-type'   => 'scaler-h-item',
                        'data-x'      => $x,
                        'data-y'      => $y,
                        'data-path-x' => $c_path_x,
                        'data-path-y' => $c_path_y,
                        'type'        => 'radio',
                        'name'        => $grid->id.'-item_h['.$x.']['.$y.']',
                        'value'       => $num_h]);
                    if ($num_h < $y) $c_scaler->attribute_insert('disabled', true);
                    $c_scaler->attribute_insert('checked', static::value_item_scaler_h_get($grid, $x, $y, $num_h));
                    $manager->child_insert($c_scaler);
                }
                # item
                $manager->child_insert(
                    new Markup('x-grid-item', [
                        'data-grid-item' => true,
                        'data-x'         => $x,
                        'data-y'         => $y,
                        'data-path-x'    => $c_path_x,
                        'data-path-y'    => $c_path_y],
                        'Cell_'.$y.'_'.$x
                    )
                );
                if ($x < $grid->col_count) {
                    $manager->child_insert(new Text_simple('<!-- '.str_repeat('-', 41).' -->'));
                }
            }
        }
        $result->child_insert($manager, 'manager');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function generate_styles($col_count = self::COL_COUNT_MAX,
                                    $row_count = self::ROW_COUNT_MAX) {
        $result = '/* col_count = '.$col_count.', '.
                     'row_count = '.$row_count.' */'.NL;
        $c_weight = $col_count * $row_count * 3;
        for ($y = 1; $y <= $row_count; $y++) {
            for ($x = 1; $x <= $col_count; $x++) {
                $c_path_x = static::generate_attribute_data_path($x);
                $c_path_y = static::generate_attribute_data_path($y);
                $result.= NL.'/* row '.$y.'.'.$x.' */'.NL.NL;
                # z-index
                $result.=
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='item-activator'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {z-index: ".($c_weight--)."}".NL;
                $result.=
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='scaler-w-item'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'],".NL.
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='scaler-h-item'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {z-index: ".($c_weight--)."}".NL;
                $result.=
                    "[data-type='grid-custom'][data-manage-mode='customization'] [data-grid-item][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {z-index: ".($c_weight--)."}".NL.NL;
                # scaler-w
                for ($num_w = 1; $num_w <= $col_count; $num_w++) {
                    if ($num_w > $x - 1) {
                        $result.=
                            "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='scaler-w-item'][".
                                 "data-x='".$x."'][".
                                 "data-y='".$y."'][".
                                 "value='".$num_w."']:checked ~ ".
                            "[data-grid-item][".
                                 "data-x='".$x."'][".
                                 "data-y='".$y."'] {".
                                 "grid-column-end: ".($num_w + 1).
                            "}".NL;
                    }
                }
                # scaler-h
                for ($num_h = 1; $num_h <= $row_count; $num_h++) {
                    if ($num_h > $y - 1) {
                        $result.=
                            "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='scaler-h-item'][".
                                 "data-x='".$x."'][".
                                 "data-y='".$y."'][".
                                 "value='".$num_h."']:checked ~ ".
                            "[data-grid-item][".
                                 "data-x='".$x."'][".
                                 "data-y='".$y."'] {".
                                 "grid-row-end: ".($num_h + 1).
                            "}".NL;
                    }
                }
                # activator
                $result.= NL.
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='item-activator'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."']:not(:checked) ~ ".
                    "input[data-type='scaler-w-item'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {".
                        "display: none"."}".NL;
                $result.=
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='item-activator'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."']:not(:checked) ~ ".
                    "input[data-type='scaler-h-item'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {".
                        "display: none"."}".NL;
                $result.=
                    "[data-type='grid-custom'][data-manage-mode='customization'] input[data-type='item-activator'][".
                        "data-x='".$x."'][".
                        "data-y='".$y."']:not(:checked) ~ ".
                    "[data-grid-item][".
                        "data-x='".$x."'][".
                        "data-y='".$y."'] {".
                        "grid-column-end: ".($x + 1)." !important; ".
                        "grid-row-end: "   .($y + 1)." !important; ".
                        "opacity: .1}".NL;
                if ($x < $col_count) {
                    $result.= NL;
                }
            }
        }
        return $result;
    }

}
