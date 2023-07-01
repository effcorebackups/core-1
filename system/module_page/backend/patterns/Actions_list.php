<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Actions_list extends Markup {

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
            $list = new Markup('x-actions-list');
            $this->child_insert($list, 'actions_list');
            foreach ($this->actions as $c_name => $c_title) {
                $c_href = $c_name[0] === '/' ? $c_name : Page::get_current()->args_get('base').'/'.($c_name);
                $list->child_insert(new Markup('a', ['data-id' => Core::sanitize_id($c_title), 'title' => new Text($c_title), 'href' => $c_href],
                    new Markup('x-action-title', $this->action_title_attributes, $c_title)
                )); }
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return (Template::make_new($this->template, [
            'tag_name'   => $this->tag_name,
            'attributes' => $this->render_attributes(),
            'self'       => $this->render_self(),
            'children'   => $this->render_children($this->children_select(true))
        ]))->render();
    }

    function render_self() {
        return $this->title ? (new Markup($this->title_tag_name, $this->title_attributes, [
            new Text($this->title)
        ]))->render() : '';
    }

}
