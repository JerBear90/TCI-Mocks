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
	<!-- menu start -->
	<nav id="menu-1" class="mega-menu">
		<!-- menu list items container -->
		<div class="menu-list-items">
			<div class='menu-inner'>
				<div class="container">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="header-boxed header-main-inner">
								<!-- menu logo -->
								<ul class="menu-logo">
									<li>
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
									</li>
								</ul>
								<!-- menu links -->
								<?php cardealer_primary_menu(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<!-- menu end -->
</div>
