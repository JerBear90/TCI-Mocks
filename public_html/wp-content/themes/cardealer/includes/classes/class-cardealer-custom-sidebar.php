<?php
/**
 * Custom Sidebar.
 *
 * @package cardealer-helper/classes
 * @since   5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class CarDealer_Custom_Sidebar.
 *
 * @since   5.0.0
 */
class CarDealer_Custom_Sidebar {

	/**
	 * Variable
	 *
	 * @var $sidebars .
	 */
	var $sidebars = array();
	/**
	 * Variable
	 *
	 * @var $option_id .
	 */
	var $option_id = 'cardealer_custom_sidebars';

	/**
	 * Construct
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'cardealer_custom_sidebar_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'cardealer_custom_sidebar_scripts_styles' ) );

		add_action( 'widgets_init', array( $this, 'cardealer_register_custom_sidebars' ), 100 );

		add_action( 'wp_ajax_create_cardealer_sidebar', array( $this, 'create_cardealer_sidebar' ), 10 );
		add_action( 'wp_ajax_nopriv_create_cardealer_sidebar', array( $this, 'create_cardealer_sidebar' ), 10 );

		add_action( 'wp_ajax_delete_cardealer_sidebar', array( $this, 'delete_cardealer_sidebar' ), 10 );
		add_action( 'wp_ajax_nopriv_delete_cardealer_sidebar', array( $this, 'delete_cardealer_sidebar' ), 10 );
	}

	/**
	 * Cardealer load files
	 */
	public function cardealer_custom_sidebar_page() {
		add_submenu_page(
			'themes.php',
			esc_html__( 'Cardealer Custom Sidebars', 'cardealer' ),
			esc_html__( 'Custom Sidebars', 'cardealer' ),
			'manage_options',
			'cardealer-custom-sidebar',
			array( $this, 'cardealer_custom_sidebar_page_callback' )
		);
	}

