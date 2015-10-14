#!/usr/bin/php
<?php

require_once 'lib.php';

class Tab2xml extends Tab2processor {

    public function processDir(){
        $this->ensureDirExists($this->target);
        foreach($this->regularFiles($this->src) as $file){
            $xml = $this->exportXML($this->extractKvp($file));
	    $destination = $this->target.DIRECTORY_SEPARATOR.$file->getBasename($file->getExtension()).'xml';
	    file_put_contents($destination, $xml);
        }
    }

    public function extractKvp(SplFileInfo $file){
        $kvpLines   = array();
        $raw = file_get_contents($file->getRealPath());
        $lines = explode("\n", $raw);
        $keys = explode($this->separator, array_shift($lines));
        foreach($lines as $line){
            if(empty($line)) continue;

            $values = explode($this->separator, $line);
            $kvpLines[] = array_combine($keys, $values);
        }
        return $kvpLines;
    }

    private function cleanFieldName($dirty) {
        $parens = array(" ", "(", ")");
        $clean  = $dirty;
        $clean  = strtolower(str_replace(" ", "_", $clean));
        $clean  = str_replace("(", "-", $clean);
        $clean  = str_replace(")", "-", $clean);
        return $clean;
    }

    public function exportXML($kvplines){
	$doc = new DOMDocument();
	$root = $doc->createElement('records');
	foreach($kvplines as $kv){
	    $record = $doc->createElement('record');
	    foreach($kv as $fieldname => $value){
		try{
		    $field = $doc->createElement($this->cleanFieldName($fieldname), htmlspecialchars($value));
		    $record->appendChild($field);
		}catch(Exception $e){
		    printf("\nProblem creating element '%s' with value '%s'\n", $fieldname, $value);
		    
		    var_dump($e);
		    exit(0);
		}
	    }
	    $root->appendChild($record);
	}
	$doc->appendChild($root);
	return $doc->saveXML();
    }
}

$t2x = new Tab2xml($argv);
$t2x->processDir();