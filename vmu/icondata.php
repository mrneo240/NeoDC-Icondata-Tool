<?php
include_once('basic.php');

//$page_title = "icondata";
$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
  <?php
  require_once('../ajax_browser.php');
  getAllImages("..");
  ?>
</center>
<br>
<?php
$TBS->LoadTemplate('_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>