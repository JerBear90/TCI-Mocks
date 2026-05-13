<?php
function getDataAttr( $name='', $data=[], $conditions=[] ) {
    // d('name:',$name);
    // d($data);
    $dataAttr = '';
    if( !isset($data['name']) ) $dataAttr = "data-name=\"{$name}\"";
    if( is_array($data) ) {
        foreach( $data as $attr=>$value ) {
            if( is_string($value) ) $dataAttr .= " data-$attr=\"$value\"";
        }
    }
    // d($dataAttr);
    // Conditions
    if( is_array($conditions) ) {
        $dataAttr .= " data-conditions=\"true\"";
        foreach( $conditions as $attr=>$value ) {
            $dataAttr .= " data-condition-$attr=\"$value\"";
        }
    }
    // d($dataAttr);
    // dl();
    return $dataAttr;
}