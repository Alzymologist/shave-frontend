<?php /* ������ ��� ���������� ����������� �����

���� �������� ������ ���� � ������ > 1 - �������� ����� ����� ������������ |, ���� ������� - �� ����� ����� |.

{_is_karma: ���� ������� � �������� �� �������, ��� �� � �����. | ����� ��������, ����� �����. _}
*/


function is_karma($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return md($GLOBALS['IS']['capchakarma']>1 ? c0($a) : c0($b) );
}

?>