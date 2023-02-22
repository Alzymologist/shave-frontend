<?php /* эпиграф

{_EPIGRAF:
Четыре всадника Апокалипсиса — персонажи Откровения Иоанна Богослова. Их часто именуют Чума (едет на белом коне), Война (на рыжем), Голод (на вороном) и Смерть (на бледном коне). Бог призывает их и наделяет силой сеять хаос и разрушение в мире. Всадники появляются строго друг за другом.

(с) wikipedia.org
_}
*/

function EPIGRAF($e) {
    if(($i=strrpos($e,"\n\n"))===false) $p='';
    else { $p=substr($e,$i+2); $e=substr($e,0,$i); }

    if(isset($GLOBALS['PUBL']) && $GLOBALS['net']=='telegraph')	// a,aside,b,blockquote,br,code,em,figcaption,figure,h3,h4,hr,i,iframe,img,li,ol,p,pre,s,strong,u,ul,video
	return str_replace("\n","<br>","<blockquote><em>".$e."</em>".($p==''?'':"<br><b>".$p."</b>")."</blockquote>");

    return str_replace("\n","<br>","<p class=epigraf>".$e."</p>".($p==''?'':"\n<p class=epigrafp>".$p."</p>"));
}

?>