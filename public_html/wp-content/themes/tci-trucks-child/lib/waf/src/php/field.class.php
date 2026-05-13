<?php
// function get_field( $data ) {
//     $f = $data['name'];
    
//     $form = new form( [$f=>$data] );
//     $field = $form->fields[$f];
//     return $field;
// }
// function the_field($data) {
//     $field = get_field($data);
//     // d($field);
//     echo $field->render();
// }
class field{
    function __construct($data,$k,$form,$parent=null) {
        if( !@$data['name'] ) $data['name'] = $k;
        else $k = @$data['name'];
        
        $this->name = $k;
        $this->form = $form;
        if( !@$data['value'] && $callback = $form->args['callback'] ) {
            if( function_exists($callback) )
                $data['value'] = call_user_func( $callback, $k );
            // else d("CALLBACK FUNCTION DOES NOT EXIST");
        }
        if( @$data['text'] && @$data['type'] == 'submit' )
            $data['value'] = $data['text'];
        $this->context = $data;
        if( $parent ) $this->parent = $parent;
        // $this->getDataAttr();
        $this->input = $this->getInput($data,$form);
    }
    function getNameAttr() {
        if( @$this->context['nameAttr'] ) return $this->context['nameAttr'];
        $name =  waf_path_to_name( $this->getPath( true ) );
        if( @$this->context['multiple'] ) $name .= '[]';
        // d('get name',$this->getPath(true),$name);
        return $name;
    }
    function getPath( $skipFieldsets=false ) {
        $path = [$this->context['name']];
        $parent = @$this->parent;
        while( $parent ) {
            if( $parent instanceof Fieldset && !is_numeric($parent->name) && $skipFieldsets ) {
                if( @$parent->context['useInPath'] ) $path[] = $parent->name;
            }
            else {
                // d($parent->name);
                $path[] = $parent->name;
            }
            // d($parent);
            $parent = @$parent->parent;
        }
        if( @$this->form->args['useInPath']) $path[] = $this->form->args['path'] ? $this->form->args['path'] : $this->form->name;
        $path = array_reverse( $path );
        

        $path = implode( '.', $path );
        // dl();
        
        return $path;
    }
    function getInput($data,$form) {
        $type = @$this->context['type'];
        
        
        $file = $this->form->getTemplatePath( $type, 'classes', 'php' );
        if( file_exists($file) ) {
            // d("include file",$file);
            require_once $file;
            return new $type($data,$form);
        }
        return new input($data,$form);
    }
    function getContainer() {
        $container = @$this->context['container'] ? $this->context['container'] : @$this->context['type'];
        
        $custom = $this->form->getTemplatePath( $container, 'containers' );
        // d('custom',$container);
        return file_exists($custom)  ? $container : 'field';
    }
    function getTemplate() {
        $src = $this->form->getTemplate( $this->getContainer(), 'containers' );    
        
        if( !$src ) $src = '{{> input}}';
        return $src;
    }
    function render() {
        global $handlebars;
        if( $this->input ) {
            // d($this->input->context);
            $this->getDataAttr();
        }
        // $context = array_merge( $this->form->args, $this->context );
        $context = $this->context;
        $context['name'] = $this->name;
        $context['nameAttr'] = $this->getNameAttr();
        $context['showStar'] = $this->form->args['showStar'];
        
        $this->input->context = $context;
        $this->input->registerPartial();
        $this->context['invalid'] = false;
        // d($context);
        // d('contextual options',$this->context->options);
        // d($this->input->context);
        
        
        
        try {
            $html = $handlebars->render( $this->getTemplate(), $context );
        } catch( Exception $e ) {
            d("FIELD RENDER ERROR:",$e->getMessage(),$this->getContainer());
        }
        return $html;   
    }

    function getDataAttr(  ) {
        $data = @$this->context['data'];
        $data['path'] = $this->getPath();
        $conditions = @$this->context['conditions'];
        $dataAttr = getDataAttr( $this->name, $data, $conditions );
		
        $this->data = $data;
        $this->context['dataAttr'] = $dataAttr;
        return $dataAttr;
	}
}
?>