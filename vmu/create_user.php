<?php 
include_once('basic.php');

$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);

if (!isset($_POST['uname']) || !isset($_POST['pass'])) {
    echo "<p>Please enter a username and password!</p>";
} else {

    $ourFileName = "./users/" . $_POST['uname'] . "_pass.txt";
    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
    fwrite($ourFileHandle, $_POST['pass']);
    fclose($ourFileHandle);
    $html_output = "<h1>User [" . $_POST['uname'] . "] Created Successfully!</h1>";
    $TBS->LoadTemplate('_basic_empty.html');
    $TBS->Show(TBS_OUTPUT);
    $_SESSION['user'] = $_POST['uname']; 
    $_SESSION['logged'] = 1; 
    if(!is_dir('uploads/'.$_POST['uname'])){
        mkdir('uploads/'.$_POST['uname']);
    }
}
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
