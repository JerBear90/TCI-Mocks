<?php
class wp_theme_options {
	function __construct($options='') {
		$posts = get_posts( [
			'post_type' => 'page',
			'posts_per_page' => -1,
		]);
		foreach( $posts as $post ) {
			$id = $post->ID;
			$pages[$id] = $post->post_title;
		}
		// Build our options array into a proper form array
		if( !$options ) {
			$options = apply_filters( 'theme_options', array() );
		}
		
		// Process each element of our array as a group
		foreach( $options as $g=>$group ) if( is_array($group) ) {
			$title = @$group['title'];
			
			// Set each group to be a feildset, for the form class to understand
			$options[$g]['type'] = 'fieldset';
			
			// Set the fieldset id to the group id
			$options[$g]['id'] = $g;
			
			// Process each option in group if is array
			foreach( $group as $o=>$option ) if( is_array($option) ) {
				// if it has a value already skip it
				if( isset($option['value']) ) continue;
				// get Standard Value for option
				$std = @$option['std'];
				
				// Get option, usind $std as default
	
				if( isset($option['fields'] ) ) {
					foreach( $option['fields'] as $f=>&$field ) {
						$std = @$field['std'];
						// Unset false checkboxes
						if( @$field['type'] == 'checkbox' && (string)@$val == 'false' ) {
							unset($field['value']);
							continue;
						}
						if( @$field['type'] === 'page' ) {
							$field['type'] = 'select';
							$field['placeholder'] = '--';
							$field['options'] = $pages;
						}
						// Otherwise, Set it in our options aray
						$field['value'] = g($f);
					}
				}
				// Unset false checkboxes
				if( @$option['type'] == 'checkbox' && (string)@$val == 'false' ) {
					unset($options[$g][$o]['value']);
					continue;
				}
				if( @$option['type'] === 'page' ) {
					$option['type'] = 'select';
					$option['placeholder'] = '--';
					$option['options'] = $pages;
				}
				
				// Otherwise, Set it in our options aray
				$option['value'] = g($o);
				$options[$g][$o] = $option;
			}
		}
		// die;
		// Set options to object
		
		$this->options = $options;
	}

	function fieldsets() {
		$fieldsets = [];
		$options = $this->options;
		foreach( $options as $g=>$group ) {
			$fieldset = ['type'=>'fieldset','id' => $g];
			if( is_array($group) ) foreach( $group as $o=>$option ) {
				if( is_array($option) ) $fieldset['fields'][$o] = $option;
				else $fieldset[$o] = $option;

				// if( $o === 'title' ) $fieldset['legend'] = $option;
			} else {
				d($group);
			}
			$fieldsets[$g] = $fieldset;
		}
		return $fieldsets;
	}
	
	// Save Options
	function save($redirect=true) {
		header("Content-Type: text/html;charset=UTF-8");
		// Loop through groups & options
		foreach($this->options as $g => $group) {

			if( is_array($group) ) foreach($group as $o=>$option) {


				// clear option values array (used for multi items)
				$option_values = array();
				// Skip if this isn't an option array
				if( ! is_array( $option ) ) continue;
				// Clean our value
				$val = @$_REQUEST[$o];
				if( is_string($val ) ) {
					$val = stripslashes(trim($_REQUEST[$o]));
					$val = str_replace( array( '\\"', "\\'" ), array( '"', "'" ), $val );
				}
				
				if( @$option['type'] == 'fieldset' ) {
					foreach( $option['fields'] as $f=>&$field ) {
						// d('f:',$f,$_REQUEST[$f]);
						update_option( $f, $_REQUEST[$f] );
					}
				} elseif( @$option['type'] == 'checkboxes' || @$option['type'] == 'radio-group' ) {
					// Get all values from checkboxes
					foreach( (array)$val as $k=>$onoff ) {
						$values[] = $k;
					}
					$val = implode( (array)$values, ',' );
				} elseif( @trim((string)$val) == @$option['std'] ) {
					// if it has standard value, delete the option
					delete_option( $o );
					if( @$option['type'] == 'image' ) {
						delete_option( $o.'_id' );
						delete_option( $o.'_x' );
						delete_option( $o.'_y' );
					}
					$val = apply_filters( 'save_option', $val, $o );
					continue;
				} elseif( @$option['type'] == 'checkbox' ) {
					if( !$val ) $val = 'false';
				}				
				// If it's an image, process extra information
				if( $val && @$option['type'] == 'image' ) {
					preg_match( '|(.*)-([0-9]*)x([0-9]*)\.([a-zA-Z])*|', $val, $matches );
					$size = '-'.$matches[2].'x'.$matches[3];
					$val = str_replace( $size, '', $val );
					$image_id = get_image_from_basepath( $val );
					update_option( $o.'_id', $image_id ) ;
					update_option( $o.'_x', $_REQUEST[$o.'_x'] ) ;
					update_option( $o.'_y', $_REQUEST[$o.'_y'] ) ;
					if( $option['caption'] && $option['title'] ) {
						$my_post = array(
							'ID' => $image_id,
							'post_title' => reverse_escape( $_REQUEST[$o.'_title'] ),
							'post_excerpt' => reverse_escape( $_REQUEST[$o.'_caption'] )
						);
						wp_update_post( $my_post );
					}
				}
				// Set to a "space" if it's empty.  This way we can save an empty option.
				// Need to do this because if option is empty, it won't be added to the database, and will be overriden by the default
				if( !$val ) $val = ' ';
				$val = apply_filters( 'save_option', $val, $o );
				if( !is_array($val) ) {
					//$val = (string) utf8_encode($val);
					$val = (string)$val;
				}
				// If it's not the standard value, save it.
				if( $val != @$option['std'] ) {
					if( !update_option( $o, maybe_serialize($val) ) ) 
					// d($o,$val);
						add_option( $o, maybe_serialize($val) );
				}
			}
		}
		// Do Something Else
		do_action( 'save_options', $this->options );
		// if( is_devel() ) die;
		// redirect
		if( $redirect ) {
			header("Location: admin.php?page={$_GET['page']}&msg=saved");
			die();
		}
		d("SAVED");
		die;
	}
	
	// Reset Options
	function reset() {
		foreach($this->options as $group)  {
			foreach($group as $o=>$option) {
				// Keep if the "no_reset" flag is present
				if( !$option['no_reset'] ) {
					// Delete it
					delete_option( $o );
					
					// If option, delete others
					if( $option['type'] == 'image' ) {
						delete_option( $o.'_x' );
						delete_option( $o.'_y' );
						delete_option( $o.'_id' );
					}
				}
			}
		}
		// Do something more
		do_action( 'options_reset' );
		
		// Reload the page with our message
		header("Location: themes.php?page={$_GET['page']}&msg=reset");
		die;
	}
} ?>
