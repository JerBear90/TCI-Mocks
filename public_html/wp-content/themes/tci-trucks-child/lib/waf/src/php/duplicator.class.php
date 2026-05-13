<?php
class duplicator extends field {
    function __construct($data,$k,$form,$parent=null) {
        $this->fields = [];
        $this->context = [];
        // d('create duplicator',$k);
        if( !@$data['value'] ) $data['value'] = [[]];
        else $data['value'] = (array)$data['value'];

        $data['type'] = 'fieldset';
        $id = @$data['id'];
        $values = $data['value'];
        unset($data['value']);
        foreach( $values as $index=>$value ) {
            if( is_array($data['fields']) ) foreach( $data['fields'] as $d=>$datum ) {
                // d('value:',$value,'field:',$d,'field value:',$value[$d]);
                if( is_array($value) ) {
                    if( $data['fields'][$d]['type'] == 'info' ) {
                        if( !@$data['fields'][$d]['info'] ) 
                            $data['fields'][$d]['info'] = @$value[$d];
                    }
                    $data['fields'][$d]['value'] = @$value[$d];
                }
                // d('field:',$d,$data['fields'][$d]);
                // else d('value',$value,$d);
            }
            $data['duplicator_fieldset'] = true;
            unset($data['name']);
            if( @$id ) $data['id'] = $id.'-'.$index;
            $this->fields[] = new fieldset( $data, $index, $form, $this );
        }

        $this->context = array_merge( $form->args, [
            'id' => @$data['id'],
            'class' => @$data['class'],
            'classes' => @$data['classes'],
            'desc' => @$data['desc'],
            'bdClass' => @$data['bdClass'],
            'legend' => @$data['legend'],
            'label' => @$data['label']
        ]);
        if( $parent ) $this->parent = $parent;
        parent::__construct($data,$k,$form);
    }
    
    function getContainer() {
        $container = @$this->context['container'] ? $this->context['container'] : 'duplicator';
        $custom = $this->form->getTemplatePath( $container, 'containers' );
        // d('custom',$container);
        return file_exists($custom)  ? $container : 'duplicator';
    }

    function render() {
        global $handlebars;
        // d('contextual options',$this->context->options);
        // d($this->input->context);
        $this->getDataAttr();
        $context = $this->context;
        $fieldset = [];
        foreach( $this->fields as $f=>$field ) {
            $field->context['container'] = 'fieldset';
            
            $fieldsets[] = [
                'html' => $field->render(),
                'index' => $f+1,
                'id' => @$field->context['id'],
                'label' => @$field->context['label'],
                'legend' => @$field->context['legend'],
                'title' => @$field->context['title']
            ];
            // d($field->context['name'],$field->render());
        }   
        $context['fieldsets'] = $fieldsets;
        $html = $handlebars->render( $this->getTemplate(), $context );
        return $html;   
    }
}