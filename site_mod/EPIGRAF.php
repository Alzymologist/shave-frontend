<?php /* �������

{_EPIGRAF:
������ �������� ������������ � ��������� ���������� ������ ���������. �� ����� ������� ���� (���� �� ����� ����), ����� (�� �����), ����� (�� �������) � ������ (�� ������� ����). ��� ��������� �� � �������� ����� ����� ���� � ���������� � ����. �������� ���������� ������ ���� �� ������.

(�) wikipedia.org
_}
*/

function EPIGRAF($e) {
    if(($i=strrpos($e,"\n\n"))===false) $p='';
    else { $p=substr($e,$i+2); $e=substr($e,0,$i); }

    if(isset($GLOBALS['PUBL']) && $GLOBALS['net']=='telegraph')	// a,aside,b,blockquote,br,code,em,figcaption,figure,h3,h4,hr,i,iframe,img,li,ol,p,pre,s,strong,u,ul,video
	return str_replace("\n","<br>","<blockquote><em>".$e."</em>".($p==''?'':"<br><b>".$p."</b>")."</blockquote>");

    return str_replace("\n","<br>","<p class=epigraf>".$e."</p>".($p==''?'':"\n<p class=epigrafp>".$p."</p>"));
}

?>