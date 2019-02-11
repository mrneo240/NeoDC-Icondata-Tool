<?php
include_once('basic.php');

//$page_title = "icondata";
$TBS->LoadTemplate('template/_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
  <?php
  $dc_hide = 1;
  require_once('../ajax_browser.php');
  getAllImages("..");
  ?>
</center>
<br>
<?php
$TBS->LoadTemplate('template/_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_footer.html');
$TBS->Show(TBS_OUTPUT);
?>