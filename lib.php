<?php

abstract class Tab2processor {

    private $args;
    private $minArgs = 2;
    public $src, $target, $separator;

    public function __construct($args){
        $this->args = $args;
        // +1 since args[0] is always there.
        if(count($args) < $this->minArgs + 1){
            print $this->usage();
            exit(1);
        }

        $this->src       = new SplFileInfo($args[1]);
        $this->target    = new SplFileInfo($args[2]);
        $this->separator = isset($args[3]) ? $this->lookupSeparator($args[3]) : $this->lookupSeparator();
        $this->mappings  = isset($args[4]) ? new SplFileInfo($args[4]) : false;
    }

    private function lookupSeparator($key = null) {
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

    private function usage(){
        $msg  = "";
        $msg .= sprintf("Not enough arguments.\n");
        $msg .= sprintf("Usage:\n\tphp %s src-dir target-dir [separator] [mappings directory]\n\n", $this->args[0]);
        $msg .= sprintf("\tseparator: comma | pipe | tab       (defaults to comma)\n\n");
        return $msg;
    }

    abstract public function processDir();

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
}

