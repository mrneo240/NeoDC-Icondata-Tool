<?php

require_once('../vmi_format.php');

$filename = "testing_doa2";
$handle = fopen($filename, "rb");
$Buffer = fread($handle, filesize($filename));
fclose($handle);

function decode($data){
    $enc_chars = 'AZOLYNdnETmP6ci3Sze9IyXBhDgfQq7l5batM4rpKJj8CusxRF+k2V0wUGo1vWH/=';
    $dec_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    $decoded = '';
    for ($i=0; $i<strlen($data); $i++) {
        if (strpos($enc_chars, $data[$i])!== false) {
            $decoded .= $dec_chars[strpos($enc_chars, $data[$i])];
        } else {
            $decoded .= $data[$i];//echo "<br>not found:".$data[$i];
        }
    }
    return $decoded;
}

function getVms($body) {
    $vms = base64_decode(decode($body));
    /*$vms = '';
    $body = decode($body);
    /*for ($i=0; $i < ceil(strlen($body)/512); $i++) {
        $vms .= base64_decode(substr($body,$i*512,512));
    }*/
    return $vms;
}

$saveData = substr(substr($Buffer,strpos($Buffer, "&tm=")),19);
$decoded = getVms($saveData);

$filename = substr($Buffer,strpos($Buffer, "=")+1,+strpos($Buffer, "&")-strpos($Buffer, "=")-1);
$filename = uniqueFilename($filename.'.VMS', false);

$fp2 = fopen($filename, "wb");
fwrite($fp2, $decoded);
fclose($fp2);
$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => "NeoDC VMU Uploader", //up to 32 characters
                        'Copyright'=> "NeoDC 2018"); //up to 32 characters
                        
createVMI($vmiDescription,substr($filename,0,-4),"Checking");

?>