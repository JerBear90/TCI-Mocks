<?php
// Debug function
if( !function_exists('dd') ) {
	function dd(...$args) {
		d(...$args);
	}
}
if( !function_exists('d') ) {
	function d(...$args) { 
		global $noDebug;
		if( $noDebug || @$_SESSION['noDebug'] ) return;
		if( is_devel() ) {
			echo '<pre>';
			foreach( $args as $arg ) echo htmlentities( print_r($arg,1) ).' ';
			echo "</pre>\n";
		}
	}
}
if( !function_exists('dl') ) {
	function dl() {
		d('-----');
	}
}
if( !function_exists('is_devel') ) {
	function is_devel() {
		$my_ip = $_SERVER['REMOTE_ADDR'];
		if( @DEBUG_IP ) {
			if( is_array( @DEBUG_IP ) )
				if( in_array( $my_ip, DEBUG_IP ) ) return true;
			if( $my_ip == @DEBUG_IP || strpos($my_ip,'192.168.0') === 0 || $my_ip === '127.0.0.1' ) return true;
		}
		return false;
	}
}

function slp_admin_bar_menu($admin_bar){
    @session_start();
    $status = @$_SESSION['showDebug'] ? 0 : 1;
    if( current_user_can('edit_users') ) {
        $admin_bar->add_menu( array(
            'id'    => 'toggle-debug',
            'title' => $status ? 'Enable Debug ' : 'Disable Debug',
            'href'  => '?toggleDebug='.(int)$status,
            'meta'  => array(
                'title' => __('Show Age Gate'),            
            ),
        ));
    }
    session_write_close();
}
add_action('admin_bar_menu', 'slp_admin_bar_menu', 100);

function admin_toggle_debug() {
    @session_start();
    if( isset($_GET['toggleDebug'] ) ) {
        
        $_SESSION['showDebug'] = $_GET['toggleDebug'];
        wp_redirect(get_current_url(false));
        die;
    }
    session_write_close();
}
add_action( 'init', 'admin_toggle_debug' );

function aad(...$args) {
    global $showDebug;
    @session_start();
    if( @$showDebug || @$_SESSION['showDebug'] ) {
        if( current_user_can('edit_users') && is_devel() ) {
            echo '<pre class="aad">';
            foreach( $args as $a=>$arg ) {
                echo htmlentities( print_r($arg,1) );
                if( $a != count($args) -1 ) echo ' ';   
            }
            echo "</pre>\n";
        }
    }
    session_write_close();
}