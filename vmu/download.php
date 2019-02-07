<?php 
include_once('basic.php');

$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
<br> 
<table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
  <tr><td align="center"><font face="arial" color="#EEEEEE"><b><i>VMU Downloads</i></b></font></td></tr>
  <tr><td bgcolor="#EEEEEE" align="center">
  <table cellspacing="3" cellpadding="3"><br>
  <?php
 $dir = new DirectoryIterator(dirname(__FILE__).'//uploads//');
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
    if(stristr($fileinfo->getFilename(),"VMI")){
        print('<tr><td colspan="2" align="center"><a href="uploads/'.$fileinfo->getFilename().'">'.$fileinfo->getFilename().'</a><br></td></tr>' );
    }
    }
}
?>
  </table>
</td></tr>
</table>
</center>
<br>
<?php
$TBS->LoadTemplate('_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>