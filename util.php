<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

// Output JSON
function outputJSON($msg, $status = 'error'){
    header('Content-Type: application/json');
    die(json_encode(array(
        'data' => $msg,
        'status' => $status
    )));
}

function createZipAndDownload($folder) {
	
    $file_names = array('ICONDATA.VMI','ICONDATA.VMS');
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open('.//upload//'.$folder.'//tmp.zip', ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach($file_names as $files)
    {
        $zip->addFile('.//upload//'.$folder.'//'.$files,$files);
    }
    $zip->close();
    
    //also zip up vmu image stuff
    $file_names = array('PALLETTE.BIN','IMAGE.BIN');
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open('.//upload//'.$folder.'//vmu.zip', ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach($file_names as $files)
    {
        $zip->addFile('.//upload//'.$folder.'//'.$files,$files);
    }
    $zip->close();
    //then send the headers to force download the zip file
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=ICONDATA.ZIP"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile('.//upload//'.$folder.'//tmp.zip');
    exit;
}

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

function ConvertBMP2GD($src, $dest = false) {
    if(!($src_f = fopen($src, "rb"))) {
    return false;
    }
    if(!($dest_f = fopen($dest, "wb"))) {
    return false;
    }
    $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,
    14));
    $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
    fread($src_f, 40));
    
    extract($info);
    extract($header);
    
    if($type != 0x4D42) { // signature "BM"
    return false;
    }
    
    $palette_size = $offset - 54;
    $ncolor = $palette_size / 4;
    $gd_header = "";
    // true-color vs. palette
    $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
    $gd_header .= pack("n2", $width, $height);
    $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
    if($palette_size) {
    $gd_header .= pack("n", $ncolor);
    }
    // no transparency
    $gd_header .= "\xFF\xFF\xFF\xFF";
    
    fwrite($dest_f, $gd_header);
    
    if($palette_size) {
    $palette = fread($src_f, $palette_size);
    $gd_palette = "";
    $j = 0;
    while($j < $palette_size) {
    $b = $palette{$j++};
    $g = $palette{$j++};
    $r = $palette{$j++};
    $a = $palette{$j++};
    $gd_palette .= "$r$g$b$a";
    }
    $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
    fwrite($dest_f, $gd_palette);
    }
    
    $scan_line_size = (($bits * $width) + 7) >> 3;
    $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
    0x03) : 0;
    
    for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
    // BMP stores scan lines starting from bottom
    fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
    $l));
    $scan_line = fread($src_f, $scan_line_size);
    if($bits == 24) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $b = $scan_line{$j++};
    $g = $scan_line{$j++};
    $r = $scan_line{$j++};
    $gd_scan_line .= "\x00$r$g$b";
    }
    }
    else if($bits == 8) {
    $gd_scan_line = $scan_line;
    }
    else if($bits == 4) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $byte = ord($scan_line{$j++});
    $p1 = chr($byte >> 4);
    $p2 = chr($byte & 0x0F);
    $gd_scan_line .= "$p1$p2";
    } $gd_scan_line = substr($gd_scan_line, 0, $width);
    }
    else if($bits == 1) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $byte = ord($scan_line{$j++});
    $p1 = chr((int) (($byte & 0x80) != 0));
    $p2 = chr((int) (($byte & 0x40) != 0));
    $p3 = chr((int) (($byte & 0x20) != 0));
    $p4 = chr((int) (($byte & 0x10) != 0));
    $p5 = chr((int) (($byte & 0x08) != 0));
    $p6 = chr((int) (($byte & 0x04) != 0));
    $p7 = chr((int) (($byte & 0x02) != 0));
    $p8 = chr((int) (($byte & 0x01) != 0));
    $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
    } $gd_scan_line = substr($gd_scan_line, 0, $width);
    }
    
    fwrite($dest_f, $gd_scan_line);
    }
    fclose($src_f);
    fclose($dest_f);
    return true;
    }
    
function imagecreatefrombmp($filename) {
$tmp_name = tempnam("/tmp", "GD");
if(ConvertBMP2GD($filename, $tmp_name)) {
$img = imagecreatefromgd($tmp_name);
unlink($tmp_name);
return $img;
} return false;
}

?>