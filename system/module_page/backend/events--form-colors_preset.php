<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_colors_preset {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    $preset = color_preset::get($id);
    if ($preset) {
      $items['#color__page_id'                  ]->color_set($preset->colors->color__page_id                  );
      $items['#color__text_id'                  ]->color_set($preset->colors->color__text_id                  );
      $items['#color__main_id'                  ]->color_set($preset->colors->color__main_id                  );
      $items['#color__link_id'                  ]->color_set($preset->colors->color__link_id                  );
      $items['#color__link_active_id'           ]->color_set($preset->colors->color__link_active_id           );
      $items['#color__table_row_odd_id'         ]->color_set($preset->colors->color__table_row_odd_id         );
      $items['#color__table_row_even_id'        ]->color_set($preset->colors->color__table_row_even_id        );
      $items['#color__relation_id'              ]->color_set($preset->colors->color__relation_id              );
      $items['#color__menu_id'                  ]->color_set($preset->colors->color__menu_id                  );
      $items['#color__menu_active_id'           ]->color_set($preset->colors->color__menu_active_id           );
      $items['#color__menu_text_id'             ]->color_set($preset->colors->color__menu_text_id             );
      $items['#color__menu_link_id'             ]->color_set($preset->colors->color__menu_link_id             );
      $items['#color__menu_link_active_id'      ]->color_set($preset->colors->color__menu_link_active_id      );
      $items['#color__tabs_id'                  ]->color_set($preset->colors->color__tabs_id                  );
      $items['#color__tabs_link_id'             ]->color_set($preset->colors->color__tabs_link_id             );
      $items['#color__tabs_link_active_id'      ]->color_set($preset->colors->color__tabs_link_active_id      );
      $items['#color__tabs_link_active_no_bg_id']->color_set($preset->colors->color__tabs_link_active_no_bg_id);
      $items['#color__ok_id'                    ]->color_set($preset->colors->color__ok_id                    );
      $items['#color__warning_id'               ]->color_set($preset->colors->color__warning_id               );
      $items['#color__error_id'                 ]->color_set($preset->colors->color__error_id                 );
      $items['#color__fieldset_id'              ]->color_set($preset->colors->color__fieldset_id              );
      $items['#color__fieldset_nested_id'       ]->color_set($preset->colors->color__fieldset_nested_id       );
      $items['#color__field_id'                 ]->color_set($preset->colors->color__field_id                 );
      $items['#color__field_text_id'            ]->color_set($preset->colors->color__field_text_id            );
      $items['#color__button_id'                ]->color_set($preset->colors->color__button_id                );
      $items['#color__button_active_id'         ]->color_set($preset->colors->color__button_active_id         );
      $items['#color__button_text_id'           ]->color_set($preset->colors->color__button_text_id           );
    } else $items['~apply']->disabled_set(true);
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $id = page::get_current()->args_get('id');
        $preset = color_preset::get($id);
        if ($preset) {
          $changes = [];
          if ($items['#color__page_id'                  ]->checked_get()) $changes['color__page_id'                  ] = true;
          if ($items['#color__text_id'                  ]->checked_get()) $changes['color__text_id'                  ] = true;
          if ($items['#color__main_id'                  ]->checked_get()) $changes['color__main_id'                  ] = true;
          if ($items['#color__link_id'                  ]->checked_get()) $changes['color__link_id'                  ] = true;
          if ($items['#color__link_active_id'           ]->checked_get()) $changes['color__link_active_id'           ] = true;
          if ($items['#color__table_row_odd_id'         ]->checked_get()) $changes['color__table_row_odd_id'         ] = true;
          if ($items['#color__table_row_even_id'        ]->checked_get()) $changes['color__table_row_even_id'        ] = true;
          if ($items['#color__relation_id'              ]->checked_get()) $changes['color__relation_id'              ] = true;
          if ($items['#color__menu_id'                  ]->checked_get()) $changes['color__menu_id'                  ] = true;
          if ($items['#color__menu_active_id'           ]->checked_get()) $changes['color__menu_active_id'           ] = true;
          if ($items['#color__menu_text_id'             ]->checked_get()) $changes['color__menu_text_id'             ] = true;
          if ($items['#color__menu_link_id'             ]->checked_get()) $changes['color__menu_link_id'             ] = true;
          if ($items['#color__menu_link_active_id'      ]->checked_get()) $changes['color__menu_link_active_id'      ] = true;
          if ($items['#color__tabs_id'                  ]->checked_get()) $changes['color__tabs_id'                  ] = true;
          if ($items['#color__tabs_link_id'             ]->checked_get()) $changes['color__tabs_link_id'             ] = true;
          if ($items['#color__tabs_link_active_id'      ]->checked_get()) $changes['color__tabs_link_active_id'      ] = true;
          if ($items['#color__tabs_link_active_no_bg_id']->checked_get()) $changes['color__tabs_link_active_no_bg_id'] = true;
          if ($items['#color__ok_id'                    ]->checked_get()) $changes['color__ok_id'                    ] = true;
          if ($items['#color__warning_id'               ]->checked_get()) $changes['color__warning_id'               ] = true;
          if ($items['#color__error_id'                 ]->checked_get()) $changes['color__error_id'                 ] = true;
          if ($items['#color__fieldset_id'              ]->checked_get()) $changes['color__fieldset_id'              ] = true;
          if ($items['#color__fieldset_nested_id'       ]->checked_get()) $changes['color__fieldset_nested_id'       ] = true;
          if ($items['#color__field_id'                 ]->checked_get()) $changes['color__field_id'                 ] = true;
          if ($items['#color__field_text_id'            ]->checked_get()) $changes['color__field_text_id'            ] = true;
          if ($items['#color__button_id'                ]->checked_get()) $changes['color__button_id'                ] = true;
          if ($items['#color__button_active_id'         ]->checked_get()) $changes['color__button_active_id'         ] = true;
          if ($items['#color__button_text_id'           ]->checked_get()) $changes['color__button_text_id'           ] = true;
          if (!count($changes)) {
            message::insert('No one item was selected!', 'warning');
          } else {
            $result = color_preset::apply($id, $changes, true);
            if ($result) message::insert('Colors was applied.'             );
            else         message::insert('Colors was not applied!', 'error');
            static::on_init(null, $form, $items);
          }
        }
        break;
    }
  }

}}