<?php /* Если на этой странице впервые

Если посетитель еще не был на этой странице - выдается текст перед разделителем |, если уже бывал - текст после |.

{_is_pervonah: Вы здесь впервые? | Хватит сюда ходить! _}

*/

function is_pervonah($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return md( empty($GLOBALS['page_pervonah']) ? c0($a) : c0($b));
}

?>