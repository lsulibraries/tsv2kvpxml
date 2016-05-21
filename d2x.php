<?php
require_once 'Processor.php';



$options = getopt('', array('ini:'));
if (array_key_exists('ini',$options)){
    $options = parse_ini_file($options['ini']);
}else {
    // for running in simple cli mode
    $options['input_file'] = $argv[1];
    $options['delimiter']   = isset($argv[2]) ? $argv[2] : null;
}

$processorClass = 'Processor';
if(array_key_exists('processorClass', $options)){
    $processorClass = $options['processorClass'];
}
$processor = new $processorClass($options);

try{
    $processor->process();
}catch (Exception $e){
    echo sprintf("error while attempting to process with "
            . "processor %s\n message was:\n%s", $processor, $e->getMessage());
}