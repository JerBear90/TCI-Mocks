<?php
function js_functionD_init() {
    ?>
    <script>
    var d = console.log;
    var showdebug = <?php echo is_devel() ? 'true' : 'false'; ?>
    </script>
    <?php
}
add_action( 'wp_head', 'js_functionD_init' );
add_action( 'admin_head', 'js_functionD_init' );