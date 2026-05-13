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
	<nav id="menu-1" class="header-navbar-v2">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12">
					<div class="header-inner">
						<div class="header-left">
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
							</div>
							<div class="header-menu">
								<?php cardealer_primary_menu(); ?>
							</div>
							<div class="header-actions">
								<div class="action-cart">
									<?php
									if ( class_exists( 'woocommerce' ) && ( isset( $car_dealer_options['cart_icon'] ) && 'no' !== $car_dealer_options['cart_icon'] ) ) {
										get_template_part( 'woocommerce/minicart-ajax' );
									}
									?>
								</div>
								<div class="action-compare">
									
										<div class="menu-item menu-item-compare" style="display:none'">
											<a class="" href="javascript:void(0)">
												<span class="compare-items">
													<i class="fas fa-exchange-alt"></i>
												</span>
												<span class="compare-details count">0</span>
											</a>
										</div>
								</div>
								<div class="action-search menu-item menu-item-search">
									<?php
									if ( isset( $car_dealer_options['show_search'] ) && '1' === $car_dealer_options['show_search'] ) {
										$search_placeholder_text = ( isset( $car_dealer_options['search_placeholder_text'] ) ) ? $car_dealer_options['search_placeholder_text'] : '';
										$search_post_type        = cardealer_search_post_type();
										?>
										<form role="search" method="get" id="menu-searchform" name="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
											<div class="search cd-search-wrap menu-search-wrap">
												<a class="search-open-btn not_click" href="javascript:void(0);"> </a>
												<div class="search-box not-click">
													<?php
													if ( 'any' !== $search_post_type ) {
														?>
														<input type="hidden" name="post_type" value="<?php echo esc_attr( $search_post_type ); ?>" />
														<?php
													}
													?>
													<input type="text" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="menu-s"  placeholder="<?php echo esc_attr( $search_placeholder_text ); ?>" class="cd-search-autocomplete-input not-click" data-seach_type="default"/>
													<button class="cd-search-submit" value="Search" type="submit"><i class="fas fa-search"></i></button>
													<div class="cd-search-autocomplete"><ul class="cd-search-autocomplete-list"></ul></div>
												</div>
											</div>
										</form>
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<div class="header-right">
							<div class="header-info">
								<?php
								if ( isset( $car_dealer_options['header_info_icon'] ) && $car_dealer_options['header_info_icon'] ) {
									?>
									<div class="info-icon">
										<i class="<?php echo esc_attr( $car_dealer_options['header_info_icon'] ) ?>"></i>
									</div>
									<?php
								}
								?>
								<div class="info-content">
									<?php
									if ( isset( $car_dealer_options['header_info_label'] ) && $car_dealer_options['header_info_label'] ) {
										?>
										<span class="info-label"><?php echo esc_html( $car_dealer_options['header_info_label'] ) ?></span>
										<?php
									}
									if ( isset( $car_dealer_options['header_info_value'] ) && $car_dealer_options['header_info_value'] ) {
										?>
										<h4 class="info-number"><?php echo esc_html( $car_dealer_options['header_info_value'] ) ?></h4>
										<?php
									}
									?>
								</div>
							</div>
							<div class="header-button">
								<?php
								if ( class_exists( 'CDFS' ) ) {
									if ( isset( $car_dealer_options['cdfs-menu'] ) && 1 === (int) $car_dealer_options['cdfs-menu'] ) {
										$menu_label = ! empty( $car_dealer_options['cdfs-menu-label'] ) ? $car_dealer_options['cdfs-menu-label'] : esc_html__( 'Add vehicle', 'cardealer' );					
										?>
										<div class="menu-item cdfs-add-vehicle">
											<a href="<?php echo esc_url( cdfs_get_add_car_url() ); ?>" class="listing_add_cart heading-font button">
												<?php echo esc_html( $menu_label ); ?>
											</a>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<!-- menu end -->
</div>
