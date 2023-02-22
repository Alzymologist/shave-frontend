<?php /* только для админа

Если страницу открыл админ - выдается текст перед разделителем |, если не админ - то текст после |.

{_is_admin: Не забудь свой пароль: 1Кe2fHD | Это закрытая запись, как вы здесь оказались? _}
{_is_admin: | &lt;script&gt;href.location='http://lleo.aha.ru/na'&lt;/script&gt; _}

*/

function is_admin($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return ($GLOBALS['admin']||$GLOBALS['ADM'] ? c($a) : c($b) );
}

?>