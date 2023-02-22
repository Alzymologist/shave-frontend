<?php /* только дл€ подзамочных друзей

≈сли страницу открыл посетитель, с доступом 'podzamok' - выдаетс€ текст перед разделителем |, если обычный 'user' - то текст после |.

{_is_podzamok: »сходники тут: http://10.8.0.1/rrr.zip | »сходники решил пока не выкладывать. _}
{_is_podzamok:  ак много набежало идиотов! | _}

*/

function is_podzamok($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));

	if(!$GLOBALS['podzamok']) return md(c0($b));
	$a=c0($a);
	if(strstr($a,"\n")) return md("<div style=\"background-color:".$GLOBALS['podzamcolor']."\">"
    ."<i class='e_podzamok'></i>&nbsp;"
    .$a."</div>");

	return md("<span style=\"background-color:".$GLOBALS['podzamcolor']."\">$a</span>");

}

?>