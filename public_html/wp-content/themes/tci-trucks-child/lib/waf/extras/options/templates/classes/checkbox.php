<?php
class checkbox extends input {
    function __construct($field,$form) {
        // Unset value if value is "no"
        if( strtolower(@$field['value']) == 'no' ) unset($field['value']);

        // Make sure checkbox has an ID so clicking on label works properly
        if( !@$field['id'] ) $field['id'] = 'checkbox-'.rand(0,999);

        if( @$field['side'] === 'left' ) $field['left'] = true;
        else if( @$field['side'] === 'right' ) $field['right'] = true;
        else $field['left'] = true;
        parent::__construct($field,$form);
    }
}