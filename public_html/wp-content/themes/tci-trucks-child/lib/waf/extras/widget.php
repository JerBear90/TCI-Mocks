<?php
/**
 * Example Widget Class
 */
class WAF_widget extends WP_Widget {
	/** constructor -- name this the same as the class above */
    function __construct( $args=[] ) {
        extract($args);
        $this->args = $args;
		if( $jsonPath ) {
            $this->form = json_decode( file_get_contents( $jsonPath ), 1 );
            if( !@$this->form['args'] ) $this->form['args'] = [];
            $this->form['args']['container'] = 'fields';
		    $this->form['submit'] = false;
        }
        parent::__construct(false, $name );
    }
  
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		return $new_instance;
		foreach( $this->form as $f=>$field ) {
			if( !is_array($field) || $f == 'args' || $f == 'submit' ) continue;
			$instance = $new_instance[$f];
		}
		return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {
        
        if( is_array($this->form ) ) {
            foreach( $this->form as $f=>&$field ) {
                if( !is_array($field) || $f == 'args' || $f == 'submit' ) continue;
                $field['value'] = @$instance[$f];
                $field['id'] = $this->get_field_id($f);
                $field['nameAttr'] = $this->get_field_name($f);
                $field['container'] = @$field['type'] == 'checkbox' ? 'admin-checkbox' : 'admin-field';
                $field['inputClass'] = 'widefat';
                
                $this->form[$f] = $field;
            }
            the_form($this->form );
        } else {
            echo '<h1>This widget has no configuration</h1>';
        }
    }
 
 
}