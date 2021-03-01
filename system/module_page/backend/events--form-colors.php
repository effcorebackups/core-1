<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_colors {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['*color__page_id'                  ]->value_set($settings->color__page_id                  );
    $items['*color__text_id'                  ]->value_set($settings->color__text_id                  );
    $items['*color__main_id'                  ]->value_set($settings->color__main_id                  );
    $items['*color__link_id'                  ]->value_set($settings->color__link_id                  );
    $items['*color__link_active_id'           ]->value_set($settings->color__link_active_id           );
    $items['*color__table_row_odd_id'         ]->value_set($settings->color__table_row_odd_id         );
    $items['*color__table_row_even_id'        ]->value_set($settings->color__table_row_even_id        );
    $items['*color__relation_id'              ]->value_set($settings->color__relation_id              );
    $items['*color__menu_id'                  ]->value_set($settings->color__menu_id                  );
    $items['*color__menu_active_id'           ]->value_set($settings->color__menu_active_id           );
    $items['*color__menu_text_id'             ]->value_set($settings->color__menu_text_id             );
    $items['*color__menu_link_id'             ]->value_set($settings->color__menu_link_id             );
    $items['*color__menu_link_active_id'      ]->value_set($settings->color__menu_link_active_id      );
    $items['*color__tabs_id'                  ]->value_set($settings->color__tabs_id                  );
    $items['*color__tabs_link_id'             ]->value_set($settings->color__tabs_link_id             );
    $items['*color__tabs_link_active_id'      ]->value_set($settings->color__tabs_link_active_id      );
    $items['*color__tabs_link_active_no_bg_id']->value_set($settings->color__tabs_link_active_no_bg_id);
    $items['*color__ok_id'                    ]->value_set($settings->color__ok_id                    );
    $items['*color__warning_id'               ]->value_set($settings->color__warning_id               );
    $items['*color__error_id'                 ]->value_set($settings->color__error_id                 );
    $items['*color__fieldset_id'              ]->value_set($settings->color__fieldset_id              );
    $items['*color__fieldset_nested_id'       ]->value_set($settings->color__fieldset_nested_id       );
    $items['*color__field_id'                 ]->value_set($settings->color__field_id                 );
    $items['*color__field_text_id'            ]->value_set($settings->color__field_text_id            );
    $items['*color__button_id'                ]->value_set($settings->color__button_id                );
    $items['*color__button_active_id'         ]->value_set($settings->color__button_active_id         );
    $items['*color__button_text_id'           ]->value_set($settings->color__button_text_id           );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $selected = [];
        $selected['color__page_id'                  ] = $items['*color__page_id'                  ]->value_get();
        $selected['color__text_id'                  ] = $items['*color__text_id'                  ]->value_get();
        $selected['color__main_id'                  ] = $items['*color__main_id'                  ]->value_get();
        $selected['color__link_id'                  ] = $items['*color__link_id'                  ]->value_get();
        $selected['color__link_active_id'           ] = $items['*color__link_active_id'           ]->value_get();
        $selected['color__table_row_odd_id'         ] = $items['*color__table_row_odd_id'         ]->value_get();
        $selected['color__table_row_even_id'        ] = $items['*color__table_row_even_id'        ]->value_get();
        $selected['color__relation_id'              ] = $items['*color__relation_id'              ]->value_get();
        $selected['color__menu_id'                  ] = $items['*color__menu_id'                  ]->value_get();
        $selected['color__menu_active_id'           ] = $items['*color__menu_active_id'           ]->value_get();
        $selected['color__menu_text_id'             ] = $items['*color__menu_text_id'             ]->value_get();
        $selected['color__menu_link_id'             ] = $items['*color__menu_link_id'             ]->value_get();
        $selected['color__menu_link_active_id'      ] = $items['*color__menu_link_active_id'      ]->value_get();
        $selected['color__tabs_id'                  ] = $items['*color__tabs_id'                  ]->value_get();
        $selected['color__tabs_link_id'             ] = $items['*color__tabs_link_id'             ]->value_get();
        $selected['color__tabs_link_active_id'      ] = $items['*color__tabs_link_active_id'      ]->value_get();
        $selected['color__tabs_link_active_no_bg_id'] = $items['*color__tabs_link_active_no_bg_id']->value_get();
        $selected['color__ok_id'                    ] = $items['*color__ok_id'                    ]->value_get();
        $selected['color__warning_id'               ] = $items['*color__warning_id'               ]->value_get();
        $selected['color__error_id'                 ] = $items['*color__error_id'                 ]->value_get();
        $selected['color__fieldset_id'              ] = $items['*color__fieldset_id'              ]->value_get();
        $selected['color__fieldset_nested_id'       ] = $items['*color__fieldset_nested_id'       ]->value_get();
        $selected['color__field_id'                 ] = $items['*color__field_id'                 ]->value_get();
        $selected['color__field_text_id'            ] = $items['*color__field_text_id'            ]->value_get();
        $selected['color__button_id'                ] = $items['*color__button_id'                ]->value_get();
        $selected['color__button_active_id'         ] = $items['*color__button_active_id'         ]->value_get();
        $selected['color__button_text_id'           ] = $items['*color__button_text_id'           ]->value_get();
        $result = color_preset::apply_with_custom_ids($selected, true);
        if ($result) message::insert('Changes was saved.'             );
        else         message::insert('Changes was not saved!', 'error');
        break;
      case 'reset':
        $result = color_preset::reset();
        if ($result) message::insert('Changes was deleted.'             );
        else         message::insert('Changes was not deleted!', 'error');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}