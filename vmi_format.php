<?php
//NeoDC, 2018.
//License:
//Respect and dont steal.
//(not that would be hard for anyone to figure out) its just more about the ethics of it. 
//feel free to modify though and expand and such
//Open source your changes!!!
//Remember: its for the community
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

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

function createVMI($desc,$filename){
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
    $fp = fopen($filename.'.VMI', 'w');
    
    $checksumInput = unpack("H2a/H2b/H2c/H2d","SEGA");
    $checksumData = unpack("H2a/H2b/H2c/H2d",$filename);
    $desc['Checksum'] = 
    chr(hexdec($checksumInput[a]) & hexdec($checksumData[a])).
    chr(hexdec($checksumInput[b]) & hexdec($checksumData[b])).
    chr(hexdec($checksumInput[c]) & hexdec($checksumData[c])).
    chr(hexdec($checksumInput[d]) & hexdec($checksumData[d]));

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
                0, //dont touch
                0, //dont touch
                $filename,
                $filename.".VMS",
                0, //dont touch
                0, //dont touch
                filesize($filename_orig.".VMS"));
                
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

//Create a VMI like this, the second argument is the VMS filename(max length 8)
//make sure the vms exists, rename after generation if nessecary to match the VMI
$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => $desc, //up to 32 characters
                        'Copyright'=> $cpy); //up to 32 characters
//Generate a .VMI for the file "SONIC.VMS"
 createVMI($vmiDescription, $name);
 echo $name.'.VMI Written successfully<br>';
 print_r($vmiDescription);
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
echo  './/upload//'.$folder.'//<h3>ICONDATA.VMI Written successfully</h3>';
 //print_r($vmiDescription);
 
 //createZipAndDownload($folder);
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
    //then send the headers to force download the zip file
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=ICONDATA.ZIP"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile('.//upload//'.$folder.'//tmp.zip');
    exit;
}

?>