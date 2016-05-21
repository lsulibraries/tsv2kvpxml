<?php
require_once 'Delim2xml.php';

class MIKDelim2xml extends Delim2xml {
    protected function mapKeys($raw_keys, $keyMap) {
        $keys = array();
        foreach($raw_keys as $key){
            if(array_key_exists($key,$keyMap)){
                $keys[] = $keyMap[$key];
            }
            else{
                $keys[] = $this->sanitizeFieldName($key) ;
            }
        }
        return $keys;
    }
}