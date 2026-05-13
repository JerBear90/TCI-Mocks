<?php
function formConfig_head() {
    if( !is_user_logged_in() ) return;
    ?>
    <script>
    var $ = jQuery.noConflict();
    var adminajax = '<?php  admin_url(); ?>/admin-ajax.php';
    var eventUrl = '<?php site_url(); ?>/wp-json/waf/v1/events';
    var formConfig = <?php echo json_encode( getFormConfig(false,true) ); ?>
    
    </script>
    <?php
}
add_action( 'wp_head', 'formConfig_head' );