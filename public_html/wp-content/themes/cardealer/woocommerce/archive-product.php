<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $car_dealer_options, $cardealer_page_sidebar;

$cardealer_page_sidebar = isset( $car_dealer_options['page_sidebar'] ) ? $car_dealer_options['page_sidebar'] : '';
if ( empty( $cardealer_page_sidebar ) ) {
	$cardealer_page_sidebar = 'right_sidebar';
}
$curremt_post_id = get_the_ID();
if ( is_archive() ) {
	$curremt_post_id = cardealer_get_current_post_id();
}
if ( is_shop() && ! is_single() ) {
	$shop_page_id = wc_get_page_id( 'shop' );
	if ( ! empty( $shop_page_id ) && -1 !== $shop_page_id ) {
		$curremt_post_id = $shop_page_id;
	}
}

$page_layout_custom = get_post_meta( $curremt_post_id, 'page_layout_custom', true );
if ( $page_layout_custom ) {
	$page_sidebar = get_post_meta( $curremt_post_id, 'page_sidebar', true );
	if ( $page_sidebar ) {
		$cardealer_page_sidebar = $page_sidebar;
	}
}
$width = 12;

$sidebar_stat = '';

if ( 'left_sidebar' === $cardealer_page_sidebar || 'right_sidebar' === $cardealer_page_sidebar ) {
	$width_lg      = $width - 3;
	$width_md      = $width - 3;
	$width_sm      = $width - 4;
	$sidebar_stat .= ' with-sidebar';
	$sidebar_stat .= " with-$cardealer_page_sidebar";
} elseif ( 'two_sidebar' === $cardealer_page_sidebar ) {
	$width_lg = 6;
	$width_md = 6;
	$width_sm = 6;

	$sidebar_stat .= ' with-sidebar';
	$sidebar_stat .= " with-$cardealer_page_sidebar";
} else {
	$width_lg = $width;
	$width_md = $width;
	$width_sm = $width;

	$sidebar_stat .= 'without-sidebar';
}
?>
<section class="product-listing page-section-ptb">
	<div class="container">
		<div class="row <?php echo esc_attr( $sidebar_stat ); ?>">
			<?php if ( ( 'left_sidebar' === $cardealer_page_sidebar || 'two_sidebar' === $cardealer_page_sidebar ) ) { ?>
			<aside id="right" class="sidebar col-lg-3 col-md-3 col-sm-4 sidebar-left">
				<?php
				/**
				 * Hook woocommerce_sidebar.
				 *
				 * @hooked woocommerce_get_sidebar - 10
				 */
				get_sidebar( 'left' );
				?>
			</aside>
			<?php } ?>
			<div class="content col-lg-<?php echo esc_attr( $width_lg ); ?> col-md-<?php echo esc_attr( $width_md ); ?> col-sm-<?php echo esc_attr( $width_sm ); ?>">
				<?php
				/**
				 * Hook: woocommerce_before_main_content.
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 * @hooked WC_Structured_Data::generate_website_data() - 30
				 */
				do_action( 'woocommerce_before_main_content' );

				/**
				 * Hook: woocommerce_shop_loop_header.
				 *
				 * @since 8.6.0
				 *
				 * @hooked woocommerce_product_taxonomy_archive_header - 10
				 */
				do_action( 'woocommerce_shop_loop_header' );

				if ( woocommerce_product_loop() ) {

					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					do_action( 'woocommerce_before_shop_loop' );

					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );
				}
				?>
			</div>
			<?php
			/**
			 * Hook: woocommerce_after_main_content.
			 *
			 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action( 'woocommerce_after_main_content' );

			if ( ( 'right_sidebar' === $cardealer_page_sidebar || 'two_sidebar' === $cardealer_page_sidebar ) ) {
				?>
				<aside id="right" class="sidebar col-lg-3 col-md-3 col-sm-4 sidebar-right">
					<?php
					/**
					 * Hook: woocommerce_sidebar.
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					do_action( 'woocommerce_sidebar' );
					?>
				</aside>
				<?php
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer( 'shop' );
