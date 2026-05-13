<?php
/**
 * Template part to show related cars.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $car_dealer_options;

$layout           = cardear_get_vehicle_detail_page_layout();
$sidebar_position = cardealer_get_cars_details_page_sidebar_position();
$data_item        = 3;

if ( isset( $car_dealer_options['cars-related-vehicle'] ) && ! filter_var( $car_dealer_options['cars-related-vehicle'], FILTER_VALIDATE_BOOLEAN ) ) {
	return;
}

$related_vehicles_title = ( isset( $car_dealer_options['related_vehicles_title'] ) && ! empty( $car_dealer_options['related_vehicles_title'] ) ) ? $car_dealer_options['related_vehicles_title'] : esc_html__( 'Related Vehicle', 'cardealer' );

if ( '2' === $layout ) {
	$data_item = 4;
}

if ( 'no' === $sidebar_position ) {
	$data_item = 4;
}

if ( 'modern-1' === $layout && 'no' !== $sidebar_position ) {
	$data_item = 3;
}

$vehicle_id       = get_the_ID();
$related_vehicles = cardealer_get_related_vehicles( $vehicle_id );

if ( ! $related_vehicles ) {
	return;
}

$related_query = $related_vehicles['query'];
$nav_arrow     = ( (int) $related_vehicles['post_count'] > 4 ) ? true : false;

if ( $related_query->have_posts() ) {
	?>
	<div class="feature-car">
		<h6 class="related-title"><?php echo esc_html( $related_vehicles_title ); ?></h6>
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<div class="owl-carousel related-vehicle" data-lazyload="<?php echo esc_attr( cardealer_lazyload_enabled() ); ?>" data-nav-arrow="<?php echo esc_attr( $nav_arrow ); ?>" data-nav-dots="false" data-items="<?php echo esc_attr( $data_item ); ?>" data-md-items="3" data-sm-items="2" data-xs-items="2" data-xx-items="2" data-space="20">
					<?php
					$list_style       = cardealer_get_inv_list_style();
					$is_hover_overlay = cardealer_is_hover_overlay();
					$getlayout        = cardealer_get_cars_list_layout_style();
					if ( 'classic' === $list_style ) {
						while ( $related_query->have_posts() ) {
							$related_query->the_post();
							?>
							<div class="item ">
								<div class="car-item gray-bg text-center style-classic">
									<div class="car-image">
										<?php
										$related_vehicle_id = get_the_ID();

										do_action( 'cardealer_car_loop_link_open', $related_vehicle_id, $is_hover_overlay );

										cardealer_get_cars_condition( $related_vehicle_id, true );
										cardealer_get_cars_status( $related_vehicle_id, true );
										cardealer_featured_vehicle_badge( $related_vehicle_id );
										echo wp_kses_post( cardealer_get_cars_image( 'car_catalog_image', $related_vehicle_id ) );

										if ( 'yes' === $is_hover_overlay ) {
											?>
											<div class="car-overlay-banner">
												<ul>
													<?php
													/**
													 * Hook car_overlay_banner.
													 *
													 * @hooked cardealer_view_cars_overlay_link - 10
													 * @hooked cardealer_compare_cars_overlay_link - 20
													 * @hooked cardealer_images_cars_overlay_link - 30
													 */
													if ( 'view-list' === $getlayout ) {
														do_action( 'vehicle_classic_list_overlay_gallery', $related_vehicle_id );
													} else {
														do_action( 'vehicle_classic_grid_overlay', $related_vehicle_id );
													}
													?>
												</ul>
											</div>
											<?php
										}
										do_action( 'cardealer_car_loop_link_close', $related_vehicle_id, $is_hover_overlay );
										?>
									</div>
									<div class="car-content">
										<?php
										/**
										 * Hook cardealer_classic_list_car_title.
										 *
										 * @hooked cardealer_list_car_link_title - 5
										 * @hooked cardealer_list_car_title_separator - 10
										 */
										do_action( 'cardealer_classic_list_car_title' );
										cardealer_car_price_html( 'related-slider', $related_vehicle_id, true );
										cardealer_get_cars_list_attribute( $related_vehicle_id );
										cardealer_get_vehicle_review_stamps( $related_vehicle_id );
										?>
										<ul class="car-bottom-actions classic-grid">
											<?php
											cardealer_classic_view_cars_overlay_link( $related_vehicle_id );
											cardealer_classic_vehicle_video_link( $related_vehicle_id );
											?>
										</ul>
									</div>
								</div>
							</div>
							<?php
						}
					} else {
						while ( $related_query->have_posts() ) {
							$related_query->the_post();
							?>
							<div class="item">
								<div class="car-item gray-bg text-center">
									<div class="car-image">
										<?php
										$related_vehicle_id = get_the_ID();
										cardealer_get_cars_condition( $related_vehicle_id, true );
										cardealer_get_cars_status( $related_vehicle_id, true );
										cardealer_featured_vehicle_badge( $related_vehicle_id );
										echo wp_kses_post( cardealer_get_cars_image( 'car_catalog_image', $related_vehicle_id ) );

										if ( 'yes' === $is_hover_overlay ) {
											?>
											<div class="car-overlay-banner">
												<ul>
													<?php
													/**
													 * Hook car_overlay_banner.
													 *
													 * @hooked cardealer_view_cars_overlay_link - 10
													 * @hooked cardealer_compare_cars_overlay_link - 20
													 * @hooked cardealer_images_cars_overlay_link - 30
													 */
													do_action( 'car_overlay_banner', $related_vehicle_id );
													?>
												</ul>
											</div>
											<?php
										}
										cardealer_get_cars_list_attribute( $related_vehicle_id );
										?>
									</div>
									<div class="car-content">
										<?php
										/**
										 * Hook cardealer_list_car_title.
										 *
										 * @hooked cardealer_list_car_link_title - 5
										 * @hooked cardealer_list_car_title_separator - 10
										 */
										do_action( 'cardealer_list_car_title' );
										cardealer_car_price_html( 'related-slider', $related_vehicle_id, true );
										cardealer_get_vehicle_review_stamps( $related_vehicle_id );
										?>
									</div>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
wp_reset_postdata();
