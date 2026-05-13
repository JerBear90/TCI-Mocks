<?php
function setup_taxonomy_fields( ) {
    $taxonomies = get_taxonomies();
    foreach( $taxonomies as $t ) {
        add_action( $t.'_add_form_fields', 'waf_render_term_fields' );
        add_action( $t.'_edit_form_fields', 'waf_render_term_fields', 100000 );
        
        add_action( 'created_'.$t, 'waf_save_term_fields' );
        add_action( 'edited_'.$t, 'waf_save_term_fields' );
    }
}
add_action( 'admin_init', 'setup_taxonomy_fields' );