<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package CarDealer
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="potenzaglobalsolutions.com" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php do_action( 'cardealer_head_before' ); ?>
	<?php wp_head(); ?>
	<?php do_action( 'cardealer_head_before_close' ); ?>
</head>
<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage" <?php cardealer_body_styles(); ?>>
	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	}

	do_action( 'cardealer_page_before' );
	?>
	<!-- Main Body Wrapper Element -->
	<div id="page" class="hfeed site page-wrapper">
		<div class="wrapper" id="main">
