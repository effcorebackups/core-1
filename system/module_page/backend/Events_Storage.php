<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use const effcore\DIR_ROOT;
use effcore\Field_File;
use effcore\File_history;
use effcore\Widget_Files;
use ReflectionClass;

abstract class Events_Storage {

    static function on_instance_delete_before($event, $instance) {
        $entity = $instance->entity_get();
        foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_control_class)) {
                $c_control = (new ReflectionClass($c_field->managing_control_class))->newInstanceWithoutConstructor();
                if (isset($c_field->managing_control_properties) && is_array($c_field->managing_control_properties))
                    foreach ($c_field->managing_control_properties as $c_prop_name => $c_prop_value)
                        $c_control->{$c_prop_name} = $c_prop_value;
                # deleting the file associated with Field_File
                if ($c_control instanceof Field_File) {
                    if (!empty($instance->{$c_name})) {
                        @unlink(DIR_ROOT.$instance->{$c_name});
                    }
                }
                # deleting files associated with Widget_Files
                if ($c_control instanceof Widget_Files) {
                    if (!empty($instance->{$c_name})) {
                        foreach ($instance->{$c_name} as $c_item) {
                            if ($c_item->object instanceof File_history) {
                                @unlink($c_item->object->get_current_path());
                            }
                        }
                    }
                }
            }
        }
    }

}
