<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Area extends Markup {

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
            if ($this->id && $this->managing_is_enabled) $this->child_insert(new Markup('x-area-info', [], [
                'id'       => new Markup('x-area-id',       [], new Text_simple($this->id)),
                'tag_name' => new Markup('x-area-tag-name', [], new Text_simple($this->tag_name_real)) ]), 'id');
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
