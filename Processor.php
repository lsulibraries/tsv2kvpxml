<?php
require_once 'Delim2xml.php';
class Processor {
    
    public $minArgs = 1;
    public $d2x;
    protected $options;
    public $delimited;
    
    public function __construct($options = array()) {
        $this->checkOptions($options);
        $delimiter = $this->lookupSeparator($this->getOpt('delimiter'));
        $this->d2x = new Delim2xml($delimiter);
    }

    protected function checkOptions($options){
        $this->options = $options;

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
            case 'tab':
                $key = "\t";
                break;
            default:
                $key = ',';
                break;
        }
        return $key;
    }

    public function process(){
        echo $this->d2x->getXML($this->delimited);
    }
}