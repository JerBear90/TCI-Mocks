<?php
function waf_nonassociative_options( $datum ) {
    if( @$datum['options'] ) {
        if( is_array($datum['options'] ) ) if( !isAssoc($datum['options'] ) ) {
            $options = [];
            foreach( $datum['options'] as $o ) {
                $options[$o] = $o;
            }
            $datum['options'] = $options;
        }
    }
    return $datum;
}
add_filter( 'waf_field_data', 'waf_nonassociative_options' );