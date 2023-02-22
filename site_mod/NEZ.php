<?php

function NEZ($e) {
    $td=date('Y-m-d');
    $tmpf="tmp/nez-";
    $ff=$tmpf.$td.".webp";
    $or="design/nez.webp";
    $file=$GLOBALS['filehost'].$ff;
    $web=$GLOBALS['wwwhost'].$ff;

    if(!is_file($file)) {
        $g=glob($GLOBALS['filehost'].$tmpf."*.webp"); foreach($g as $l) unlink($l); // почистить
        $t=time();
        $orig=$GLOBALS['filehost'].$or;
        $dn=floor(($t-1645650000)/86400);
        $img=imagecreatefromwebp($orig);
        $fontsize=55;
        $color = imagecolorallocate($img, 0x2F, 0x69, 0xC2); // 2f69c2
        $font = $GLOBALS['filehost']."design/ttf/ARIAL.TTF"; // ARIAL.TTF MTCORSVA.TTF PTC55F.ttf
        $bbox = imageftbbox($fontsize, 0, $font, $dn);
        $w = $bbox[2] - $bbox[6];
        $h = $bbox[3] - $bbox[7];
        imagefttext($img,$fontsize,0,(534-$w)/2,(461+$h)/2+50,$color,$font,$dn); // 240 300
        imagewebp($img,$file); // Сохранение рисунка
        imagedestroy($img); // Освобождение памяти и закрытие рисунка
    }

    if(empty($e)) {

    $k=0.3;
    return "<img src='".$web."' style='width:".(525*$k)."px;height:".(454*$k)."px;"
    ."padding-right:20px;"
    ."pointer-events: none;"
    ."position:absolute;top:0;right:0;transform:rotate(-12deg);z-index:30;'>";

    } else return $web;
}

?>