<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_item_settings extends container {

  public $tag_name = null;
  public $content_tag_name = 'x-settings';
  public $template = 'container_content';
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $parent_widget = null;
  public $parent_row_id = null;

  function render_self() {
    return $this->render_opener();
  }

  function render_opener() {
    $form_id      = request::value_get('form_id');
    $submit_value = request::value_get($this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id);
    $has_error    = $this->has_error_in();
    if ($form_id === ''                                                 ) /*               default = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'id' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'checked' => true                          ]))->render();
    if ($form_id !== '' && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'id' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'checked' => null                          ]))->render();
    if ($form_id !== '' && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'id' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'checked' => true                          ]))->render();
    if ($form_id !== '' && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'id' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'checked' => null, 'aria-invalid' => 'true']))->render();
    if ($form_id !== '' && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'id' => $this->parent_widget->name_get_complex().'__settings_opener__'.$this->parent_row_id, 'checked' => null, 'aria-invalid' => 'true']))->render();
  }

}}