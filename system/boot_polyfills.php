<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace {

    if (!function_exists('str_starts_with')) {
        function str_starts_with($haystack, $needle) {
            if (is_null    ($haystack)) trigger_error('str_starts_with(): Passing null to parameter #1 ($haystack) of type string is deprecated in '.__FILE__, E_USER_DEPRECATED);
            if (is_null    ($needle)  ) trigger_error('str_starts_with(): Passing null to parameter #2 ($needle) of type string is deprecated in '.__FILE__, E_USER_DEPRECATED);
            if (is_array   ($haystack)) throw new TypeError('str_starts_with(): Argument #1 ($haystack) must be of type string, array given');
            if (is_object  ($haystack)) throw new TypeError('str_starts_with(): Argument #1 ($haystack) must be of type string, object given');
            if (is_resource($haystack)) throw new TypeError('str_starts_with(): Argument #1 ($haystack) must be of type string, resource given');
            if (is_array   ($needle)  ) throw new TypeError('str_starts_with(): Argument #2 ($needle) must be of type string, array given');
            if (is_object  ($needle)  ) throw new TypeError('str_starts_with(): Argument #2 ($needle) must be of type string, object given');
            if (is_resource($needle)  ) throw new TypeError('str_starts_with(): Argument #2 ($needle) must be of type string, resource given');
            if ((string)$needle === '') return true;
            return strpos((string)$haystack, (string)$needle) === 0;
        }
    }

    if (!function_exists('str_contains')) {
        function str_contains($haystack, $needle) {
            if (is_null    ($haystack)) trigger_error('str_contains(): Passing null to parameter #1 ($haystack) of type string is deprecated in '.__FILE__, E_USER_DEPRECATED);
            if (is_null    ($needle)  ) trigger_error('str_contains(): Passing null to parameter #2 ($needle) of type string is deprecated in '.__FILE__, E_USER_DEPRECATED);
            if (is_array   ($haystack)) throw new TypeError('str_contains(): Argument #1 ($haystack) must be of type string, array given');
            if (is_object  ($haystack)) throw new TypeError('str_contains(): Argument #1 ($haystack) must be of type string, object given');
            if (is_resource($haystack)) throw new TypeError('str_contains(): Argument #1 ($haystack) must be of type string, resource given');
            if (is_array   ($needle)  ) throw new TypeError('str_contains(): Argument #2 ($needle) must be of type string, array given');
            if (is_object  ($needle)  ) throw new TypeError('str_contains(): Argument #2 ($needle) must be of type string, object given');
            if (is_resource($needle)  ) throw new TypeError('str_contains(): Argument #2 ($needle) must be of type string, resource given');
            if ((string)$needle === '') return true;
            return strpos((string)$haystack, (string)$needle) !== false;
        }
    }

}
