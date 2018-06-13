<?php
//NeoDC, 2018.
//License:
//Respect and dont steal.
//(not that would be hard for anyone to figure out) its just more about the ethics of it. 
//feel free to modify though and expand and such
//Open source your changes!!!
//Remember: its for the community
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

function detectColors($image, $num, $level = 5, &$paletteOUT, &$palette_RawOUT) {
  $level = (int)$level;
  $palette = array();
  $palette_Raw = array();
  $size = getimagesize($image);
  if(!$size) {
    return FALSE;
  }
  switch($size['mime']) {
    case 'image/jpeg':
      $img = imagecreatefromjpeg($image);
      break;
    case 'image/png':
      $img = imagecreatefrompng($image);
      break;
    case 'image/gif':
      $img = imagecreatefromgif($image);
      break;
    default:
      return FALSE;
  }
  if(!$img) {
    return FALSE;
  }
  for($i = 0; $i < $size[0]; $i += $level) {
    for($j = 0; $j < $size[1]; $j += $level) {
      $thisColor = imagecolorat($img, $i, $j);
      $rgb = imagecolorsforindex($img, $thisColor);
      $color = sprintf('%02X%02X%02X', (round(round(($rgb['red'] / 0x11)) * 0x11)), round(round(($rgb['green'] / 0x11)) * 0x11), round(round(($rgb['blue'] / 0x11)) * 0x11));
      $palette[$color] = isset($palette[$color]) ? ++$palette[$color] : 1;
      $color = sprintf('%X%XF%X', (round(round(($rgb['green'] / 0x11)))), round(round(($rgb['blue'] / 0x11))), round(round(($rgb['red'] / 0x11))));
      $palette_Raw[$color] = isset($palette_Raw[$color]) ? ++$palette_Raw[$color] : 1;
    }
  }
  arsort($palette);
  arsort($palette_Raw);
  $paletteOUT = array_slice(array_keys($palette), 0, $num);
  $palette_RawOUT = array_slice(array_keys($palette_Raw), 0, $num);
}
function detectColors_Raw($image, $num, $level = 5) {
  $level = (int)$level;
  $palette = array();
    $size = getimagesize($image);
  if(!$size) {
    return FALSE;
  }
  switch($size['mime']) {
    case 'image/jpeg':
      $img = imagecreatefromjpeg($image);
      break;
    case 'image/png':
      $img = imagecreatefrompng($image);
      break;
    case 'image/gif':
      $img = imagecreatefromgif($image);
      break;
    default:
      return FALSE;
  }
  if(!$img) {
    return FALSE;
  }
  for($i = 0; $i < $size[0]; $i += $level) {
    for($j = 0; $j < $size[1]; $j += $level) {
      $thisColor = imagecolorat($img, $i, $j);
      $rgb = imagecolorsforindex($img, $thisColor);
      $color = sprintf('%X%XF%X', (round(round(($rgb['green'] / 0x11)))), round(round(($rgb['blue'] / 0x11))), round(round(($rgb['red'] / 0x11))));
      $palette[$color] = isset($palette[$color]) ? ++$palette[$color] : 1;
    }
  }
  arsort($palette);
  return array_slice(array_keys($palette), 0, $num);
}
$image_binary = array();
$image_string = "";
function image2BW($im) {
    global $image_binary;
    global $image_string;
    for ($y = imagesy($im); $y--;) {
        $data_string = "";
        for ($x =0;$x< imagesx($im); $x++) {
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8 ) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r* 0.299 + $g* 0.587 + $b* 0.114) ;
            //$gray = ($r + $g+ $b)/3 ;
            $threshold = 0x66;
            if (isset($_REQUEST['threshold'])) { $threshold = hexdec($_REQUEST['threshold']);}
            if ($gray < $threshold) {
                imagesetpixel($im, $x, $y, 0xFFFFFF);
                $data_string .= "1";
            }else{
                imagesetpixel($im, $x, $y, 0x000000);
                $data_string .= "0";
            }
        $image_binary[$y]=$data_string;
        }
    }
    $invert = 1;
    if (isset($_REQUEST['invert'])) { $invert = $_REQUEST['invert'];}
    if($invert){
    imagefilter($im, IMG_FILTER_NEGATE);
    }
}

/*function chbo($num) {
    $data = dechex($num);
    if (strlen($data) <= 2) {
        return $num;
    }
    $u = unpack("H*", strrev(pack("H*", $data)));
    $f = hexdec($u[1]);
    return $f;
}*/

