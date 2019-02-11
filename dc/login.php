<?php
include_once 'basic.php';

$page_title = "login";
if (!isset($_POST['uname']) || !isset($_POST['pass'])) {
    header('create_page.php');
}
$username = _c($_POST['uname']);
$postpass = _c($_POST['pass']);
$myfile = "../users/" . $username . "_pass.txt";

$exists = file_exists($myfile);
if ($exists) {
    $file = $myfile;
    $fh = fopen($file, 'r');
    $pass = fread($fh, filesize($file));
    fclose($fh); //Above checks if exists and sets pass as the real password
}
if (($exists) and ($pass == $postpass)) {
    //Above checks if the real pass is equal to the entered pass
    $_SESSION['user'] = $username;
    $_SESSION['logged'] = 1;
    //Above sets the session which is used to do stuff with the profiles (up next)
    header('Location: hidden.php');
} else {
    header('Location: create_page.php');
}
