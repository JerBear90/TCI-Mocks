<?php
class fieldset extends field {
    function __construct($data,$k,$form,$parent=null) {
        $this->fields = [];
        $this->context = [];
        
        if(  !@$data['name'] ) $data['name'] = $k;
        if( is_array($data['fields']) ) foreach( $data['fields'] as $d=>$datum ) {
            if( is_string($datum) ) d('datum:',$datum);
            $type = @$datum['type'] ? $datum['type'] : 'text';
            if( !@$datum['name'] ) $datum['name'] = $d;
            
            $datum = apply_filters( 'waf_'.@$type.'_field_data', $datum, $data );
            $datum = apply_filters( 'waf_field_data', $datum, $data );
            // if( $d == 'users' ) d('users:',$datum['value'],$datum);
            if( !$datum ) continue;
            if( @$datum['name'] ) $d = @$datum['name'];
            if( @$datum['type'] === 'fieldset' ) $this->fields[$d] = new fieldset($datum,$d,$form,$this);
            elseif( @$datum['type'] === 'duplicator' ) {
                $this->fields[$d] = new duplicator($datum,$d,$form,$this);
            }
            else if( is_array($datum) ) $this->fields[$d] = new field($datum,$d,$form,$this);
        }
        foreach( $data as $d=>$datum ) {
            if( is_string($datum) ) $this->context[$d] = $datum;
        }
        if( $parent ) $this->parent = $parent;
        parent::__construct($data,$k,$form);
    }
    function registerPartial() {
        global $handlebars;
        $html = '';
        foreach( $this->fields as $field ) {
            if( @$this->context['fieldClass'] ) {
                @$field->context['class'] .= $this->context['fieldClass'];
            }
            if( is_object($field) ) $html .= $field->render();
            else d('no object: '.$field);
        }
        $handlebars->registerPartial( 'fields', $html );
    }
    function render() {
        global $handlebars;
        $this->registerPartial();
        $this->getDataAttr();

        $context = array_merge( $this->form->args, ['id'=>'','class'=>''],$this->context );
        // d('fieldset',$context);
        return $handlebars->render( $this->getTemplate(), $context );
    }
}