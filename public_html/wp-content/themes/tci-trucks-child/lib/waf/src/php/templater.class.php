<?php
class formTemplater {
    // Get JSON path
    static function wafDir() {
        $d = wp_upload_dir();
		$user_dir = $d['basedir'].'/waf';
		if( !@is_dir($user_dir) ) @mkdir( $user_dir );
        return $user_dir;
    }
    static function getJsonPaths( $path='' ) {
		// Default to child theme path, theme path, forms plugin "templates" path
		$paths =array(
			STYLESHEETPATH.'/waf-json/',
			TEMPLATEPATH.'/waf-json/',
			WAFPATH.'/forms'
		);
		if( $path ) $paths = [$path] + $paths;
		
		
		// Apply filters & make sure entries are unique
		if( function_exists('apply_filters') ) {
			
             $paths = apply_filters( 'waf_json_paths', $paths );
		}
		$paths = array_merge( [formTemplater::wafDir().'/json'], $paths);
		$paths = array_unique( $paths );
		
		return $paths;
	}
	
	// Get full file path based on partial filename (forms/{slug}.json)
	static function getJsonPath( $var ) {
		// Get set form template paths
		$paths = formTemplater::getJsonPaths();
		
		$form_id = str_replace( '.json', '', $var );
		$name = $form_id.'.json';

		// Check each path for file
		
		foreach( $paths as $path ) {
			$path = rtrim( $path, "/" );
			// Build the filenames
			$fname = $path.'/'.ltrim($name,"/");
			
			// If file exists, return fname
			// d($fname);
            if( file_exists($fname) ) {
				// d("FOUND",$fname);
                return $fname;
            }
		}
	}
	
	// Get directories holding form templates
	static function getTemplatePaths( $path='' ) {
		// Default to child theme path, theme path, forms plugin "templates" path
		$paths = [
			STYLESHEETPATH.'/waf-templates/',
			TEMPLATEPATH.'/waf-templates/',
			WAFPATH.'/templates'
        ];
        
        if( $path ) $paths = [$path] + $paths;
		// Apply filters & make sure entries are unique
        if( function_exists('apply_filters') ) 
            $paths = apply_filters( 'waf_template_paths', $paths );
		$paths = array_merge( [formTemplater::wafDir().'/templates'], $paths);
		// $paths = array_unique( $paths );
		// d($paths);
		return $paths;
	}
	
	// Get full file path based on partial filename (forms/{slug}.json)
	static function getTemplatePath( $name ) {
		// Get set form template paths
		$paths = formTemplater::getTemplatePaths();
        // d('paths',$paths);
        
		// Check each path for file
		foreach( $paths as $path ) {
			$path = rtrim( $path, "/" );
			// Build the filenames
			$fname = $path.'/'.ltrim($name,"/");
			// d($name,$fname);
			// If file exists, return fname
			if( file_exists($fname) ) {
				// d($fname);
				// if( $name == '/containers/fullscreen.html' ) 
				// d('FOUND path',$fname);
                return $fname;
            }
        }
        // dl();
	}
	
	// Get partial filename based on form slug
	function getTemplateFilename( $slug ) {
		$slug = str_replace( '.json', '', $slug );
		$name = 'forms/'.$slug.'.json';
		//if( $this ) return $this->get_template_path( $name );
		//else return form::get_template_path( $name );
		return formTemplater::getTemplatePath( $name );
	}
	
	// Get all possible files for template
	function getTemplatePossibilities( $name ) {
		$paths = formTemplater::getTemplatePaths();
		$files = array();
		
		// Check each path for file
		foreach( $paths as $path ) {
			// Build the filenames
			$fname = $path.'/'.$name;
			
			// If file exists, return fname
			if( file_exists($fname) ) $files[] = $fname;
		}
		return $files;
	}
	
	// Get all possible files for JSON
	function getJsonPossibilities( $name ) {
		$paths = formTemplater::getJsonPaths();
		$files = array();
		
		// Check each path for file
		foreach( $paths as $path ) {
			// Build the filenames
			$fname = $path.'/'.$name;
			
			// If file exists, return fname
			if( file_exists($fname) ) $files[] = $fname;
		}
		
		array_unique( $files );
		return $files;
	}	
}