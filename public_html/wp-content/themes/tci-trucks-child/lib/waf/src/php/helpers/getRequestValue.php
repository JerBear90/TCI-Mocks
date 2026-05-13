<?php
function getRequestValue( $k ) {
    return @$_GET[$k];
}