<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

require_once('util.php');

function combineVMU($bw, $color) {
	//starts at 0x20, is 81 bytes long and ends at 0xAF
	$bwHandle = fopen('.//upload//'.$bw.'//ICONDATA.VMS', 'r');
	$colorHandle = fopen('.//upload//'.$color.'//ICONDATA.VMS', 'r');
	$bwHeaderAndImage = stream_get_contents($bwHandle, 160, $offset = 0);
	$colorImage = stream_get_contents($colorHandle, $length = 864, $offset = 160);
	fclose($bwHandle);
	fclose($colorHandle);

	$finalBuffer = $bwHeaderAndImage.$colorImage;
	if (!file_exists('.//upload//'.$bw.'_')) {
	mkdir('.//upload//'.$bw.'_');
	}
	$moshHandle = fopen('.//upload//'.$bw.'_//ICONDATA.VMS', 'c+');
	copy('.//upload//'.$bw.'//ICONDATA.VMI','.//upload//'.$bw.'_//ICONDATA.VMI');
	
	$bytes_written = false;
	$bytes_written = fwrite($moshHandle, $finalBuffer, 1024);
	fclose($moshHandle);
	
	createZipAndDownload('.//'.$bw.'_//');
}

function getCombined() {
	$bw = "";
	$color = "";
	if (isset($_REQUEST['bw'])) { $bw = $_REQUEST['bw'];} else {outputJSON("Incorrect parameters");}
	if (isset($_REQUEST['color'])) { $color = $_REQUEST['color'];} else {outputJSON("Incorrect parameters");}
	
	if (!file_exists('.//upload//'.$bw) ||  !file_exists('.//upload//'.$color) ) {
		outputJSON("Incorrect File Selected!");
	}
	
	combineVMU($bw, $color);
}

// Report all PHP errors (see changelog)
error_reporting(E_ALL);
$command = "NONE";
if (isset($_REQUEST['cmd'])) { $command = $_REQUEST['cmd'];}
switch($command){
    case "NONE":
    break;
    case "getCombined":
    getCombined();
    break;
    default:
    break;
}


?>