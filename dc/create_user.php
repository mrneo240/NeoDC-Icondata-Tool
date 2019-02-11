<?php
include_once 'basic.php';

$TBS->LoadTemplate('template/_header.html');
$TBS->Show(TBS_OUTPUT);

if (!isset($_POST['uname']) || !isset($_POST['pass'])) {
    echo "<p>Please enter a username and password!</p>";
} else {
    $uname = _c($_POST['uname']);
    $pass = _c($_POST['pass']);
    $ourFileName = "../users/" . $uname . "_pass.txt";
    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
    fwrite($ourFileHandle, $pass);
    fclose($ourFileHandle);
    $html_output = "<h1>User [" . $uname . "] Created Successfully!</h1>";
    $TBS->LoadTemplate('template/_basic_empty.html');
    $TBS->Show(TBS_OUTPUT);
    $_SESSION['user'] = _c($uname);
    $_SESSION['logged'] = 1;
    if (!is_dir('../save-uploads/' . $uname)) {
        mkdir('../save-uploads/' . $uname);
    }
}
$TBS->LoadTemplate('template/_footer.html');
$TBS->Show(TBS_OUTPUT);
