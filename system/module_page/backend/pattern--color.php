<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  const return_hex  = 0b00;
  const return_rgb  = 0b01;
  const return_rgba = 0b10;

  const named_colors = [
    'aliceblue'            => ['value_hex' => '#f0f8ff', 'r' => 240, 'g' => 248, 'b' => 255],
    'antiquewhite'         => ['value_hex' => '#faebd7', 'r' => 250, 'g' => 235, 'b' => 215],
    'aqua'                 => ['value_hex' => '#00ffff', 'r' =>   0, 'g' => 255, 'b' => 255],
    'aquamarine'           => ['value_hex' => '#7fffd4', 'r' => 127, 'g' => 255, 'b' => 212],
    'azure'                => ['value_hex' => '#f0ffff', 'r' => 240, 'g' => 255, 'b' => 255],
    'beige'                => ['value_hex' => '#f5f5dc', 'r' => 245, 'g' => 245, 'b' => 220],
    'bisque'               => ['value_hex' => '#ffe4c4', 'r' => 255, 'g' => 228, 'b' => 196],
    'black'                => ['value_hex' => '#000000', 'r' =>   0, 'g' =>   0, 'b' =>   0],
    'blanchedalmond'       => ['value_hex' => '#ffebcd', 'r' => 255, 'g' => 235, 'b' => 205],
    'blue'                 => ['value_hex' => '#0000ff', 'r' =>   0, 'g' =>   0, 'b' => 255],
    'blueviolet'           => ['value_hex' => '#8a2be2', 'r' => 138, 'g' =>  43, 'b' => 226],
    'brown'                => ['value_hex' => '#a52a2a', 'r' => 165, 'g' =>  42, 'b' =>  42],
    'burlywood'            => ['value_hex' => '#deb887', 'r' => 222, 'g' => 184, 'b' => 135],
    'cadetblue'            => ['value_hex' => '#5f9ea0', 'r' =>  95, 'g' => 158, 'b' => 160],
    'chartreuse'           => ['value_hex' => '#7fff00', 'r' => 127, 'g' => 255, 'b' =>   0],
    'chocolate'            => ['value_hex' => '#d2691e', 'r' => 210, 'g' => 105, 'b' =>  30],
    'coral'                => ['value_hex' => '#ff7f50', 'r' => 255, 'g' => 127, 'b' =>  80],
    'cornflowerblue'       => ['value_hex' => '#6495ed', 'r' => 100, 'g' => 149, 'b' => 237],
    'cornsilk'             => ['value_hex' => '#fff8dc', 'r' => 255, 'g' => 248, 'b' => 220],
    'crimson'              => ['value_hex' => '#dc143c', 'r' => 220, 'g' =>  20, 'b' =>  60],
    'cyan'                 => ['value_hex' => '#00ffff', 'r' =>   0, 'g' => 255, 'b' => 255],
    'darkblue'             => ['value_hex' => '#00008b', 'r' =>   0, 'g' =>   0, 'b' => 139],
    'darkcyan'             => ['value_hex' => '#008b8b', 'r' =>   0, 'g' => 139, 'b' => 139],
    'darkgoldenrod'        => ['value_hex' => '#b8860b', 'r' => 184, 'g' => 134, 'b' =>  11],
    'darkgray'             => ['value_hex' => '#a9a9a9', 'r' => 169, 'g' => 169, 'b' => 169],
    'darkgreen'            => ['value_hex' => '#006400', 'r' =>   0, 'g' => 100, 'b' =>   0],
    'darkkhaki'            => ['value_hex' => '#bdb76b', 'r' => 189, 'g' => 183, 'b' => 107],
    'darkmagenta'          => ['value_hex' => '#8b008b', 'r' => 139, 'g' =>   0, 'b' => 139],
    'darkolivegreen'       => ['value_hex' => '#556b2f', 'r' =>  85, 'g' => 107, 'b' =>  47],
    'darkorange'           => ['value_hex' => '#ff8c00', 'r' => 255, 'g' => 140, 'b' =>   0],
    'darkorchid'           => ['value_hex' => '#9932cc', 'r' => 153, 'g' =>  50, 'b' => 204],
    'darkred'              => ['value_hex' => '#8b0000', 'r' => 139, 'g' =>   0, 'b' =>   0],
    'darksalmon'           => ['value_hex' => '#e9967a', 'r' => 233, 'g' => 150, 'b' => 122],
    'darkseagreen'         => ['value_hex' => '#8fbc8f', 'r' => 143, 'g' => 188, 'b' => 143],
    'darkslateblue'        => ['value_hex' => '#483d8b', 'r' =>  72, 'g' =>  61, 'b' => 139],
    'darkslategray'        => ['value_hex' => '#2f4f4f', 'r' =>  47, 'g' =>  79, 'b' =>  79],
    'darkturquoise'        => ['value_hex' => '#00ced1', 'r' =>   0, 'g' => 206, 'b' => 209],
    'darkviolet'           => ['value_hex' => '#9400d3', 'r' => 148, 'g' =>   0, 'b' => 211],
    'deeppink'             => ['value_hex' => '#ff1493', 'r' => 255, 'g' =>  20, 'b' => 147],
    'deepskyblue'          => ['value_hex' => '#00bfff', 'r' =>   0, 'g' => 191, 'b' => 255],
    'dimgray'              => ['value_hex' => '#696969', 'r' => 105, 'g' => 105, 'b' => 105],
    'dodgerblue'           => ['value_hex' => '#1e90ff', 'r' =>  30, 'g' => 144, 'b' => 255],
    'firebrick'            => ['value_hex' => '#b22222', 'r' => 178, 'g' =>  34, 'b' =>  34],
    'floralwhite'          => ['value_hex' => '#fffaf0', 'r' => 255, 'g' => 250, 'b' => 240],
    'forestgreen'          => ['value_hex' => '#228b22', 'r' =>  34, 'g' => 139, 'b' =>  34],
    'fuchsia'              => ['value_hex' => '#ff00ff', 'r' => 255, 'g' =>   0, 'b' => 255],
    'gainsboro'            => ['value_hex' => '#dcdcdc', 'r' => 220, 'g' => 220, 'b' => 220],
    'ghostwhite'           => ['value_hex' => '#f8f8ff', 'r' => 248, 'g' => 248, 'b' => 255],
    'gold'                 => ['value_hex' => '#ffd700', 'r' => 255, 'g' => 215, 'b' =>   0],
    'goldenrod'            => ['value_hex' => '#daa520', 'r' => 218, 'g' => 165, 'b' =>  32],
    'gray'                 => ['value_hex' => '#808080', 'r' => 128, 'g' => 128, 'b' => 128],
    'green'                => ['value_hex' => '#008000', 'r' =>   0, 'g' => 128, 'b' =>   0],
    'greenyellow'          => ['value_hex' => '#adff2f', 'r' => 173, 'g' => 255, 'b' =>  47],
    'honeydew'             => ['value_hex' => '#f0fff0', 'r' => 240, 'g' => 255, 'b' => 240],
    'hotpink'              => ['value_hex' => '#ff69b4', 'r' => 255, 'g' => 105, 'b' => 180],
    'indianred'            => ['value_hex' => '#cd5c5c', 'r' => 205, 'g' =>  92, 'b' =>  92],
    'indigo'               => ['value_hex' => '#4b0082', 'r' =>  75, 'g' =>   0, 'b' => 130],
    'ivory'                => ['value_hex' => '#fffff0', 'r' => 255, 'g' => 255, 'b' => 240],
    'khaki'                => ['value_hex' => '#f0e68c', 'r' => 240, 'g' => 230, 'b' => 140],
    'lavender'             => ['value_hex' => '#e6e6fa', 'r' => 230, 'g' => 230, 'b' => 250],
    'lavenderblush'        => ['value_hex' => '#fff0f5', 'r' => 255, 'g' => 240, 'b' => 245],
    'lawngreen'            => ['value_hex' => '#7cfc00', 'r' => 124, 'g' => 252, 'b' =>   0],
    'lemonchiffon'         => ['value_hex' => '#fffacd', 'r' => 255, 'g' => 250, 'b' => 205],
    'lightblue'            => ['value_hex' => '#add8e6', 'r' => 173, 'g' => 216, 'b' => 230],
    'lightcoral'           => ['value_hex' => '#f08080', 'r' => 240, 'g' => 128, 'b' => 128],
    'lightcyan'            => ['value_hex' => '#e0ffff', 'r' => 224, 'g' => 255, 'b' => 255],
    'lightgoldenrodyellow' => ['value_hex' => '#fafad2', 'r' => 250, 'g' => 250, 'b' => 210],
    'lightgray'            => ['value_hex' => '#d3d3d3', 'r' => 211, 'g' => 211, 'b' => 211],
    'lightgreen'           => ['value_hex' => '#90ee90', 'r' => 144, 'g' => 238, 'b' => 144],
    'lightpink'            => ['value_hex' => '#ffb6c1', 'r' => 255, 'g' => 182, 'b' => 193],
    'lightsalmon'          => ['value_hex' => '#ffa07a', 'r' => 255, 'g' => 160, 'b' => 122],
    'lightseagreen'        => ['value_hex' => '#20b2aa', 'r' =>  32, 'g' => 178, 'b' => 170],
    'lightskyblue'         => ['value_hex' => '#87cefa', 'r' => 135, 'g' => 206, 'b' => 250],
    'lightslategray'       => ['value_hex' => '#778899', 'r' => 119, 'g' => 136, 'b' => 153],
    'lightsteelblue'       => ['value_hex' => '#b0c4de', 'r' => 176, 'g' => 196, 'b' => 222],
    'lightyellow'          => ['value_hex' => '#ffffe0', 'r' => 255, 'g' => 255, 'b' => 224],
    'lime'                 => ['value_hex' => '#00ff00', 'r' =>   0, 'g' => 255, 'b' =>   0],
    'limegreen'            => ['value_hex' => '#32cd32', 'r' =>  50, 'g' => 205, 'b' =>  50],
    'linen'                => ['value_hex' => '#faf0e6', 'r' => 250, 'g' => 240, 'b' => 230],
    'magenta'              => ['value_hex' => '#ff00ff', 'r' => 255, 'g' =>   0, 'b' => 255],
    'maroon'               => ['value_hex' => '#800000', 'r' => 128, 'g' =>   0, 'b' =>   0],
    'mediumaquamarine'     => ['value_hex' => '#66cdaa', 'r' => 102, 'g' => 205, 'b' => 170],
    'mediumblue'           => ['value_hex' => '#0000cd', 'r' =>   0, 'g' =>   0, 'b' => 205],
    'mediumorchid'         => ['value_hex' => '#ba55d3', 'r' => 186, 'g' =>  85, 'b' => 211],
    'mediumpurple'         => ['value_hex' => '#9370db', 'r' => 147, 'g' => 112, 'b' => 219],
    'mediumseagreen'       => ['value_hex' => '#3cb371', 'r' =>  60, 'g' => 179, 'b' => 113],
    'mediumslateblue'      => ['value_hex' => '#7b68ee', 'r' => 123, 'g' => 104, 'b' => 238],
    'mediumspringgreen'    => ['value_hex' => '#00fa9a', 'r' =>   0, 'g' => 250, 'b' => 154],
    'mediumturquoise'      => ['value_hex' => '#48d1cc', 'r' =>  72, 'g' => 209, 'b' => 204],
    'mediumvioletred'      => ['value_hex' => '#c71585', 'r' => 199, 'g' =>  21, 'b' => 133],
    'midnightblue'         => ['value_hex' => '#191970', 'r' =>  25, 'g' =>  25, 'b' => 112],
    'mintcream'            => ['value_hex' => '#f5fffa', 'r' => 245, 'g' => 255, 'b' => 250],
    'mistyrose'            => ['value_hex' => '#ffe4e1', 'r' => 255, 'g' => 228, 'b' => 225], 
    'moccasin'             => ['value_hex' => '#ffe4b5', 'r' => 255, 'g' => 228, 'b' => 181],
    'navajowhite'          => ['value_hex' => '#ffdead', 'r' => 255, 'g' => 222, 'b' => 173],
    'navy'                 => ['value_hex' => '#000080', 'r' =>   0, 'g' =>   0, 'b' => 128],
    'oldlace'              => ['value_hex' => '#fdf5e6', 'r' => 253, 'g' => 245, 'b' => 230],
    'olive'                => ['value_hex' => '#808000', 'r' => 128, 'g' => 128, 'b' =>   0],
    'olivedrab'            => ['value_hex' => '#6b8e23', 'r' => 107, 'g' => 142, 'b' =>  35],
    'orange'               => ['value_hex' => '#ffa500', 'r' => 255, 'g' => 165, 'b' =>   0],
    'orangered'            => ['value_hex' => '#ff4500', 'r' => 255, 'g' =>  69, 'b' =>   0],
    'orchid'               => ['value_hex' => '#da70d6', 'r' => 218, 'g' => 112, 'b' => 214],
    'palegoldenrod'        => ['value_hex' => '#eee8aa', 'r' => 238, 'g' => 232, 'b' => 170],
    'palegreen'            => ['value_hex' => '#98fb98', 'r' => 152, 'g' => 251, 'b' => 152],
    'paleturquoise'        => ['value_hex' => '#afeeee', 'r' => 175, 'g' => 238, 'b' => 238],
    'palevioletred'        => ['value_hex' => '#db7093', 'r' => 219, 'g' => 112, 'b' => 147],
    'papayawhip'           => ['value_hex' => '#ffefd5', 'r' => 255, 'g' => 239, 'b' => 213],
    'peachpuff'            => ['value_hex' => '#ffdab9', 'r' => 255, 'g' => 218, 'b' => 185],
    'peru'                 => ['value_hex' => '#cd853f', 'r' => 205, 'g' => 133, 'b' =>  63],
    'pink'                 => ['value_hex' => '#ffc0cb', 'r' => 255, 'g' => 192, 'b' => 203],
    'plum'                 => ['value_hex' => '#dda0dd', 'r' => 221, 'g' => 160, 'b' => 221],
    'powderblue'           => ['value_hex' => '#b0e0e6', 'r' => 176, 'g' => 224, 'b' => 230],
    'purple'               => ['value_hex' => '#800080', 'r' => 128, 'g' =>   0, 'b' => 128],
    'rebeccapurple'        => ['value_hex' => '#663399', 'r' => 102, 'g' =>  51, 'b' => 153],
    'red'                  => ['value_hex' => '#ff0000', 'r' => 255, 'g' =>   0, 'b' =>   0],
    'rosybrown'            => ['value_hex' => '#bc8f8f', 'r' => 188, 'g' => 143, 'b' => 143],
    'royalblue'            => ['value_hex' => '#4169e1', 'r' =>  65, 'g' => 105, 'b' => 225],
    'saddlebrown'          => ['value_hex' => '#8b4513', 'r' => 139, 'g' =>  69, 'b' =>  19],
    'salmon'               => ['value_hex' => '#fa8072', 'r' => 250, 'g' => 128, 'b' => 114],
    'sandybrown'           => ['value_hex' => '#f4a460', 'r' => 244, 'g' => 164, 'b' =>  96],
    'seagreen'             => ['value_hex' => '#2e8b57', 'r' =>  46, 'g' => 139, 'b' =>  87],
    'seashell'             => ['value_hex' => '#fff5ee', 'r' => 255, 'g' => 245, 'b' => 238],
    'sienna'               => ['value_hex' => '#a0522d', 'r' => 160, 'g' =>  82, 'b' =>  45],
    'silver'               => ['value_hex' => '#c0c0c0', 'r' => 192, 'g' => 192, 'b' => 192],
    'skyblue'              => ['value_hex' => '#87ceeb', 'r' => 135, 'g' => 206, 'b' => 235],
    'slateblue'            => ['value_hex' => '#6a5acd', 'r' => 106, 'g' =>  90, 'b' => 205],
    'slategray'            => ['value_hex' => '#708090', 'r' => 112, 'g' => 128, 'b' => 144],
    'snow'                 => ['value_hex' => '#fffafa', 'r' => 255, 'g' => 250, 'b' => 250],
    'springgreen'          => ['value_hex' => '#00ff7f', 'r' =>   0, 'g' => 255, 'b' => 127],
    'steelblue'            => ['value_hex' => '#4682b4', 'r' =>  70, 'g' => 130, 'b' => 180],
    'tan'                  => ['value_hex' => '#d2b48c', 'r' => 210, 'g' => 180, 'b' => 140],
    'teal'                 => ['value_hex' => '#008080', 'r' =>   0, 'g' => 128, 'b' => 128],
    'thistle'              => ['value_hex' => '#d8bfd8', 'r' => 216, 'g' => 191, 'b' => 216],
    'tomato'               => ['value_hex' => '#ff6347', 'r' => 255, 'g' =>  99, 'b' =>  71],
    'turquoise'            => ['value_hex' => '#40e0d0', 'r' =>  64, 'g' => 224, 'b' => 208],
    'violet'               => ['value_hex' => '#ee82ee', 'r' => 238, 'g' => 130, 'b' => 238],
    'wheat'                => ['value_hex' => '#f5deb3', 'r' => 245, 'g' => 222, 'b' => 179],
    'white'                => ['value_hex' => '#ffffff', 'r' => 255, 'g' => 255, 'b' => 255],
    'whitesmoke'           => ['value_hex' => '#f5f5f5', 'r' => 245, 'g' => 245, 'b' => 245],
    'yellow'               => ['value_hex' => '#ffff00', 'r' => 255, 'g' => 255, 'b' =>   0],
    'yellowgreen'          => ['value_hex' => '#9acd32', 'r' => 154, 'g' => 205, 'b' =>  50],
  ];

  public $id;
  public $value;
  public $value_hex;
  public $group;

  function __construct($id = null, $value = null, $value_hex = null, $group = null) {
    if ($id       ) $this->id        = $id;
    if ($value    ) $this->value     = $value;
    if ($value_hex) $this->value_hex = $value_hex;
    if ($group    ) $this->group     = $group;
  }

  function rgb_get($is_int = true) {
    if (!empty($this->value_hex)) {
      $value = ltrim($this->value_hex, '#');
      $parts = [];
      if (strlen($value) === 3) {
        $parts['r'] = $value[0].$value[0];
        $parts['g'] = $value[1].$value[1];
        $parts['b'] = $value[2].$value[2]; }
      if (strlen($value) === 6) {
        $parts['r'] = $value[0].$value[1];
        $parts['g'] = $value[2].$value[3];
        $parts['b'] = $value[4].$value[5]; }
      if (count($parts) && $is_int === true) {
        $parts['r'] = (int)hexdec($parts['r']);
        $parts['g'] = (int)hexdec($parts['g']);
        $parts['b'] = (int)hexdec($parts['b']); }
      return $parts ?: null;
    }
  }

  function filter_shift($r_offset, $g_offset, $b_offset, $opacity = 1, $return_mode = self::return_rgb) {
    $rgb = $this->rgb_get();
    if ($rgb) {
      $new_r = max(min($rgb['r'] + (int)$r_offset, 255), 0);
      $new_g = max(min($rgb['g'] + (int)$g_offset, 255), 0);
      $new_b = max(min($rgb['b'] + (int)$b_offset, 255), 0);
      switch ($return_mode) {
        case static::return_rgb:  return  'rgb('.$new_r.','.$new_g.','.$new_b.             ')';
        case static::return_rgba: return 'rgba('.$new_r.','.$new_g.','.$new_b.','.$opacity.')';
        case static::return_hex:  return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
                                             str_pad(dechex($new_g), 2, '0', STR_PAD_LEFT).
                                             str_pad(dechex($new_b), 2, '0', STR_PAD_LEFT);
      }
    }
  }

  function is_dark() { # return: true | false | null
    $rgb = $this->rgb_get();
    if ($rgb) {
      return $rgb['r'] +
             $rgb['g'] +
             $rgb['b'] <= 127 * 3;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('files')->select('colors') as $c_module_id => $c_colors) {
        foreach ($c_colors as $c_row_id => $c_color) {
          if (isset(static::$cache[$c_color->id])) console::report_about_duplicate('color', $c_color->id, $c_module_id);
                    static::$cache[$c_color->id] = $c_color;
                    static::$cache[$c_color->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function get_color_name_by_value_hex($value_hex) {
    foreach (static::named_colors as $c_value => $c_info) {
      if ($c_info['value_hex'] === $value_hex) {
        return $c_value;
      }
    }
  }

}}