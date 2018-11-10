<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

// Output JSON
function outputJSON($msg, $status = 'error'){
    header('Content-Type: application/json');
    die(json_encode(array(
        'data' => $msg,
        'status' => $status
    )));
}

function createZipAndDownload($folder) {
	
    $file_names = array('ICONDATA.VMI','ICONDATA.VMS');
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open('.//upload//'.$folder.'//tmp.zip', ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach($file_names as $files)
    {
        $zip->addFile('.//upload//'.$folder.'//'.$files,$files);
    }
    $zip->close();
    
    //also zip up vmu image stuff
    $file_names = array('PALLETTE.BIN','IMAGE.BIN');
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open('.//upload//'.$folder.'//vmu.zip', ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach($file_names as $files)
    {
        $zip->addFile('.//upload//'.$folder.'//'.$files,$files);
    }
    $zip->close();
    //then send the headers to force download the zip file
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=ICONDATA.ZIP"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile('.//upload//'.$folder.'//tmp.zip');
    exit;
}

function bigdecbin($dec,$doublewords=1) { 
    $erg = ""; 
    do { 
          $rest = $dec%2147483648; 
          if ($rest<0) $rest+=2147483648; 
          $erg = str_pad(decbin($rest),31,"0",STR_PAD_LEFT).$erg; 
          $dec = ($dec-$rest)/2147483648; 
      } while (($dec>0)&&(!($dec<1))); 
      
      return str_pad($erg,$doublewords*31,"0",STR_PAD_LEFT); 
}

?>