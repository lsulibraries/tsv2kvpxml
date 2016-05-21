<?php

class Delim2xml {

    public $delimiter, $delimited;

    public function __construct($delimiter){
        $this->delimiter = $delimiter;
    }

    protected function extractKvp($delimited){
        $kvpLines   = array();
        $lines = explode("\n", $delimited);
        $keys = explode($this->delimiter, array_shift($lines));
        foreach($lines as $line){
            if(empty($line)) continue;

            $values = explode($this->delimiter, $line);
            $kvpLines[] = array_combine($keys, $values);
        }
        return $kvpLines;
    }

    protected function sanitizeFieldName($dirty) {
        $parens = array(" ", "(", ")");
        $clean  = $dirty;
        $clean  = strtolower(str_replace(" ", "_", $clean));
        $clean  = str_replace("(", "-", $clean);
        $clean  = str_replace(")", "-", $clean);
        $clean  = str_replace("/", "-", $clean);
        $clean  = str_replace(":", "-", $clean);
        return $clean;
    }

    public function getXML($delimited){
	$doc = new DOMDocument('1.0','utf-8');
	$root = $doc->createElement('records');
	foreach($this->extractKvp($delimited) as $kv){
	    $record = $doc->createElement('xml');
	    foreach($kv as $fieldname => $value){
		try{
		    $field = $doc->createElement($this->sanitizeFieldName($fieldname), htmlspecialchars($value));
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
        $doc->formatOutput = true;
	return $doc->saveXML();
    }
}

