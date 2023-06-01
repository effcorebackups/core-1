<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class actions_list extends markup {

    public $title = 'actions';
    public $tag_name = 'x-actions';
    public $title_tag_name = 'x-actions-title';
    public $title_attributes = ['data-actions-title' => true];
    public $action_title_attributes = ['data-action-title' => true];
    public $template = 'actions_list';
    public $actions = [];

    function __construct($title = null, $attributes = [], $weight = 0) {
        parent::__construct(null, $attributes, [], $weight);
        $this->title = $title;
    }

    function action_insert($action_name, $title) {
        $this->actions[$action_name] = $title;
    }

    function build() {
        if (!$this->is_builded) {
            $list = new markup('x-actions-list');
            $this->child_insert($list, 'actions_list');
            foreach ($this->actions as $c_name => $c_title) {
                $c_href = $c_name[0] === '/' ? $c_name : page::get_current()->args_get('base').'/'.($c_name);
                $list->child_insert(new markup('a', ['data-id' => core::sanitize_id($c_title), 'title' => new text($c_title), 'href' => $c_href],
                    new markup('x-action-title', $this->action_title_attributes, $c_title)
                )); }
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return (template::make_new($this->template, [
            'tag_name'   => $this->tag_name,
            'attributes' => $this->render_attributes(),
            'self'       => $this->render_self(),
            'children'   => $this->render_children($this->children_select(true))
        ]))->render();
    }

    function render_self() {
        return $this->title ? (new markup($this->title_tag_name, $this->title_attributes, [
            new text($this->title)
        ]))->render() : '';
    }

}
