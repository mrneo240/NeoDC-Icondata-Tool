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

function readVMI($filename){
/* VMI Format */
$header_format =
        'A4Checksum/'. //="SEGA" AND sub(VMSName,4);
        'a32Description/'.
        'a32Copyright/'.
        'SCreationYear/'.
        'CCreationMonth/'.
        'CCreationDay/'.
        'CCreationHour/'.
        'CCreationMinute/'.
        'CCreationSecond/'.
        'CCreationWeekday/'. //0 sunday, 6 saturday
        'SVMIVer/'.
        'SFileNumber/'.
        'A8VMSName/'. //Just name, no extension
        'A12Filename/'.
        'SModeField/'. //1(1=game,0=data), 0(1=no_copy,0=copyable)
        'SPadding/'. //set to 0
        'LFileSize'; //in bytes  
    
    if(!$fp = fopen ($filename, 'rb')) return 0;
    if(!$data = fread ($fp, 108)) return 0;
    $header = unpack ($header_format, $data);

    return $header;
}

function createVMI($desc,$filename, $folder = './/'){
    $header_format =
        'A4'. //="SEGA" AND sub(VMSName,4);
        'a32'.
        'a32'.
        'S'.
        'C'.
        'C'.
        'C'.
        'C'.
        'C'.
        'C'. //0 sunday, 6 saturday
        'S'.
        'S'.
        'A8'. //Just name, no extension
        'A12'.
        'S'. //1(1=game,0=data), 0(1=no_copy,0=copyable)
        'S'. //set to 0
        'L'; //in bytes  
        
    $filename_orig = $filename;
    $filename = formatFilename($filename);
    $fp = fopen($folder.$filename.'.VMI', 'w');
    
    $checksumInput = unpack("H2a/H2b/H2c/H2d","SEGA");
    $checksumData = unpack("H2a/H2b/H2c/H2d",$filename);
    $desc['Checksum'] = 
    chr(hexdec($checksumInput['a']) & hexdec($checksumData['a'])).
    chr(hexdec($checksumInput['b']) & hexdec($checksumData['b'])).
    chr(hexdec($checksumInput['c']) & hexdec($checksumData['c'])).
    chr(hexdec($checksumInput['d']) & hexdec($checksumData['d']));

    $date = getdate();
    
    $data = pack($header_format,
                $desc['Checksum'],
                substr($desc['Description'],0,32),
                substr($desc['Copyright'],0,32),
                $date['year'],
                $date['mon'],
                $date['mday'],
                $date['hours'],
                $date['minutes'],
                $date['seconds'],
                $date['wday'],
                0, //dont touch
                1, //dont touch
                $filename,
                $desc['vmuFilename'],
                0, //dont touch
                0, //dont touch
                filesize($folder.$filename_orig.".VMS"));
                
    fwrite($fp,$data);
    fclose($fp);
}
function createVMI_ICON($desc,$filename,$folder){
    $header_format =
        'A4'. //="SEGA" AND sub(VMSName,4);
        'a32'.
        'a32'.
        'S'.
        'C'.
        'C'.
        'C'.
        'C'.
        'C'.
        'C'. //0 sunday, 6 saturday
        'S'.
        'S'.
        'A8'. //Just name, no extension
        'A12'.
        'S'. //1(1=game,0=data), 0(1=no_copy,0=copyable)
        'S'. //set to 0
        'L'; //in bytes  
        
    $filename_orig = $filename;
    $filename = str_pad(strtoupper(substr($filename,0,8)),8,"_");
	if (!file_exists('.//upload//'.$folder)) {
	mkdir('.//upload//'.$folder);
	}
    $fp = fopen('.//upload//'.$folder.'//'.$filename.'.VMI', 'w');
    
    $checksumInput = unpack("H2a/H2b/H2c/H2d","SEGA");
    $checksumData = unpack("H2a/H2b/H2c/H2d",$filename);
    $desc['Checksum'] = 
    chr(hexdec($checksumInput['a']) & hexdec($checksumData['a'])).
    chr(hexdec($checksumInput['b']) & hexdec($checksumData['b'])).
    chr(hexdec($checksumInput['c']) & hexdec($checksumData['c'])).
    chr(hexdec($checksumInput['d']) & hexdec($checksumData['d']));

    $date = getdate();
    
    $data = pack($header_format,
                $desc['Checksum'],
                str_pad(substr($desc['Description'],0,32),32),
                str_pad(substr($desc['Copyright'],0,32),32),
                $date['year'],
                $date['mon'],
                $date['mday'],
                $date['hours'],
                $date['minutes'],
                $date['seconds'],
                $date['wday'],
                256, //dont touch
                1, //dont touch
                $filename,
                $filename."_VMS",
                0, //dont touch
                0, //dont touch
                filesize('.//upload//'.$folder.'//'.$filename_orig.".VMS"));
                
    fwrite($fp,$data);
    fclose($fp);
}
$command = "NONE";
if (isset($_REQUEST['cmd'])) { $command = $_REQUEST['cmd'];}
date_default_timezone_set('UTC');
switch($command){
    case "NONE":
    break;
    case "writeVMI":
    writeVMI();
    break;
    case "writeVMI_ICON":
    writeVMI_ICON('.');
    break;
    case "readVMI":
    readVMI_File();
    break;
    default:
    break;
}

function readVMI_File(){
$name = 'SONIC';
if (isset($_REQUEST['name'])) { $name = $_REQUEST['name'];}
//Reads a .VMI and parses the information into an array
$header = readVMI($name.'.VMI');
print_r($header);
//$ver = $header['VMIVer']; //Use like this 
}

function writeVMI(){
$desc = 'TEST';
if (isset($_REQUEST['desc'])) { $desc = $_REQUEST['desc'];}
$cpy = 'NeoDC';
if (isset($_REQUEST['cpy'])) { $cpy = $_REQUEST['cpy'];}
$name = 'SONIC';
if (isset($_REQUEST['name'])) { $name = $_REQUEST['name'];}
$vmu = 'FILE.SYS';
if (isset($_REQUEST['vmu'])) { $vmu = $_REQUEST['vmu'];}

//Create a VMI like this, the second argument is the VMS filename(max length 8)
//make sure the vms exists, rename after generation if nessecary to match the VMI
$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => $desc, //up to 32 characters
                        'Copyright'=> $cpy,
                        'vmuFilename'=> $vmu); //up to 32 characters
//Generate a .VMI for the file "SONIC.VMS"
 createVMI($vmiDescription, $name);
 echo $name.'.VMI Written successfully<br>';
}

function writeVMI_ICON($folder){
$desc = 'TEST';
if (isset($_REQUEST['desc'])) { $desc = $_REQUEST['desc'];}
$cpy = 'NeoDC';
if (isset($_REQUEST['cpy'])) { $cpy = $_REQUEST['cpy'];}

//Create a VMI like this, the second argument is the VMS filename(max length 8)
//make sure the vms exists, rename after generation if nessecary to match the VMI
$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => $desc, //up to 32 characters
                        'Copyright'=> $cpy); //up to 32 characters
//Generate an ICONDATA.VMI file
 createVMI_ICON($vmiDescription,"ICONDATA", $folder);
//echo  './/upload//'.$folder.'//<h3>ICONDATA.VMI Written successfully</h3>';
 //print_r($vmiDescription);
 
 createZipAndDownload($folder);
}
?>