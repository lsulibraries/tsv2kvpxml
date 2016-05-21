<?php
require_once 'DirectoryProcessor.php';

class MIKDirectoryProcessor extends DirectoryProcessor {

    public function process(){

        foreach($this->files as $file){
            $alias = str_replace('_export', '', $file->getBasename('.txt'));
            $delimited = file_get_contents($file->getPathname());
            $this->alias_output_dir = new SplFileInfo($this->output_dir->getPathname() . DIRECTORY_SEPARATOR . $alias);
            $this->ensureDirExists($this->alias_output_dir, true);
            $mapFilePath = $this->getopt('mappings_dir') . DIRECTORY_SEPARATOR . $alias . DIRECTORY_SEPARATOR . 'Collection_Fields.json';
            $mapFile = new SplFileInfo($mapFilePath);
            $keyMap = $this->getFieldMappings($mapFile);

            $xmls = simplexml_load_string($this->d2x->getXML($delimited, $keyMap));
            $this->writeItemLevelMeta($xmls);
            $this->writeElemsInCollectionMeta($xmls, $alias);
            $this->writeTotalRecs($xmls, $alias);
        }
    }

    public function writeTotalRecs($xmls, $alias){
        $totalRecs = count($xmls->xml);
        $emptyDoc = <<<EOXML
<?xml version="1.0" encoding="UTF-8"?>
<results>
<totalrecs>
  <suggestedtopic></suggestedtopic>
  <total>{$totalRecs}</total>
</totalrecs>
</results>

EOXML;
        $doc = simplexml_load_string($emptyDoc);
        foreach(array('.xml' => $doc->asXML()) as $ext => $content){
            $outfile = $this->alias_output_dir . DIRECTORY_SEPARATOR . 'Collection_TotalRecs_dbg' . $ext;
            file_put_contents($outfile, $content);
        }
    }


    public $elemsMappings = array(
            'pointer' => 'dmrecord',
            'dmrecord' => 'dmrecord',
            'find'    => 'find',
            'filetype' => 'type',
        );
    
    protected function writeElemsInCollectionMeta(SimpleXMLElement $xmls, $alias){
        $totalRecs = count($xmls->xml);
        $emptyElemsDoc = <<<EOXML
<?xml version="1.0" encoding="UTF-8"?>
<results><pager>
  <start>1</start>
  <maxrecs>99999999</maxrecs>
  <total>{$totalRecs}</total>
</pager><records></records></results>
EOXML;
        $doc = simplexml_load_string($emptyElemsDoc);
        foreach($xmls->xml as $xml){
            $record = $doc->records->addChild('record', $xml->dmrecord);
            foreach($this->elemsMappings as $elem => $nick){
                $record->addChild($elem, htmlspecialchars($xml->$nick, ENT_XML1, 'UTF-8'));
            }
        }
        foreach(array('.xml' => $doc->asXML(), '.json' => json_encode($doc->children())) as $ext => $content){
            $outfile = $this->alias_output_dir . DIRECTORY_SEPARATOR . 'Elems_in_Collection_dbg' . $ext;
            file_put_contents($outfile, $content);
        }
    }

    
    protected function writeItemLevelMeta(SimpleXMLElement $xmls){
        foreach($xmls->xml as $xml){
            $pointer = $xml->dmrecord;
            $outfile = $this->alias_output_dir . DIRECTORY_SEPARATOR . $pointer . '.xml';
            file_put_contents($outfile, $xml->asXML());
        }
    }


    public function getFieldMappings(SplFileInfo $mapFile){

        $file = file_get_contents($mapFile->getPathname());
        $mapRecord = json_decode($file);
        $mappings = array();
        foreach($mapRecord as $map){
            $mappings[$map->name] = $map->nick;
        }
        return $mappings;
    }
}
