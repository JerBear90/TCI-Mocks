<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options;

$team_per_page = 10;

if ( isset( $car_dealer_options['team_members_per_page'] ) && ! empty( $car_dealer_options['team_members_per_page'] ) ) {
	$team_per_page = $car_dealer_options['team_members_per_page'];
}

$team_paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args       = array(
	'post_type'      => 'teams',
	'posts_per_page' => $team_per_page,
	'paged'          => $team_paged,
	'post_status'    => 'publish',
);

$teams_query = new WP_Query( $args );

if ( $teams_query->have_posts() ) {
	$i = 1;
	while ( $teams_query->have_posts() ) {
		$teams_query->the_post();

		$title     = get_the_title();
		?>
		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="team-2">
				<div class="team-image">
					<?php
					if ( has_post_thumbnail() ) {
						$img_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'cardealer-team-thumb' );
						if ( cardealer_lazyload_enabled() ) {
							?>
							<img src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $img_url ); ?>" class="img-responsive icon cardealer-lazy-load" alt="<?php echo esc_attr( $title ); ?>"/>
							<?php
						} else {
							?>
							<img src="<?php echo esc_url( $img_url ); ?>" class="img-responsive icon" alt="<?php echo esc_attr( $title ); ?>"/>
							<?php
						}
					}
					?>
				</div>
				<div class="team-info">
					<div class="team-name">
						<span><?php echo esc_html( get_post_meta( get_the_ID(), 'designation', true ) ); ?></span>
						<h5>
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
						</h5>
						<?php cardealer_the_excerpt_max_charlength( 85 ); ?>
					</div>
					<?php cardealer_team_social_profiles_html(); ?>
				</div>
			</div>
		</div>
		<?php
		if ( ( $i % 4 ) === 0 ) {
			?>
			<div class="clearfix"></div>
			<?php
		}
		$i++;
	}
	?>
	<div class="col-sm-12">
		<?php
		if ( function_exists( 'cardealer_wp_bs_pagination' ) ) {
			cardealer_wp_bs_pagination( $teams_query->max_num_pages );
		}
		?>
	</div>
	<?php
	wp_reset_postdata();
}
