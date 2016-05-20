#!/usr/bin/php
<?php

require_once 'lib.php';

class Tab2xml extends Tab2processor {
    public $alias;
    
    public function trimExt($any){
        //$pathFrag = explode( '.', $any);
        return explode('.', $any)[0];
           }
    public function processDir(){
        $this->ensureDirExists($this->target);
        foreach($this->regularFiles($this->src) as $file){
            $this->alias = $file;
            $xml = $this->exportXML($this->extractKvp($file));
            //iterate over record
            $sxml = new SimpleXMLElement($xml);
            $pathFrag = $this->trimExt($this->alias->getFilename());
            mkdir($this->target.DIRECTORY_SEPARATOR.$pathFrag, 0777, true);
            foreach($sxml->xml as $record){
                  $destination = $this->target.DIRECTORY_SEPARATOR.$pathFrag.DIRECTORY_SEPARATOR.$record->dmrecord.'.xml';
                  file_put_contents($destination, $record->asXML());
            }
        }
    }

    public function getFieldMappings(){
        $shrap = explode('.',$this->alias);
        $alias = explode('/' , $shrap[0]);
        $alias = array_pop($alias);
        $path = 'mappings'.DIRECTORY_SEPARATOR . $alias . '.json';
        
        $file = file_get_contents($path);
        $mappings = json_decode($file);
        $namesnicks = array();
        foreach($mappings as $map ){
            $namesnicks[$map->name] = $map->nick;
        }
        return $namesnicks;
    }

    
    public function extractKvp(SplFileInfo $file){
        $nickMappings = $this->getFieldMappings();
        
        $kvpLines   = array();
        $raw = file_get_contents($file->getRealPath());
        $lines = explode("\n", $raw);
        $keys = explode($this->separator, array_shift($lines));
        $nickkeys = array();
        foreach($keys as $key){
            if(array_key_exists($key,$nickMappings)){
                $nickkeys[] = $nickMappings[$key];
            }
            else{
                $nickkeys[] = $this->cleanFieldName($key) ;
            }
        }
        foreach($lines as $line){
            if(empty($line)) continue;

            $values = explode($this->separator, $line);
            $kvpLines[] = array_combine($nickkeys, $values);
        }
        return $kvpLines;
    }

    private function cleanFieldName($dirty) {
        $parens = array(" ", "(", ")");
        $clean  = $dirty;
        $clean  = strtolower(str_replace(" ", "_", $clean));
        $clean  = str_replace("(", "-", $clean);
        $clean  = str_replace(")", "-", $clean);
        $clean  = str_replace("/", "-", $clean);
        $clean  = str_replace(":", "-", $clean);
        return $clean;
    }

    public function exportXML($kvplines){
	$doc = new DOMDocument('1.0','utf-8');
	$root = $doc->createElement('records');
	foreach($kvplines as $kv){
	    $record = $doc->createElement('xml');
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