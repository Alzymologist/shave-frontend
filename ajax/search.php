<?php // Авторизация пользователей

include "../config.php"; include $include_sys."_autorize.php"; $a=RE("a"); ADH();

$num=RE0("num");

//=================================== search ===================================================================
if($a=='header') { $search=RE("search"); $m=array();
foreach(ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`Header` LIKE '%".e($search)."%'")." ORDER BY `DateDatetime` DESC") as $p) 
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("ohelpc('search',\"Поиск записей для ".h($search)."\",\"".njsn("<small>".implode("<br>",$m)."</small>")."\")");
}

//=================================== tag ===================================================================
if($a=='tag') { $tag=ifu(RE("tag"));
$m=array();
foreach(ms("SELECT z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`",'','z')." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("ohelpc('search',\"Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."': ".sizeof($m)."</a>\""
.",\"".njsn("<small>".implode("<br>",$m)."</small>")."\")");
}

//=================================== tagpage ===================================================================
if($a=='tagpage') { $tag=ifu(RE("tag")); $m=array();
	include $include_sys."_onetext.php";
	include $include_sys."_modules.php";
foreach(ms("SELECT z.`Body`,z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`",'','z')." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"
."<p>".onetext($p);
}
otprav("ohelpc('search',\"Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."'</a>\","
."\"".njsn("<small>".implode("<br>",$m)."</small>")."\")");
}

//=================================== alltag ===================================================================
if($a=='alltag') {
$m=ms("SELECT `tag`, count(*) AS `n` FROM `dnevnik_tags` WHERE 1=1".ANDC()." GROUP BY `tag` ORDER BY `n` DESC","_a");

$o=''; foreach($m as $n) $o.="<tr><td><div class=ll onclick=\"majax('search.php',{a:'tag',tag:'".h($n['tag'])."'})\">".h($n['tag'])."</div></td><td> &nbsp; ".$n['n']."</td></tr>";
otprav("ohelpc('search',\"Все тэги\",\"<table>".njsn($o)."</table>\")");
}

idie(nl2br(h(__FILE__.": unknown action `".$a."`")));
?>