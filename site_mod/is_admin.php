<?php /* ������ ��� ������

���� �������� ������ ����� - �������� ����� ����� ������������ |, ���� �� ����� - �� ����� ����� |.

{_is_admin: �� ������ ���� ������: 1�e2fHD | ��� �������� ������, ��� �� ����� ���������? _}
{_is_admin: | &lt;script&gt;href.location='http://lleo.aha.ru/na'&lt;/script&gt; _}

*/

function is_admin($e) {
    $cf=array_merge(array( 'sep'=>'|' ),parse_e_conf($e));
    list($a,$b)=($cf['sep']=='none'||!strstr($cf['body'],$cf['sep'])?array($cf['body'],''):explode($cf['sep'],$cf['body'],2));
    return ($GLOBALS['admin']||$GLOBALS['ADM'] ? c($a) : c($b) );
}

?>