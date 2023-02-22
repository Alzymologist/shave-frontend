<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

ob_clean(); header("Content-Type: text/xml; charset=".$wwwcharset);

$skip=intval($_GET['skip']);

$whe=array();
if(!$podzamok) $whe[]="(`scr`='0' OR `unic`=".intval($unic).")";
if(isset($_GET['unic'])) $whe[]="`unic`=".intval($_GET['unic']);
if(isset($_GET['name'])) $whe[]="`Name`='".e($_GET['name'])."'";

// выбрать заметки, которые visible=1 и разрешены для просмотра этому юзеру
$pp=ms("SELECT `num`,`Date` FROM `dnevnik_zapisi`".WHERE($podzamok || $ADM  ? '' : " WHERE (`scr`='0' OR `unic`=".intval($unic).")" ).ANDC() ,"_a",$ttl_longsite);
	$d=array(); foreach($pp as $l) $d[$l['num']]=getlink($l['Date']);

$pp=ms("SELECT `id`,`Text`,`Name`,`Parent`,`Time`,`DateID` FROM `dnevnik_comm` WHERE `DateID` IN (".implode(',',array_keys($d)).")"
.(sizeof($whe) ? " AND ".implode(' AND ',$whe) : '')
." ORDER BY `Time` DESC LIMIT ".intval($skip).",".intval($RSSC_skip),'_a');

$s="<?xml version='1.0' encoding='".$wwwcharset."'?>
<rss version='2.0' xmlns:ya='http://blogs.yandex.ru/yarss/'>

<channel>
	<title>".h($admin_name).": comments</title>
	<link>".h($httphost)."</link>
	<generator>LLeoBlog 1.0:comments</generator>
"; //  <lastBuildDate></lastBuildDate>

$s.="	<ya:more>".h($httpsite.$mypage)."?skip=".h($skip+$RSSC_skip)."</ya:more>
	<category>ya:comments</category>
";

foreach($pp as $p) {
	$post=$d[$p['DateID']];
	$link=$post."#".$p['id'];

 $p['Text']=str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$p['Text']);

$s .= "\n<item>
	<guid isPermaLink='true'>".$link."</guid>
	<ya:post>".$post."</ya:post>
".($p['Parent']!=0?"        <ya:parent>".$post."#".$p['Parent']."</ya:parent>":'')."
	<pubDate>".date("r", $p['Time'])."</pubDate>
	<author>".h(strtr($p['Name'],"\r",""))."</author>
	<link>".h($link)."</link>
	<title></title>
	<description>".strtr($p['Text'],"\r","")."</description>
</item>\n";

}

$s .= "\n</channel>\n\n</rss>\n";

ob_end_clean();
die($s);
// die($s1.date("r",$lastupdate).$s);

?>