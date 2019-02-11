<?php
include_once('basic.php');

$page_title = "login";
$TBS->LoadTemplate('template/_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_basic_start.html');
$TBS->Show(TBS_OUTPUT);

if ((isset($_SESSION['logged'])) and ($_SESSION['logged'] == 1)) {
    echo "<h1>" . $_SESSION['user'] . "'s Profile and secret area.</h1>";
} else {
    //Below alerts the user if they aren't logged in. It also makes the window go back.
    echo "<script>\nalert(\"Sorry you must be logged in to view this.\");\nwindow.history.back();\n</script>\n";
}
?>
<center>
  <br>
  <table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
    <tr>
      <td align="center">
        <font face="arial" color="#EEEEEE"><b><i>VMU Downloads</i></b></font>
      </td>
    </tr>
    <tr>
      <td bgcolor="#EEEEEE" align="center">
        <table cellspacing="3" cellpadding="3"><br>
          <?php
          if ((isset($_SESSION['logged'])) and ($_SESSION['logged'] == 1)) {
              $subdir = "";
              if (isset($_SESSION['user'])) {
                  $subdir = $_SESSION['user'];
              }
              $dir = new DirectoryIterator(dirname(__FILE__).'//..//save-uploads//'.$subdir);
              foreach ($dir as $fileinfo) {
                  if (!$fileinfo->isDot()) {
                      if (stristr($fileinfo->getFilename(), "VMI")) {
                          print('<tr><td colspan="2" align="center"><a href="../save-uploads/'.$subdir."//".$fileinfo->getFilename().'">'.$fileinfo->getFilename().'</a><br></td></tr>');
                      }
                  }
              }
          }
?>
        </table>
      </td>
    </tr>
  </table>
</center>
<br>
<?php
$TBS->LoadTemplate('template/_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_footer.html');
$TBS->Show(TBS_OUTPUT);
?>