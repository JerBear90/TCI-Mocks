<?php
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options;

if ( isset( $car_dealer_options['cars-details-layout'] ) && '3' === $car_dealer_options['cars-details-layout'] ) {
	echo '<div class="container">';
}
?>
<div class="row">
	<div class="col-lg-9 col-md-9 col-sm-9">
		<h3><?php the_title(); ?></h3>
		<?php the_excerpt(); ?>
		<?php back_button(); ?>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<?php cardealer_car_price_html( 'text-right' ); ?>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="details-nav">
			<ul>
				<?php
				get_template_part( 'template-parts/cars/single-car/forms/request_info' );
				get_template_part( 'template-parts/cars/single-car/forms/make_an_offer' );
				get_template_part( 'template-parts/cars/single-car/forms/schedule_test_drive' );
				get_template_part( 'template-parts/cars/single-car/forms/email_to_friend' );
				get_template_part( 'template-parts/cars/single-car/forms/financial_form' );
				get_template_part( 'template-parts/cars/single-car/forms/pdf_brochure' );
				get_template_part( 'template-parts/cars/single-car/forms/print_form' );
				?>
			</ul>
		</div>
		<div class="car-detail-post-option">
			<ul>
				<?php
				$element_id      = uniqid( 'cd_video_' );
				$video_link      = get_post_meta( get_the_ID(), 'video_link', $single = true );
				$element_classes = array(
					'play-video',
					'popup-gallery',
				);
				$element_classes = implode( ' ', array_filter( array_unique( $element_classes ) ) );
				if ( isset( $video_link ) && ! empty( $video_link ) ) {
					?>
					<li>
						<div id="<?php echo esc_attr( $element_id ); ?>"  class="<?php echo esc_attr( $element_classes ); ?> default">
							<a class="popup-youtube" href="<?php echo esc_url( $video_link ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"> <i class="fas fa-play"></i> <?php esc_html_e( 'Vehicle video', 'cardealer' ); ?></a>
						</div>
					</li>
					<?php
				}
				if ( ! isset( $car_dealer_options['is-compare-on-vehicle-detail'] ) || 'yes' === $car_dealer_options['is-compare-on-vehicle-detail'] ) {
					?>
					<li><a href="javascript:void(0)" title="<?php echo esc_attr( get_the_title() ); ?>" data-id="<?php echo get_the_ID(); ?>" class="pgs_compare_popup compare_pgs"><i class="fas fa-exchange-alt"></i> <?php esc_html_e( 'Add to compare', 'cardealer' ); ?></a></li>
					<?php
				}
				?>
			</ul>
			<?php get_template_part( 'template-parts/cars/single-car/share' ); ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php

if ( isset( $car_dealer_options['cars-details-layout'] ) && '3' === $car_dealer_options['cars-details-layout'] ) {
	echo '</div>';
}
if ( isset( $car_dealer_options['cars-details-layout'] ) && '1' === $car_dealer_options['cars-details-layout'] ) {
	?>
	<div class="row">
		<?php
		$sidebar_position = cardealer_get_cars_details_page_sidebar_position();
		if ( 'left' === $sidebar_position ) {
			?>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
				<?php dynamic_sidebar( 'detail-cars' ); ?>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		} elseif ( 'right' === $sidebar_position ) {
			?>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
				<?php dynamic_sidebar( 'detail-cars' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		} else {
			?>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
				<?php cardealer_get_widget_fuel_efficiency(); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-12">
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		}
		?>
	</div>
	<?php
} elseif ( isset( $car_dealer_options['cars-details-layout'] ) && '2' === $car_dealer_options['cars-details-layout'] ) {
	?>
	<div class="row">
		<?php
		$sidebar_position = cardealer_get_cars_details_page_sidebar_position();
		if ( 'left' === $sidebar_position ) {
			?>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php dynamic_sidebar( 'detail-cars' ); ?>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-12">
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		} elseif ( 'right' === $sidebar_position ) {
			?>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php dynamic_sidebar( 'detail-cars' ); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-12">
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		} else {
			?>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
				<?php cardealer_get_widget_fuel_efficiency(); ?>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-12">
				<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
				<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
			</div>
			<div class="clearfix"></div>
			<?php
		}
		?>
	</div>
	<?php
} else {
	?>
	<div class="row">
		<?php get_template_part( 'template-parts/cars/single-car/car-image' ); ?>
	</div>
	<div class="container">
		<div class="row">
			<?php
			$sidebar_position = cardealer_get_cars_details_page_sidebar_position();
			if ( 'left' === $sidebar_position ) {
				?>
				<div class="clearfix"></div>
				<div class="col-lg-4 col-md-4 col-sm-4">
					<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
					<?php dynamic_sidebar( 'detail-cars' ); ?>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8">
					<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
					<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
				</div>
				<div class="clearfix"></div>
				<?php
			} elseif ( 'right' === $sidebar_position ) {
				?>
				<div class="clearfix"></div>
				<div class="col-lg-8 col-md-8 col-sm-8">
					<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
					<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
					<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
					<?php dynamic_sidebar( 'detail-cars' ); ?>
				</div>
				<div class="clearfix"></div>
				<?php
			} else {
				?>
				<div class="clearfix"></div>
				<div class="col-lg-8 col-md-8 col-sm-8">
					<?php get_template_part( 'template-parts/cars/single-car/tabs/tabs' ); ?>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
					<?php get_template_part( 'template-parts/cars/single-car/car-summary' ); ?>
					<?php cardealer_get_widget_fuel_efficiency(); ?>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-12">
					<?php get_template_part( 'template-parts/cars/single-car/related' ); ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
