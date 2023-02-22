<?php

function ADDPOSTS_ajax() { $a=RE('a');

/*
verse/00000231
verse/00000315
verse/00000257
verse/00000222
verse/00000308
*/

//=====================================================
if($a=='deltags') {
	$tag=trim(RE('tags'));

	// просто очистка тэгов
	if($tag=='') {
	    $sq0="FROM `dnevnik_tags` WHERE num NOT IN (SELECT `num` FROM `dnevnik_zapisi` ".ANDC("WHERE").")".ANDC();
	    $c0=ms("SELECT COUNT(*) ".$sq0,"_l");
	    msq("DELETE ".$sq0);
	    idie('Удалено пустых тэгов: '.$c0);
	}

	$sq1="FROM `dnevnik_zapisi` WHERE num IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag`='".e($tag)."'".ANDC().")".ANDC();
	$c1=ms("SELECT COUNT(*) ".$sq1,"_l");
	$o="<p>Записей с тэгом `".h($tag)."`: ".$c1;
	msq("DELETE ".$sq1);
	$o.=" - DELETED ".$GLOBALS['msqe'];

	$sq2="FROM `dnevnik_tags` WHERE `tag`='".e($tag)."'".ANDC();
	$c2=ms("SELECT COUNT(*) ".$sq2,"_l");
	$o.="<p>Тэгов `".h($tag)."`: ".$c2;
	msq("DELETE ".$sq2);
	$o.=" - DELETED ".$GLOBALS['msqe'];

	idie($o);
}


if($a=='check') {
    $Body=RE('Body');
    $Date=RE('Date');
    $name=RE('name');

    $result=false;

    $Body_orig=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'".ANDC(),"_l",0);
    if($Body_orig!==false) {
	similar_text($Body,$Body_orig,$percent);
	if($percent > 60) $result="OK, требуются изменения на ".(100-floor($percent))."%";
	else $result="ошибка, другая заметка с тем же именем";
    } else {
	$result="OK";
	$pall=ms("SELECT Date,Body FROM `dnevnik_zapisi`".ANDC("WHERE"),"_a");
        foreach($pall as $p) {
	    similar_text($Body,$p['Body'],$percent); $percent=floor($percent);
	    if($percent>60) {
		    $result="ошибка, совпадение ".floor($percent)."% с заметкой <a href='".getlink($p['Date'])."'>".$p['Date']."</a>";
		    break;
	    } // else $result="ошибка, ГЕ совпадение ".floor($percent)."% с заметкой ".h($p['Date']);
	}
    }

    otprav("
	var result=\"".$result."\";
	var w=idd('".h($Date)."');
	var e=w.querySelector('I.e_false');
	e.style.width='auto';
	e.style.lineHeight='normal';
	e.style.fontSize='25px';
	e.style.padding='0 0 0 40px';
	var f=e.closest('form');
	    e.innerHTML=result;
	if(result.indexOf('OK')==0) {
	    e.className='e_true';
	    e.style.color='green';
	} else {
	    e.style.color='red';
	}
    ".(RE0('all')==1?'':"checkall(0);")
    );
}

if($a=='make') {

$DATS=array();

    include_once $GLOBALS['include_sys']."_modules.php";

    $TT=str_replace("\r",'',RE('txt'));
    $TT=trim($TT,"\n");

    $cf_main=array_merge(array(
	'Access'=>'all',
	'autoformat'=>"p",
	'autokaw'=>"auto",
        'Date'=>'',
        'base'=>'',
	'numeration'=>false,
        'tags'=>'addposts',
        'Header'=>'',
    ),parse_e_conf($TT));

    $txt=trim($cf_main['body'],"\n ");

    $w='';
    $TXTS=explode('=========================',$txt);

$kk=0;

foreach($TXTS as $ii=>$s) {

    $s=trim($s,"\n=");
    if($cf_main['mode']!='gazeta') $s=preg_replace("/(^|\n) {15,}/s","<p class=epigraf>",$s);
    $s=trim($s," ");
    if(empty($s)) continue;

    $cf=array_merge($cf_main,array(
	'filename'=>'', //verse.php3?id=00000001
	'name'=>'', // 00000001
	'title'=>'', // Прогулка на два оборота
	'description'=>'', // Все (или почти все) стихи Игоря Иртеньева
	'keywords'=>'', // иртеньев правдоруб итого ироническая литература поэзия проза афоризмы ирония юмор
	'letter'=>'',
	'Header'=>'',
	'text'=>'' // Прогулка на два оборота
    ),parse_e_conf($s));
    $s=trim($cf['body'],"\n");

    $kk++;

    if($cf['name']=='') $cf['name']=(false===$cf_main['numeration'] || strlen($cf_main['numeration'])<2 ? $kk : sprintf("%0".substr_count($cf_main['numeration'],'0')."d",$kk));


    $ara=array(
	'acn'=>$GLOBALS['acn'],
        'Access'=>$cf_main['Access'],
	'Date'=>($cf_main['base']==''?(isset($cf_main['numeration'])?$cf['name']:''):rtrim($cf_main['base'],'/').'/'.$cf['name']),
	'Body'=>$s
    );

// idie($ara['Date']."#".$cf_main['base']);
    if(!empty($cf['Header'])) $cf['title']=$cf['Header'];
// else dier($cf);
// idie($cf['Header']." / ".$cf['title']);



    $is_title=(str_replace(array('*','-',' '),'',$cf['title']) !=''); // Есть Header?

    if(!empty($cf['Header'])) $ara['Header']=$cf['Header'];
    elseif($cf_main['mode']=='gazeta') {
	$razbor=STIH_razberi($s);
	$ara['Header'] = (empty($razbor['header']) ? cc_s(STR_one($razbor['body']))."…" : cc_s(STR_one($razbor['header'])));
	// if(strstr($razbor['header'],'СОЛОВЕЙ')) dier($razbor,"@".$ara['Header']."@");
    } else $ara['Header'] = ($is_title ? $cf['title'] : STR_First($s)."…");

    $cf_main['tags']=str_replace(array(',[year]',',[let]',',[nlet]',', [year]',', [let]',', [nlet]'),'',$cf_main['tags']);

    $tags=(strstr($cf_main['tags'],',')?explode(',',$cf_main['tags']):array($cf_main['tags']));

    if(!empty($cf['addtag'])) $tags=array_merge($tags,	( strstr($cf['addtag'],',') ? explode(',',$cf['addtag']) : array($cf['addtag']) ) );


    // поискать дату в подписи
    $adat=array();
    $j=cc_s($razbor['podp']);
    if(isset($cf['date']) && preg_match("/^(\d{4})\-(\d{2})\-(\d{2})/s",$cf['date'],$m) ) $adat=array( $m[1] , $m[2] , $m[3] );
    elseif(preg_match("/(\d{1,2})[\.\/](\d{2})[\.\/](\d{4})/s",$j,$m)) $adat=array( $m[3] , $m[2] , sprintf("%02d",$m[1]) ); // полная дата 1.02.2000 или 01/02/2000
    elseif(preg_match("/(\d{4})[\.\/](\d{2})[\.\/](\d{1,2})/s",$j,$m)) $adat=array( $m[1] , $m[2] , sprintf("%02d",$m[3]) ); // полная дата 2000.02.1 или 2000/02/01
    else {
	if(preg_match("/\d{4}/s",$j,$m)) $adat=array( $m[0] ); // просто найти год четыре цифры YYYY
	$md=explode(' '," январ[ья] феврал[ья] марта* апрел[ья] ма[йя] июн[ья] июл[ья] августа* сентябр[ья] октябр[ья] ноябр[ья] декабр[ья]");
	foreach($md as $n=>$l) { if(!$n) continue;
	    if(preg_match("/(\d{1,2}) *".$l."/si",$j,$m)) { $adat[1]=sprintf("%02d",$n); $adat[2]=sprintf("%02d",$m[1]); break; } // 12 марта
	    if(preg_match("/".$l."/si",$j,$m)) { $adat[1]=sprintf("%02d",$n); break; } // март
	}
    }


//    $year='';
    if(!isset($cf_main['noyear'])) { // тэг ГОД
	    if($cf_main['mode']=='gazeta') {
		if(preg_match("/\d{4}/s",cc_s($razbor['podp']),$x)) $year=$tags[]=$x[0];
		elseif(!empty($adat[0])) $year=$tags[]=$adat[0];
	    } else {
		if(($x=STR_Year($s))!='') $year=$tags[$i]=$x;
		else if(preg_match("/^(\d{4})\//s",$ara['Date'],$x) && isset($x[1])) $year=$tags[]=$x[1];
	    }
    }

    if(!isset($cf_main['nolet'])) { // тэг ПЕРВАЯ БУКВА
        if($cf_main['mode']=='gazeta') {    $x=mb_strtoupper(cc_s($razbor['body']),'cp1251'); if(!empty($x)) $tags[]=$x[0];  }
	else {   $x=mb_strtoupper(STR_First($s),'cp1251'); if(!empty($x)) $tags[]=$x[0]; }
    }

    if(!isset($cf_main['nonlet'])) { // тэг ПЕРВАЯ БУКВА ЗАГОЛОВКА
        if($cf_main['mode']=='gazeta') { $x=mb_strtoupper(cc_s($razbor['header']),'cp1251'); if(!empty($x)) $tags[]='1'.$x[0]; }
	else if(($x=$is_title)) { $x=mb_strtoupper(cc_s($cf['title']),'cp1251'); if(!empty($x)) $tags[]='1'.$x[0]; }
    }



    if(isset($cf_main['tag_day']) && isset($adat[2])) $tags[]='day_'.$adat[2];
    if(isset($cf_main['tag_mon']) && isset($adat[1])) $tags[]='mon_'.$adat[1];

    $ara['tags']=implode(',',$tags);

    $url=getlink( ($cf_main['base']==''?'':rtrim($cf_main['base'],'/').'/') .$cf['name']);

    if(empty($ara['Date'])) {
	if( !empty($adat[0]) && !empty($adat[1]) && !empty($adat[2]) ) $ara['Date']=implode('/',$adat);
	elseif( !empty($adat[0]) && !empty($adat[1]) ) $ara['Date']=$adat[0]."/".$adat[1]."/".toeng($ara['Header']);
	elseif(!empty($adat[0])) $ara['Date']=$adat[0]."/".toeng($ara['Header']);
	else $ara['Date']=toeng($ara['Header']);
    }

    if($cf_main['mode']=='gazeta') {

        if(preg_match("/(\d{1,2})[\.\/](\d{2})[\.\/](\d{4})/s",cc_s($razbor['podp']),$m)) $ara['Date']=rtrim($cf_main['base'],'/').'/'.$m[3].'/'.$m[2].'/'.sprintf("%02d",$m[1]);
	elseif(preg_match("/(\d{4})[\.\/](\d{2})[\.\/](\d{1,2})/s",cc_s($razbor['podp']),$m)) $ara['Date']=rtrim($cf_main['base'],'/').'/'.$m[1].'/'.$m[2].'/'.sprintf("%02d",$m[3]);

    } else {
        $year=STR_Year($s); if(!empty($year)) $ara['Body']=preg_replace("/([^\n]+)$/s","<p class=podp>$1",$ara['Body']);
    }

    if($cf_main['mode']!='gazeta') {
	$ara['Body']=str_ireplace("<br>","{{br}}",$ara['Body']);
	$ara['Body']=preg_replace("/^\n*([^\n<>]+)\n\n/s","<p class=z>$1\n\n",$ara['Body']);
	$ara['Body']=str_ireplace("{{br}}","<br>",$ara['Body']);
    }


$WW='';

if($cf_main['check']) {
    if(!isset($GLOBALS['pall'])) {
	$tag=($cf_main['check']===0||$cf_main['check']===1?'':$cf_main['check']);
	if($tag=='') $GLOBALS['pall']=ms("SELECT Date,Body FROM `dnevnik_zapisi`".ANDC(),"_a");
	else $GLOBALS['pall']=ms("SELECT Date,Body FROM `dnevnik_zapisi` WHERE `num` IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag`='".e(trim($tag))."'".ANDC().")".ANDC(),"_a");
    }

    foreach($GLOBALS['pall'] as $p) {
	    similar_text($p['Body'],$ara['Body'],$percent); $percent=floor($percent);
	    if($percent>80) {
		    $wlink=getlink($p['Date']);
		    $WW.="<tr class=br><td>совпадение<div style='font-size:40px;'>".$percent."%</div><br><a href='".$wlink."' target='_blank'>$wlink</a></td><td>".nl2br(h($p['Body']))."</td></tr>";
	    }
    }
    if($WW!='') $WW="<table border=1>".$WW."</table>";

}


$i=0; $d=$ara['Date']; while(in_array($d,$DATS)) $d=$ara['Date']."_".(++$i);
$DATS[]=$ara['Date']=$d;

$w.="<div id='".h($ara['Date'])."' class='mumu'>
<hr>
<!-- form name='i_".h($ii)."' onsubmit=\"return ajaxform(this,'module.php',{mod:'ADDPOSTS',a:'save',i:'".h($ii)."'})\" -->

<form name='i_".h($ii)."' onsubmit=\"return ajaxform(this,'editor.php',{a:'submit',asave:'save_done(result_editor);'})\">
<p><input type='submit' value='Save_".h($ii)."'> <i class='e_false'></i>
<div>tags: <input size=50 name='tags' value=\"".nl2br(h($ara['tags']))."\"></div>
<div>Date: <input size=50 name='Date' value=\"".nl2br(h($ara['Date']))."\" onchange='chandeDate(this)'></div>

<div class=r>
Доступ: ".selecto('Access',$ara['Access'],array('all'=>"всем",'admin'=>"админу",'podzamok'=>"друзьям"))."
Формат: ".selecto('autoformat',$cf_main['autoformat'],array("no"=>'html',"p"=>'текст',"pd"=>'текст с отступом'))."
Кавычки: ".selecto('autokaw',$cf_main['autokaw'],array("auto"=>'заменять',"no"=>'нет'))."
<i class='e_cancel' onclick='DelItem(this)'></i>
</div>

<div>Заголовок: <input size=80 name='Header' value=\"".nl2br(h($ara['Header']))."\"></div>
<div><textarea name='Body' style='width:95%;height:170px' class=t>".h($ara['Body'])."</textarea></div>
$WW
</form>
</div>";

}

//=====================================================

$o="<div>acn: ".$GLOBALS['acn']."</div>

<div>
<input type='button' onclick='DOI=false;saveall();' value='SaveAll'>
 &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' onclick='DOC=false;checkall(0);' value='CheckAll'>
 &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' onclick='checkall(1);' value='Check'>
</div>

".$w

// .nl2br(h(print_r($GLOBALS['pall'],1)))
;

return "zabil('buka',\"".njsn($o)."\");";

}

idie('OK');
}


