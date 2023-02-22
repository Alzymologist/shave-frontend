<?php /* ������ ANONS

��������� ����� ������ ANONS. ��� ���������� ������ � ������� ����� ����� ��������� ����� ������, ���, ��������:
- ������� 10 ��������� ��������
- ������� �������, ������� �� ������ 30 ����
- ������� ������ �� ��� �������� � ������ ?�������?, ?������? ��� ?������� �������?
- ������� ������ �� ��������, ������� �� (��, ������ ��!) ��� �� �������� ����� ���������
- �������� ������� ��������� ��� �������� ����� ������ � ������� �� ����������� � �.�.

��� ����� ������ ���� ������ ANONS, �� ����� ������ ��� ����������������� � ����� ����������� � ����������. ���, ��� ������, ����� ���������� ���� � �������, ���� � �������� ����� ��������.

��� ������ ��� ��������:

acn - ����� ��������, � ������� ���� ������� (�� ��������� - �� ���� ��������� �������) ������ �����������: ����������� �� ��� ��������, � ��� �����.

mode - ��� �������, ��������:
- all (�� ���������) - ��� ��������
- blog - ������ �������� �����
- page - ������ ����������� ��������

tags - ���� �� ����� (�� ��������� �����), �� ������� ����� ����������� �� �����, ������������� ����� ����� �������

cut - ���� �� ����� (�� ��������� �����), �� �������� �� ������� ������ ����� ������ (��������� *) ����� ���������� �������������, ������: cut=<div class=stihi>{@STIH:*@}</div>, ������� ��������, ��� � ����� ���� _ ���������� �� @

tags_and - ��� ���������� ����, ���� �� � ������ ���������. ��������:
- OR (�� ���������) - �������� ������, ���������� ���� �� �����-�� �� ������������� �����
- AND - �������� ������, ���������� ��� ������������� ����

limit - �������� ������������ ����� �������, �� ��������� 20, 'random' - ���� ���������

days - �������� ������ ������ �� ��������� N ���� (�� ��������� 0 ? �������� ���)

sort - ����������� ������:
- date (�� ���������) - �� ���� ������� �����
- update - �� ������� ���������� ����������
- name - �� ����� �������

sortx - ��� ����������� ������: DESC (�� ���������) �������� ����������, ����� (������ ������� '') - ������

// podzamok - �� ��������� 0, �� ���� 1, �� ��� ����������� ������ ����� ���������� ������ ����������� ������, ��� ���� ��������� - ������ ������

length - ����� ���� � ������� ������, ���� 0 - �� ����� ������� (�� ��������� 200)

media - ���� 0 (�� ���������) �� �������� ����� ����� ��� �������, �������� � �.�. ��� media=1 �������� length ���� �� ������ - ������� ��������� ������� 2 - �������� �� html

showmy - ���������� �� � ������ ���� ��� ������� 1 - ��������, 0 (�� ���������) - ���������

template - html-������ ������, �� ���������:
<div style='text-align:left; padding: 10px 0 10px 0; font-size:12px;'><b>{Y}-{M}-{D}: {Header}</b><br>{Body}&nbsp;<a href='{link}'>(...)</a></div>\n\n
�� ����� �������, � {Header}, {Body}, {link} ������������� ��������� �������, �� ���������� � ������ �� ���, {Y} {M} {D} ? ���, ����� � ���� ������� (���� ������� �����)

{BodySrc} - ����� ����� Body, ��� ����������� �����

template_me - html-������ ������ ��� �������, �� ������� �� ������ ���������, �� ��������� - ������ (�� �������� �)

��������� �������� ������ ������:

1. ������� ��� ������ �� ������� � ����� ?� ���� � ����? ��� ?�������?

{_ANONS:
template = <div style='border: 1px dotted red'>{Y}-{M}-{D}: <a href='{link}'>{Header}</a></div>
tags = �������, � ���� � ����
days = 0
_}

2. ������� 5 ����� ������ �� ������������� ����������� �������

{_ANONS:
template = <div style='border: 1px dotted red'>{Y}: <a href='{link}'>{Header}</a></div>
mode = page
sort = update
sortx = ''
limit = 5
_}

3. ������� 10 ��������� ������� ����� � ����� ?������?, ������� ����� �� (����������) ��� �� �����, �� �� ������ 365 ����, � ���������� � ������� ������� � 60 ��������. ���������� �� �� �������� ������� � ������� �������.

<div style='border: 3px solid green; padding: 10px;'>{_BLOKI: WIDTH=150
{_ANONS:
template = <div class=r><font color=red>{Y}/{M}/{D}:</font><br><b>{Header}</b><br>{Body} <a href='{link}'>������&nbsp;�����</a></div>\n\n
days = 365
mode = blog
sort = date
tags = ������
limit = 10
length = 60
unread = 1
_}_}</div>


PS: ��������� ���������� $GLOBALS['ANONS_count'] ��������������� ������ ����� ��������� ������ ��� ��������� �������.
��� ����� ����� ����� ��������, ��������, �������� {_ANONS:count_}, �� ������ ������ ����������� ��� ������.

*/

