<?php
// Get options.list.php file

include 'options.class.php';
include dirname(__FILE__).'/admin.php';

// Init functions
function options_init() {
	// Include theme options file(s)	
	$template_path = apply_filters( 'template_path', get_template_directory() );
	$stylesheet_path = apply_filters( 'stylesheet_path', get_stylesheet_directory() );

	$template_options = $template_path.'/options.list.php';

	if ( file_exists( $template_options ) ) include_once($template_options);
	if ( file_exists( $stylesheet_path.'/options.list.php' ) && $template_path != $stylesheet_path) 
		include_once $stylesheet_path.'/options.list.php';
}
add_action( 'init', 'options_init', 2 );

// Load theme options into globals
function load_theme_options( $options='' ) {
	global $javascripts;
	if( !$options ) {
		$options = apply_filters( 'theme_options', @$GLOBALS['options'] );
	}

	$javascripts = array();
	//echo "<h1>load</h1>";
	if( is_array($options) ) foreach ($options as $g=>$group) {
		if( is_array($group) ) foreach($group as $o=>$option) if(is_array($option)) {
			$val = trim( get_option( $o, @$option['std'] ) );
			if( @$option['type'] == 'image' ) {
				$x = $o.'_x';
				$y = $o.'_y';
				$GLOBALS[$x] = get_option($x, $option['width']);
				$GLOBALS[$y] = get_option($y, $option['height']);
			} elseif( @$option['type'] == 'checkbox' && $val == 'false' ) {
				unset($GLOBALS[$o]);
			}

			if( @is_array($option['fields'] ) ) {
				foreach( $option['fields'] as $f=>&$field ) {
					$val = get_option( $f );
					$GLOBALS[$f] = maybe_unserialize($val);
				}
			}
			$GLOBALS[$o] = maybe_unserialize($val);
			// d("$o: ".get_option($o).", ".g($o)."<br/>\n");
			
			// Process javascript options
			if( $js = @$option['json'] ) {
				$val = maybe_unserialize( $val );
				if( is_array($val) && $option['type'] == 'multi') { 
					foreach( $val as $i=>$item ) {
						foreach( $item as $v=>$value ) {
							if( $value == 'false' ) $val[$i][$v] = 0;
						}
					}
				}
				$script = $option['script'];
				$javascripts[$script][$js][$o] = $val;
			}
		}
	}
}
add_action( 'init', 'load_theme_options' );

// Get global
function g($var,$default='') {
	$value = @$GLOBALS[$var] ? $GLOBALS[$var] : $default;
	return $value;
}

// Debug global
function dg($var) {
	d(g($var));
}

// Echo global
function o($var) {
	echo g($var);
}

// JS options
function load_js_options() {
	global $javascripts;
	if( is_array($javascripts) ) foreach( $javascripts as $s=>$script ) {
		foreach( $script as $js=>$params ) {
			if( is_array($params) ) $params = json_encode($params);
			wp_localize_script( $s, $js, $params );
		}
	}
}
add_action( 'init', 'load_js_options', 999 );


function replace_text_in_option_thickbox($translated_text, $source_text, $domain) {
	if('Insert into Post' == $source_text && $_REQUEST['_button_label'])
		return $_REQUEST['_button_label'];
	return $translated_text;
}



function my_options_scripts() {
	$pages = explode( '?', basename($_SERVER['REQUEST_URI'] ) );
	$page = $pages[0];
	if ( @$_GET['page'] == basename('options') ) {
		load_golden_tb();
	}
	global $include_url;
}
add_action( 'admin_init', 'my_options_scripts' );

// Load golden thickbox scripts for image upload buttons
function load_golden_tb() {
	// wp_enqueue_script('media-upload');
	// wp_enqueue_script('thickbox');
	wp_enqueue_script( 'hashchange', WAFURL . '/extras/options/js/jquery.ba-hashchange.js?v=2', array( 'jquery') );
	// wp_enqueue_script( 'tabs', plugins_url( 'js/tabs.js?v=2', __FILE__ ), 
							// array( 'jquery', 'hashchange') );
	wp_enqueue_script('options-admin', WAFURL . '/extras/options/js/admin.js?v=3',
							array('jquery','media-upload','thickbox','hashchange') );
	wp_enqueue_style('thickbox');
}

/* = Tweaks
 ======================== */

// Css option
function options_css() {
	reverse_escape($GLOBALS['_css']);
}
add_action( '_css', 'options_css' );

function option_js() {
	reverse_escape($GLOBALS['_js']);
}
add_action( '_js', 'option_js' );

function options_jquery() {
	reverse_escape($GLOBALS['_jquery']);
}
add_action( '_jquery', 'options_jquery' );

add_action( 'stylesheet_scripts', 'options_script' );
function options_php() {
	global $_no_hacks;
	$parts = explode( '/', trim( $_SERVER['REQUEST_URI'], '/' ) );
	$c = count( $parts );
	list($basename) =  explode( '?', basename($parts[$c-1]) );
	if(	$_GET['nohacks'] || $_no_hacks	) $no_hacks = 1;
	if ( ! $no_hacks ) {
		global $_php;
		eval('?>' . reverse_escape($_php) . '<?php '); 
	}
}

function reverse_escape($str) {
  $search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
  $replace=array("\\","\0","\n","\r","\x1a","'",'"');
  return str_replace($search,$replace,$str);
}

// Post type options
function post_type_options( $options='' ) {
	$post_types=get_post_types( array( 'public'   => true, '_builtin' => false ), 'objects', 'and' );
	foreach ($post_types  as $post_type ) {
		$options['pages']["_{$post_type->name}_page"] = array (
			"name" 	=> $post_type->label.' Page',
			'desc' 	=> 'Page showing list of '.strtolower($post_type->labels->name),
			"type" 	=> "page-id",
		);
		$options['pages']["_{$post_type->name}_per_page"] = array (
			"name" 	=> $post_type->label.' Per Page',
			'desc' 	=> 'Number of '.$post_type->labels->name.' to show per page',
			"type" 	=> "text",
			"std" => 10
		);
	}
	return $options;
}

//add_filter( 'theme_options', 'post_type_options' );

// Get an session variable
function s($k,$std='') {
	$val = $_SESSION[$k];
	return $val ? $val : $std;
}
function ss($k,$val) {
	$_SESSION[$k] = $val;
	return $val;
}

?>
