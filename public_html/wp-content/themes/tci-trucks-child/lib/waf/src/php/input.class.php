<?php
class input {
    function __construct($field,$form) {
        $this->form = $form;
        $this->context = $field;
        if( @$field['options'] ) $this->sanitizeOptions();
    }
    function getTemplatePath() {
        return $this->form->getTemplatePath( $this->context['type'] );    
    }
    function getTemplate() {
        $src = $this->form->getTemplate( @$this->context['type'] );    
        // d('src',$src);
        if( !$src ) {
            $src = $this->form->getTemplate( 'default' );    
        }
        return $src;
    }
    function registerPartial() {
        global $handlebars;
        // d('registerPartial',$this->context);
        
        // Attributes partial
        $attrTemplatePath = $this->form->getTemplatePath( 'attributes','helpers');
        $attrTemplate = $this->form->getTemplate( 'attributes', 'helpers' );
        
        try {
            $attributes = $handlebars->render( $attrTemplate, $this->context );
        } catch( Exception $e ) {
            d("ATTRIBUTES TEMPLATE ERROR:",$e->getMessage(),$attrTemplatePath);
        } catch( Error $e ) {
            d("ATTRIBUTES TEMPLATE ERROR:",$e->getMessage(),$attrTemplatePath);
        }
        $this->context['attributes'] = $attributes;
        
        $handlebars->registerPartial( 'attributes', $attributes );

        // Input partial
        if( @$this->context['options'] ) $this->sanitizeOptions();
        // d('register partial',$this->context['options']);
        try {
            $input = $handlebars->render( $this->getTemplate(), $this->context );
        } catch( Exception $e ) {
            d("INPUT TEMPLATE ERROR:",$e->getMessage(),$this->getTemplatePath());
        }
        $handlebars->registerPartial( 'input', @$input );

        // OuterHTML partial, if applicable
        if( @$this->context['outerhtml'] ) {
            $outerhtml = $handlebars->render( $this->context['outerhtml'], $this->context );
            $handlebars->registerPartial( 'outerhtml', $outerhtml );
        } else if( @$this->context['html'] ) {
            // HTML partial, if applicable
            $html = $handlebars->render( $this->context['html'], $this->context );
            $handlebars->registerPartial( 'html', $html );
        }
    }
    function sanitizeOptions( ) {
        $options = $this->context['options'];
        // if( !isset($this->context['placeholder']) ) $this->context['placeholder'] = '--';
        $value = @$this->context['value'];
        // if( $this->context['name'] == 'users' ) d($this->name,$this->context);
        if( is_array($options) ) foreach( $options as $o=>$option ) {
            if( is_string($option) ) {
                $v = strtolower($option) === 'other' ? '' : $o;
                $v = $o;
                $option = [ "value" => $v, "label" => $option ];
            }
                
    
            if( $value ) {
                if( in_array( $option['value'], (array)$value ) ) $option['checked'] = true;
            }
            $options[$o] = $option;
            
        }
        $this->context['options'] = $options;
        
    }
}