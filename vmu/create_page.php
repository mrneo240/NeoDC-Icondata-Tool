<?php
include_once 'basic.php';

$page_title = "login";
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
          <tr>
            <td align="center">
              <font face="arial" color="#EEEEEE"><b><i>Create New User</i></b></font>
            </td>
          </tr>
          <tr>
            <td bgcolor="#EEEEEE" align="center">
              <br>
              <table cellspacing="3" cellpadding="3">
                <tr>
                  <td>
                    <form action="create_user.php" method="post">
                      Username: <input type="text" name="uname" id="uname"><br> Password: <input type="password"
                        name="pass" id="pass"><br>
                      <input type="submit" value="Submit Account">
                    </form>
                </tr>
            </td>
        </table>
  </table>
  </td>
  <td>
    <table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
      <tr>
        <td align="center">
          <font face="arial" color="#EEEEEE"><b><i>Login</i></b></font>
        </td>
      </tr>
      <tr>
        <td bgcolor="#EEEEEE" align="center">
          <br>
          <table cellspacing="3" cellpadding="3">
            <tr>
              <td>
                <form action="login.php" method="post">
                  Username: <input type="text" name="uname"><br> Password: <input type="password" name="pass"><br>
                  <input type="submit" value="Login">
                </form>
            </tr>
        </td>
    </table>
    </table>
  </td>
  </tr>
  </table>
</center>
<?php
$TBS->LoadTemplate('_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('_footer.html');
$TBS->Show(TBS_OUTPUT);
?>