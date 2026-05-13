<?php
function getForms() {
    $path = dirname(dirname(__FILE__));
    global $wpdb;
    $q = "SELECT * FROM {$wpdb->prefix}forms";
    $items =  $wpdb->get_results($q);

    $formsPath = $path.'/forms/';
    $sharedForms = getDirContents($formsPath);
    foreach( $sharedForms as $slug=>$form ) {
        $items[] = [
            'slug' => $slug,
            'name' => $form->args->name,
            'json' => $form,
            'type' => 'file',
            'status' => 'publish'
        ];
    }
    // $forms = array_merge( getDirContents( $formsPath ), $items );
    return $items;
}
function getForm( $id ) {
    global $wpdb;
    $table = $wpdb->prefix.'forms';
    global $formsTable;
    $q = $wpdb->get_row( "SELECT * FROM $table WHERE ID=$id" );
  
   return $form;
}
function createForm( $data) {
    global $wpdb;
    $table = $wpdb->prefix.'forms';
    
    $wpdb->insert( $table, [
        "json" => $data
    ]);
    
    if( $wpdb->last_error != $last_error ) {
        return false;
    }
    
    return getForm( $wpdb->insert_id );
}
function updateForm( $data ) {
    global $wpdb;
    $table = $wpdb->prefix.'forms';
    
    $id = $data['id'];
    unset( $data['id'] );
    
    $wpdb->update( $table, $data, ["id" => $id] );
    if( $wpdb->last_error ) return false;
    return getForm( $id );
}
function deleteForm( $id ) {
    global $wpdb;
    $table = $wpdb->prefix.'forms';
    $wpdb->delete( $table, ["id"=>$id] );

    if( $wpdb->last_error ) return false;
    return true;
}