<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

require_once('util.php');

$hash = ".";
if (isset($_REQUEST['hash'])) { $hash = $_REQUEST['hash'];}

$vms_image_Color = imagecreate(32, 32);
$vms_image = imagecreate(32, 32);
$background = imagecolorallocate($vms_image_Color, 0, 0, 0);
$white = imagecolorallocate($vms_image, 255, 255, 255);
$black = imagecolorallocate($vms_image, 0, 0, 0);
$fp = fopen('./upload/'.$hash.'/ICONDATA.VMS', 'rb');
$vms_name = fread($fp,16);
$offsetImage = unpack("l",fread($fp,4));
$offsetImageColor = unpack("L",fread($fp,4));
fseek($fp,$offsetImage[1]);
for($y=0;$y<32;$y++){
	$inputData = fread($fp, 4);
	$value = unpack('N', $inputData);
	$lineData = substr(bigdecbin($value[1]),-32);
	//$lineData = bigdecbin($value[1]);
	//echo 'line: len:'.strlen(bigdecbin($value[1])).'- '.$lineData.'<br>';
	for($x=-1;$x<32;$x++){
		if(substr($lineData,$x,1)=="1"){
			imagesetpixel($vms_image,$x+1,$y,$black);
		}
	}
}

//printf('name:%s image:%d color:%d',$vms_name,$offsetImage[1],$offsetImageColor[1]);
fseek($fp,$offsetImageColor[1]);
$r=$g=$b=$a=0xF;
$paletteData = fread($fp, 32);
$array = unpack("v16", $paletteData);
$vms_palette=array();
echo "<br><h2 style='padding:0;margin:0;'>Palette:</h2><br>";
for($i = 1; $i <=16;$i++){
    $b = $array[$i] & 0x0F; //low nibble
    $g= ($array[$i] & 0xF0) >> 4; //high nibble
    $r= ($array[$i] & 0xF00) >> 8; //higher nibble
    $a= ($array[$i] & 0xF000) >> 12; //highest nibble
    $b = ($b*16)+$b;
    $r = ($r*16)+$r;
    $a = ($a*16)+$a;
    $g = ($g*16)+$g;
    printf("<div style='background-color:#%02X%02X%02X;width:32;height:32;float:left;' >".($i-1)."</div>\n",$r,$g,$b);
   $vms_palette[$i-1] = imagecolorallocate($vms_image_Color, $r,$g,$b);
}
echo '<br style="clear:left">';
$x=$y=0;
for($y=0;$y<32;$y++){
    $rowDataRaw = fread($fp, 16);
    $array = unpack("C16", $rowDataRaw);
   for($x=1;$x<=16;$x++){
        $col1 = $array[$x] & 0x0F; //low nibble
        $col2= ($array[$x] & 0xF0) >> 4; //high nibble
        imagesetpixel($vms_image_Color,($x-1)*2,$y,$vms_palette[$col2]);
        imagesetpixel($vms_image_Color,($x-1)*2+1,$y,$vms_palette[$col1]);
    }
}

//header( "Content-type: image/png" );
ob_start();
imagepng($vms_image);
$image_data = ob_get_contents();
ob_end_clean();
ob_start();
imagepng($vms_image_Color);
$image_data_Color = ob_get_contents();
ob_end_clean();
echo '<br><br><img width=64 height=64 style="image-rendering: pixelated" 
src="data:image/png;base64,'.base64_encode($image_data).'" alt="bw image" />
<img width=64 height=64 style="image-rendering: pixelated" 
src="data:image/png;base64,'.base64_encode($image_data_Color).'" alt="color image" />';

imagecolordeallocate($vms_image_Color, $background );
imagedestroy( $vms_image_Color );
imagedestroy( $vms_image);

fclose($fp);
?>