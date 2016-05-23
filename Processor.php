<?php
require_once 'Delim2xml.php';

/**
 * Simple Processor class that takes a single file given as CLI arg
 * Echoes the result of transformation to xml.
 */
class Processor {
    
    public $minArgs = 1;
    public $d2x;
    protected $options;
    public $delimited;
    
    public function __construct($options = array()) {
        $this->options = $options;
        $this->initialize($options);
        $delimiter = $this->lookupSeparator($this->getOpt('delimiter'));
        $Delim2xmlClass = $this->getOpt('Delim2xmlClass');
        $this->d2x = new $Delim2xmlClass($delimiter);
    }

    protected function initialize(){
        $file = $this->getOpt('input_file');
        if(!file_exists($file)){
            throw new Exception(sprintf("Input file %s does not exist.", $file));
        }else{
            $this->delimited = file_get_contents($file);
        }
    }

    public function usage(){
        $msg  = "";
        $msg .= sprintf("Unexpected arguments.\n");
        $msg .= sprintf("Usage:\n\tphp %s delimited-file [-d separator]\n\n", 'd2x.php');
        $msg .= sprintf("\t-d [comma | pipe | tab]       (defaults to comma)\n\n");
        print $msg;
        exit(1);
    }
    protected function getOpt($key){
        if(array_key_exists($key, $this->options)){
            return $this->options[$key];
        }
        return false;
    }

    protected function lookupSeparator($key = null) {
        switch ($key) {
            case 'pipe':
                $key = '|';
                break;
            case 'comma':
                $key = ",";
                break;
            default:
                $key = "\t";
                break;
        }
        return $key;
    }

    public function process(){
        echo $this->d2x->getXML($this->delimited);
    }
}