include_once $GLOBALS['include_sys']."_onetext.php";

function ANONS($e) {
	if($e=='count') return $GLOBALS['ANONS_count'];

	$oldarticle=$GLOBALS['article'];
	$old_premodule_enable=isset($GLOBALS['premodule_enable'])?$GLOBALS['premodule_enable']:0;
	$GLOBALS['premodule_enable']=0;

$conf=array_merge(array(
'acn'=>false,
'cut'=>false,
'redirect'=>0, // ������ �������� �� ����� ������ ������� �� ��������� �� ���� ���������
'mode'=>'all', // ��� �������: 'all' - ���, 'blog' - ������ ����, 'page' - ����������� ��������, count - ������ �����
'unread'=>0, // �� �������� �����������
'dostup'=>'admin',
// 'podzamok'=>0, // ������ ��������
'tags'=>'', // ������� �� ���� (���� ����� - �� ��� ������), ���� ����� ����������� ����� �������
'tags_and'=>'OR', // ����������� ����� OR ��� AND
'limit'=>20, // ������������ ����� �������
'days'=>0, // ���������� ���������� N ����� (0 - ��� �����������)
'timeformat'=>"Y-m-d",
'sort'=>'date', // ����������: 'date' - �� ���� �������, 'update' - �� ���������� ����������
'sortx'=>'DESC', // ����������: 'DESC' - ����� ����� ������; ���� '' - �� ��������
'length'=>200, // ����� ���� � ������� ������, ���� 0 - �� ���� �������
'length1'=>100, // ����� ���� � ������� Body1
'media'=>0, // 0 - ����� ����� (��� �������, �������� � �.�.)
'list'=>0, // 1 - ������ ����������
'list_element'=>"<div>{numer}. <a href='#L{num}'>{Header}</a></div>", // �������� �������� ����������
'list_tmpl'=>"<div><h2>����������</h2></div><div>{list}</div>", // �������� ����������
'preBody'=>'',
'postBody'=>'',
'template_me'=>"",
'template'=>"<div style='text-align:left; padding: 10px 0 10px 0; font-size:12px;'>"
// ."{edit}"
."<b> {acn} {Y}-{M}-{D}: {Header}</b>"
."<br>{Body}&nbsp;<a href='{link}'>(...)</a>"
."</div>\n\n"
),parse_e_conf($e));


// dier($conf);

$conf['template']=str_replace(array('{@','@}',"\\n"),array('{_','_}',"\n"),$conf['template']);


if(!empty($conf['preBody'])) $conf['preBody']=SlSl($conf['preBody']);
if(!empty($conf['postBody'])) $conf['postBody']=SlSl($conf['postBody']);


if($conf['redirect']) $conf['limit']=1; // ���� ����� �������� - �� ����� ����� ����

$bodyneed=( strstr($conf['template'],'{Body') || strstr($conf['template'],'{FIND:') );

$wher=array();

if($conf['mode']=='blog') $wher[]="`DateDatetime`!=0";
elseif($conf['mode']=='page') { $wher[]="`DateDatetime`=0"; $conf['days']=0; }

if($conf['acn']!==false) $wher[]="`acn`=".intval($conf['acn']);

if($conf['days']!=0) $wher[]="`DateDatetime`>=".intval(time()-$conf['days']*86400);

    if($conf['dostup']=='admin' && (!$GLOBALS['mnogouser'] || $GLOBALS['acn']===$conf['acn'])) $wher[]="`Access` IN ('all','podzamok','admin')";
    elseif($conf['dostup']=='podzamok' && !$GLOBALS['mnogouser'] && $GLOBALS['podzamok']) $wher[]="`Access` IN ('all','podzamok')";
    else $wher[]="`Access`='all'"; // ����� ������� ����� �������!

/*
if($conf['podzamok']) { // deprecated

//	if($conf['podzamok']=='all') $wher[]="`Access`='all'";
	if($conf['podzamok']=='podzamok' && $GLOBALS['podzamok']) $wher[]="`Access`='podzamok'";
	if($conf['podzamok']=='admin' && $GLOBALS['admin']) $wher[]="`Access`='admin'";

	if($conf['podzamok']=='podzamok' && $GLOBALS['podzamok']) $wher[]="`Access`='all'";

	else {
	    if($GLOBALS['podzamok']) $wher[]="`Access`='podzamok'";
	else return '';
	}
}
*/

if($conf['unread']) $wher[]="(`num` NOT IN (SELECT `url` FROM `dnevnik_posetil` WHERE `unic`=".intval($GLOBALS['unic'])."))";

$mstag_gr='';
if($conf['tags']!='') {
    $a=explode(',',$conf['tags']);
    $wtag=array(); foreach($a as $l) $wtag[]="num IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag`='".e(trim($l))."'".ANDC().")";
    $wher[]="(".implode(' '.e($conf['tags_and']).' ',$wtag).")";
    $mstag_gr="GROUP BY `num`"; // ����� ���?!
}

if($conf['sort']=='date') $sortby='DateDatetime';
elseif($conf['sort']=='name'||$conf['sort']=='Date') $sortby='Date';
elseif($conf['sort']=='Header') $sortby='Header';
elseif($conf['sort']=='Body') $sortby='Body';
else $sortby='DateUpdate';

$sq="SELECT DateDatetime,DateUpdate,Access,acn,opt,Date,".($bodyneed?"Body,":'')."Header,num FROM `dnevnik_zapisi` "
.WHERE(implode(' AND ',$wher))
." $mstag_gr"
." ORDER BY $sortby ".($conf['sortx']=='DESC'?'DESC':'')
.($conf['limit']==0||$conf['limit']=='radnom'?'':" LIMIT ".e($conf['limit']));

$pp=ms($sq,"_a");
if($GLOBALS['msqe']!='') return $o=$GLOBALS['msqe'];
$GLOBALS['ANONS_count']=sizeof($pp);
if($conf['mode']=='count') return $GLOBALS['ANONS_count'];


if($conf['limit']=='random') { $pp=array($pp[rand(0,sizeof($pp))]); }

if($conf['redirect']) { $p=$pp[0];
    if($oldarticle['Date']==$p['Date']) return "<font color=red> error: redirect </font>"; // ������ �� �������������
    redirect($GLOBALS['httphost'].$p['Date'].".html".($GLOBALS['admin']?"?redir=".$oldarticle['Date']:''),302); // �� ���������
}

$body1='';
$my_num=$GLOBALS['article']['num'];
$list=''; $s=''; $numer=0; if(sizeof($pp)) foreach($pp as $p) {
	$numer++;
	$p=mkzopt($p); $GLOBALS['article']=$p;
	list($Y,$M,$D) = @explode('/',$p['Date'],3); $D=substr($D,0,2);
// $article["Day"]=substr($article["Day"],0,2); ?????????

	if($bodyneed) {
		if($my_num == $p['num']) continue;

		$p['Body']=str_replace("{"."_ALLTAG:_"."}",'',$p['Body']);

		if(strstr($conf['template'],'{BodySrc}')) $bodySrc=str_replace(array('{_','_}'),array('{<b></b>_','_<b></b>}'),$p['Body']);
		if(strstr($conf['template'],'{Body}')) {
			if($conf['cut']) {
			    $c=str_replace(array('{@','@}'),array('{_','_}'),$conf['cut']);
			    list($cut1,$cut2)=explode('*',$c);
			    list(,$p['Body'])=explode($cut1,$p['Body'],2);
			    list($p['Body'],)=explode($cut2,$p['Body'],2);
			    $p['Body']=c0($p['Body']);
			    if(substr($cut2,0,2)=='_}') { // ������ ��������� ���� ��� ��� ���
				$c=explode("\n",$p['Body']);
				for($i=0;$i<sizeof($c)-1;$i++) {
				    if($c[$i]=="") break;
				    if(false!==strpos($c[$i],'=')) unset($c[$i]);
				}
				$p['Body']=implode("\n",$c);
			    }
			}
			$p['Body']=$conf['preBody'].$p['Body'].$conf['postBody'];
	//		 idie(nl2br(h("$cut1,$cut2 = ".$p['Body'])));
			$body=onetext($p,0);

	if($conf['media']==1) { // ����� ������
		$body=preg_replace("/(<img[^>]+src\=[\'\"]*)([^\/\:]{4,})/si","$1".$GLOBALS['wwwhost'].$Y."/".$M."/$2",$body);
	} elseif($conf['media']==0) { // ����� ���������
		$body=str_replace('<',' <',$body); // �������� ������� ����� ��������� �����
		$body=strip_tags($body); // ��������� ��� ����
		$body=str_ireplace('&nbsp;',' ',$body);
		$body=preg_replace("/\s+/s",' ',$body); // ������ ������� ������� � ��������
		$body=trim($body);
		if($conf['length']!=0) $body=substr($body,0,$conf['length']+strcspn($body,' ,:;.',$conf['length'])); // ��������
	} elseif($conf['media']==2) { // ����� ��������� �� html
		$body=strip_tags($body); // ��������� ��� ����
		$body=nl2br($body); // ��������� ������ enter
	} elseif($conf['media']==3||$conf['media']=='nohtml') { // ����� ��������� �� html, �� � ��������
		$body=str_ireplace(array('<br>','<p>','<div>'),array("\n","\n","\n"),$body);
		$body=strip_tags($body); // ��������� ��� ����
		$body=trim($body);
		$body=nl2br($body); // ��������� ������ enter
	} else return "ANONS: wrong media type";

		}
		if(strstr($conf['template'],'{Body1}')) {
			$body1=str_ireplace(array('<br>','<p>','<div>'),array("\n","\n","\n"),$body);
// idie(h('###'.$p['Body']));
			$body1=strip_tags(html_entity_decode($body1));
			$body1=preg_replace("/\n+/s","\n",trim($body1,"\t\r\n ")); // ������ ������
			list($body1,)=explode("\n",$body1,2); // ����� ������ ������
			if(strlen($body1)>$conf['length1']) $body1=substr($body1,0,$conf['length1']+strcspn($body1,' ,:;.',$conf['length1'])); // ��������
		}

	}

$srcmat=str_replace("\\n","\n",($p['num']==$oldarticle['num']?$conf['template_me']:$conf['template']));

if(strstr($srcmat,'{FIND:')) {
    preg_match_all("/\{FIND\:(.+?)\}/s",$srcmat,$a);

    foreach($a[0] as $i=>$l) {
	$c=str_replace(array('[START]','[END]',"\\n"),array('{_','_}',"\n"),$a[1][$i]);
	$c=str_replace('\*','(.*?)',preg_quote($c,'/'));
	preg_match("/".$c."/s",$p['Body'],$xa);
	$srcmat=str_replace($l,$xa[1],$srcmat);
    }
}

$s.=($conf['list']?"<a name='L".$p["num"]."'></a>":'').mper($srcmat,array(
'Body'=>$body,
'BodySrc'=>$bodySrc,
'Body1'=>h($body1),
'Header'=>(empty($p["Header"])?'[...]':$p["Header"]),
'acn'=>$p['acn'],
'link'=>getlink($p["Date"],$p['acn']), // �������� ������ �� ������
'num'=>$p["num"],
'numer'=>$numer,
'date'=>date($conf['timeformat'],$p['DateDatetime']),
'update'=>date($conf['timeformat'],$p['DateUpdate']),
'Access'=>zamok($p['Access']), // .$p['Access'],
'Y'=>$Y,'M'=>$M,'D'=>$D
));

if($conf['list']) $list.=mper($conf['list_element'],array(
'numer'=>$numer,
'Header'=>(empty($p["Header"])?'[...]':$p["Header"]),
'link'=>getlink($p["Date"],$p['acn']), // �������� ������ �� ������
'Date'=>$p["Date"], // �������� ������ �� ������
'num'=>$p["num"],
'Y'=>$Y,'M'=>$M,'D'=>$D
));

}

$GLOBALS['article'] = $oldarticle;
$GLOBALS['premodule_enable']=$old_premodule_enable;


$s=str_replace(array('-{M}','-{D}'),'',$s);

if($conf['list']) { $list=mper($conf['list_tmpl'],array('list'=>$list)); return $list.$s; }


// if($GLOBALS['admin']) idie($s);

return $s;
}

function SlSl($s) { return str_replace(array('{|','|}','|'),array('{_','_}',"\n"),$s); }

?>