//Expose Threshold and Invert

//echo var_dump(1073741826, chbo(1073741826));
//echo str_pad(decbin(chbo(1073741826)), 32, "0", STR_PAD_LEFT);
//00000010000000000000000001000000
//00000010000000000000000001000000
//echo str_pad(dechex(bindec("00000010000000000000000001000000")),8, "0", STR_PAD_LEFT)."<br>";
$img = 'watermelon.png';
if (isset($_REQUEST['img'])) { $img = $_REQUEST['img'];}
$image = @imagecreatefrompng($img);
$img_tmp = imagecreatetruecolor(32, 32);
imagecopyresampled($img_tmp, $image, 0, 0, 0, 0, 32, 32, imagesx($image), imagesy($image));
$image = $img_tmp;
$imageBW = @imagecreatefrompng($img);
$img_tmp = imagecreatetruecolor(32, 32);
imagecopyresampled($img_tmp, $image, 0, 0, 0, 0, 32, 32, imagesx($image), imagesy($image));
$imageBW = $img_tmp;
$detect_levels = 2;
if (isset($_REQUEST['levels'])) { $detect_levels = $_REQUEST['levels'];}
detectColors($img,16,$detect_levels,$palette,$palette_Raw);
echo '<img width=64 height=64 style="image-rendering: pixelated" src="' . $img . '" />';
//get to b&w then capture it
image2BW($imageBW);
ob_start();
imagepng($imageBW);
$image_data = ob_get_contents();
ob_end_clean();
//Output the stuff
echo '<img width=64 height=64 style="image-rendering: pixelated" 
src="data:image/png;base64,'.base64_encode($image_data).'" alt="Red dot" /><br><br>';
echo '<table>';
foreach($palette as $color) {
  echo '<tr><td style="background:#' . $color . '; width:36px;"></td><td>#' . $color . '</td></tr>';
}
echo '</table>';

//Write ICONDATA.VMS
$fp = fopen("ICONDATA.VMS","w");
//write name
fwrite($fp, str_pad(substr($img,0,16),16," ",STR_PAD_RIGHT));
//Write Header
fwrite($fp,pack("H*","20000000A00000000000000000000000"));
//write b&w icon
for($x=0;$x<32;$x++){
$data = str_pad(dechex(bindec($image_binary[$x])),8, "0", STR_PAD_LEFT);
$data2 = pack("H*",$data);
fwrite($fp,$data2);
}
//Write Color Palette
for($x=0;$x<16;$x++){
fwrite($fp,pack("H*",(isset($palette_Raw[$x])) ? $palette_Raw[$x] : "0000" ));
}

function compareColors($colorA, $colorB) {
    return abs(hexdec(substr($colorA,0,2))-hexdec(substr($colorB,0,2)))+
            abs(hexdec(substr($colorA,2,2))-hexdec(substr($colorB,2,2))) +
            abs(hexdec(substr($colorA,4,2))-hexdec(substr($colorB,4,2)));
}

function findColorMatch($colorInput, $palette){
$deviation = PHP_INT_MAX;
   foreach ($palette as $color) {
        $curDev = compareColors($colorInput, $color);
        if ($curDev < $deviation) {
            $deviation = $curDev;
            $selectedColor = $color;
        }
    }
    return array_search($selectedColor,$palette);
}
for ($y=0;$y<32;$y++) {
    for ($x=0;$x<16; $x++) {
        $temp = "";
        //nibble high;
        $rgb = imagecolorat($image, $x*2, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8 ) & 0xFF;
        $b = $rgb & 0xFF;
        $color_rgb = sprintf('%02X%02X%02X',$r,$g,$b);
        $match = findColorMatch($color_rgb,$palette);
        $temp .= (string)dechex($match);
         //nibble low;
        $rgb = imagecolorat($image, ($x*2)+1, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8 ) & 0xFF;
        $b = $rgb & 0xFF;
         $color_rgb = sprintf('%02X%02X%02X',$r,$g,$b);
        $match = findColorMatch($color_rgb, $palette);
       $temp .= (string)dechex($match);
        fwrite($fp,pack("H*",$temp));
    }
}
//pad out i guess
for ($y=0;$y<320;$y++) {
fwrite($fp,pack("H*","1A"));
}
//Done Finally!
fclose($fp);

?>