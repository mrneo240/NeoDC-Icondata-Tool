<?php 
include_once('basic.php');

$html_output = '<form action="create_user.php" method="post">
Username: <input type="text" name="uname" id="uname"><br>
Password: <input type="password" name="pass" id="pass"><br>
<input type="submit" value="Submit Account">
</form>
<br>
<form action="login.php" method="post">
Username: <input type="text" name="uname"><br>
Password: <input type="password" name="pass"><br>
<input type="submit" value="Login">
</form>';
$page_title = "login";
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_basic_empty.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>  