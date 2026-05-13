<?php 
function waf_download_file( $url, $filepath ) {
    $ch = curl_init( $url );
    $fp = fopen( $filepath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}