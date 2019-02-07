<?php
session_start();
$_SESSION['user'] = '';
$_SESSION['logged'] = 0;
header('Location: new.php');
