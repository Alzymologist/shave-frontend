<?php /* ������������

{_LLEOP: [R,F] [+]2010 arhive/text.html ������� ��� ���� _}
*/

STYLES("
.lltR,.lltF,.lltR0,.lltF0 { font-size: 16px;}
.llR,.llR0 { color: red; }
.llF,.llF0 { color: #800080; }
.llLN { text-decoration: none; }
.llLN:hover { text-decoration: underline; }
");

//border: 1px dotted transparent; border: 1px dotted gray; 
// $GLOBALS['llcolors']=array('R'=>'red','F'=>'#800080');

$GLOBALS['BOOKS_NAME']=array(
'G'=>"�������� �� �������<br>�������� ������",
'KLF'=>"�������� �� �������<br>���Ի",
'EQ'=>"�������� ��<br>������������ �������",
'OWES'=>"�������� ��<br>�����-�������",
'K'=>"�������� �� �����-�� �������",
'TENETA'=>"�������� �� �������<br>����-��ͨ���",

'sq'=>"������ � �������<br>����� �������� � ������� ����������",
'masha'=>"������ � �������<br>����� ����",
'roman'=>"������ � �������<br>������ � ������",
'harizma'=>"������ � �����<br>��������",
'dbk'=>"������ � �������<br>���� ��� �������",
'komm'=>"������ � �������<br>������������",
'belka'=>"������ � �������<br>�������� �����",
'pohel'=>"������ � �������<br>����� ��������� �������",
'epos'=>"������ � �������<br>����� �������"
);

function LLYEAR($e,$only=false) { $x='';
		if(substr($e,0,1)=='+') { $x.="<img src='/arhive/bookimg/new.gif' title='����������������'>&nbsp;"; $e=substr($e,1); }
		if(!strstr($e,'_')) return $e;
		list($a,$e)=explode('_',$e,2); $b=(strstr($a,',')?explode(',',$a):array($a));
		if($only) $only=explode('_',$only);
		foreach($b as $l) {
		    if($only && !in_array($l,$only)) continue;
//		    if($l=="K") $x.="<span title='������ � �������<br>�".$GLOBALS['BOOKS_NAME'][$l]."�'>&nbsp;";
		    $x.="<img width=16 height=16 src='/arhive/bookimg/books-".h($l).".png' title='".$GLOBALS['BOOKS_NAME'][$l]."�'>&nbsp;";
		}
		return $x.$e;
}

function LLEOPS($e) {
	$cf=array_merge(array(
		'only'=>false,
		'style'=>"R",
//		'new'=>"<img src='/new.gif'>&nbsp;",
		'template'=>"<div class='llt{style}'><span class='ll{style}'>{year}</span>&nbsp;{link}{text}{linka}</div>"
	),parse_e_conf($e));

$pdir=('/'!=substr($GLOBALS['MYPAGE'],strlen($GLOBALS['MYPAGE'])-1,1) ? $GLOBALS['MYPAGE'].'/' : '');

	$s=''; foreach(explode("\n",$cf['body']) as $p) { if(empty($p)) continue;
		list($cf['year'],$lnk,$cf['text'])=@explode(' ',$p,3);

		$cf['year']=LLYEAR($cf['year'],$cf['only']);

		if($lnk=='.') { $cf['link']=$cf['linka']=''; }
		else { if(!strstr($lnk,'://') && '/'!=substr($lnk,0,1)) $lnk=$pdir.$lnk;
		$cf['link']="<a class='llLN' href='".h($lnk)."'>"; $cf['linka']="</a>"; }
		$s.=mper($cf['template'],$cf); // array_merge($cf,array('new'=>$n)));
	}
	return $s;
}
?>