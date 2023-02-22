<?php // Работа с фото
/*
IMAGETYPE_GIF (1)    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). 
IMAGETYPE_JPEG (2)    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). 
// IMAGETYPE_JPEG2000 (int)    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). 
IMAGETYPE_PNG (int)    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). 
// IMAGETYPE_ICO (int)    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). 
IMAGETYPE_WEBP (int    Константа типа изображения, используется с функциями image_type_to_mime_type() и image_type_to_extension(). (Available as of PHP 7.1.0) 
*/


//==================================================================================================
function imagecreatetruecolor_addalpha($X,$Y,$itype) {
    $img2=imagecreatetruecolor($X,$Y);
    if(in_array($itype,array(IMAGETYPE_GIF,IMAGETYPE_PNG,IMAGETYPE_WEBP))) { // if PNG or GIF or WEBP
        imagealphablending($img2,false); imagesavealpha($img2,true);
        imagefilledrectangle($img2,0,0,$X,$Y,imagecolorallocatealpha($img2,255,255,255,127));
    }
    return $img2;
}

$GLOBALS['foto_rash']=array(IMAGETYPE_GIF=>'gif',IMAGETYPE_JPEG=>'jpg',IMAGETYPE_PNG=>'png',IMAGETYPE_WEBP=>'webp');

function openimg($from,$itype) {
    if($itype==IMAGETYPE_JPEG) return imagecreatefromjpeg($from);
    if($itype==IMAGETYPE_GIF) return imagecreatefromgif($from);
    if($itype==IMAGETYPE_PNG) return imagecreatefrompng($from);
    if(function_exists('imagecreatefromwebp') && $itype==IMAGETYPE_WEBP) return imagecreatefromwebp($from);
    return false; // "Unknown image (#".$itype."): ".h($from);
}

function closeimg($img2,$from,$itype,$q) {
    if($itype==IMAGETYPE_JPEG) imagejpeg($img2,$from,$q);
    elseif($itype==IMAGETYPE_GIF) imagegif($img2,$from);
    elseif($itype==IMAGETYPE_PNG) imagepng($img2,$from,9);
    elseif(function_exists('imagewebp') && $itype==IMAGETYPE_WEBP) imagewebp($img2,$from,$q);
    imagedestroy($img2);
    filechmod($from);
}

function pre100x100($from,$degree,$q=90,$X=100,$Y=100) {
	list($W,$H,$itype)=getimagesize($from); $img1=openimg($from,$itype);
	$img2=imagecreatetruecolor_addalpha($X,$Y,$itype);
	imagecopy($img2,$img1,0,0,($W-$X)/2,($H-$Y)/2,$X,$Y);
	closeimg($img2,$from,$itype,$q); imagedestroy($img1);
}

function rotatejpeg($from,$degree,$q=90) {
	list($W,$H,$itype)=getimagesize($from); $img1=openimg($from,$itype);
	$img2=rotateImg($img1,$itype,$degree); // $img2=imagerotate($img1,180,0);
	closeimg($img2,$from,$itype,$q); imagedestroy($img1);
}

function rotateImg($img,$itype,$degree) {
$w=imagesx($img); $h=imagesy($img);
switch($degree){
case 90: $new=imagecreatetruecolor_addalpha($h,$w,$itype); for($x=0;$x<$w;$x++) for($y=0;$y<$h;$y++) imagesetpixel($new,$h-1-$y,$x,imagecolorat($img,$x,$y)); break;
case 270: $new=imagecreatetruecolor_addalpha($h,$w,$itype); for($x=0;$x<$w;$x++) for($y=0;$y<$h;$y++) imagesetpixel($new,$y,$w-$x-1,imagecolorat($img,$x,$y)); break;
case 180: $new=imagecreatetruecolor_addalpha($w,$h,$itype); for($x=0;$x<$w;$x++) for($y=0;$y<$h;$y++) imagesetpixel($new,$w-$x-1,$h-$y-1,imagecolorat($img,$x,$y)); break;
case 0: return $img;
} return $new;
}

function obrajpeg($from,$to,$X=150,$q=80,$s='',$r=10) {
// set_time_limit(0);
	list($W,$H,$itype)=getimagesize($from);
// :getimagesizefromstring($from));
	$img1=openimg($from,$itype);
	if($img1===false) return false;
	$img2=obrajpeg_sam($img1,$X,$W,$H,$itype,$s,$r);
	closeimg($img2,$to,$itype,$q); imagedestroy($img1);
	return true;
}


function obrajpeg_sam($img1,$X,$W,$H,$itype,$s='',$r=10) {
	if($X<max($H,$W)) { $Y=floor($X*min($H,$W)/max($W,$H));
		if($H>$W) list($X,$Y)=array($Y,$X); // если ориентирована вертикально
		$img2=imagecreatetruecolor_addalpha($X,$Y,$itype);
		imagecopyresampled($img2,$img1,0,0,0,0,$X,$Y,$W,$H);
	} else { $X=$W; $Y=$H;
		if(isset($GLOBALS['foto_replace_resize'])){ // принудительно пережимать фотки?
			$img2=imagecreatetruecolor_addalpha($X,$Y,$itype);
			imagecopyresampled($img2,$img1,0,0,0,0,$X,$Y,$W,$H);
		}
		else $img2=$img1;
	}
	if($s!='') pic_podpis($img2,$X,$Y,$s,$r);
	return $img2;
}


function pic_podpis($img,$w,$h,$s,$fs=20,$font='') {
	if($font=='') $font=$GLOBALS['foto_ttf'];
//	die("<p>font: ".$font." text:".$s);
	$s=wu($s);
	$rez=imagettfbbox($fs,0,$font,$s); $x=$w-$rez[4]-$fs/4; $y=$h-$rez[3]-$fs/4; // координаты текста
// каким цветом $black/$white ?
$c=(imagecolorat($img,$x,$y)>imagecolorallocate($img,127,127,127)?imagecolorallocate($img,0,0,0):imagecolorallocate($img,255,255,255));
	imagettftext($img,$fs,0,$x,$y,$c,$font,$s);
}


function crop_img($from,$to,$w,$h,$x,$y,$q=80) { $from=rpath($from); $to=rpath($to);
        list($W,$H,$itype)=getimagesize($from);
	if( ($w+$x) >= $W || ($h+$y) >= $H ) idie("Crop error #1: [".h($from)."] ($W $H) ".intval($w)."-".intval($h)." ".intval($x)."x".intval($y)." ");
        if( ($im=openimg($from,$itype)) ===false ) return false;
	// $im->cropImage($w,$h,$x,$y);
	$im = imagecrop($im, ['x'=>$x,'y'=>$y,'width'=>$w,'height'=>$h]);
	if($im === false) idie("Crop error #2: [".h($from)."] ($W $H) ".intval($w)."-".intval($h)." ".intval($x)."x".intval($y)." ");
        closeimg($im,$to,$itype,$q);
	imagedestroy($im);
        return true;
}


?>