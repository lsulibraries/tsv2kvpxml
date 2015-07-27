<?php
require_once 'tab2xml.php';
class Tabbuilder extends Tab2xml{
    
    public $fields = array('filename');
    public $data = array();
    
    public function processDir() {
	
	
	    $this->ensureDirExists($this->target);
	    
		foreach($this->regularFiles($this->src) as $file){
		    try{
			$kvp = $this->extractKvp($file);
			$this->mergeFields($kvp, $file);
			foreach($kvp as $row){
			    $row['filename'] = $file->getFilename();
			    $this->data[] = $row;
			}
		    }catch(Exception $e){
			printf("Caught exception while processing file %s; message was: %s\n", $file, $e->message);
		    }
		}

	    $tab = $this->buildTab();
	    if(($handle = fopen($this->target.DIRECTORY_SEPARATOR.'out.txt','w')) != false){
		fputcsv($handle, $this->fields, '|');
		$rowcount = 0;
		foreach($tab as $row){
		    fputcsv($handle, $row, '|');
		    $rowcount++;
		}
		printf("Done writing %d rows into %d fields\n", $rowcount, count($this->fields));
	    }
	
    }
    
    public function mergeFields($kvp, $file){
    if(!empty($kvp)){
	$newKeys = array_keys($kvp[0]);
	$new = 0;
	foreach($newKeys as $key){
	    if(!in_array($key, $this->fields)){
		$this->fields[] = $key;
		$new++;
	    }
	}
	printf("Processing file %s - added %d new keys\n",$file->getRealPath(), $new);
    }else{
	printf("!!!! No rows found in file %s\n",$file->getRealPath());
    }
    }

    public function buildTab(){
	$tab = array();
	foreach($this->data as $row){
	    $newRow = array();
	    foreach($this->fields as $field){
		if(array_key_exists($field, $row)){
		    $newRow[$field] = $row[$field];
		}else{
		    $newRow[$field] = '';
		}
	    }
	    $tab[] = $newRow;
	}
	return $tab;
    }
}

$tb = new Tabbuilder($argv);
$tb->processDir();
