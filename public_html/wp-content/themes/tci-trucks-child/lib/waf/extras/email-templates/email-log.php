<?php
// Menu Item
function emt_email_log_init() {
	add_submenu_page( 'edit.php?post_type=email', 'Email Log', 'Email Log', 'manage_options', 'email-log', 'emt_email_log_page' );
}
add_action( 'admin_menu', 'emt_email_log_init' );

// Form
function emt_email_log_page() {
	// Log path(s)
	$log_path = ABSPATH.'email-log/';
	$devtest_log_path = ABSPATH.'email-log/dev-test';
	
	// Nonce
	$nonce = wp_create_nonce( 'view-email-log' );
	
	echo '<h1>Email Logs</h1>';
	
	// Real Logs
	$logs = scandir( $log_path );
	echo '<h2>Real Logs</h2>';
	foreach( $logs as $log ) {
		if( $log == '.' || $log == '..' || $log == '.htaccess' || is_dir( $log_path.'/'.$log ) ) continue;
		$name = basename( $log );
		echo '<a class="view-email-log" data-nonce="'.$nonce.'" href="'.$name.'">'.$name.'</a><br/>';
	}
	
	//Dev Tests
	$logs = scandir( $devtest_log_path );
	echo '<br><br><br><h2>Developer Tests</h2>';
	foreach( $logs as $log ) {
		if( $log == '.' || $log == '..' || $log == '.htaccess'  ) continue;
		$name = basename( $log );
		echo '<a class="view-email-log" data-nonce="'.$nonce.'" href="'.$name.'" data-devtest="true">'.$name.'</a><br/>';
	}
	
	echo '<br><br><br><div id="email-log-content"></div>';
}

// Form Submission
function emt_ajax_view_email_log() {
	extract( $_POST );
	if( wp_verify_nonce( $nonce, 'view-email-log' ) ) {
		if( $devtest ) $path = ABSPATH.'email-log/dev-test/';
		else $path = ABSPATH.'email-log/';
		
		$fname = $path.$file;
		$contents = @file_get_contents($fname);
		if( !$contents ) $contents = '- File Empty';
		echo str_replace( "\n", "<br>", htmlentities($contents) );
	}
	exit;
}
add_action( 'wp_ajax_view-email-log', 'emt_ajax_view_email_log' );

// Button Click
function emt_email_log_js() {
	?>
	<script>
	jQuery(document).ready( function($) {
		var admin_ajax = "<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php";
		$('.view-email-log').click( function(e) {
			var data = {
				action : 'view-email-log',
				file : $(this).attr('href'),
				nonce : $(this).attr('data-nonce'),
				devtest : $(this).attr('data-devtest')
			}
			
			$('#email-log-content').addClass('loading');
			$.post( admin_ajax, data, function( response ) {
				$('#email-log-content').removeClass('loading').html(response);
			});
			return false;
		});
	});
	</script>
	<?php
}
add_action( 'admin_head', 'emt_email_log_js' );
