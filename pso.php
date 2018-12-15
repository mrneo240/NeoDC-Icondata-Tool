<?php
/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class PSOImage { 
    private $ImgWidth	= 256;
    private $ImgHeight	= 192;

    // Screen Shot Format
    private $SS_ImageTop    = 0x284;
    private $SS_ImageLen	= 0;//$this->ImgWidth * $this->ImgHeight;
    private $SS_FileLen		= 99360;
    
    private $Buffer;
    
    private $image;
    
    public function __construct()
    {
        $this->SS_ImageLen	= $this->ImgWidth * $this->ImgHeight;
        $this->Buffer = "";
    }

    public function readFile($filename){
        $handle = fopen($filename, "rb");
        $this->Buffer = fread($handle, filesize($filename));
        fclose($handle);
        $this->swapEndian();
        $this->parse();
    }
    
    private function swapEndian(){
    /*for($i=0; $i<$SS_FileLen/4; $i++){
    	($a,$b,$c,$d) = unpack("CCCC",substr($Buffer,$SS_ImageTop+$i*4,4));
    	//substr($buf,$i*4,4) = pack("CCCC",$d,$c,$b,$a); //Only needed for DCI because byteswapped
        substr($buf,$i*4,4) = pack("CCCC",$a,$b,$c,$d);
    }*/
    }
    
    private function parse(){
        $this->image = imagecreatetruecolor($this->ImgWidth, $this->ImgHeight);
        $background = imagecolorallocate($this->image, 0, 0, 0);    
            
        $header_format = 
                'C1lo/' .
                'C1hi';
                
        for($y=0; $y<$this->ImgHeight; $y++){
            for($x=0; $x<$this->ImgWidth; $x++){
                //$bytes = unpack("CC",substr($buf,$this->SS_ImageTop+($x+$y*$this->ImgWidth)*2,2)); //for DCI only
                $bytes = unpack ($header_format, substr($this->Buffer,$this->SS_ImageTop+(($x+$y*$this->ImgWidth)*2),2));
                        
                $pal = ($bytes['hi']<<8)+$bytes['lo'];
                $r = ($pal>>8) & 0xf8;	if ($r){$r += 0x7;}
                $g = ($pal>>3) & 0xfc;	if ($g){$g += 0x3;}
                $b = ($pal<<3) & 0xf8;	if ($b){$b += 0x7;}
                
                $color = imagecolorallocate($this->image, $r,$g,$b);
                imagesetpixel($this->image,$x,$y,$color);
            }
        }
    } 
    public function display(){
        ob_start();
        imagepng($this->image);
        $image_data = ob_get_contents();
        ob_end_clean();
        echo '<img width='.$this->ImgWidth*2 .' height='.$this->ImgHeight*2 .' style="image-rendering: pixelated" 
        src="data:image/png;base64,'.base64_encode($image_data).'" alt="color image" />';
        imagedestroy($this->image);
    }
}

$foo = new PSOImage; 

$foo->readFile("PSO_____.VMS");
$foo->display();


?>