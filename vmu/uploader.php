<?php 
include_once('basic.php');

$html_output = "";
$page_title = "uploader";
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
<table>
  <tr>
    <td>
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>PlanetWeb 2.X/3.0 VMU Uploader</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center"><br>
  <table cellspacing="3" cellpadding="3">
  <form action="uploadDC.php" enctype="multipart/form-data" method="POST">
    <tr><td>File to upload:</td><td><input type="VMFILE" name="upfile" id="upfile"></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="Upload"></td></tr>
      </form>
  </table>
</td></tr>
</table>
</td>
<td>
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>DreamKey 2.0 & Other VMU Uploader</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center"><br>
  <table cellspacing="3" cellpadding="3">
  <form action="uploadDC-alt.php" method="POST">
    <tr><td>File to upload:</td><td><input type="VMFILE" name="upfile" id="upfile"></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="Upload"></td></tr>
  </form>
  </table>
</td></tr>
</table>
</td>
</tr>
</table>
<br>
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>VMU Links</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center"><br>
  <table cellspacing="3" cellpadding="3">
    <tr><td><a href="index.html">Generated Downloads</a></td></tr>
    <tr><td><a href="extras/index.html">Extras</a></td></tr>
   </table>
</td></tr>
</table>
<br> 
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>VMU Downloads</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center"><br>
  <table cellspacing="3" cellpadding="3">
  <?php
 $dir = new DirectoryIterator(dirname(__FILE__).'//uploads//');
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
    if(strstr($fileinfo->getFilename(),"VMI")){
        print('<tr><td colspan="2" align="center"><a href="uploads/'.$fileinfo->getFilename().'">'.$fileinfo->getFilename().'</a><br></td></tr>' );
    }
    }
}
?>
  </table>
</td></tr>
</table>
</center>
<?php
$TBS->LoadTemplate('_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>