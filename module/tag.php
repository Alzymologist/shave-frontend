<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// tag
	ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); // включить сообщения об ошибках

// === code ===
$tag=RE("tag"); if(empty($tag)) $tag=urldecode($_SERVER['QUERY_STRING']);
if(strstr($tag,'&')) list($tag,$no)=explode('&',$tag,2);
$tag=maybelink($tag);
$tag=c($tag);
    $m=array();

if($tag=='') {
    $pp=ms("SELECT DISTINCT `tag` FROM `dnevnik_tags` WHERE 1=1".ANDC()." ORDER BY `tag`","_a1");
    $k=0; foreach($pp as $l) {
	$c=ms("SELECT COUNT(*) FROM `dnevnik_tags` WHERE `tag`='".e($l)."' ".ANDC(),"_l");
	$k+=$c;
	$m[]="<tr><td>$c</td><td>   </td><td><a href=\"javascript:majax('search.php',{a:'tag',tag:'".h($l)."'});\">".h($l)."</a></td></tr>";
    }

    $o="<center><h2>Все тэги блога (".sizeof($pp).")</h2>"
    ."<p><br><table width=80% style='max-width:800px;border:none'>".implode('',$m)."</table>

<p>тэги использованы $k раз

</center>";
//     dier($pp,$GLOBALS['msqe']);

} else {

// idie("SELECT z.Access,z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num` AND t.`acn`='".$GLOBALS['acn']."' ORDER BY `DateDatetime` DESC");


    $pp=ms("SELECT z.Access,z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`"
." AND t.`acn`='".$GLOBALS['acn']."'")
// .ANDC())
." ORDER BY z.`DateDatetime` DESC");
    foreach($pp as $p) {
	$m[]=zamok($p['Access']).$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>";
    }

    $o="<center>Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."': ".sizeof($m)."</a>"
    ."<p><br><table width=80% style='max-width:800px;border:none'><tr><td>".implode("<br>",$m)."</td></tr></table></center>";
}
// === /code ===

$article=array('Date'=>'','Header'=>'','Body'=>$o,
'Access'=>'all','DateUpdate'=>0,'num'=>0,'DateDatetime'=>0,'DateDate'=>0,'opt'=>'a:3:{s:8:"template";s:5:"blank";s:10:"autoformat";s:2:"no";s:7:"autokaw";s:2:"no";}','view_counter'=>0);

?>