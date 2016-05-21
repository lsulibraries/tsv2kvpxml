<?php
require_once 'Processor.php';
require_once 'Delim2xml.php';

class DirectoryProcessor extends Processor {

    protected $files;
    
    protected function initialize() {
        foreach(array('input_dir', 'output_dir') as $dir){
            $directory = $this->getOpt($dir);
            if(file_exists($directory) && is_dir($directory)){
                $this->$dir = new SplFileInfo($directory);
            }else {
                throw new Exception(sprintf("Path %s does not exist or is not a directory\n", $directory));
            }
        }
        $this->files = $this->getFiles();
    }

    protected function getFiles(){
        return $this->regularFiles($this->input_dir);
    }

    public function regularFiles(SplFileInfo $dir, $mode = 'files'){
        $files = array();
        foreach(scandir($dir) as $file){
            if($file === '.' || $file === '..'){
                continue;
            }
            elseif($mode == 'file' && is_dir($file)){
                continue;
            }
            elseif($mode == 'dir' && !is_dir($file)){
                continue;
            }
            else{
                $files[] = new SplFileInfo($this->input_dir.DIRECTORY_SEPARATOR.$file);
            }
        }
        return $files;
    }

    public function ensureDirExists(SplFileInfo $target, $create = true){
        if(!file_exists($target->getPathname())){
            printf("Creating target directory %s\n", $target->getPathname());
            if($create){
                return mkdir($target->getPathname());
            }else {
                return false;
            }
        }
        return true;
    }

    public function process(){
        
        foreach($this->files as $file){
            $delimited = file_get_contents($file->getPathname());
            echo $this->d2x->getXML($delimited);
        }
    }
}