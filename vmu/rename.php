<?php

require_once('../vmi_format.php');

$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => "NeoDC VMU Uploader", //up to 32 characters
                        'Copyright'=> "NeoDC 2018"); //up to 32 characters

$dir = new DirectoryIterator(dirname(__FILE__));
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
    if(strstr($fileinfo->getFilename(),"VMS")){
        createVMI($vmiDescription,substr($fileinfo->getFilename(),0,-4),"Checking");
    }
    if(strstr($fileinfo->getFilename(),"VMI")){
        //print('<a href="'.uniqueFilename($fileinfo->getFilename()).'">'.$fileinfo->getFilename().'</a><br>');
    }
    }
}

?>