<?php /* ?????? ????? ??? ?????? ????????? v2

???????????: | ? ?????? ????????? ?? ????????? ????? ??? ?????? ? ????? ???????? ?????????? (???? ?? ????????? - ?? ????? ???????).
???? ? ????? ???????? ?????????? ???? ????? ????? - ????????? ????????? ???????? ?? ??????????? |, ???? ?? ??????? - ?????????.

{_IFBRO: linux,nokia | ?, ??? ? ??, ????????? ???????? ??? ??????! | ?????????! ? ??? ?? ??????! _}
*/

function IFBRO($e) { if($e=='') return $GLOBALS['BRO'];
	list($l,$a,$b)=explode('|',$e);
	$p=explode(',',$l);
	foreach($p as $l) if(stristr($GLOBALS['BRO'],c0($l))) return md(c0($a));
	return md(c0($b));
}

?>