function ADDPOSTS($e) {

/*

$s="

В связи с очередной стабилизацией положения в Чечне, раздаются голоса о необходимости создания нового гимна для этого субъекта РФ.

             Приходи к нам Господь
                   От Моисея

 ГОСУДАРСТВЕННЫЙ ГИМН РЕСПУБЛИКИ ИЧКЕРИЯ
 НЕСОГЛАСОВАННЫЙ ПРОЕКТ
 Музыка А. Александрова
 Слова И. Иртеньева

Сбылась наших дедов мечта вековая
С Россией в единую слиться семью,
Законный кусок получив каравая,
За стол мы уселись на самом краю.

ПРИПЕВ:
Славься Ичкерия наша свободная,
Раньше голодной ты вечно была,
Клика Масхадова антинародная
Нас от несчастья к несчастью вела.

В союз ваш семейный стремясь неустанно,
Мы, как и положено членам семьи,
На музыку вашу в степях Казахстана
Счастливые песни слагали свои.

ПРИПЕВ.

Кто были мы раньше? Простые абреки.
Что знали мы раньше? Уклад родовой.
Но связаны узами дружбы навеки,
Мы твердо вступили на путь трудовой.

ПРИПЕВ.

Тот путь освещают Ермолов с Грачевым,
С него нам теперь не свернуть никуда,
Случится беда – мы подставим плечо вам,
И это действительно будет беда.

9.11.2001




";

idie(implode("\n",STIH_razberi($s)));

dier(STIH_razberi($s));
*/


SCRIPTS("

DOI=false;
DOC=false;

function checkall(q) {
    if(DOC===false) DOC=0;
    var w=idd('buka').querySelectorAll('.mumu');
    for(var i=DOC;i<w.length;i++) {
	var e=w[i];
	if(e) {
	    DOC=i+1;
	    var f=e.querySelector('form');
	    majax('module.php',{mod:'ADDPOSTS',a:'check',name:f.name,Date:f.Date.value,Body:f.Body.value,all:q});
	    return;
	}
    }
    DOC=false;
}


function saveall() {
    if(DOI===false) DOI=0;
    var w=idd('buka').querySelectorAll('.mumu');
    for(var i=DOI;i<w.length;i++) {
	var e=w[i];
	e=e.querySelector('INPUT[type=\"submit\"]');
	if(e) { DOI=i; e.click(); return; }
    }
    DOI=false;
}

function chandeDate(e) {
 var w=e.closest('.mumu');
  w.id=e.value;
}

function DelItem(e) {
 var w=e.closest('.mumu');
 if(confirm('Delete '+w.id)) clean(w);
}

save_done=function(xx){

    x=xx.split(' ');

    if(DOI!==false && (x[0]=='EDIT_OK' || x[0]=='NEW_OK') ) {
	idie('OK ['+xx+'] DOI='+DOI);
	var link=mypage.replace(/addposts$/,'')+x[2];
	zabil( x[2] , '<a href=\"'+link+'\">'+link+'</a> '+xx);
	DOI++; saveall();
    }

    if(x[0]=='NEW_OK' || x[0]=='EDIT_OK') {
	var link=mypage.replace(/addposts$/,'')+x[2];
	return zabil( x[2] , '<a href=\"'+link+'\">'+link+'</a> DO '+xx);
    }

    if(x[0]=='NEW_ERROR') {
	if(x[1]=='DATE') return alert('Already Exist, try: '+x[2]);
    }

alert(x);

};

");


return $o="
<div style='position:relative'>
<form onsubmit=\"return ajaxform(this,'module.php',{mod:'ADDPOSTS',a:'make'})\">
<p><textarea name='txt' style='width:95%;height:170px' class=t>".h($TT)."</textarea>
<p><input type='submit' name='go' value='добавить'>
</form>

<div style='position:absolute;bottom:0px;right:0px'>
<i class='e_cancel' onclick=\"otkryl('DIVtagdel');clean(this);\"></i><div style='display:none' id='DIVtagdel'>
<form onsubmit=\"if(!confirm('Реально удалить все заметки с этим тэгом?!')) return false; return ajaxform(this,'module.php',{mod:'ADDPOSTS',a:'deltags'})\">
<input type='text' name='tags' size=10 value=''>
<input type='submit' value='Delete'>
</form>
</div>

</div>
</div>

<div id='buka'></div>";

}


function STR_one($s) {
    $s=str_replace('<br>',"\n",$s);
    if(false!==strpos($s,"\n")) $s=explode("\n",$s)[0];
    return strip_tags($s);
}

function STR_First($s) { // $s=str_replace('     ','<p class=epigraf>',$s);
    $s=cc_s($s);
    $e=explode("\n",$s);
    for($i=0;$i<sizeof($e);$i++) {
	if(strstr($e[$i],'epigraf')) {
	    while($i+1<sizeof($e) && $e[$i]!='' && $e[$i+1]!='') $i++;
	    $i++;
	    if($i>=sizeof($e)) return '';
	    continue;
	}
	break;
    }
    if($i>=sizeof($e)) return '';
    return cc_s(strip_tags($e[$i]));
}

function STR_Year($s) { $e=explode("\n",trim($s,"\n")); $e=$e[sizeof($e)-1];
    if(!preg_match("/(^|[^\d]+)(\d{4})([^\d]+|$)/s",$e,$m)) return '';
    return $m[2];
}

function cc_s($s) { return trim(strip_tags($s),"\n -—,.?!:;…„“«»–'\""); }



function ree($e) { return explode("\n",trim(implode("\n",$e),"\n")); }
function STIH_razberi($s) { $s=trim($s,"\n");
    $len=0; $ilen=0;
    $e=explode("\n",$s);
    for($i=0;$i<sizeof($e);$i++) { $x=strlen($e[$i]); if($x) { $len+=$x; $ilen++; } }
    $sred=$len/$ilen;
    $a=array(
	'prolog'=>'',
	'epigraf'=>'',
	'header'=>'',
	'body'=>'',
	'podp'=>''
    );

    if( strlen($e[sizeof($e)-1]) && !strlen($e[sizeof($e)-2]) ) { $a['podp']="<p class=podp>".$e[sizeof($e)-1]; unset($e[sizeof($e)-1]); $e=ree($e); } // подпись

    if( !strlen($e[1]) && (strlen($e[0]) > $sred*1.5) ) { $a['prolog']="<p class=prolog>".trim($e[0]).""; unset($e[0]); $e=ree($e); } // пролог - первая фраза длиннее средней строки

    $lastm=50; $stop=200;
    while( --$stop && preg_match("/ {5,}/s",$e[0],$m) ) {
	$a['epigraf'].= (strlen($m[0]) > $lastm ? "<p class=epigrafp>" : "<p class=epigraf>") .trim($e[0])."\n";
        $lastm=strlen($m[0]);
        unset($e[0]);
	$e=ree($e);
    } // эпиграф: "      А.Б."

    if( mb_strtoupper($e[0],'cp1251') == $e[0] ) { // если первая строка вся заглавная, то заголовок и все, что под ней
	$ep=array();
	for($i=0;$i<sizeof($e);$i++) {
	    if(!strlen($e[$i])) break;
	    $ep[]=trim($e[$i]); unset($e[$i]);
	}
	$a['header']="<p class=z>".implode("<br>",$ep);
	$e=ree($e);
    } elseif( strlen($e[0]) && !strlen($e[1]) ) { // если первая строка отдельностоящая, то она заголовок
	$a['header']=$e[0];
	unset($e[0]); unset($e[1]);
	$e=ree($e);
    }

    $a['body']=implode("\n",$e);
    return $a;
}


function toeng($s) {
    $s=cc_s($s);
    $s=mb_strtolower($s,"cp1251");
    $s=str_replace(str_split("\n-—,.?!:;…„“«»–'\"ьъ"),'',$s);

    $s=str_replace(
	array('ж','ц','ч','ш','щ','я','ю'),
	array('zh','ts','ch','shch','shc','ya','yu')
	,$s);

    $s=strtr($s,"абвгдеёзийклмнопрстуфхыэ ","abvgdeezijklmnoprstufhye_");

    if(preg_match("/([^0-9a-z\_\-])/s",$s,$m)) idie("Transliterate error \"".$m[0]."\": [".h($s)."]");


    return $s;
}

?>