<?php /* ≈сли пришел на страницу с определенного refferer

≈сли страницу открыл пришедший по указанной ссылка (ссылка или ее перва€ часть указана первой, после нее пробел) - выдаетс€ текст перед разделителем |, если не админ - то текст после |.

{_is_ref: http://eushestakov.f5.ru Ўестаков-дурак | ...Ќормальный текст заметки... _}

*/

function is_ref($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($ref,$cf['body'])=explode(' ',$cf['body'],2);
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));

    return md( strstr($_SERVER['HTTP_REFERER'],c0($ref)) ? c0($a) : c0($b));
}

?>