<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ������ - �� ����, �� ��

	ini_set("display_errors","1");
	ini_set("display_startup_errors","1");
	ini_set('error_reporting', E_ALL); // �������� ��������� �� �������

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>"�������� ��� ������",
'title'=>"�������� ��� ������",

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);

$txt1=RE('txt1');
$txt2=RE('txt2');

$o="<form action=".$mypage." method=post><center>
<p>�����1:<br><textarea name='txt1' cols=80 class=t>".h($txt1)."</textarea>
<br>�����2:<br><textarea name='txt2' cols=80 class=t>".h($txt2)."</textarea>
<p><input type='submit' name='go' value='��������'>
</form>";

if($txt1==''||$txt2=='') die($o."</center>");

$pi=similar_text($txt1,$txt2,$percent);

$o.= "

<style>
.p1 { color: #3F3F3F; text-decoration: line-through; background: #DFDFDF; } /* ����������� */
.p2 { background: #FFD0C0; } /* ����������� */
</style>

<p>���������:
<table border=1 cellspacing=0 cellpadding=10 width=90%><td> [$pi] $percent %</td></table>
<p>&nbsp;";

die($o."</center>");

?>