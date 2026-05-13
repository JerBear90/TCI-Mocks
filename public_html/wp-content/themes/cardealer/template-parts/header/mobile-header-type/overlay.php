<?php
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options;

get_template_part( 'template-parts/header/topbar' );
?>
<div class="menu">
	<nav id="menu-1" class="header-navbar-v2 header-navbar-v2-mobile">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="header-inner">
						<div class="header-logo">
							<?php
							// Logo image
							get_template_part( 'template-parts/header/menu-elements/logo' );

							$description      = get_bloginfo( 'description', 'display' );
							$site_description = ( isset( $car_dealer_options['site_description'] ) ) ? $car_dealer_options['site_description'] : '0';
							if ( ( $site_description && $description ) || ( $site_description && is_customize_preview() ) ) {
								?>
								<p class="site-description"><?php echo esc_html( $description ); ?></p>
								<?php
							}
							?>
							<div class="mobile-icons-trigger">
								<?php
								$show_search = ( isset( $car_dealer_options['show_search'] ) ) ? ( true === $car_dealer_options['show_search'] ? '1' : $car_dealer_options['show_search'] ) : '1';
								$show_cart   = ( isset( $car_dealer_options['cart_icon'] ) ) ? $car_dealer_options['cart_icon'] : 'yes';
								if ( '1' === $show_search ) {
									get_template_part( 'template-parts/header/menu-elements/search-mobile' );
								}
								if ( 'yes' === $show_cart ) {
									?>
									<div class="mobile-cart-wrapper">
										<?php get_template_part( 'woocommerce/minicart-ajax' ); ?>
									</div>
									<?php
								}
								?>
								<div class="menu-item menu-item-compare" style="<?php echo esc_attr( ( ! isset( $_COOKIE['compare_ids'] ) || empty( $_COOKIE['compare_ids'] ) ) ? 'display:none;' : '' ); ?>">
									<a class="" href="javascript:void(0)">
										<span class="compare-items"><i class="fas fa-exchange-alt"></i></span>
										<span class="compare-details count">0</span>
									</a>
								</div>
							</div>
							<?php

							// WooCommerce.
							if ( class_exists( 'woocommerce' ) ) {
								?>
								<div class="widget_shopping_cart_content hidden-xs">
									<?php
									$mini_cart_defaults = array(
										'list_class' => '',
									);

									$mini_cart_args = array();
									$mini_cart_args = wp_parse_args( $mini_cart_args, $mini_cart_defaults );

									wc_get_template( 'cart/mini-cart.php', $mini_cart_args );
									?>
								</div>
								<?php
							}
							?>
							<div class="menu-mobile-collapse-trigger">
								<div class="trigger-span">
									<span></span>
									<span></span>
									<span></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<!-- menu end -->
</div>
