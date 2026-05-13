<?php
/*
 * form.class.php
 * 
 * Copyright 2017 Steve <sfraser657@gmail.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under t	 terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,s
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
// Class used to generate forms from php array or json


Handlebars\Autoloader::register();
use Handlebars\Handlebars;

class form {
    function __construct( $data='', $args=[] ) {
		
		global $handlebars;
		if( !$handlebars ) $handlebars = new Handlebars();
		$path = dirname(dirname(dirname(__FILE__)));
		$formArgs = [];
		$this->fields = [];
		$this->templater = new formTemplater();

		$defaults = array(
			'ajax' => true,						// Process submission using ajax
			'edit' => false,						// Enable form editor
			'container' => 'form', 				// Template to use for form container
			'url' => '',						// Form submit url
			'enctype' => '',					// Form encoding type (use multipart/form-data for uploads)
			'enableUploads' => false, 			// Convienence arg to set enctype to multipart/form-data to enable file uploads
			'target' => '',						// Form target
			'showColon' => true,				// Show ':' in labels,
			'showStar' => true,					// Show '*' on required fields
			'savePath' => formTemplater::wafDir(),
			'class' => '',						// Form Class,
			'id' => '',							// For CSS ID.  Use "false" for no id, "" for default
			'action' => '',				// Form action input, for ajax
			'method' => 'post',					// Form Method,
			'callback' => 'getRequestValue',	// Callback function to fill in form values
			'timeout' => 1500,					// Wait time for reloads
			'jsonPath' => $path.'/json',
			'templatePath' => $path.'/templates/',
			'loadPath' => $path.'/forms',
			'data' => [],
			'title' => '',
			'action_args' => array(), 			// Action specific settings
			'effects' => array(), 				// Form submit effects (fadeOut, clear, reset, etc.)
			'messages' => array( ),				// Messages based on form status
			'oneClick' => false,				// One click fields
			'debug' => false,
			'renderMode' => 'html',
			'path' => ''						// used if including form path in input name
		);

		// Set args from defaults & supplied in order to have jsonPath available
		$args['data'] = @array_merge( $defaults['data'], (array)$args['data'] );
		$this->args = array_merge( $defaults, $args );
		// if( $data == 'upload' ) d($this->args['data'],$args['data']);
		
		if( is_string($data) ) list($data,$formArgs) = $this->loadFromString($data);
		else {
			$formArgs = @$data['args'] ? $data['args'] : [];
			unset($data['args']);
		}
		
		// Redo args to add args provided in form JSON
		if( !@is_array($defaults['data']) ) $defaults['data'] = [];
		if( !@is_array($args['data']) ) $args['data'] = [];
		if( !@is_array($formArgs['data']) ) $formArgs['data'] = [];
		$args['data'] = @array_merge( $defaults['data'],$args['data'],$formArgs['data']);
		$args = array_merge( $defaults, $formArgs, $args );
		// d($args['data']);
		if( function_exists('apply_filters') ) {
			
			
			if( @$data['args'] ) {
				$args = array_merge( $args, $data['args'] );
				unset($data['args']);
			}
			
			$data = apply_filters( 'waf_'.@$args['form'].'_form_data', $data, $args );
			global $wp_filter;
			
			$data = apply_filters( 'waf_form_data', $data, $args );
			
			// d($data);
			// d('waf_'.$args['form'].'_form_data');
			
			// $data = apply_filters( 'wsf_'.$args['form'].'_form_data', $data, $args );
			// d('APPLY FITLERS wsf_'.$args['form'].'_form_data');
		}
	
		if( is_array($data) ) foreach( $data as $k=>$datum ) {
			if( is_string($datum) && $k === 'submit' && $datum ) {
				$this->fields[$k] = new field(['type' =>'submit', 'value' => $datum],'submit',$this );
			}
			elseif( is_array($datum) && $k != 'args' ) {
				if( @$datum['type'] === 'fieldset' ) $this->fields[$k] = new fieldset($datum,$k,$this);
				elseif( @$datum['type'] == 'duplicator' ) $this->fields[$k] = new duplicator($datum,$k,$this);
				else $this->fields[$k] = new field($datum,$k,$this);
			}
		}
        
		foreach( $this->fields as $field ) 
			if( @$field->context['type'] === 'submit' ) $submit = $field;


		if( !@$submit && @!array_key_exists('submit', $data) ) {
			$this->fields['submit'] = new field(['type' => 'submit', 'value' => 'Submit'],'submit',$this );
		}
		
		if( @$args['form'] ) $this->form = $args['form'];

		if( function_exists('apply_filters') ) {
			
			
			// d('waf_'.$args['form'].'_form_args');
			$this->args = apply_filters( 'waf_'.@$args['form'].'_form_args', $args, $data );
			$this->args = apply_filters( 'waf_form_args', $this->args, $data );
			
		}
		else $this->args = $args;
		$this->name = @$this->args['form'];

		// if( is_devel() ) $this->args['renderMode'] = 'html';
	}
	
	function renderFields( ) {
		$mode = $this->args['renderMode'];
		$html = '';
		
		$fieldlist = [];
		if( $mode === 'html' ) {
			$this->args['data']['rendered'] = true;
			$i = 0;
			foreach( $this->fields as $field ) {
				$i++;
				if( $i == 0 ) @$field->context['class'] .= ' active';
				$html .= $field->render();
				if( @$field->context['type'] != 'fieldset') continue;
				
				$fieldlist[] = [
					'index' => $i,
					'active' => $i == 1 ? true : false,
					'name' => $field->name,
					'id' => @$field->context['id'],
					'title' => @$field->context['title']
				];
			}
		}
		
		$this->fieldlist = $fieldlist;
		return $html;
	}
	function allFields($set='') {
		$data = [];
		if( !$set ) {
			if( @count($fields) ) return [];
			else {
				// d("--FIRST RUN!");
				$set = $this;
			}
		}
		foreach( $set->fields as $field ) {
			if( $field->fields ) {
				$data = array_merge( $data, $this->allFields($field) );
			}
			$data[] = $field;
		}
		
		
		// d('---return fields',count($data),'total',count($fields),$set->name);
		return $data;
	}
	public function add_values( $formdata, $values ) {
		foreach( $formdata as $d=>$datum ) {
			if( !is_array($datum) || $d == 'args' ) continue;
			if( $datum['type'] == 'fieldset' || $datum['type'] == 'duplicator' ) {
				$formdata[$d]['fields'] = form::add_values( $datum['fields'], $values );
			}
			// d($d,$values[$d]);
			if( $values[$d] ) $formdata[$d]['value'] = $values[$d];
		}
		return $formdata;
	}
	function get_form() {
		return $this->render( false );
	}
	function the_form() {
		return $this->render( true );
	}
	function render($echo=true) {
		global $handlebars;
		$fields = $this->renderFields();
		$context = $this->args;
		$context['fields'] = $this->fieldlist;
        $handlebars->registerPartial( 'fields', $fields );
		
		// d('form template',$this->getTemplate('form','containers'));
		$context['name'] = @$context['form'];
		

		$c = $context['container'] ? $context['container'] : 'form';
		
		$templatePath = $this->getTemplatePath($c,'containers');
		$template = $this->getTemplate($c,'containers');
		// d('template',$template);
		
		if( $context['renderMode'] != 'walk' ) {
			$context['data']['rendered'] = true;
		}
		try {
			$context['data']['rendered'] = true;
			$context['dataAttr'] = getDataAttr( @$this->args['form'], @$context['data'] );
			$output = $handlebars->render( $template, $context );
		} catch( Exception $e ) {
			d("[Form render] Error rendering form:",$e->getMessage(), 'using', $templatePath );
		} catch( Error $e ) {
			d("[Form render] Error rendering form:",$e->getMessage(), 'using', $templatePath );
		}

		// Get data attributes
		// d($context);
		
		if( $echo ) echo $output;
		else return $output;
	}
	
	// Load from string
	function loadFromString( $slug ) {
		$slug = str_replace( '.json', '', $slug );
		$json_path = $this->getFormJSONPath($slug);
		// d('json path',$slug,$json_path);
		// Show error message if file does not exist
		if( !file_exists($json_path) && $this->args['debug'] ) {
			return [ 
				'messages' => [
					'type' => 'html',
					'html' => '<div class="alert alert-danger">Cannot find form '.$slug.'</a>'
				],
				'submit' => false
			];
		}
		
		$src = @file_get_contents( $json_path );
		$data = json_decode( $src, 1 );
		// d('find form:',$src,$data,$slug,$json_path);
		// Show error message if no valid json found
		if( !$data && $this->args['debug'] ) {
			return [ ['submit' => ''], array(
				'messages' => array(
					[
						'status' => 'danger',
						'message' => "Invalid/missing JSON for form $slug"	
					]
				),
				'valid' => false
			) ];
		}

		
		if( isset($data['args']) ) {
			$args = $data['args'];
			unset($data['args']);
		} else $args = [];

		// Set form id arg, default css id, and default action
		$args = array_merge([
			'form' => $slug,
			'id' => 'waf_'.$slug,
			'action' => $slug
		],$args);
		return [ $data, $args ];
	}
	// Form JSON
	function getFormJSONPath( $slug ) {
		$slug = str_replace( '.json', '', $slug );
		// d('slug',$slug);
		// d(formTemplater::getJsonPath($slug));
		return formTemplater::getJsonPath($slug);
		return rtrim( $this->args['loadPath'], '/' ).'/'.$slug.'.json';
	}

	// JSON config
	function getConfigJSON( $slug, $path='' ) {
		$path = $this->getConfigJSONPath( $slug, $path );
		return json_decode( @file_get_contents($path), 1 );
	}
	function getConfigJSONPath( $slug, $path='' ) {
		$path = '/'.trim( $path, '/' ).'/';
		return rtrim( $this->args['jsonPath'], '/' ).$path.$slug.'.json';
	}

	// Templates 
	function getTemplatePath( $slug, $type='inputs', $ext='html' ) {
		$relativePath = "/$type/$slug.$ext";
	
		return formTemplater::getTemplatePath( $relativePath );
	}
	function getTemplate( $slug, $type='inputs' ) {
		$file = $this->getTemplatePath( $slug, $type );
		// d('template file',$file);
		return @file_get_contents( $file );
	}

	function verifyRecaptcha(  ) {
		// d("VERIFIY");
		@session_start();
		// d($_SESSION);
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$fields = $this->allFields();
		// d('verifying',$fields);
		foreach( $fields as $field ) {
			if( $field->context['type'] == 'recaptcha2' || $field->context['type'] == 'recaptcha3' )
				$captcha = $field;
			
		}
		// d('captcha',$captcha);
		if( !$captcha ) {
			// d('-- no captcha');
			return true;
		}
		$name = $captcha->context['name'];
		
		$response = $_REQUEST[ $name ];
		if( $expires = $_SESSION['recaptcha'][$this->name] ) {
			// d($expires);
			$expires_ts = strtotime( $expires );
			// d($expires_ts);
			// d(current_time('timestamp'));
			if( $expires_ts <= current_time('timestamp') ) {
				// d('valid!');
				return true;
			}
			else d('verification expired');
		} else if( !$response ) {
			wp_send_json( ['status' => 'warning','message' => 'Please complete recpatcha']);
		}
		
		$data = [
			'secret' => get_option( 'recaptcha_secret' ),
			'response' => $response 
		];
		session_write_close();
		d($data);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
		// curl_setopt($ch, CURLOPT_VERBOSE, true);
		// $verbose = fopen('php://temp', 'w+');
		// curl_setopt($ch, CURLOPT_STDERR, $verbose);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		
		curl_close ($ch);
		d('captcha:',$name,'Verified:',$result);

		// if ($result === FALSE) {
		// 	printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
		// 		   htmlspecialchars(curl_error($handle)));
		// }
		
		// rewind($verbose);
		// $verboseLog = stream_get_contents($verbose);
		
		// echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
		if( $result ) $result = json_decode( $result, 1 );
		if( $result['success'] ) {
			$_SESSION['recaptcha'][$this->name] = $result['challenge_ts'];
			return true;
		}
		else wp_send_json(['status'=>'danger','message' => implode( ', ', $result['error-codes']) ]);
		die;
	}
}