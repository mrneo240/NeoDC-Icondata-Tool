<?php
// get the q parameter from URL
$slide = $_REQUEST["slide"];

echo "Slider: ".(0x11*($slide-1));
?>