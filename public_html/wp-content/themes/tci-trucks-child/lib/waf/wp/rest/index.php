<?php
// include 'loginUser.php';
// include 'registerUser.php';
include 'formConfig.php';
include 'posts/index.php';
// include 'events.php';
// include 'forgotPassword.php';
// include 'changePicture.php';
// include 'changePassword.php';
// include 'contact.php';
// include 'resetPassword.php';
// include 'user.php';

// header("Access-Control-Allow-Origin: *");
require_once 'formApi.class.php';
$controller = new Forms_Custom_route;
add_action( 'rest_api_init', [$controller,'register_routes'] );
