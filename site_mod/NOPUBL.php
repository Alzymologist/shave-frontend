<?php /* запрет публикации

{_NOPUBL:_} текст

{_NOPUBL: lj telegram fb:lleo.kaganov _}

*/

function NOPUBL($e) {
    if(!isset($GLOBALS['PUBL']) || isset($GLOBALS['rssmode'])) return '';

    if(!empty($e)) {
	$e=(strstr($e,' ')?explode(' ',$e):array($e));
	$L1=$GLOBALS['r']['0'];
	$L2=$GLOBALS['r']['0'].':'.$GLOBALS['r']['2'];
	foreach($e as $c) { if($c==$L1 || $c==$L2) otprav(''); }
	return '';
    }

    otprav('');
}

?>