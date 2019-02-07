<?php
session_start();
include_once '../tbs_class.php';
$TBS = new clsTinyButStrong;
$page_title = "";
$html_output = "";

//basic input sanitize
function _c($in)
{
    $out = preg_replace("/[^[:alnum:]]/u", '', $in);
    return $out;
}
