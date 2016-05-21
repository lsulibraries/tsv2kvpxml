<?php
require_once('tab2xml.php');

class testTab2XML extends PHPUnit_Framework_TestCase {
    public $args, $src, $target, $testdata, $testfields, $testfile, $unit;

    public function setup(){
        $this->testfields = array('columnA', 'columnB', 'columnC');
        $this->testdata = array(array('A', 'B', 'C'));
        $this->testfile = new SplFileInfo('devresources/testdata/test.txt');
        $this->writeTestFile($this->testfile, $this->testdata, $this->testfields);

        $this->src = new SplFileInfo('devresources/testdata');
        $this->target = sha1($this->randomString(10));
        $this->args = array(null, $this->src, $this->target);
        $this->unit = new Tab2xml($this->args);
    }

    private function writeTestFile($testfile, $testdata, $testfields){
        if(false !== ($handle = fopen($testfile->getRealPath(), 'w'))){
            fputcsv($handle, $testfields, "\t");
            foreach($testdata as $data){
                fputcsv($handle, $data, "\t");
            }
        }
        fclose($handle);
    }

    public function randomString($length){
        return substr(md5(microtime()),rand(0,26),$length);
    }

    public function testConstructor(){
        $this->assertEquals($this->src->getRealPath(),    $this->unit->src->getRealPath(),    "src directory was not initialized properly.\n");
        $this->assertEquals($this->unit->output, $this->unit->output->getFilename(), "target directory was not initialized properly.\n");

        // @TODO make this testable
        //$noArgs = new Tab2xml(array('convert'));
    }


    public function testEnsureDirExists(){
        $this->assertTrue($this->unit->ensureDirExists($this->unit->output));
        $this->assertFileExists($this->unit->output->getPathname());
        rmdir($this->unit->output->getPathname());
    }

    public function testProcessFile(){
        $kvplines = $this->unit->extractKvp($this->testfile);

	$keys = array_keys($kvplines[0]);
	$this->assertEquals($this->testfields, $keys);

	$values = array_values($kvplines[0]);
	$this->assertEquals($this->testdata[0], $values);
	return $kvplines;
    }

    /**
     * @depends testProcessFile
     * @param type $kvplines
     */
    public function testExportXML($kvplines){
	$this->assertNotEmpty($kvplines);
	var_dump($this->unit->exportXML($kvplines));
    }
}