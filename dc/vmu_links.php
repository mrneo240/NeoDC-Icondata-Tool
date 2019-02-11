<?php
include_once('basic.php');

$TBS->LoadTemplate('template/_header.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_basic_start.html');
$TBS->Show(TBS_OUTPUT);
?>
<center>
  <table bgcolor="#000000" cellspacing="2" cellpadding="0" border="1" bordercolor="#000000">
    <tr>
      <td align="center">
        <font face="arial" color="#EEEEEE"><b><i>VMU Downloads</i></b></font>
      </td>
    </tr>
    <tr>
      <td bgcolor="#EEEEEE" align="center"><br>
        <table cellspacing="3" cellpadding="3">
          <tr>
            <td>
              <a href="../save-uploads/DEMORA_0.VMI">DEMORA_0.VMI</a><br><a href="../save-uploads/DOA2DA_0.VMI">DOA2DA_0.VMI</a><br><a
                href="../save-uploads/DRAGON_0.VMI">DRAGON_0.VMI</a><br>
              <a href="../save-uploads/MARINE_0.VMI">MARINE_0.VMI</a><br><a href="../save-uploads/OOGA___0.VMI">OOGA___0.VMI</a><br><a
                href="../save-uploads/POD_ML_0.VMI">POD_ML_0.VMI</a><br>
              <a href="../save-uploads/RUSH20_0.VMI">RUSH20_0.VMI</a><br><a href="../save-uploads/RUSH20_1.VMI">RUSH20_1.VMI</a><br><a
                href="../save-uploads/S.ARCA_0.VMI">S.ARCA_0.VMI</a><br>
              <a href="../save-uploads/S.ARCA_1.VMI">S.ARCA_1.VMI</a><br><a href="../save-uploads/SCOOTE_0.VMI">SCOOTE_0.VMI</a><br><a
                href="../save-uploads/SONIC2_0.VMI">SONIC2_0.VMI</a><br>
              <a href="../save-uploads/SONIC2_1.VMI">SONIC2_1.VMI</a><br><a href="../save-uploads/SONIC2_2.VMI">SONIC2_2.VMI</a><br><a
                href="../save-uploads/SONICA_0.VMI">SONICA_0.VMI</a><br>
              <a href="../save-uploads/SONICA_1.VMI">SONICA_1.VMI</a><br><a href="../save-uploads/SPACER_0.VMI">SPACER_0.VMI</a><br><a
                href="../save-uploads/SPRT_J_0.VMI">SPRT_J_0.VMI</a><br>
              <a href="../save-uploads/TOKYOB_0.VMI">TOKYOB_0.VMI</a><br><a href="../save-uploads/VIRTUA_0.VMI">VIRTUA_0.VMI</a><br>
          </tr>
      </td>
  </table>
  </table>
</center>
<br>
<?php
$TBS->LoadTemplate('template/_basic_end.html');
$TBS->Show(TBS_OUTPUT);
$TBS->LoadTemplate('template/_footer.html');
$TBS->Show(TBS_OUTPUT);
?>