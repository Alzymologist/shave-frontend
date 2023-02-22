<?php // Комментарии

include_once $GLOBALS['include_sys']."getlastcom.php"; getlastcom();

function COMM() { global $lim,$admin,$mode,$lastcom,$ncom,$acn;
// $GLOBALS['opt']=mkzopt(array('opt'=>$GLOBALS['article']['opt']));

$mytime=time();
$er=$ok=array(); // сюда пишутся ошибки и системные сообщения

if($GLOBALS['admin']) {

    // переполнение логов
    $log_ok=array();
    $hostw=$GLOBALS['filehost'].'hidden/nginx/';
    if(is_dir($hostw)) foreach(explode(' ',"access.log error.log access_log error_log") as $l) {
    $i=''; $k=0; while(is_file($hostw.$l.$i)) {
	$size=floor(filesize($hostw.$l.$i));
	if($size>2*1024*2014*1024) $er[]="<font color=red>опасное переполнение логов ".floor($size/(1024*1024))."Mb - ".$hostw.$l.$i."</font>";
	$log_ok[]=$l.$i.' '.floor($size/1024).'Kb';
	$i='.'.(++$k);
     }
    }
    if(!empty($log_ok)) $ok[]="<a alt='просмотр логов' href='".$GLOBALS['wwwhost']."nginx'>логи:</a> ".implode(', ',$log_ok);


	$a=glob($GLOBALS['antibot_file']."*.jpg"); $abot=sizeof($a); unset($a); // сколько антиботовых картинок?
	if($abot>5000) $er[]="антиботовых картинок накопилось $abot";
	else $ok[]="антиботовых картинок $abot";

	if(!$GLOBALS['memcache']) $er[]='memcache выключен...'; else {
	    $r=$GLOBALS['memcache']->getStats();
	    $ok[]='memcache: '.floor($r['bytes']/(1024*1024)).'Mb ('.floor(100/$r['limit_maxbytes']*$r['bytes']).'%) из '.floor($r['limit_maxbytes']/(1024*1024)).'Mb, элементов '.$r['curr_items'].' / '.$r['total_items'].', эффект '.floor(100/($r['get_hits']+$r['get_misses'])*$r['get_hits']).'% ('.$r['get_hits'].'/'.$r['get_misses'].')'
	    ." &nbsp; <span class=ll onclick=\"majax('login.php',{a:'memcache_flush'})\" title='сбросить memcache сервера'>[flush]</span>";
	}

	$mt=(is_file($GLOBALS['cronfile'])?(time()-filemtime($GLOBALS['cronfile'])):9999999); $mt=$mt/(60*60*10); // 10 часов

		if($mt > 1) $er[]="cron последний раз запускался ".floor($mt)." часов назад!
настрой crontab или <a href='".$GLOBALS['httphost']."cron.php'>запусти вручную</a>";
		else $ok[]="cron последний раз запускался ".floor($mt*60)." минут назад";

//	$ok[]="Список <a href=".$GLOBALS['wwwhost']."logon/?list>друзей</a>";
	$ok[]="Читать <a href=".$GLOBALS['wwwhost']."pravki/>правки</a>";

	if($er) { $s.="<font color=red><i><ul>"; foreach($er as $e) $s.="<li>$e</li>"; $s.="</ul></i></font>"; }
	if($ok) { $s.="<font color=green><i><ul>"; foreach($ok as $e) $s.="<li>$e</li>"; $s.="</ul></i></font>"; }
}

//--------------------------------------------------------------------------------------------------------------------------------

/*

$sqlref="SELECT c.`IPN`,c.`id`,c.`unic`,c.`group`,c.`Name`,c.`Text`,c.`Parent`,c.`Time`,c.`whois`,c.`rul`,c.`ans`,
c.`golos_plu`,c.`golos_min`,c.`scr`,c.`DateID`,c.`BRO`,
u.`capchakarma`,u.`mail`,u.`admin`,
u.`realname`,u.`login`,u.`openid`,u.`img`,
".($GLOBALS['admin']?"z.Access,z.num,":'')."
z.`opt`,z.Access,z.`Date`,z.`DateDate`,z.`Header`,z.`view_counter`
FROM
`dnevnik_comm` AS c
JOIN `dnevnik_zapisi` AS z ON c.`DateID`=z.`num` ".(empty($acn)?'':" AND z.`acn`=ffffffffffffffffffffff$acn")."
LEFT JOIN ".$GLOBALS['db_unic']." AS u ON c.`unic`=u.`id`

WHERE "
// админу показать все
// если подзамок - прятать заметки скрытые или с отключенными комментами
.($GLOBALS['admin']?"1":"z.`Access`='all' AND (c.`scr`='0' OR c.`unic`='".$GLOBALS['unic']."')")


//($GLOBALS['podzamok']?" AND z.`Access`!='admin' AND z.`Comment_view`!='off'":
// для всех прочих запретить комментарии к закрытым
// " AND z.`Access`!='all' AND z.`Comment_view`!='off'"))

.($mode=='one'?" AND c.`unic`='".e($_GET['unic'])."'":"") // если запрошены комменты только от одного
." AND ".($ncom!='-'?"c.`Time`>'".$lastcom."' ORDER BY c.`Time`":"c.`Time`<'".$lastcom."' ORDER BY c.`Time` DESC")." LIMIT ".($lim+1);

*/

$WW=array();
if(!$GLOBALS['admin']) $WW[]="(`scr`='0' OR `unic`=".intval($GLOBALS['unic']).")";
if($mode=='one') $WW[]="`unic`=".intval($_GET['unic']); // если запрошены комменты только от одного
$WW[]=($ncom!='-'?"`Time`>".intval($lastcom)." ORDER BY `Time`":"`Time`<".intval($lastcom)." ORDER BY `Time` DESC");

$sql=ms("SELECT `IPN`,`id`,`unic`,`group`,`Name`,`Text`,`Parent`,`Time`,`whois`,`rul`,`ans`,`golos_plu`,`golos_min`,`scr`,`DateID`,`BRO`
FROM `dnevnik_comm` WHERE ".implode(" AND ",$WW)." LIMIT ".($lim+1));
// dier($sql,'dddddddd'.$GLOBALS['msqe']);
$colnewcom=$colcom=sizeof($sql);

if($colnewcom) {

    // чтоб не загружать сложными запросами mysql сука блять нежную кретины блять криворукие написать нормально не могли,
    // мы возьмем сами недостающие значения из `dnevnik_zapisi`
    $SQ=e(implode(',',array_unique(array_column($sql,'DateID'))));
    $WW=array("`num` IN (".e($SQ).")","`visible`=1"); // только видимые записи
    if(!empty($acn)) $WW[]="`acn`=".intval($acn); // но только если acn
    if(!$GLOBALS['admin']) { // админу разрешены комменты любых записей
        if($GLOBALS['podzamok']) $WW[]="`Access`!='admin'"; // подзамкам разрешены комменты к записям all,podzamok
        else $WW[]="`Access`='all'"; // обычным юзерам только комменты к записям all
    }
    $SQ=ms("SELECT num,opt,Access,Date,DateDate,Header,view_counter FROM `dnevnik_zapisi` WHERE ".implode(" AND ",$WW),"_a");
    $ON_dnevnik_zapisi=array(); foreach($SQ as $e) { $n=$e['num']; unset($e['num']); $ON_dnevnik_zapisi[$n]=$e; }

    // чтоб не загружать сложными запросами mysql сука блять нежную кретины блять криворукие написать нормально не могли,
    // теперь мы еще возьмем недостающие значения из db_unic
    $SQ=array_unique(array_column($sql,'unic'));
    if(($k=array_search('0',$SQ)) !== false) unset($SQ[$k]); // удалим комменты 0
if(sizeof($SQ)) {
    $SQ=e(implode(',',$SQ));
    $SQ=ms("SELECT id,capchakarma,mail,admin,realname,login,openid,img FROM ".$GLOBALS['db_unic']." WHERE `id` IN (".e($SQ).")");
}
    $ON_db_unic=array(); foreach($SQ as $e) { $n=$e['id']; $e=get_ISi($e); unset($e['id']); $ON_db_unic[$n]=$e; }

    // теперь еще возьмем недостающие parent_name
    $SQ=array_unique(array_column($sql,'Parent'));
    // if(false !== ($i = array_search(0,$SQ)) ) unset($SQ[$i]); // удалим нулевой
if(sizeof($SQ)) {
    $SQ=e(implode(',',$SQ));
    $SQ=ms("SELECT `unic` as `Parent_unic`,`Name` as `Parent_name`,`id` as `Parent_id` FROM `dnevnik_comm` WHERE `id` IN (".e($SQ).")");
}
    $ON_parent=array(); foreach($SQ as $e) { if(false != ($i=strpos($e['Parent_name'],' ('))) $e['Parent_name']=substr($e['Parent_name'],0,$i); $ON_parent[$e['Parent_id']]=$e; }

    // теперь добавим в наш массив основной

// if($GLOBALS['admin']) dier($sql);

    unset($SQ);
    foreach($sql as $i=>$p) {
    	if(!isset($p['DateID'])) { unset($sql[$i]); continue; } // если запрещенная заметка
	$sql[$i]=@array_merge(
	    (empty($ON_dnevnik_zapisi[$p['DateID']])?array():$ON_dnevnik_zapisi[$p['DateID']]),
	    (empty($ON_db_unic[$p['unic']])?array():$ON_db_unic[$p['unic']]),
	    (empty($ON_parent[$p['Parent']])?array():$ON_db_unic[$p['unic']]),
	$p); // array('Parent_unic'=>0,'Parent_name'=>'','Parent_id'=>0),

//	$e=array_merge( $ON_dnevnik_zapisi[$p['DateID']], $ON_db_unic[$p['unic']], $ON_parent[$p['Parent']], $p );
//	$e=array_merge( $ON_dnevnik_zapisi[$p['DateID']], $ON_db_unic[$p['unic']],  $p );
// if($GLOBALS['admin']) {    if(empty($sql[$i]))   dier($e,"================ [$i] ======================");}

	// $p['Parent_name']=($p['Parent']!=0 ? $ON_parent[$p['Parent']]['Name'] : '' );
    }

    $colnewcom=$colcom=sizeof($sql);
}

// dier($sql);

/*
    [0] => Array
            [IPN] => 1294322745
            [id] => 330111
            [unic] => 4
            [group] => 0
            [Name] => LLeo (openid/index.php)
            [Text] => Понимаете, вся проблема в том, что я на всю голову ебанутый. У меня в каждом коробке то 59, то 63 спички.
            [Parent] => 330108
            [Time] => 1605909119
            [whois] => RU Москва
            [rul] => 0
            [ans] => u
            [golos_plu] => 1
            [golos_min] => 0
            [scr] => 0
            [DateID] => 4779
            [BRO] => Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:83.0) Gecko/20100101 Firefox/83.0
*/




// if($GLOBALS['admin']) $s.="{_BC: ".h($sqlref)." _}";
/*
SELECT c.`IPN`,c.`id`,c.`unic`,c.`group`,c.`Name`,c.`Text`,c.`Parent`,c.`Time`,c.`whois`,c.`rul`,c.`ans`, c.`golos_plu`,c.`golos_min`,c.`scr`,c.`DateID`,c.`BRO`,
u.`capchakarma`,u.`mail`,u.`admin`, u.`realname`,u.`login`,u.`openid`,u.`img`,
z.Access,z.num, z.`opt`,z.Access,z.`Date`,z.`DateDate`,z.`Header`,z.`view_counter`
FROM `dnevnik_comm` AS c
JOIN `dnevnik_zapisi` AS z
ON c.`DateID`=z.`num`
LEFT JOIN `lleoblog`.`unic` AS u
ON c.`unic`=u.`id`
WHERE 1 AND c.`Time`<'1605909120' ORDER BY c.`Time` DESC LIMIT 51
*/


if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']);

if($colnewcom) {
	include_once $GLOBALS['include_sys']."_onecomm.php";

// if($colnewcom>$lim) { $colnewcom--; unset($sql[$colnewcom]); }
	$sql=array_values($sql); // сброс ключей массива

// if($GLOBALS['admin']) dier($sql);

	$a=date('Y-m-d_H-i-s',$sql[0]['Time']);
	$b=date('Y-m-d_H-i-s',$sql[$colnewcom-1]['Time']);
	if($ncom!='-') { /*list($a,$b)=array($b,$a);*/ $l=$a; $a=$b; $b=$l; }
	$linknext_x=$a; $linkprev_x=$b;

if($colcom<=$lim) if($ncom=='-') $linkprev_x=''; else { $link_x='<br>на этом всё пока'; }

$s .= "<div id=0>";

	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=mkzopt($sql[$i]);
		$num=$p['DateID']; //$s .= print_header($num);
$s .= print_headerp($p);
		foreach($sql as $i2=>$p2) if($p2['DateID']==$num) { $s.=print1($p2); unset($sql[$i2]); }
	}

$s .= "<div></div></div>";

} else {
	$link_x1=true;
	if($ncom!='-') $linkprev_x=date('Y-m-d_H-i-s',$lastcom+1); else $linknext_x=date('Y-m-d_H-i-s',$lastcom-1);
}


$linkprev_link=$GLOBALS['mypage']."?".makeGET(array('ncom'=>'prev','lastcom'=>urlencode($linkprev_x)));
$linknext_link=$GLOBALS['mypage']."?".makeGET(array('ncom'=>'next','lastcom'=>urlencode($linknext_x)));

$prevnext="<p>".mk_prevnest(
($linkprev_x!=''?'<a href="'.$linkprev_link.'">&lt;&lt; предыдущие (до '.$linkprev_x.')</a>':''),
($linknext_x!=''?'<a href="'.$linknext_link.'">следующие (c '.$linknext_x.') &gt;&gt'.(isset($link_x)?$link_x:'').'</a>':'')
);

$s = ($link_x1!==true?$prevnext:'').$s.$prevnext;

$_PAGE['prevlink'] = $linkprev_link;
$_PAGE['nextlink'] = $linknext_link;
#$_PAGE['uplink'] = $GLOBALS['httphost']."contents";
#$_PAGE['downlink'] = $GLOBALS['httphost'];

return $s.$GLOBALS['msqe'];
}

function makeGET($ar) { $r=''; $m=$_GET;
	foreach($ar as $a=>$b) $m[$a]=$b;
	foreach($m as $a=>$b) if($b!='') $r.=urlencode($a)."=".urlencode($b)."&";
	return trim($r,'&');
}

function print_headerp($p) {
	$p['counter']=get_counter($p);
	$x=$p['Header']; if($x=='') $x='(&nbsp;)';

	return "<p>"
.($GLOBALS['ADM']?"<i class='knop ".($p['Comment_write']=='off'?'e_ledred':'e_ledgreen')."' id='knopnocomment_".$p['DateID']."' onclick=\"majax('editor.php',{a:'nocomment',num:'".$p['DateID']."'})\" alt='запретить/разрешить комментарии'></i>&nbsp;":'')

."<b><a href=".h($GLOBALS['wwwhost'].$p['Date'].($p['DateDate']?".html":'')).">".h($p['Date']." - ".$x)."</a></b> (счетчик: ".$p['counter'].")"
.ADMINSET($p);
}


function print1($p) { $level=($p['Parent']!=0?'-4':'0'); return comment_one($p,0,$level); }

?>