<?php
// Initialize meta boxes
function metabox_init() {
	global $metaboxes;
	// QUery style
	
	wp_enqueue_style( 'metaboxes', WAFURL . '/extras/metaboxes/metaboxes.css' );

	// get current post type
	$id = @$_GET['post'];
	$ptype = $id ? get_post_type( $id ) : @$_GET['post_type'];
	if( !$ptype ) $ptype = 'post';
	
	// Process meta boxes for current post type
	if( is_array( @$metaboxes[$ptype] ) ) {

		// Process each meta item as possible meta-box
		foreach( $metaboxes[$ptype] as $slug=>$array ) {
			// if type matches, process metabox as key/datum
			if( $array['type'] == 'meta-box' || $array['type'] == 'metabox' ) {
				// Variables
				$position = $array['position'] ? $array['position'] : 'side';
				$priority = $array['priority'] ? $array['priority'] : 'high';
				$title = @$array['title'];
				if( !$title ) $title = $slug;
				
				// callback args
				$callback_args = array(
					'title' => $title,
					'slug' => $slug,
					'ptype' => $ptype
				);
				
				// Create meta box
				// d("add metabox",$slug,$title,$ptype);
				add_meta_box( $slug, $title, 'metaboxes_setup', $ptype, $position, $priority, $callback_args );
			}
		}
	}
}
add_action( 'admin_init', 'metabox_init', 999 );

// Setup metabox
function metaboxes_setup($post,$d) {
	global $metaboxes, $nonced;
	extract( $d['args'] );

	// Get meta formula & post meta	
	$meta = $metaboxes[$ptype][$slug];
	$post_meta = get_post_custom( $post->ID );
	// update meta with values
	
	foreach ( $meta as $f=>$field ) {
		if( !is_array($field) ) continue;
		if( $f == 'title' || $f == 'type' ) continue;
		$key = 'metaboxes_'.$ptype.'_'.$slug.'_'.$f;
		if( @$field['type'] == 'fieldset' || @$field['type'] == 'duplicator' ) {	
			if( @$post_meta[$f] ) $value = $post_meta[$f];
			else $value = @$field['std'];
			if( is_array($value) ) {
				foreach( $value as &$vv ) $vv = maybe_unserialize( $vv );
			}
			$meta[$f]['value'] = apply_filters( 'metabox_field_value', $value, $f ); 
		} else {
			
			if( is_array( @$post_meta[$f] ) && count(@$post_meta[$f]) > 1 ) $value = $post_meta[$f];
			elseif(  is_array(@$post_meta[$f]) )  $value = $post_meta[$f][0];
			else if( @$post_meta[$f] ) $value = $post_meta[$f];
			else $value = @$field['std'];
			$value = maybe_unserialize($value);
			
			// d('field',$f,'value',$value);
			if( is_array($meta) ) $meta[$f]['value'] = apply_filters( 'metabox_field_value', $value, $f );
		}

		if( @$field['type'] == 'file' && is_numeric( $value ) ) {
			$value = get_attached_file( $value );
			$meta[$f]['value'] = basename( $value );
		}
	}
	
	// echo the form
	if( $meta ) {
		echo '<div class="metaboxes-metabox">';
		$meta['submit'] = false;
		 the_form( $meta, array( 'container' => 'none', 'ajax' => false ) );
		if( !@$nonced ) {
			wp_nonce_field( 'metaboxes_noncename', 'metaboxes_noncename' );
			@$nonced = 1;
		}
		echo '</div>';
	}
}

