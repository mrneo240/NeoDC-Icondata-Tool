<?php
//NeoDC, 2017.
//License:
//Respect and dont steal.
//(not that would be hard for anyone to figure out) its just more about the ethics of it. 
//feel free to modify though and expand and such
//Open source your changes!!!
//Remember: its for the community

function bigdecbin($dec,$doublewords=1) { 
    $erg = ""; 
    do { 
          $rest = $dec%2147483648; 
          if ($rest<0) $rest+=2147483648; 
          $erg = str_pad(decbin($rest),31,"0",STR_PAD_LEFT).$erg; 
          $dec = ($dec-$rest)/2147483648; 
      } while (($dec>0)&&(!($dec<1))); 
      
      return str_pad($erg,$doublewords*31,"0",STR_PAD_LEFT); 
}

$vms_image_Color = imagecreate(32, 32);
$vms_image = imagecreate(32, 32);
$background = imagecolorallocate($vms_image_Color, 0, 0, 0);
$white = imagecolorallocate($vms_image, 255, 255, 255);
$black = imagecolorallocate($vms_image, 0, 0, 0);
$fp = fopen('ICONDATA.VMS', 'rb');
$vms_name = fread($fp,16);
$offsetImage = unpack("l",fread($fp,4));
$offsetImageColor = unpack("L",fread($fp,4));
fseek($fp,$offsetImage[1]);
for($y=0;$y<32;$y++){
$inputData = fread($fp, 4);
$value = unpack('N', $inputData);
$lineData = bigdecbin($value[1]);
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