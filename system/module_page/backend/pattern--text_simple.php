<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class text_simple {

    public $text = '';
    public $weight = 0;
    public $delimiter = NL;

    function __construct($text = '', $weight = 0) {
        if ($text !== '') $this->text_update($text);
        $this->weight = $weight;
    }

    function text_lines_count()                {$lines = explode(strlen($this->delimiter) ? $this->delimiter : NL, $this->text); return count($lines);}
    function text_line_select($key)            {$lines = explode(strlen($this->delimiter) ? $this->delimiter : NL, $this->text); if (array_key_exists($key, $lines)) return $lines[$key];             else return null;}
    function text_line_append($key, $new_text) {$lines = explode(strlen($this->delimiter) ? $this->delimiter : NL, $this->text); if (array_key_exists($key, $lines))        $lines[$key].= $new_text; else $lines[$key] = $new_text; $this->text = implode($this->delimiter, $lines);}
    function text_line_update($key, $new_text) {$lines = explode(strlen($this->delimiter) ? $this->delimiter : NL, $this->text);       $lines[$key] = $new_text;                                                                     $this->text = implode($this->delimiter, $lines);}
    function text_line_delete($key)            {$lines = explode(strlen($this->delimiter) ? $this->delimiter : NL, $this->text); unset($lines[$key]);                                                                                $this->text = implode($this->delimiter, $lines);}

    function text_select()          {return $this->text;}
    function text_update($new_text) {$this->text = $new_text;}
    function text_append($new_text) {$this->text.= $new_text;}
    function text_delete()          {$this->text = '';}

    function text_length($is_in_bytes = false) {
        if ($is_in_bytes === true) return    strlen($this->render());
        if ($is_in_bytes !== true) return mb_strlen($this->render());
    }

    function render() {
        return $this->text;
    }

}
