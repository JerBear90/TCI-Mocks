<?php
// Add Menu Item
function options_add_admin() {
	$options = apply_filters( 'theme_options', array() );
	if( !defined('OPTIONS_TITLE') ) define( 'OPTIONS_TITLE', 'Options' );
	add_menu_page( OPTIONS_TITLE, OPTIONS_TITLE,  'manage_options', 'options', 'options_admin', 
		WAFURL . '/extras/options/golden.png' );
	foreach( $options as $slug=>$tab ) {
		$title = @$tab['title'];
		add_submenu_page( 'options', $title, $title, 'manage_options', 'options#'.$slug, 'options_admin' );
	}
}
add_action('admin_menu', 'options_add_admin');

// Save options
function options_admin_init() {
	// Save or reset if necessary
	$page = @$_GET['page'];
	if ( $page == 'options' || $page == 'hacks' ) {
		$options = new wp_theme_options();
		if ( @$_REQUEST['save_options'] ) {
			$options->save();
 		} elseif( @$_REQUEST['reset_options'] ) {
 			$options->reset();
 		} elseif( $msg = @$_REQUEST['msg'] ) {
	 		add_action('admin_messages','options_'.$msg);
		}
		wp_enqueue_style('waf-bootstrap');
		wp_enqueue_style('waf-fa');
		// wp_enqueue_script('waf');
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
}
add_action( 'admin_init','options_admin_init' );

// Options saved Message
function options_saved() { ?>
	<div class='success'>Options Saved!</div>
	<?php
}

// Options Reset Message
function options_reset() { ?>
	<div class='success'>Options Reset!</div>
	<?php
}

// The Css for the options page
function load_options_style() {
	global $include_url;
	wp_enqueue_style( 'options', WAFURL . '/extras/options/options.css?v=3' );
	// wp_enqueue_style( 'tabs', plugins_url( 'tabs.css?v=3', __FILE__ ) );
}
add_action( 'admin_init', 'load_options_style' );

// Little bit of jquery DEPENDS on the 'admin_js' action being used
function options_admin_jquery() { ?>
	$("input[name='reset']").click(function() {
		return confirm( 'Are you sure you want to do that?' );
	});
	<?php
}
add_action( 'admin_jquery', 'options_admin_jquery' );

// Use admin container on fields
function waf_admin_fields( $data ) {
	if( is_array($data) ) foreach( $data as $d=>&$datum ) {
		if( $d == 'args' ) continue;
		if( !is_array($datum) ) continue;
		
		if( @$datum['type'] == 'fieldset' ) {
			// d($datum);
			if( !@isset($datum['container']) ) @$datum['container'] = 'admin-table-fieldset';
			@$datum['fields'] = waf_admin_fields( $datum['fields'] );
		}
		elseif( @$datum['type'] == 'checkbox' ) {
			if( !@isset($datum['container']) ) $datum['container'] = 'admin-table-checkbox';
			$datum['side'] = 'right';
		}
		elseif( @$datum['type'] == 'submit' ) continue;
		elseif( !@isset($datum['container']) )  $datum['container'] = 'admin-table-field';
	}
	// d($data);
	return $data;
}

// The admin page
// Requires the Golden Form component
function options_admin() { 
	// TODO :THIS SHOULD SIMPLY BE A GLOBAL VAR
	$theme_options = apply_filters( 'theme_options', array() );
	$options = new wp_theme_options($theme_options);
	add_filter( 'waf_form_data', 'waf_admin_fields' );

	// The Buttons
	$buttons =  array(
		'type' => 'fieldset',
		'title' => 'Save/Reset',
		'class' => 'save-reset',
		'fields' => [
			'save_options' => array(
				'inputClass' => 'button button-primary save-button',
				'type' => 'submit',
				'value' => __( 'Save Changes', 'options' )
			)
		]
		/*
		'reset_options' => array(
			'type' => 'submit',
			'value' => 'Reset',
		),
		*/
	);
	
	// Set "top" fieldset to buttons, with id of "top"
	$args = array(
		'ajax' => false,
		'class' => 'options',
		'id' => 'options',
		'form' => 'options',
		'method' => 'post',
		'enctype' => 'multipart/form-data',
		'url' => 'admin.php?page=options&save_options=true',
		'action' => '',
		'edit' => false,
		'format' => 'formData'
	);
	$option_forms['submit'] = false;
	// $options_form['top'] = $buttons;
	$options_form['top'] = ['type'=>'fieldset'];
	$options_form['top']['id'] = 'top';
	
	// Add in the form
	$options_form = array_merge( $options_form, $options->fieldsets() );
	
	// Repeat buttons on bottom with id of "bottom"
	$options_form['bottom'] = $buttons;
	$options_form['bottom']['id'] = 'bottom';
	$options_form['submit'] = array();
	$options_form['save_options'] = [
		'type' => 'hidden',
		'value' => true
	];
	
	$options_form['submit'] = false;
	// Set current path & create from using options.xsl file
	$form = new form( $options_form, $args );
	// Start the output?>
	
	<!-- BEGIN OPTIONS ADMIN PAGE -->
	<div id="icon-options-general" class="icon32"></div><h2><?php echo @$themename; ?> <?php echo OPTIONS_TITLE; ?></h2>
	
	<!-- Messages, for save/rest -->
	<div id="messages" class="fade">
		<?php do_action('admin_messages'); ?>
	</div>
	
	<!-- loop through options group, create a form part for each -->
	<div id="options">
		<!-- tabbed links to groups -->
		<div class="tabs nav-tab-wrapper">
			<?php $i = 0; ?>
			<?php foreach( $options->fieldsets() as $f=>$fieldset) : $i++; ?>
				<a class="nav-tab" href="#<?php echo $f; ?>" tabindex="<?php echo $i; ?>"><?php echo $fieldset['title']; ?></a>
			<?php endforeach; ?>
		</div>
		
		<!-- output our form -->
		<?php // d($form); ?>
		<?php echo $form->render(); ?>
		<?php remove_filter( 'waf_form_data', 'waf_admin_fields' ); ?>
	</div>
	<!-- END OPTIONS ADMIN PAGE -- >
	<?php
}

function options_noajax() { ?>
	$('form.options').removeClass('ajax');
	<?php
}
add_action( 'admin_jquery', 'options_noajax' );

function admin_js() { ?>
	<script>
	<?php do_action( 'admin_js' ); ?>
	jQuery(document).ready( function($) {
		if( $.fn.wpColorPicker ) $('#options .color input').wpColorPicker();
		$('*[type=submit]').removeAttr('disabled');
		<?php do_action( 'admin_jquery' ); ?>
	});
	</script>
	<?php
}
add_action( 'admin_footer', 'admin_js' );