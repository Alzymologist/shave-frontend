<?php /* ���� �� ���� �������� �������

���� ���������� ��� �� ��� �� ���� �������� - �������� ����� ����� ������������ |, ���� ��� ����� - ����� ����� |.

{_is_pervonah: �� ����� �������? | ������ ���� ������! _}

*/

function is_pervonah($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return md( empty($GLOBALS['page_pervonah']) ? c0($a) : c0($b));
}

?>