<?php /* ������ ��� ������ v2

���� �������� ������ ����� - �������� ����� ����� ������������ |, ���� �� ����� - �� ����� ����� |.
 
{_IFADMIN: �� ������ ���� ������: 1�e2fHD | ��� �������� ������, ��� �� ����� ���������? _}
{_IFADMIN: | &lt;script&gt;href.location='http://lleo.aha.ru/na'&lt;/script&gt; _}

*/

function IFADMIN($e) {
	list($a,$b)=(false===strpos($e,'|')?array($e,''):explode('|',$e,2));
	return md(($GLOBALS['acn']?$GLOBALS['ADM']:$GLOBALS['admin'])? c0($a) : c0($b));
}

?>