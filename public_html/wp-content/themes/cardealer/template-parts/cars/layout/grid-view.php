<?php
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */
?>
<div <?php cardealer_grid_view_class(); ?>>
	<?php
	$list_style = cardealer_get_inv_list_style();
	if ( 'classic' === $list_style ) {
		?>
		<div class="car-item gray-bg text-center style-classic <?php echo esc_attr( cardealer_cars_loop() ); ?>">

			<?php
			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/car-item-start' );

			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-image-wrapper' );
			?>

			<div class="car-image">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-image' );

				$grid_view_post_id = get_the_ID();
				get_template_part( 'template-parts/cars/layout/img-overlay' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/car-image' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/after-car-image' );
				?>
			</div>
			<?php do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-content-wrapper' ); ?>
			<div class="car-content">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-content' );
				/**
				 * Hook cardealer_classic_list_car_title.
				 *
				 * @hooked cardealer_list_car_link_title - 10
				 */

				do_action( 'cardealer_classic_list_car_title' );
				cardealer_car_price_html( $grid_view_post_id );
				cardealer_get_cars_list_attribute( $grid_view_post_id );
				cardealer_get_vehicle_review_stamps( $grid_view_post_id );
				?>
				<ul class="car-bottom-actions classic-grid">
					<?php
					cardealer_classic_view_cars_overlay_link( $grid_view_post_id );
					cardealer_classic_vehicle_video_link( $grid_view_post_id );
					?>
				</ul>
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/car-content' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/after-car-content' );
				?>
			</div>
			<?php do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-footer-wrapper' ); ?>
			<div class="car-footer">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/before-car-footer' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/car-footer' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/after-car-footer' );
				?>
			</div>
			<?php
			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/after-car-footer-wrapper' );

			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-classic/car-item-start' );
			?>
		</div>
		<?php
	} else {
		?>
		<div class="car-item gray-bg text-center <?php echo esc_attr( cardealer_cars_loop() ); ?>">
			<?php
			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/car-item-start' );

			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-image-wrapper' );
			?>
			<div class="car-image">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-image' );

				$grid_view_post_id = get_the_ID();
				get_template_part( 'template-parts/cars/layout/img-overlay' );
				cardealer_get_cars_list_attribute( $grid_view_post_id );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/car-image' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/after-car-image' );
				?>
			</div>
			<?php do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-content-wrapper' ); ?>
			<div class="car-content">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-content' );

				/**
				 * Hook cardealer_list_car_title.
				 *
				 * @hooked cardealer_list_car_link_title - 5
				 * @hooked cardealer_list_car_title_separator - 10
				 */
				do_action( 'cardealer_list_car_title' );
				cardealer_car_price_html( $grid_view_post_id );
				cardealer_get_vehicle_review_stamps( $grid_view_post_id );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/car-content' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/after-car-content' );
				?>
			</div>
			<?php do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-footer-wrapper' ); ?>
			<div class="car-footer">
				<?php
				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/before-car-footer' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/car-footer' );

				do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/after-car-footer' );
				?>
			</div>
			<?php
			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/after-car-footer-wrapper' );

			do_action( 'cardealer/vehicle-list-item/view-grid/list-style-default/car-item-start' );
			?>
		</div>
		<?php
	}
	?>
</div>
