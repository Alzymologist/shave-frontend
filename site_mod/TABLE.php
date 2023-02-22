<?php /* Таблица

Быстрое создание таблиц

{_TABLE:
название | номер | цена | количество
штатиф | 1 | 28.6 | 12121
коробка | 2 | 25.6 | 12ыва121
рукомойник | 3 | 24.6 | 12ывы121
швабра | 4 | 28.6 | 12121
персонал | 5 человек| 27 | 12121
_}
*/

function TABLE($e) {
    $cf=array_merge(array(
	'BORDER'=>1,
	'CELLSPACING'=>0,
	'CELLPADDING'=>10,
	'align_left'=>'left',
	'align_right'=>'right'
    ),parse_e_conf($e)); $e=c0($cf['body']);

	$s="<center><table border=".h($cf['BORDER'])." cellspacing=".h($cf['CELLSPACING'])." cellpadding=".h($cf['CELLPADDING']).">";
	$c=(strstr($e,"\n\n")?"\n\n":"\n");
	$p=explode($c,$e);
		foreach($p as $l) { $l=c($l); $s.="<tr valign='top' align='".$cf['align_left']."'><td>".preg_replace("/\s*\|\s*/s","</td><td align='".$cf['align_right']."'>",$l)."</td></tr>"; }
	return $s."</table></center>";
}

?>