<?php
function forms_head() {
    ?>
    <script>
        window.apiNonce = "<?php echo wp_create_nonce('wp_rest'); ?>";
        window.eventUrl = "<?php echo get_bloginfo('url').'/wp-json/waf/v1/events?_wpnonce='.wp_create_nonce('wp_rest'); ?>";
    </script>
    <?php
}
add_action( 'wp_head','forms_head' );
add_action( 'admin_head','forms_head' );