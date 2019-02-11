<?php
include_once('basic.php');

$html_output = "";
$page_title = "uploader";
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('template/_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
  <table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
    <tr>
      <td align="center">
        <font face="arial" color="#EEEEEE"><b><i>VMU Upload Status</i></b></font>
      </td>
    </tr>
    <tr>
      <td bgcolor="#EEEEEE" align="center"><br>
        <table cellspacing="3" cellpadding="3">
          <tr>
            <td>

              <?php
require_once '../vmi_format.php';

$target_dir = "../save-uploads//";
if (isset($_POST['private']) && $_SESSION['logged']==1) {
    $target_dir .= $_SESSION['user']."//";
}

$logname = $target_dir . md5(time());
function logInfo()
{
    global $logname;
    $txt = 'Normal Uploader\n' .
    print_r($_SERVER, true) .
    print_r($_POST, true) .
    print_r($_GET, true) .
    print_r($_REQUEST, true) .
    print_r($_FILES, true) .
    file_get_contents('php://input');
    file_put_contents($logname . '.txt', $txt);
}
logInfo();


$target_file = $target_dir . 'default';
$uploadOk = 1;
if (isset($_POST['upfile']) && strlen($_POST['upfile'])>1) {
    $ret = file_put_contents($target_file, $_POST['upfile']);
    file_put_contents($logname, $_POST['upfile']);
    handleUpload('default');
    unlink($target_file);
} else {
    echo "Sorry, there was an error uploading your file.";
}

function handleUpload($filename)
{
    global $target_dir;
    $handle = fopen($target_dir . $filename, "rb");
    $Buffer = fread($handle, filesize($target_dir . $filename));
    fclose($handle);

    $saveData = substr(substr($Buffer, strpos($Buffer, "&tm=")), 19);
    $decoded = getVms($saveData);

    $vmuFilename = substr($Buffer, strpos($Buffer, "=") + 1, +strpos($Buffer, "&") - strpos($Buffer, "=") - 1);
    $filename = uniqueFilename($target_dir . $vmuFilename . '.VMS', false);

    print(substr($filename, 0, -4) . '.VMS Created and ready for download<br>');
    $fp2 = fopen($target_dir . $filename, "wb");
    fwrite($fp2, $decoded);
    fclose($fp2);
    $vmiDescription = array('Checksum' => '0000', //dont touch
        'Description' => "NeoDC VMU Uploader", //up to 32 characters
        'Copyright' => "NeoDC 2018",
        'vmuFilename' => $vmuFilename); //up to 32 characters

    createVMI($vmiDescription, substr($filename, 0, -4), $target_dir);
}

function decode($data)
{
    $enc_chars = 'AZOLYNdnETmP6ci3Sze9IyXBhDgfQq7l5batM4rpKJj8CusxRF+k2V0wUGo1vWH/=';
    $dec_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    $decoded = '';
    for ($i = 0; $i < strlen($data); $i++) {
        if (strpos($enc_chars, $data[$i]) !== false) {
            $decoded .= $dec_chars[strpos($enc_chars, $data[$i])];
        } else {
            $decoded .= $data[$i]; //echo "<br>not found:".$data[$i];
        }
    }
    return $decoded;
}

function getVms($body)
{
    $vms = base64_decode(decode($body));
    return $vms;
}
?>


            </td>
          </tr>
          <tr>
            <td><a href="uploader.php">Back to Uploader</a></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</center>
<?php
$TBS->LoadTemplate('template/_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_footer.html');
$TBS->Show(TBS_OUTPUT);
?>