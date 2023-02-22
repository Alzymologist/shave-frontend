<?php // Правки

include_once $GLOBALS['include_sys']."_one_pravka.php"; // процедура вывода окошка с одной правкой

STYLES("pravka.css","

.po { font: 90% sans-serif, Helvetica, Arial, Verdana; margin: 0 5% 0 10%; } /* общий блок */

.pc,.pcy,.pcn {font-size: 90%; border: 1px solid #B0B0B0; margin-right: 5%; overflow: auto; padding: 5px;}

.pc  { background-color: #FAFAFA; } /* new */
.pcy { background-color: #DFFAFA; } /* submit */
.pcn { background-color: #FADFFA; } /* discard */

.pch { margin-left: 1%; font-weight: bold; } /* ник коментария */
.pcc { padding: 5px; margin: 0 3% 0 3%; } /* текст комментария */

/* блок ответа */
.pct { background-color:#FFFCF1; border: 1px solid #E00000; margin-left:5%; margin-right:10%; overflow:auto; padding:5px; position:relative; top: -10px;  }

/* кнопки */
.pkk { position:relative; text-align: right; margin-right: 5%; top: 5px; left: 10px; }

.pkl,.pkr,.pkg {margin-left: 5px; border: 1px solid #B0B0B0; font-size: 8px; padding: 1px;
cursor: pointer; color: blue; display: inline;
}
.pkl { background-color: #E0E0E0; }
.pkr { background-color: #90FF90; }
.pkg { background-color: #FFA0A0; }

.ptime { font-size: 10px; position:relative; top: 10px; float: right; font-weight: bold; } /* дата комментария */

/* отображение правок */
.p1 { color: #3F3F3F; text-decoration: line-through; background: #DFDFDF; } /* вычеркнутый */
.p2 { background: #FFD0C0; } /* вставленный */

/* отображение ответов на правки */
.y { color: green; } /* принятая правка */
.n { color: red; } /* отклоненная правка */
");

SCRIPTS("
page_onstart.push('helper_go=function(){}; helper_napomni=1000;'); // отключить систему правки

// function ppo(e) { alert(ecom(e).id); }

function pp(e) { pravka(e,'podrobno'); }
function pdi(e) { var i=prompt('\\nДа, принято. Потому что:\\n','Спасибо, '); if(i && i.length != 0 ) pravka(e,1,i); }
function pni(e) { var i=prompt('\\nНет, отклонено. Потому что:\\n','Нет, '); if(i && i.length != 0 ) pravka(e,0,i); }
function pd(e) { pravka(e,1,'da'); }
function pd(e) { pravka(e,1,'da'); }$pravki_npage
function pu(e) { pravka(e,1,'ugovorili'); }
function pz(e) { pravka(e,0,'zadumano'); }
function pg(e) { pravka(e,0,'gramotei'); }
function pl(e) { pravka(e,0,'len'); }
function ps(e) { pravka(e,0,'spam'); }
function pe(e) { pravka(e,'edit'); }
function pc(e) { pravka(e,'edit_c'); }
function px(e) { // if(confirm('Точно удалить?'))
pravka(e,'del'); }

function pravka(e,volya,answer){ if(typeof e == 'object') e=ecom(e).id;
majax('ajax_pravka.php',{a:volya,id:e,answer:answer,hash:'".$GLOBALS['hashpage']."'});}
");

function PRAVKI($e='') { global $mypage,$db_pravka,$db_unic,$pravki_npage;
$conf=array_merge(array(
'act'=>h($_GET['a']),
'skip'=>intval($_GET['skip']),
'nam'=>h($_GET['search']),
'npage'=>50,
'mode'=>1
),parse_e_conf($e));

$s="<center>"; $npage1=$conf['npage']+1;
$act=$conf['act']; $nam=$conf['nam']; $skip=$conf['skip'];

// idie("acn: ".$GLOBALS['acn']." ".$GLOBALS['acc']);

// ИСПРАВИТЬ БАЗУ
// $pp=ms("SELECT * FROM ".$db_pravka." ORDER BY `id` DESC",'_a',0);
/*
$R=array();
foreach($pp as $n=>$p) { $d=$p['Date'];
//    $a=explode('@',$p['Date']);
//    dier($a,$p['Date']);

    if(strstr($d,'@dnevnik_zapisi@Body@num@')) $id=str_replace('@dnevnik_zapisi@Body@num@','',$d);
    elseif(strstr($d,'@dnevnik_zapisi@Header@num@')) $id=str_replace('@dnevnik_zapisi@Header@num@','',$d);
    else continue;
    if(false==($macn=intval(ms("SELECT `acn` FROM `dnevnik_zapisi` WHERE `num`=".sssssssssssse($id)." LIMIT 1","_l")))) continue;
    if($macn==$p['acn']) continue;
    $p['macn']=$macn;
    $R[]=$p;
    msq_update($db_pravka,array("acn"=>e($macn)),"WHERE `id`=".eeeeeeeeeeeeeeeeeeeeeeeeee$p['id']);
}
*/
// dier($pp,$GLOBALS['msqe']);


if(!$GLOBALS['ADM']) $s .= "
<p>Здесь отображаются присланные, но ещё не рассмотренные правки. Однако, вы не админ.
<br>Можете поиграться, будто вы админ, но <font color=red>изменения не запишутся</font>.";

$SEL="SELECT p.*,u.`openid`,u.`realname`,u.`login`,u.`img`
FROM `".$db_pravka."` AS p
LEFT JOIN ".$db_unic." AS u ON p.`unic`=u.`id` ";

if($act=='all') $SEL.="WHERE 1=1 ";
else $SEL.="WHERE p.`acn`=".intval($GLOBALS['acn'])." ";

if($act=='arh') $sql = ms($SEL."ORDER BY p.`DateTime` DESC LIMIT ".$skip.",".$npage1,'_a',0);
elseif($act=='ego') $sql = ms($SEL."AND p.`unic`=".intval($_GET['unic'])." ORDER BY p.`DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='my') $sql = ms($SEL."AND p.`unic`=".intval($GLOBALS['unic'])." ORDER BY p.`DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='mynew') $sql = ms($SEL."AND p.`metka`='new' AND p.`unic`=".intval($GLOBALS['unic'])." ORDER BY p.`DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='ras') $sql = ms($SEL."AND p.`Date` LIKE '%".e($_GET['search'])."%' ORDER BY p.`DateTime` LIMIT $skip,$npage1",'_a',0);
else $sql = ms($SEL."AND p.`metka`='new' ORDER BY p.`DateTime` DESC",'_a',0);
// else $sql = ms("SELECT * FROM `".$db_pravka."` LIMIT 20",'_a',0);

$colnewcom=sizeof($sql);

// $s.="<hr>прапвок: $colnewcom <hr>";

if($act=='arh' || $act=='my' || $act=='ras' ) {
	$prev=$next='';

	if($colnewcom==$npage1) { $colnewcom--; unset($sql[$colnewcom]);
		$prev="<a href='".$mypage."?a=".$act.($act=='ras'?"&search=".$nam:'')."&skip=".($skip+$pravki_npage)."'>&lt;&lt; ранние ".$pravki_npage."</a>"; }

	if($skip>=$pravki_npage)
		$next.="<a href='".$mypage."?a=".$act.($act=='ras'?"&search=".$nam:'')."&skip=".($skip-$pravki_npage)."'>поздние ".$pravki_npage." &gt;&gt;</a>";

	$prevnext="<center><table width=96%><tr><td align=left width=32%>$prev</td><td align=center width=32%>
<a href='".$mypage."'>все новые</a>
<a href='".$mypage."'?a=mynew'>мои новые</a>

<br><form action='".$mypage."'>".($skip?"<input type=hidden name=skip value=".$skip.">":"")."
<input type=hidden name=a value='ras'>
<input type=text size=10 name=search value='".$nam."'>
<input type=submit value='искать по имени'>
</form>

</td><td align=right width=32%>$next</td></tr></table></center>";

} else {
	$prevnext="<center><p class=br><a href='".$mypage."?a=arh'>все обработанные</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href='$mypage?a=my'>только мои</a></p></center>";
}

$s .= $prevnext;

$s .= "<p>правок: ".$colnewcom."</center>";

if($colnewcom) {
	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=$sql[$i]; $data=$p['Date']; $dacn=$p['acn'];
		$s .= print_header($data,$dacn);
		foreach($sql as $i2=>$p2) {
//		$s .= "<div class=r>eee: ".h($p2['Date'])." ".h($p2['acn'])."</div>";
		    if($p2['Date']==$data && $p2['acn']==$dacn) { $s.=print11($p2); unset($sql[$i2]); }
		}
	}
	$s .= "<p>".$prevnext;
} else {
	if($conf['mode']==0) return '';
	$s .= "<center>свежих правок нет</center>";
}

return $s;
}

#################################################################################################################################

function print11($p) {
	$id=$p['id'];
	$textnew=h($p['textnew']);
	$text=h($p['text']);
	$stdpravka=($GLOBALS['pravshort']?$p['text']:$p['stdprav']);
//	$answer .= obrabotka_admina($p,$id,$text,$textnew,$stdpravka); // модель для всякой обработки - типа чистки базы от спаммеров
	return one_pravka($p,$answer);
}



function print_header($data,$dacn) {
	list($base,$table,$bodyname,$wherename,$whereid)=explode('@',$data);
	if($base=='file#') {
		$data='/'.h($table); $link='/'.h($table); // $GLOBALS['wwwhost'].$data;

//	} elseif($base.$table=="lleo"."dnevnik_zapisi") { // в случае СТАРОГО блога
//		$p=ms("SELECT `Date`,`Header` FROM `lleo`.`dnevnik_zapisi` ".WHERE("`".e($wherename)."`='".e($whereid)."'")." LIMIT 1","_1",$GLOBALS['ttl']);
//		$data=h($p['Date'].($p['Header']!=''?" ".$p['Header']:''));
//		$link="/dnevnik/".h(strtr($p['Date'],'-','/')).".html";

//	} elseif($base.$table=="lleo"."dnevnik_comments") { // в случае старого блога
//		$data="Комментарий #".h($whereid);
//		$link="";

	} elseif($base.$table=="dnevnik_comments") { // в случае нового блога
		$data="Комментарий в блоге #".h($whereid);
		$link="";

	} elseif($table=='dnevnik_zapisi') { // в случае блога
		$p=ms("SELECT `acn`,`Date`,`Header` FROM `dnevnik_zapisi` WHERE `".e($wherename)."`='".e($whereid)."'"
		    .($dacn?" AND `acn`=".intval($dacn):'')
		    ." LIMIT 1","_1");
		if($p===false) {
		    $data='FALSE';
		    $link='';
		} else {
		    $data=h($p['Date'].($p['Header']!=''?" ".$p['Header']:''));
		    $link=getlink($p['Date'],$dacn,"");
		}

	} elseif($table.$bodyname=="site"."text") { // в случае блога
		$data="Блок сайта #".h($whereid);
		$link=$GLOBALS['wwwhost']."adminsite?edit=".h($whereid);

	} else {
		$data=h("SELECT `".e($bodyname)."` FROM ".($base==''?'':"`".e($base)."`.")."`".e($table)."` WHERE ".e($wherename)."='".e($whereid)."'");
		$link='';
	}

	if($link!='') $data="<a href='".$link."'>".$data."</a>";
	return "<p><center><b>".$data."</b></center>";
}

?>