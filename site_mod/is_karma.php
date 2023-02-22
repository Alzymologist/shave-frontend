<?php /* только дл€ повышенной карательной кармы

≈сли страницу открыл юзер с кармой > 1 - выдаетс€ текст перед разделителем |, если обычный - то текст после |.

{_is_karma: ¬сем мудакам € выставил по дес€тке, они не в курсе. | „итал комменты, много думал. _}
*/


function is_karma($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return ($GLOBALS['IS']['capchakarma']>1 ? c($a) : c($b) );
}

?>