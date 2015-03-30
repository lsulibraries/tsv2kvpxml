<?php

class Tab2xml {

    private $args;
    private $minArgs = 2;
    public $src, $target;

    public function __construct($args){
        $this->args = $args;
        // +1 since args[0] is always there.
        if(count($args) < $this->minArgs + 1){
            print $this->usage();
	    var_dump($args);
            exit(0);
        }

        $this->src     = new SplFileInfo($args[1]);
        $this->target  = new SplFileInfo($args[2]);
    }

    private function usage(){
        $msg  = "";
        $msg .= sprintf("Not enough arguments.\n");
        $msg .= sprintf("Usage:\n\tphp %s src-dir target-dir\n\n", $this->args[0]);
        return $msg;
    }

    public function processDir(){
        $this->ensureDirExists($this->target);
        foreach($this->regularFiles($this->src) as $file){
            $xml = $this->exportXML($this->extractKvp($file));
	    $destination = $this->target.DIRECTORY_SEPARATOR.$file->getBasename($file->getExtension()).'xml';
	    file_put_contents($destination, $xml);
        }
    }

    public function regularFiles(SplFileInfo $dir){
        $files = array();
        foreach(scandir($dir) as $file){
            if(is_dir($file) || $file === '.' || $file === '..'){
                continue;
            }
            $files[] = new SplFileInfo($this->src.DIRECTORY_SEPARATOR.$file);
        }
        return $files;
    }

    public function ensureDirExists(SplFileInfo $target){
        if(!file_exists($target->getPathname())){
            printf("Creating target directory %s\n", $target->getPathname());
            return mkdir($target->getPathname());
        }
        return true;
    }

    public function extractKvp(SplFileInfo $file){
        $kvpLines   = array();
        $raw = file_get_contents($file->getRealPath());
        $lines = explode("\n", $raw);
        $keys = explode("\t", array_shift($lines));

        foreach($lines as $line){
            if(empty($line)) continue;

            $values = explode("\t", $line);
            $kvpLines[] = array_combine($keys, $values);
        }
        return $kvpLines;
    }

    public function exportXML($kvplines){
	$doc = new DOMDocument();
	$root = $doc->createElement('records');
	foreach($kvplines as $kv){
	    $record = $doc->createElement('record');
	    foreach($kv as $fieldname => $value){
		$field = $doc->createElement(strtolower(str_replace(" ", "-", $fieldname)), htmlspecialchars($value));
		$record->appendChild($field);
	    }
	    $root->appendChild($record);
	}
	$doc->appendChild($root);
	return $doc->saveXML();
    }
}

$t2x = new Tab2xml($argv);
$t2x->processDir();