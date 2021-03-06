<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

require_once 'vmi_format.php';
require_once 'util.php';

function detectColors($image, $num, $level = 5, &$paletteOUT, &$palette_RawOUT)
{
    $level = (int) $level;
    $palette = array();
    $palette_Raw = array();
    $size = getimagesize($image);
    if (!$size) {
        return false;
    }
    switch ($size['mime']) {
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
            return false;
    }
    if (!$img) {
        return false;
    }
    for ($i = 0; $i < $size[0]; $i += $level) {
        for ($j = 0; $j < $size[1]; $j += $level) {
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

function detectColors_Raw($image, $num, $level = 5)
{
    $level = (int) $level;
    $palette = array();
    $size = getimagesize($image);
    if (!$size) {
        return false;
    }
    switch ($size['mime']) {
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
            return false;
    }
    if (!$img) {
        return false;
    }
    for ($i = 0; $i < $size[0]; $i += $level) {
        for ($j = 0; $j < $size[1]; $j += $level) {
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
function image2BW($im)
{
    global $image_binary, $image_string;

    for ($y = imagesy($im); $y--;) {
        $data_string = "";
        for ($x = 0; $x < imagesx($im); $x++) {
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r * 0.299 + $g * 0.587 + $b * 0.114);
            $threshold = 0x66;
            if (isset($_REQUEST['threshold'])) {$threshold = ($_REQUEST['threshold']) * 0x11;}
            if ($gray < $threshold) {
                imagesetpixel($im, $x, $y, 0xFFFFFF);
                $data_string .= "1";
            } else {
                imagesetpixel($im, $x, $y, 0x000000);
                $data_string .= "0";
            }
            $image_binary[$y] = $data_string;
        }
    }
    $invert = 1;
    if (isset($_REQUEST['invert'])) {$invert = $_REQUEST['invert'];}
    if ($invert) {
        imagefilter($im, IMG_FILTER_NEGATE);
    }
}

function compareColors($colorA, $colorB)
{
    return abs(hexdec(substr($colorA, 0, 2)) - hexdec(substr($colorB, 0, 2))) +
    abs(hexdec(substr($colorA, 2, 2)) - hexdec(substr($colorB, 2, 2))) +
    abs(hexdec(substr($colorA, 4, 2)) - hexdec(substr($colorB, 4, 2)));
}
function findColorMatch($colorInput, $palette)
{
    global $selectedColor;
    $deviation = PHP_INT_MAX;
    foreach ($palette as $color) {
        $curDev = compareColors($colorInput, $color);
        if ($curDev < $deviation) {
            $deviation = $curDev;
            $selectedColor = $color;
        }
    }
    return array_search($selectedColor, $palette);
}

function safeAccess($_x, $_y, $val)
{
    global $img_arr;
    if ($_x < 32 && $_x >= 0) {
        if ($_y < 32 && $_y >= 0) {
            $img_arr[$_x][$_y] += $val;
        }
    }
}
function doDither($input, &$output)
{
    global $img_arr, $image_binary, $image_string;
    $output = imagecreatetruecolor(imagesx($input), imagesy($input));
    $img_arr = array();
    for ($y = imagesy($input); $y--;) {
        for ($x = 0; $x < imagesx($input); $x++) {
            $rgb = imagecolorat($input, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r * 0.299 + $g * 0.587 + $b * 0.114);
            $color = imagecolorallocate($output, $gray, $gray, $gray);
            $img_arr[$x][$y] = $color;
            imagesetpixel($output, $x, $y, $color);
        }
    }
    imagefilter($output, IMG_FILTER_NEGATE);
    $white = imagecolorallocate($output, 0xff, 0xff, 0xff);
    $black = imagecolorallocate($output, 0, 0, 0);
    $threshold = 0.5;
    if (isset($_REQUEST['threshold'])) {$threshold = ($_REQUEST['threshold']) / 30;}

    for ($y = 0; $y < 32; $y++) {
        for ($x = 0; $x < 32; $x++) {
            $old = $img_arr[$x][$y];
            if ($old < 0xffffff * ($threshold)) {
                $new = 0x000000;
                imagesetpixel($output, $x, $y, $white);
            } else {
                $new = 0xffffff;
            }
            $quant_error = $old - $new;
            $error_diffusion = (1 / 8) * $quant_error;
            safeAccess($x + 1, $y, $error_diffusion);
            safeAccess($x + 2, $y, $error_diffusion);
            safeAccess($x - 1, $y + 1, $error_diffusion);
            safeAccess($x, $y + 1, $error_diffusion);
            safeAccess($x + 1, $y + 1, $error_diffusion);
            safeAccess($x, $y + 2, $error_diffusion);
        }
    }

    $threshold = 0x66;
    if (isset($_REQUEST['threshold'])) {$threshold = ($_REQUEST['threshold'] - 1) * 0x11;}

    for ($y = 0; $y < 32; $y++) {
        $data_string = "";
        for ($x = 0; $x < 32; $x++) {
            $rgb = imagecolorat($output, $x, $y);
            $gray = $rgb & 0xFF;
            if ($gray < 0xff - $threshold) {
                imagesetpixel($output, $x, $y, $black);
                $data_string .= "0";

            } else {
                imagesetpixel($output, $x, $y, $white);
                $data_string .= "1";
            }
        }
        $image_binary[$y] = $data_string;
    }

    $invert = 1;
    if (isset($_REQUEST['invert'])) {$invert = $_REQUEST['invert'];}
    if ($invert) {
        imagefilter($output, IMG_FILTER_NEGATE);
    }

}

function setupBasic()
{
    global $imageBW, $image_data, $image_data_2, $img, $image_binary, $image, $img_tmp, $image_color, $palette, $palette_Raw, $output;
    $img = 'watermelon.png';
    if (isset($_REQUEST['img'])) {$img = $_REQUEST['img'];}
    $image = @imagecreatefromstring(file_get_contents($img));
    $img_tmp = imagecreatetruecolor(32, 32);
    imagecopyresampled($img_tmp, $image, 0, 0, 0, 0, 32, 32, imagesx($image), imagesy($image));
    $image = $img_tmp;
    ob_start();
    imagepng($image);
    $image_color = ob_get_contents();
    ob_end_clean();
    $imageBW = @imagecreatefromstring(file_get_contents($img));
    $img_tmp = imagecreatetruecolor(32, 32);
    imagecopyresampled($img_tmp, $image, 0, 0, 0, 0, 32, 32, imagesx($image), imagesy($image));

    $imageBW = $img_tmp;
    $dither = 1;
    if (isset($_REQUEST['dither'])) {$dither = $_REQUEST['dither'];}
    if ($dither) {
        doDither($img_tmp, $imageBW);
    }
    $detect_levels = 2;
    if (isset($_REQUEST['levels'])) {$detect_levels = $_REQUEST['levels'];}
    detectColors($img, 16, $detect_levels, $palette, $palette_Raw);
    if (!$dither) {
        image2BW($imageBW);
    }
    ob_start();
    imagepng($imageBW);
    $image_data = ob_get_contents();
    ob_end_clean();
}

$palette = array();
$imageBW = 0;
$img = "";
function getPalette()
{
    global $palette;
    setupBasic();

    foreach ($palette as $color) {
        echo '<div style="background:#' . $color . ';"><div><p style="font-weight:bold;">#' . $color . '</p></div></div>';
    }
}

function getBWPreview()
{
    global $palette, $imageBW, $image_data, $image_data_2, $img_tmp;

    setupBasic();
    //Output the stuff
    echo '<img width=128 height=128 style="image-rendering: pixelated" ' .
    'src="data:image/png;base64,' . base64_encode($image_data) . '" alt="Red dot" />';
}
function getImgPreview()
{
    global $img, $image_color;

    setupBasic();
    echo '<img width=128 height=128 style="image-rendering: pixelated" src="' . $img . '" />';
    echo '<img width=128 height=128 style="image-rendering: pixelated" ' .
    'src="data:image/png;base64,' . base64_encode($image_color) . '" alt="Red dot" />';
}

function saveVMU()
{
    $folder = ".";
    if (isset($_REQUEST['folder'])) {$folder = $_REQUEST['folder'];}
    global $img, $image_binary, $palette, $palette_Raw, $image;
    setupBasic();

    //Write ICONDATA.VMS
    //$folder = sha1_file($_FILES['SelectedFile']['tmp_name']);
    if (!file_exists('.//upload//' . $folder)) {
        mkdir('.//upload//' . $folder);
    }
    $fp = fopen('.//upload//' . $folder . '//' . 'ICONDATA.VMS', 'w');
    //write name
    fwrite($fp, str_pad(substr($img, 0, 16), 16, " ", STR_PAD_RIGHT));
    //Write Header
    fwrite($fp, pack("H*", "20000000A00000000000000000000000"));
    //write b&w icon
    for ($x = 0; $x < 32; $x++) {
        $data = str_pad(dechex(bindec($image_binary[$x])), 8, "0", STR_PAD_LEFT);
        $data2 = pack("H*", $data);
        fwrite($fp, $data2);
    }
    //Write Color Palette
    $fPAL = fopen('.//upload//' . $folder . '//' . 'PALLETTE.BIN', 'w');
    for ($x = 0; $x < 16; $x++) {
        fwrite($fp, pack("H*", (isset($palette_Raw[$x])) ? $palette_Raw[$x] : "0000"));
        fwrite($fPAL, pack("H*", (isset($palette_Raw[$x])) ? $palette_Raw[$x] : "0000"));
    }
    fclose($fPAL);
    $fPAL = fopen('.//upload//' . $folder . '//' . 'IMAGE.BIN', 'w');
    for ($y = 0; $y < 32; $y++) {
        for ($x = 0; $x < 16; $x++) {
            $temp = "";
            //nibble high;
            $rgb = imagecolorat($image, $x * 2, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $color_rgb = sprintf('%02X%02X%02X', $r, $g, $b);
            $match = findColorMatch($color_rgb, $palette);
            $temp .= (string) dechex($match);
            //nibble low;
            $rgb = imagecolorat($image, ($x * 2) + 1, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $color_rgb = sprintf('%02X%02X%02X', $r, $g, $b);
            $match = findColorMatch($color_rgb, $palette);
            $temp .= (string) dechex($match);
            fwrite($fp, pack("H*", $temp));
            fwrite($fPAL, pack("H*", $temp));
        }

    }
    fclose($fPAL);
    //pad out i guess
    for ($y = 0; $y < 320; $y++) {
        fwrite($fp, pack("H*", "1A"));
    }
    //Done Finally!
    fclose($fp);
    //echo '<h3> Icon written Successfully!</h3>';
    writeVMI_ICON($folder);
}

function uploadImg()
{

// Check for errors
    if ($_FILES['SelectedFile']['error'] > 0) {
        outputJSON('An error ocurred when uploading.');
    }

    if (!getimagesize($_FILES['SelectedFile']['tmp_name'])) {
        outputJSON('Please ensure you are uploading an image.');
    }

/*// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
// Check MIME Type by yourself.
$finfo = new finfo(FILEINFO_MIME_TYPE);
if (false === $ext = array_search(
$finfo->file($_FILES['SelectedFile']['tmp_name']),
array(
'jpg' => 'image/jpeg',
'png' => 'image/png',
'gif' => 'image/gif',
),
true
)) {
throw new RuntimeException('Invalid file format.');
}
 */
    $ext = "tmp";
    $fileSHA1 = sha1_file($_FILES['SelectedFile']['tmp_name']);

// Check filesize
    if ($_FILES['SelectedFile']['size'] > 1000000) {
        outputJSON('File uploaded exceeds maximum upload size, 1MB');
    }

// You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    if (!move_uploaded_file(
        $_FILES['SelectedFile']['tmp_name'],
        $filename = sprintf('./upload/%s.%s',
            $fileSHA1,
            $ext
        )
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

//Check if image is bmp, or otherwise and then convert to png
    $tmpImg = imagecreatefrombmp($filename);
    if (!$tmpImg) {
        $tmpImg = @imagecreatefromstring(file_get_contents($filename));
    }

    unlink($filename);
    $filename = sprintf('./upload/%s.png', $fileSHA1);
    imagepng($tmpImg, $filename);

// Success!
    outputJSON($filename, 'success');
}

// Report all PHP errors (see changelog)
error_reporting(E_ALL);
$command = "NONE";
if (isset($_REQUEST['cmd'])) {$command = $_REQUEST['cmd'];}
switch ($command) {
    case "NONE":
        break;
    case "getPalette":
        getPalette();
        break;
    case "getBWPreview":
        getBWPreview();
        break;
    case "getImgPreview":
        getImgPreview();
        break;
    case "saveVMU":
        saveVMU();
        break;
    case "uploadImg":
        uploadImg();
    default:
        break;
}
