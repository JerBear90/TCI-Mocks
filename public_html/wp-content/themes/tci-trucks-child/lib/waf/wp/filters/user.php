<?php
function waf_user_field_data( $field ){
    $users = get_users();
    // d('User Field: show roles:',@$field['roles']);
    $users = get_users();
    // d('posts',count($posts));
    $field['type'] = 'select';
    $field['placeholder'] = '--';
    foreach( $users as $user ) {
        $id = $user->ID;
        $name = $user->display_name;
        if( @$field['roles'] ) $name .= ' ('.@$user->user_login.', '.@$user->roles[0].')';
        $field['options'][$id] = $name;
    }
    return $field;
}
add_filter( 'waf_user_field_data', 'waf_user_field_data', 11 );