	/**
	 * Cardealer load files
	 */
	public function create_cardealer_sidebar() {

		$response_array  = array();
		$cardealer_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $cardealer_nonce, 'cardealer_sidebar_nonce' ) ) {
			$response_array['error'] = true;
			$response_array['msg']   = esc_html__( 'Verification fail', 'cardealer' );
			echo wp_json_encode( $response_array );
			wp_die();
		}

		$sidebars              = get_option( $this->option_id );
		$name                  = sanitize_text_field( wp_unslash( $_POST['cardealer_sidebar_name'] ) );
		$is_sidebar_name_exist = $this->cardealer_is_sidebar_name_exist( $name );

		if ( $is_sidebar_name_exist ) {
			$response_array['error'] = true;
			$response_array['msg']   = esc_html__( 'Sidebar already exist with this name.', 'cardealer' );
			echo wp_json_encode( $response_array );
			wp_die();
		}

		$id = preg_replace( '/\s+/', '-', strtolower( $name ) );
		$id = $this->cardealer_get_sidebar_id( $id );

		if ( empty( $sidebars ) ) {
			$sidebar_new[$id] = array(
				'name' => $name,
				'id'   => $id,
			);
			$sidebars = $sidebar_new;
		} else {
			if ( ! array_key_exists( $id, $sidebars ) ) {
				$sidebar_new[$id] = array(
					'name' => $name,
					'id'   => $id,
				);
				$sidebars = array_merge(
					$sidebars,
					$sidebar_new
				);
			} else {
				$response_array['error'] = false;
				$response_array['msg']   = esc_html__( 'Sidebar already exist with this name.', 'cardealer' );
				echo wp_json_encode( $response_array );
				wp_die();
			}
		}

		update_option( $this->option_id, $sidebars );

		$response_array['sidebar'] = $sidebar_new;
		$response_array['error'] = false;
		$response_array['msg']   = esc_html__( 'Sidebar Created successful', 'cardealer' );
		echo wp_json_encode( $response_array );

		wp_die();
	}

	public function cardealer_custom_sidebar_scripts_styles( $hook ) {
		if ( 'appearance_page_cardealer-custom-sidebar' === $hook ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			// jquery-confirm
			wp_enqueue_script( 'cardealer_custom_sidebar', get_template_directory_uri() . '/js/admin/custom-sidebar' . $suffix . '.js', array( 'jquery-confirm' ) );
			wp_localize_script(
				'cardealer_custom_sidebar',
				'cardealer_custom_sidebar_obj',
				array(
					'del_cf_button'          => esc_html__( 'Delete', 'cardealer' ),
					'alert'                  => esc_html__( 'Alert!', 'cardealer' ),
					'delete_sidebar_alert'   => esc_html__( 'Are you sure you want to delete sidebar ?', 'cardealer' ),
					'delete_sidebar_msg'     => esc_html__( 'You cannot delete this sidebar. This sidebar is already in use.', 'cardealer' ),
					'delete_sidebar_confirm' => esc_html__( 'Are you sure you want to delete sidebar?', 'cardealer' ),
					'sidebar_nonce'          => wp_create_nonce( 'cardealer_sidebar_nonce' ),
					'ajaxurl'                => admin_url( 'admin-ajax.php' ),
					'btn_del'                => esc_html__( 'Delete', 'cardealer' ),
					'btn_cancel'             => esc_html__( 'Cancel', 'cardealer' ),
					'no_sidebar'             => esc_html__( 'No sidebar found.', 'cardealer' ),
				)
			);
		}
	}

	/**
	 * Cardealer widgets sidebar form
	 */
	public function cardealer_custom_sidebar_page_callback() {
		$sidebars = get_option( $this->option_id );
		?>
		<div id="cardealer-widgets-form-cover" class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html__( 'Custom Sidebars', 'cardealer' ); ?></h1>
			<div id="col-container" class="wp-clearfix">
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<form method="post" action="" id="create_cardealer_sidebar_form" name="create_cardealer_sidebar_form" action="">
								<div class="form-field form-required">
									<label for="cardealer_sidebar_name"><?php echo esc_html__( 'Sidebar Name', 'cardealer' ); ?></label>
									<input type="text" id="cardealer_sidebar_name" name="cardealer_sidebar_name" size="40" required />
									<p id="sidebar-name-description"><?php echo esc_attr__( 'Enter sidebar name.', 'cardealer' ); ?></p>
								</div>
								<p class="submit">
									<input type="submit" id="create_cardealer_sidebar" class="button button-primary" name="create_cardealer_sidebar" value="<?php echo esc_attr__( 'Add New Sidebar', 'cardealer' ); ?>" />
								</p>
							</form>
						</div>
					</div>
				</div>
				<div id="col-right">
					<div class="col-wrap">
						<table class="wp-list-table widefat striped cardealer-sidebar-table" width="100%" id="cardealer-admin-sidebar-table">
							<thead>
								<tr>
									<th class="sidebar-column sidebar-column-name cardealer-sidebar-table-name-label"><strong><?php esc_html_e( 'Sidebar Name', 'cardealer' ); ?></strong></th>
									<th class="sidebar-column sidebar-column-id cardealer-sidebar-table-id-label"><strong><?php esc_html_e( 'Sidebar ID', 'cardealer' ); ?></strong></th>
									<th class="sidebar-column sidebar-column-action cardealer-sidebar-table-action-label"><strong><?php esc_html_e( 'Action', 'cardealer' ); ?></strong></th>
								</tr>
							</thead>
							<tbody class="cardealer-sidebar-table-body">
								<?php
								$count = ( ! empty( $sidebars ) ) ? count( $sidebars ) : 0;
								if ( ! is_array( $sidebars ) || ( 0 === count( $sidebars ) ) ) {
									?>
									<tr class="empty-sidebar"><td colspan="3"><?php esc_html_e( 'No sidebar found.', 'cardealer' ); ?></td></tr>
									<?php
								}
							if ( ! empty( $sidebars ) ) {
								foreach( $sidebars as $sidebar ) {
									?>
									<tr id="<?php echo esc_attr( $sidebar['id'] ); ?>">
										<td class="sidebar-column sidebar-column-name cardealer-sidebar-table-name"><?php echo esc_html( $sidebar['name'] ); ?></td>
										<td class="sidebar-column sidebar-column-id cardealer-sidebar-table-id"><?php echo esc_html( $sidebar['id'] ); ?></td>
										<td class="sidebar-column sidebar-column-action cardealer-sidebar-table-action">
											<a data-id="<?php echo esc_attr( $sidebar['id'] ); ?>" class="delete-sidebar button button-danger" href="javascript:void(0);">
												<i class="fa fa-trash"></i>
											</a>
										</td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Delete sidebar area from the db
	 */
	public function delete_cardealer_sidebar() {

		$response_array  = array();
		$cardealer_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $cardealer_nonce, 'cardealer_sidebar_nonce' ) ) {
			$response_array['error'] = true;
			$response_array['msg']   = esc_html__( 'Verification fail', 'cardealer' );
			echo wp_json_encode( $response_array );
			wp_die();
		}

		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$sidebars         = get_option( $this->option_id );
		$id               = sanitize_text_field( wp_unslash( $_POST['id'] ) );

		if ( array_key_exists( $id, $sidebars ) ) {
			if ( isset( $sidebars_widgets[$id] ) && $sidebars_widgets[$id] ) {
				unset( $sidebars_widgets[$id] );
				update_option( 'sidebars_widgets', $sidebars_widgets );
			}

			unset( $sidebars[$id] );
			update_option( $this->option_id, $sidebars );
			$response_array['sidebar_id'] = $id;
			$response_array['error']      = false;
			$response_array['msg']        = esc_html__( 'Sidebar deleted successful.', 'cardealer' );
		} else {
			$response_array['error'] = true;
			$response_array['msg']   = esc_html__( 'Something went wrong, please refresh page and try again.', 'cardealer' );
		}

		echo wp_json_encode( $response_array );
		wp_die();
	}

	/**
	 * Checks the user submitted name and makes sure that there are no colitions
	 *
	 * @param string $name .
	 */
	public function cardealer_get_sidebar_id( $id ) {
		if ( is_registered_sidebar( $id ) ) {
			$id = uniqid( $id );
			$this->cardealer_get_sidebar_id( $id );
		} else {
			return $id;
		}
	}

	/**
	 * Checks the user submitted name and makes sure that there are no colitions
	 *
	 * @param string $name .
	 */
	function cardealer_is_sidebar_name_exist( $name ) {

		$taken = array();
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			$taken[] = $sidebar['name'];
		}

		if ( in_array( $name, $taken, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Register custom sidebar areas
	 */
	function cardealer_register_custom_sidebars() {

		$sidebars = get_option( $this->option_id );
		$args     = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		);
		/**
		 * Filters arguments for custom widgets.
		 *
		 * @param array    $args      Array of widget parameters.
		 *
		 * @visible true
		 */
		$args = apply_filters( 'cardealer_custom_widget_args', $args );
		if ( is_array( $sidebars ) ) {
			$sidebar_details = array();
			foreach ( $sidebars as $key => $sidebar ) {
				if ( isset( $sidebar['name'] ) && isset( $sidebar['id'] ) ) {
					$args['name']      = $sidebar['name'];
					$args['id']        = $sidebar['id'];
					$args['class']     = 'cardealer-custom';
					$sidebar_details[] = array(
						'id'   => $args['id'],
						'name' => $args['name'],
					);
					register_sidebar( $args );
				}
			}
		}
	}
}

new CarDealer_Custom_Sidebar();
