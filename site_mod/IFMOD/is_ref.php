<?php /* ���� ������ �� �������� � ������������� refferer

���� �������� ������ ��������� �� ��������� ������ (������ ��� �� ������ ����� ������� ������, ����� ��� ������) - �������� ����� ����� ������������ |, ���� �� ����� - �� ����� ����� |.

{_is_ref: http://eushestakov.f5.ru ��������-����� | ...���������� ����� �������... _}

*/

function is_ref($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($ref,$cf['body'])=explode(' ',$cf['body'],2);
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));

    return md( strstr($_SERVER['HTTP_REFERER'],c0($ref)) ? c0($a) : c0($b));
}

?>