<?php
class stripe extends input {
    function __construct($data,$form) {
        $value = [];
        if( $data['fields'] && $data['card'] ) {
            foreach( $data['fields'] as $key=>$label ) {
                $value[] = [
                    'key'=>$key, 'label' => $label, 'value' => $data['card'][$key]
                ];
                
            }
        }
        
        $data['cardDetails'] = $value;
        // d($data['context']);   
        parent::__construct($data,$form);
    }
}