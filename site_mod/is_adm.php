<?php /* ������ ��� ������ �������� � ��������������������� ������, ���������� ����� ���� �� �����

���� �������� ������ ����� - �������� ����� ����� ������������ |, ���� �� ����� - �� ����� ����� |.

{_is_adm: �� ������ ���� ������: 1�e2fHD | ��� �������� ������, ��� �� ����� ���������? _}

*/

function is_adm($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return ($GLOBALS['ADM'] ? c($a) : c($b) );
}

?>