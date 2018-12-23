<!DOCTYPE html>
<html>
<head>
<title>VMU File Uploader - Beta Test Area</title>
</head>
<body bgcolor="#AAAAAA">
<br><br>
<center>
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>VMU Upload Status</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center"><br>
  <table cellspacing="3" cellpadding="3">
    <tr><td><a href="index.html">Generated Downloads</a></td></tr>
    <tr><td><a href="extras/index.html">Extras</a></td></tr>
    <tr><td>
<?php
require_once('vmi_format.php');

$logname = 'uploads//'.md5(time());
function logInfo(){
global $logname;
$txt='Alternate Uploader\n'.
print_r($_SERVER,true).
print_r($_POST,true).
print_r($_GET,true).
print_r($_REQUEST,true).
print_r($_FILES,true).
file_get_contents('php://input');
   file_put_contents($logname.'.txt',$txt);
}
logInfo();

$target_dir = "uploads/";
$target_file = $target_dir . 'default';
$uploadOk = 1;
if(isset($_POST['upfile'])) {
    $ret = file_put_contents($target_file, $_POST['upfile']);
    file_put_contents($logname, $_POST['upfile']);
    handleUpload('default');
    unlink($target_file);
} else {
    echo "Sorry, there was an error uploading your file.";
}

function handleUpload($filename){
    global $target_dir;
$handle = fopen($target_dir .$filename, "rb");
$Buffer = fread($handle, filesize($target_dir .$filename));
fclose($handle);


$saveData = substr(substr($Buffer,strpos($Buffer, "&tm=")),19);
$decoded = getVms($saveData);

$vmuFilename = substr($Buffer,strpos($Buffer, "=")+1,+strpos($Buffer, "&")-strpos($Buffer, "=")-1);
$filename = uniqueFilename($target_dir .$vmuFilename.'.VMS', false);

print(substr($filename,0,-4).'.VMS Created and ready for download<br>');
$fp2 = fopen($target_dir.$filename, "wb");
fwrite($fp2, $decoded);
fclose($fp2);
$vmiDescription = array('Checksum' => '0000', //dont touch
                        'Description' => "NeoDC VMU Uploader", //up to 32 characters
                        'Copyright'=> "NeoDC 2018",
                        'vmuFilename'=> $vmuFilename); //up to 32 characters
                        
createVMI($vmiDescription,substr($filename,0,-4),$target_dir);
}

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
    return $vms;
}
?>
  </td></tr><tr><td><a href="upload.php">Back to Uploader</a></td></tr></table>
  </form>
</td></tr>

</table>
</center>
</body>
</html>
