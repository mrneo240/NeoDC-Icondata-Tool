<?php
include_once('basic.php');

$page_title = "login";
$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_basic_start.html');
$TBS->Show(TBS_OUTPUT);

if ((isset($_SESSION['logged'])) and ($_SESSION['logged'] == 1)) {
    echo "<h1>" . $_SESSION['user'] . "'s Profile and secret area.</h1>";
} else {
    //Below alerts the user if they aren't logged in. It also makes the window go back.
    echo "<script>\nalert(\"Sorry you must be logged in to view this.\");\nwindow.history.back();\n</script>\n";
}
$TBS->LoadTemplate('_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>