<?php
function waf_get_template_html( $template_path ) {
    ob_start();
    get_template_part( $template_path );
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}