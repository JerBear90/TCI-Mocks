<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options, $cardealer_header_settings;

do_action( 'cardealer_before_header' );

$cardealer_header_settings                = array();
$cardealer_header_settings['header_type'] = 'defualt';

$header_classes = array(
	'defualt'               => 'defualt',
	'light'                 => 'light',
	'transparent-fullwidth' => 'transparent-fullwidth',
	'light-fullwidth'       => 'light-fullwidth',
	'logo-center'           => 'logo-center',
	'logo-right'            => 'logo-right',
	'boxed'                 => 'boxed',
	'default-new'           => 'default-new',
	'fancy'                 => 'header-fancy',
);

$header_class = array();

// Header Style.
if ( isset( $car_dealer_options['header_type'] ) && ! empty( $car_dealer_options['header_type'] ) ) {

	if ( isset( $car_dealer_options['header_above_content'] ) && 'enable' === $car_dealer_options['header_above_content'] && ( 'default-new' === $car_dealer_options['header_type'] || 'fancy' === $car_dealer_options['header_type'] ) ) {
		$header_class[] = 'header-above-content-enabled';
	}

	if ( isset( $car_dealer_options['header_width'] ) && $car_dealer_options['header_width'] && 'default-new' === $car_dealer_options['header_type'] ) {
		$header_class[] = 'header-width-' . $car_dealer_options['header_width'];
	}

	if ( array_key_exists( $car_dealer_options['header_type'], $header_classes ) ) {
		$cardealer_header_settings['header_type'] = $car_dealer_options['header_type'];
		$header_class[]                           = $header_classes[ $car_dealer_options['header_type'] ];
	} else {
		$cardealer_header_settings['header_type'] = 'defualt';
		$header_class[]                           = 'defualt';
	}
} else {
	$header_class[]                           = 'transparent';
	$cardealer_header_settings['header_type'] = 'defualt';
}

if ( isset( $car_dealer_options['mobile_header_type'] ) && ! empty( $car_dealer_options['mobile_header_type'] ) ) {
	$header_class[]     = 'mobile-header-' . $car_dealer_options['mobile_header_type'];
	$mobile_header_type = $car_dealer_options['mobile_header_type'];
} else {
	$mobile_header_type = 'default';
}

if ( isset( $car_dealer_options['header_color_settings'] ) ) {
	if ( 'default' === $car_dealer_options['header_color_settings'] ) {
		$header_class[] = 'default-header header-color-' . $car_dealer_options['header_color_settings'];
	} else {
		$header_class[] = 'header-color-' . $car_dealer_options['header_color_settings'];
	}
}

$header_class = implode( ' ', $header_class );
?>
<header id="header" class="<?php echo esc_attr( $header_class ); ?>">
	<?php
	do_action( 'cardealer_before_header_inner' );
	if ( pgs_wp_is_mobile() ) {
		get_template_part( 'template-parts/header/mobile-header-type/' . $mobile_header_type );
	} else {
		get_template_part( 'template-parts/header/header_type/' . $cardealer_header_settings['header_type'] );
	}
	do_action( 'cardealer_after_header_inner' );
	?>
</header>
<?php
// Overlay Mobile menu
if ( pgs_wp_is_mobile() && 'overlay' === $mobile_header_type ) {
	?>
	<div class="header-mobile-overlay-menu">
		<div class="menu-mobile-collapse-trigger">
			<div class="trigger-span">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<div class="header-mobile-navigation">
			<?php cardealer_primary_menu(); ?>
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
	<?php
}
?>
<?php do_action( 'cardealer_after_header' ); ?>