// Save metabox
function metaboxes_save($post_id)  {
	// Get meta formula set during init
	global $metaboxes;
	// Get post type & existing post meta
	$ptype = get_post_type( $post_id );
	if( $ptype == 'revision' ) return $post_id;
	$post_meta = get_post_custom( $post_id );
	
	// authentication checks
	// make sure data came from our meta box
	if (!wp_verify_nonce(@$_POST['metaboxes_noncename'], 'metaboxes_noncename' )) {
		return $post_id;
	}
	// check user permissions
	// Check for user authorization
	if (@$_POST['post_type'] == 'page') {
		if (!current_user_can('edit_page', $post_id)) return $post_id;
	} else {
		if (!current_user_can('edit_post', $post_id)) return $post_id;
	}
	
	// process meta
	if( is_array($metaboxes[$ptype]) ) {
		foreach( $metaboxes[$ptype] as $m=>$meta ) {
			// d($_POST);
			foreach((array)$meta as $f=>$field) {
				// d($f);
				if( !is_array($field) ) continue;
				if( @$field['type'] != 'file' ) delete_post_meta( $post_id, $f );
				// process files
				if ( $tmp_name = @$_FILES[$f]['tmp_name'][0] ) {
					if ( $field['type'] == 'file' && $tmp_name != '' &&  $tmp_name != 'none' ) {
						foreach( $post_meta[$f] as $attachment_id ) {
							wp_delete_attachment( $attachment_id );
						}
						delete_post_meta( $post_id, $f );
						$_POST[$f] = save_attachment( $_FILES[$f], $post_id );
					}
				}
				// the rest
				// d($_POST);
				$val = @$_POST[$f];
				
					// d($post_id,$f,$val);
				do_action( 'save_metabox_field', $post_id, $f, $val );
				$val = apply_filters( 'metabox_field_save_value', $val, $f, $post_id );
				
				if( is_array($val) && !isAssoc($val) ) {
					foreach( $val as $value ) {
						$not_empty = false;
						if( is_array($value) ) foreach( $value as $v ) if( @trim($v) ) $not_empty = true;
						if( $not_empty ) {
							
							add_post_meta($post_id,$f,$value);
						}
					}
				} elseif( $val ) {
					// d("SAVE",$post_id,'key',$f,'value',$val);
					add_post_meta($post_id,$f,$val);
				} elseif( @$post_meta[$f] && @$post_meta[$f]['type'] == 'checkbox' ) {
					add_post_meta($post_id,$f,$val,TRUE);
				}
			}
		}
		
	}
	
	// if( is_devel() ) die;
	return $post_id;
}
add_action( 'save_post', 'metaboxes_save' );

// Change encoding type to allow for file inputs in metaboxes
function edit_form_enctype() {
	echo 'enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'edit_form_enctype');

if( !function_exists('save_attachment') ) {
	// Save image as wordpress attachment
	function save_attachment($file,$post_id,$thumb=0,$copy=false) {
		// Return if file does not exist
		if( is_string($file['tmp_name'][0]) ) if ( ! file_exists( $file['tmp_name'][0] ) ) return 'none';

		// Require wordpress file toosl
		require_once( ABSPATH . "wp-admin/includes/file.php" );
	
		// Get file type
		$file_type = $file['type'][0];
	
		// Upload dir
		$upload_dir = wp_upload_dir();
	
		// Greate unique wordpress filename
		$filename = wp_unique_filename($upload_dir['path'], $file['name'][0]);
	
		// Get file path
		$absname = $upload_dir['path'].'/'.$filename;
	
		// Get file url
		$guid = $upload_dir['url'].'/'.$filename;
	
		// Move the file
		if( $copy && $file['tmp_name'][0] ) {
			d('copy');
		} elseif( $file['tmp_name'][0]  ) {
			rename( $file['tmp_name'][0], $absname );
		}
		chmod( $absname, 0644 );
	
		// Save as attachment if file exists
		if( file_exists($absname) ) {
			// Build attachment post details
			$basename = pathinfo( $filename, PATHINFO_FILENAME );
			$title = pathinfo( $file['name'][0], PATHINFO_FILENAME );
			$attachment = array(
				'post_mime_type' => $file_type,
				'post_title' => $title,
				'post_name' => $basename,
				'post_content' => '',
				'post_status' => 'inherit',
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'guid' => $guid
			);
			// Insert attachment
			// d($attachment);
			// d($absname);
			$attach_id = wp_insert_attachment( $attachment, $absname );
		
			// Update image meta
			require_once( ABSPATH . "wp-admin/includes/image.php" );
			if ( function_exists('wp_generate_attachment_metadata_custom' ) ) {
				$attach_meta = wp_generate_attachment_metadata_custom( $attach_id, $absname );
				wp_update_attachment_metadata($attach_id, $attach_meta);
			}
		
			// Set thumbnail, if appropriate
			// d($attach_id);
			// d("DONE");
			if($thumb) set_post_thumbnail( $post_id, $attach_id );
		}
		return $attach_id;
	}
}
?>
