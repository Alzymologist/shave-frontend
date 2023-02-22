<?php // СКРИПТ ГОЛОСОВАНИЯ v.3 ajax и всё типа круто
/*
Вот такой код вставляется в дневник:

{_GOLOS2: ПРИДУМАЙТЕ_ИМЯ_ВАШЕГО_ГОЛОСОВАНИЯ

1. Как вы думаете, что это?
-- Это голосование
-- Это проверка, как работает модуль голосования
-- Это проверка, как работает модуль, а потом будут объяснения

2. Как вы вообще сюда попали?
-- Зашел случайно
-- Подписан на здешний RSS
-- Да так, брожу, наблюдаю...

3. Нравится ли вам движок дневника?
-- По-моему пора закругляться: для теста достаточно и двух пунктов
-- Я шутник и член партии "Шаловливая Россия"

_}

знак ! в начале имени означает, что голосование закрыто для новых голосов

*/

if(function_exists('STYLES')) {

STYLES('golosovanik',"
.capchacode {width:59px;height:33px;font-size:33px;}
.uinfo { display:inline-block; cursor:pointer; font-weigth:bold;}
.golos_voprosbody { padding:20px 0 0 50px; }
");

SCRIPTS('golosovanik',"

function golos_graf_all(lee) { var id=lee.closest('.golos_all'); clean(lee);
    var N=id.querySelectorAll('.golos_vopros');
    for(var i in N) { if(isNaN(i)) continue; golos_graf(N[i].querySelector('[class~=\"e_kr_invert\"]')); }
}

function golos_graf(le) { if(!le) return; var id=le.closest('.golos_vopros').querySelector('.golos_voprosbody'); clean(le);
    var T=id.querySelectorAll('.golos_txt'); if(!T.length) return;
    var N=id.querySelectorAll('.golos_proc'); if(!N.length || N.length!=T.length) return;
    var dat=[]; for(var i in T) if(!isNaN(i)) { dat.push({name:T[i].innerHTML,y:1*(N[i].innerHTML.replace(/\%/,''))}); }

LOADS([
'https://code.highcharts.com/highcharts.js',
'https://code.highcharts.com/modules/exporting.js',
'https://code.highcharts.com/modules/export-data.js'
],function() {

id.style.height='200px';
id.style.width='600px';

Highcharts.chart(id, {
  credits: { enabled: false },
  chart: {
    backgroundColor: 'transparent',
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: { text: '' },
  tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
            style: { color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black' }
      }
    }
  },
  series: [{ name: '', colorByPoint: true, data: dat }]
});
});
}

function golos_kak(e,name,unic) {

    if(e.className!='uinfo') e=e.parentNode.querySelector('DIV');

AJAX(www_ajax+'json.php?a=vote&name='+name+'&user='+unic,{callback:function(s){
    z=JSON.parse(s);

    var tmpl_part=GOLOSBASA[name][1];
    var RR=GOLOSBASA[name][3];

    var o='',nvopros=0; for(var vopros in RR){ nvopros++; var s='';
    var notvet=0; for(var i in RR[vopros]) { notvet++;
	s+='<div>'+(z.value[nvopros]==notvet?\"<i class='e_button_accept'></i>\":\"<i class='e_cancel1'></i>\")+' '+RR[vopros][i]+'</div>';
    }
    o+=mpers(tmpl_part,{vopros:vopros,s:s});
    }
    clean('golosoval'); ohelp('golosoval','Vote List','<span class=l>'+e.innerHTML+'</span> <span class=r>'+z.time+'</span><p>'+o);
}});
}

function golos_whois(name) {

AJAX(www_ajax+'json.php?a=vote&name='+name+'&opt=unic',{callback:function(s){
s=JSON.parse(s);
var o='',ico;
for(var i in s) {
    if(s[i].user=='') s[i].user='#'+s[i].unic;
    if(s[i].ico=='') ico='';
    else if( s[i].ico.indexOf('e_')===0 ) ico=\"<i class='\"+s[i].ico+\"'></i> \";
    else ico=\"<img width=16 height=16 src='http://\"+s[i].ico+\"/favicon.ico'> \";
    o+=\"<div>\\
<i class='e_kontact_journal mv' alt='View vote' onclick=\\\"golos_kak(this,'\"+name+\"',\"+s[i].unic+\")\\\"></i>&nbsp;\\
<div class='uinfo' alt='View userinfo' onclick=\\\"majax('login.php',{a:'getinfo',unic:\"+s[i].unic+\"})\\\">\"+ico+s[i].user+\"</div></div>\";

}
ohelpc('golosovali','Список голосовавших',o);
}});
}

");
}


function GOLOS2($e) {

    if(isset($GLOBALS['PUBL'])) return "\n\n======== [ ГОЛОСОВАНИЕ ] ========\nнедоступно при трансляции в соцсети, зайдите на сайт\n======== [ /ГОЛОСОВАНИЕ ] ========\n\n";

$cf=array_merge(array(

'template'=>"<div class='golos_txt'>{text}</div><div style='margin-bottom:20px;'>"
."<img src=".$GLOBALS['www_design']."img/golo.gif class='centr' style='height:10px !important; width:{kx}px'>"
."<div class='centr' style='font-size:10px;padding-left:10px;'><b class='golos_proc'>{proc}%</b> ({x})</div></div>",
'template_ask'=>"<div class='tb'><label><input class='mv' name='golos_{nvopros}' type='radio' value='{nvariant}'> {text}</label></div>",
'template_part'=>"<div class='golos_vopros'><i class='e_kr_invert mv' alt='Graf' onclick='golos_graf(this)'></i>&nbsp;<b class='golos_voprostxt'>{vopros}</b><div class='golos_voprosbody'>{s}</div></div>",
'template_main'=>"<div id='GOLOS_{golosname}' class='golos_all'>{s}<div>{golos_off}".LL('golos:total').": <b>{sum}</b> <i class='e_help mv' alt='кто именно?' onclick=\"golos_whois('{golosname}')\"></i>"
."&nbsp;<i class='e_kr_invert mv' alt='Graf All' onclick='golos_graf_all(this)'></i>"
."</div></div>"
),parse_e_conf($e));

	list($golosname,$vopr)=explode("\n",$cf['body'],2); $golosname=trim($golosname,"\n\r\t: ");
	$golosname=c(h($golosname));

	if('!'==substr($golosname,0,1)) { // знак ! в начале имени означает, что голосование закрыто для новых голосов
	    $golosname = substr($golosname,1);
	    $golosoval = 1;
	    $golosoff = 1;
	} else {
	    $golosoff = 0;
	    $golosoval = ms("SELECT COUNT(*) FROM `golosovanie_golosa` AS a, `golosovanie_result` AS r WHERE a.`unic`=".intval($GLOBALS['unic'])." AND a.`golosid`=r.`golosid` AND r.`golosname`='".e($golosname)."'","_l");
	}

	$golosname=substr($golosname,0,32);
	$vopr=golos_chit($vopr);

//	 if($GLOBALS['ADM']) $golosoval=false;
	 if(isset($_GET['REGOLOS'])) $golosoval=false;

	// взять результаты
	if($golosoval) {
		$s=ms("SELECT `text`,`n` FROM `golosovanie_result` WHERE `golosname`='".e($golosname)."'",'_1');
		$go=unserialize($s['text']); $nn=$s['n'];
		$k=($nn?(640/$nn):0); // вычислилить коэффициент array_sum($go[$n])
		$kp=($nn?(100/$nn):0);
	} else {
		$nn=intval(ms("SELECT `n` FROM `golosovanie_result` WHERE `golosname`='".e($golosname)."'",'_l'));
	}

	$s='';

	if($GLOBALS['ADM']) $s.=nl2br(golos_recalculate($golosname)).'<p>';

//	$s="<p>Всего проголосовали: <b>".$nn."</b>";

	$voptable=array();
	$voptableall="if(typeof(GOLOSBASA)=='undefined') GOLOSBASA={}; GOLOSBASA['".h($golosname)."']=["
."\"".njsn($cf['template'])."\","
."\"".njsn($cf['template_part'])."\","
."\"".njsn($cf['template_main'])."\","
."{";

	$nvopros=0; foreach($vopr as $vopros=>$var) { $nvopros++;
		$voptable[]=$nvopros.':'.sizeof($var);

		$voptableall.='"'.h($vopros).'":[';

		$o=''; foreach($var as $nvariant=>$text) { $voptableall.='"'.e($text).'",';
			if($golosoval) { // если голосовал
				$x=$go[$nvopros][$nvariant+1]; $o.=mpers($cf['template'],array('text'=>$text,'kx'=>floor($k*$x),'proc'=>floor($kp*$x),'x'=>intval($x)));
			} else { // если не голосовал
				$o.=mpers($cf['template_ask'],array('text'=>$text,'golosname'=>$golosname,'nvopros'=>$nvopros,'nvariant'=>($nvariant+1),intval($x)));
			}
		}
		$s.=mpers($cf['template_part'],array('s'=>$o,'vopros'=>$vopros));
		$voptableall=rtrim($voptableall,',')."],";
	}

	$voptableall=rtrim($voptableall,',')."}];";

	SCRIPTS($voptableall);

	if(!$golosoval) { // если НЕ голосовал
	    if($GLOBALS['IS']['capchakarma']!=1) {
		include_once $GLOBALS['include_sys']."_antibot.php";
		$ca="<div id='capchagolos' class='centr'>".capchagolos()."</div>";
	    } else $ca='';

	    $s="<form onsubmit=\"return ajaxform(this,'module.php',{mod:'GOLOS2',a:'send',golosname:'".h($golosname)."',voptable:'".implode(' ',$voptable)."'})\">"
	    .$s."<div>".$ca."<input class='centr mv0' type='submit' value='".LL('golos:vote')."'></div>"
	    ."</form>";
	}

	return mpers($cf['template_main'],array('golosname'=>h($golosname),'s'=>"$s",'sum'=>$nn,
	'golos_off'=>($golosoff? "Голосование закончено. ": '')
	 ));
}


function capchagolos() {
    return "<input type='hidden' name='capcha_hash' value='".antibot_make()."'>".antibot_img()
    ." <input class='centr mv0 capchacode' size=".$GLOBALS['antibot_C']." type='text' name='capcha'>   ";
}

//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================

function GOLOS2_ajax() {
    if(
	empty($voptable=explode(' ',RE('voptable')))
	|| empty($golosname=RE('golosname'))
    ) idie('Error');

    if(!$GLOBALS['unic']) idie('Error unic=0 Даже не знаю, что и сказать. А попробйте залогиниться (правый верхний угол экрана)? Ответы пропасть вроде не должны при этом.');

// ============ если нужна проверка капчи ==============
    if($GLOBALS['IS']['capchakarma']!=1) {
	include_once $GLOBALS['include_sys']."_antibot.php";

$animate_capcha="try{
    var u=idd('capchagolos').querySelector('[name=capcha]').style;
	setTimeout(function(){u.transitionProperty='transform';u.transitionDuration='1s';u.transform='scale(1.5)';},1500);
	setTimeout(function(){u.transitionProperty='transform';u.transitionDuration='0.5s';u.transform='scale(1)';},2500);

    idd('capchagolos').querySelector('IMG').onload=function(e){
	e=e.target.style; e.transitionProperty='transform';e.transitionDuration='1s';e.transform='scale(1.5)';
	setTimeout(function(){e.transitionProperty='transform';e.transitionDuration='0.5s';e.transform='scale(1)';},1000);
    };
} catch(e){}";

	if(RE('capcha')=='') otprav("salert(\"Введите цифры с картинки в квадратик рядом и покажите, что вы не накрутчик\",1000);".$animate_capcha);
        if(!antibot_check(RE('capcha'),RE('capcha_hash'))) otprav("salert(\"Неверные цифры капчи, повторите!\",2000);zabil('capchagolos',\"".njsn(capchagolos())."\");".$animate_capcha);
}
// ============ // если нужна проверка капчи ==============

    $gol=array();
    foreach($voptable as $vop) { list($nvop,$nvar)=explode(':',$vop,2);
	if(false===($variant=RE('golos_'.$nvop))) otprav("salert(\"Вы не ответили на ".h($nvop)."-й вопрос!<br>Выберите ответ из ".h($nvar)." вариантов.\",2000)");
	$gol[$nvop]=$variant;
    }

    $JS='';
//    if(!golos_update($golosname,$gol)) { $GLOBALS['msqe']=''; $JS.="salert('Ваш голос уже был засчитан!',1000);"; } // добавить голос, если не голосовал такой
    golos_update($golosname,$gol);
    $go=golos_calculate($golosname,$gol); // пересчитать результат

//    $g=ms("SELECT `n`,`text` FROM `golosovanie_result` WHERE `golosname`='".e($golosname)."'","_1",0);
//    if(($go=unserialize($g['text']))===false) idie('err');

    $gop=$JS."var golosa={";
    foreach($go as $n=>$r) {
	$gop.="'$n':{";
	    foreach($r as $i=>$l) $gop.="'$i':$l,";
	$gop=rtrim($gop,",")."},";
    }
    $gop=rtrim($gop,",")."};";

// dier($gop);

    otprav($gop."
var golosname=\"".h($golosname)."\";
var tmpl=GOLOSBASA[golosname][0];
var tmpl_part=GOLOSBASA[golosname][1];
var tmpl_main=GOLOSBASA[golosname][2];
var RR=GOLOSBASA[golosname][3];

var k=1,o='',nvopros=0; for(var vopros in RR){ nvopros++;

    var s='';
    var sum=0; for(var i in golosa[''+nvopros]) sum+=1*golosa[''+nvopros][i];
    var k=(sum?(640/sum):0);
    var kp=(sum?(100/sum):0);

    var notvet=0; for(var i in RR[vopros]) { notvet++;
	var name=RR[vopros][i];
	var x=golosa[''+nvopros][''+notvet] || 0;
	s+=mpers(tmpl,{text:name,x:1*x,proc:Math.floor(kp*x),kx:Math.floor(k*x)});
    }

    o+=mpers(tmpl_part,{vopros:vopros,s:s});
}

o=mpers(tmpl_main,{sum:sum,golosname:golosname,s:o});
zabil('GOLOS_'+golosname,o);
");
}

//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================


// получить id по имени
function golos_id($name) { $name=substr($name,0,32);
    if($id=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e($name)."'","_l")) return $id;
    msq_add('golosovanie_result', arae(array('golosname'=>$name)) ); return msq_id();
}

// записать в базу голосов новый голос
function golos_update($name,$gol) { $id=golos_id($name);
    return msq_add_update('golosovanie_golosa', arae(array('unic'=>$GLOBALS['unic'],'golosid'=>e($id),'value'=>serialize($gol))),
"WHERE `golosid`=".intval($id)." AND `unic`=".intval($GLOBALS['unic']) );
}

// учесть новый голос (пересчитать результаты)
function golos_calculate($name,$gol) { $name=substr($name,0,32);
    if(false===($g=ms("SELECT `n`,`text` FROM `golosovanie_result` WHERE `golosname`='".e($name)."'","_1",0))) $g=array();
    if(($go=unserialize($g['text']))===false) $go=array();
    foreach($gol as $i=>$j) $go[$i][$j]++; // добавить голос нынешнего человека
    msq_add_update('golosovanie_result',arae(array( 'golosname'=>$name,'n'=>(intval($g['n'])+1),'text'=>serialize($go) )),'golosname');
    return $go;
}

// Провести контрольный пересчет (при заходе Админа)
function golos_recalculate($name) { $name=substr($name,0,32);
    $summ=0; // число людей
    $go=array(); // для результатов нового подсчета
    $mes=''; // строка для служебных сообщений

    $golosid=golos_id($name);

	$limit=1000; // обсчитывать порциями по 1000 штук
	$start=0; // начиная с порции 0
	$stop=0; while($stop++<1000) { // ограничение от зависания - 1000 раз по 1000 голосов (1 млн голосовавших?)
		$pp=ms("SELECT `value` FROM `golosovanie_golosa` WHERE `golosid`=".intval($golosid)." LIMIT $start,$limit","_a",0);
		if(!sizeof($pp)) break;
		$start+=$limit;
		foreach($pp as $p) {
			$g=unserialize($p['value']); if($g===false) { $mes.=' error 1'; break; } // если формат неправильный - ошибка
			foreach($g as $i=>$v) $go[$i][$v]++; // учесть голос человека по каждому пункту
			$summ++; // счетчик людей +1
		}
	}

	$mmes=''; // строка для дополнительных служебных сообщений

	$p=ms("SELECT `n`,`text` FROM `golosovanie_result` WHERE `golosname`='".e($name)."'",'_1',0);
	$go0=unserialize($p['text']); // результаты прежнего подсчета
	$summ0=$p['n']; // сумма прежнего подсчета

	if($summ!=$summ0) $mmes.="\nОПС! не сошлось число голосовавших: ".$summ0.", а правильно: ".$summ."\n";
	if(sizeof($go0)!=sizeof($go)) $mmes.="\nОПС! не равны суммы: в базе: ".sizeof($go0).", а правильно: ".sizeof($go)."\n";
/*
	foreach($go as $i=>$g) {
	   if(sizeof($go0[$i])!=sizeof($g)) $mmes.="\n $i) не совпали голоса: ".sizeof($go0[$i]).", а надо: ".sizeof($g)."\n";
	   foreach($g as $k=>$l) if($go0[$i][$k]!=$l) $mmes.="\n $i($k): ".$go0[$i][$k]." != $l";
	}
*/
	if($mmes!='') { $mes.=$mmes;
		// перезаписать:
		$mes .= "<p><font color=red>UPDATE: "
		.msq_add_update('golosovanie_result',arae(array( 'golosname'=>$name,'n'=>$summ,'text'=>serialize($go) )),'golosname')
		."</font>";
	}
	
return $mes;
}


function golos_chit($s) { // распознать голосовалку
	preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$s),$km);
	$vopr=array(); foreach($km[1] as $k=>$mm) {
		$z=trim( preg_replace("/^([^\n]+)\n.*$/si","$1",$mm) );
		preg_match_all("/\n+[\s\-]+([^\n]+)/si",trim($mm),$vv);
		if($z && sizeof($vv[1])) $vopr[$z]=$vv[1];
	}
	return $vopr;
}

?>