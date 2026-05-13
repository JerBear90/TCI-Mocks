<?php
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options;

?>
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php
	// site-logo.
	$logo_max_height    = isset( $car_dealer_options['logo_max_height']['height'] ) ? $car_dealer_options['logo_max_height']['height'] : '32px';
	$mobile_logo_height = isset( $car_dealer_options['mobile_logo_height']['height'] ) ? $car_dealer_options['mobile_logo_height']['height'] : '32px';

	$logo_max_height_sticky_header        = isset( $car_dealer_options['logo_max_height_sticky_header']['height'] ) ? $car_dealer_options['logo_max_height_sticky_header']['height'] : '32px';
	$mobile_logo_max_height_sticky_header = isset( $car_dealer_options['mobile_logo_max_height_sticky_header']['height'] ) ? $car_dealer_options['mobile_logo_max_height_sticky_header']['height'] : '32px';

	if ( wp_is_mobile() ) {
		if ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['mobile_logo_img']['url'] ) && 'yes' === $car_dealer_options['show_mobile_logo'] ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="site-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['mobile_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_height ) ?>;" />
				<?php
			} else {
				?>
				<img class="site-logo" src="<?php echo esc_url( $car_dealer_options['mobile_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_height ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_image']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="site-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_height ) ?>;" />
				<?php
			} else {
				?>
				<img class="site-logo" src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_height ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'text' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_text'] ) ) {
			?>
			<span class="site-logo logo-text"><?php echo esc_html( $car_dealer_options['logo_text'] ); ?></span>
			<?php
		} else {
			?>
			<span class="site-logo logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php
		}
	} else {
		if ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_image']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="site-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height ) ?>;" />
				<?php
			} else {
				?>
				<img class="site-logo" src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'text' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_text'] ) ) {
			?>
			<span class="site-logo logo-text"><?php echo esc_html( $car_dealer_options['logo_text'] ); ?></span>
			<?php
		} else {
			?>
			<span class="site-logo logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php
		}
	}

	// stickey-logo.
	if ( wp_is_mobile() ) {
		if ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['mobile_sticky_logo_img']['url'] ) && 'yes' === $car_dealer_options['show_mobile_logo'] ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="sticky-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['mobile_sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			} else {
				?>
				<img class="sticky-logo" src="<?php echo esc_url( $car_dealer_options['mobile_sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['sticky_logo_img']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="sticky-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			} else {
				?>
				<img class="sticky-logo" src="<?php echo esc_url( $car_dealer_options['sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_image']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="sticky-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			} else {
				?>
				<img class="sticky-logo" src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $mobile_logo_max_height_sticky_header ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'text' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_text'] ) ) {
			?>
			<span class="sticky-logo sticky-logo-text"><?php echo esc_html( $car_dealer_options['logo_text'] ); ?></span>
			<?php
		} else {
			?>
			<span class="sticky-logo sticky-logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php
		}
	} else {
		if ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['sticky_logo_img']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="sticky-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height_sticky_header ) ?>;" />
				<?php
			} else {
				?>
				<img class="sticky-logo" src="<?php echo esc_url( $car_dealer_options['sticky_logo_img']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height_sticky_header ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'image' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_image']['url'] ) ) {
			if ( cardealer_lazyload_enabled() ) {
				?>
				<img class="sticky-logo cardealer-lazy-load" src="<?php echo esc_url( LAZYLOAD_IMG ); ?>" data-src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height_sticky_header ) ?>;" />
				<?php
			} else {
				?>
				<img class="sticky-logo" src="<?php echo esc_url( $car_dealer_options['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" style="height:<?php echo esc_attr( $logo_max_height_sticky_header ) ?>;" />
				<?php
			}
		} elseif ( isset( $car_dealer_options['logo_type'] ) && 'text' === $car_dealer_options['logo_type'] && ! empty( $car_dealer_options['logo_text'] ) ) {
			?>
			<span class="sticky-logo sticky-logo-text"><?php echo esc_html( $car_dealer_options['logo_text'] ); ?></span>
			<?php
		} else {
			?>
			<span class="sticky-logo sticky-logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php
		}
	}
	?>
</a>
