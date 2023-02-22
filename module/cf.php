<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

// cf - clean cache CloudFlare

	ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); // включить сообщения об ошибках

// === code ===
$file=RE("tag"); if(empty($file)) $file=urldecode($_SERVER['QUERY_STRING']);
$file=maybelink($file);
$file=c($file);

if($file=='') $o="Empty tag";
else {
    $fil0=$file;
    if(!strstr($file,'://')) $file=$GLOBALS['httphost'].ltrim($file,'/');

    $o="<p lass='r'>".delfilecache($file)."</p>";

    $ras=getras($file);

    if(in_array($ras,array('jpg','jpeg','png','gif','svg'))) $o.="{_IMG: ".h($file)."_}";
    elseif(in_array($ras,array('mov','mp3','ogg','mp4','mkv','flv'))) $o.="{_PLAY: ".h($file)."_}";
    else $o.="<h2><a href=\"".$httphost."install?".h($file)."|\">".h($file)."</a></h2>";
//    $content=("<br>RAS:".getras($file);

    $o="<center>Очистка кэша CloudFlare для файла <a href=\"".h($file)."\">".h($file)."</a>"
    ."<p><br><table width=80% style='max-width:800px;border:none'><tr><td>".$o."</td></tr></table></center>";
}
// === /code ===

$article=array('Date'=>'','Header'=>'','Body'=>$o,
'Access'=>'all','DateUpdate'=>0,'num'=>0,'DateDatetime'=>0,'DateDate'=>0,'opt'=>'a:3:{s:8:"template";s:5:"blank";s:10:"autoformat";s:2:"no";s:7:"autokaw";s:2:"no";}','view_counter'=>0);

?>