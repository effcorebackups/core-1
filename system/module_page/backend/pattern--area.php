<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class area extends markup {

    public $tag_name = 'x-area';
    public $tag_name_real;
    public $id;
    public $title;
    public $type; # null | table | row | column
    public $render_weight = 0;
    public $managing_is_enabled = false;

    function build() {
        if (!$this->is_builded) {
            if ($this->type) $this->attribute_insert('data-area-type', $this->type);
            if ($this->id  ) $this->attribute_insert('data-area-id',   $this->id  );
            if ($this->id && $this->managing_is_enabled) $this->child_insert(new markup('x-area-info', [], [
                'id'       => new markup('x-area-id',       [], new text_simple($this->id)),
                'tag_name' => new markup('x-area-tag-name', [], new text_simple($this->tag_name_real)) ]), 'id');
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

    function managing_enable($tag_name = 'div') {
        $this->managing_is_enabled = true;
        $this->tag_name_real = $this->tag_name;
        $this->tag_name      = $tag_name;
    }

}
