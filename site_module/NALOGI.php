<?php

STYLES("

#rezok {
index:3;
padding:20px;
background-color:#eee;
position:fixed;
border:3px solid orange;
border-radius: 100%;
padding: 40px;
}

table { border: 2px solid #6cf; border-collapse: collapse; }
.pod, .pod td { border: 0px !important; }


table td { font-size:10px;
border-right: 1px dashed #6cf;
border-bottom: 1px dashed #6cf;
padding:3px;
max-width:500px;}

table th { font-size:8px;
border-bottom: 2px solid #6cf;
padding:5px;}


.alsumm { font-weight:bold; color: #800; cursor:pointer; }



.summ { font-weight:bold; color:#449; text-align:right;
    border-top: none;
    border-right: 1px dashed #6cf;
    border-bottom: 1px dashed #6cf;
    border-left: none;
    padding: 3px 2px 2px 3px;
}


.sel { font-weight:bold; color:#449; text-align:right;
    border: 3px solid green !important;
    padding: 0px 0px 0px 0px;
}

.cht { cursor:pointer; }

.podr { cursor:pointer; color: #449; }

.e_list-add, .e_list-remove { cursor:pointer; }

.alsumm:hover, .summ:hover, .podr:hover { background-color:#eef; }

.zg { font-family:\"comic sans ms\"; margin: 20px 0 5px 0; font-size:16px; font-weight:bold; text-align:center; }
.zg1 { font-size:9px; font-family:none; font-weight:normal; }

");


SCRIPTS("

page_onstart.push('nalstart()');

ALLSUMM=0;

clc=function(){
    var w=idd('clc').value;
    w=w.replace(/[^0-9\-\+\.\(\)\*\/]+/g,''); // убрать лишние символы
    w=w.replace(/\,/g,'.'); // заменить запятые на точки
    if(w=='') return;
    zabil('clcr',eval(w));
};

addclc=function(x,c) {
    x=x.target.closest('div').querySelector('SPAN').innerHTML;
    if(x==''|| 1*x==0) return;
    idd('clc').value+=c+x;
    clc();
};

function countall() {
    nonav=1;

ALLSUMM=0;
var e=document.querySelectorAll('.summ');
for(var i in e) { if(isNaN(i)) continue;
    if(e[i].className.indexOf('sel')>=0) ALLSUMM+=(1*e[i].innerHTML);
}

    zabil('rezok_itogo',(Math.ceil(ALLSUMM*100)/100));
    zabil('rezok_6',(Math.ceil(ALLSUMM*100*0.06)/100));
    zabil('rezok_300',(ALLSUMM <= 300000 ? '' : (Math.ceil((ALLSUMM-300000)*100*0.01)/100) ));

}


function nalstart(){

    doclass('e_list-add',function(e){ addEvent(e,'click',function(x){addclc(x,'+')}); });
    doclass('e_list-remove',function(e){ addEvent(e,'click',function(x){addclc(x,'-');}); });

    doclass('podr',function(e){ addEvent(e,'click',function(x){ xo=x=x.target;
	    if(x.className!='podr') x=x.closest('td.podr');

	    if(x.querySelector('i.e_cancel')) {
		if(xo.className=='e_cancel' || xo.querySelector('i.e_cancel')) x.innerHTML=x.getAttribute('dataold');
	    } else {
		var a=x.getAttribute('data').split('||');
		var o=''; for(var i in a) { if(a[i].indexOf('|')<0) continue;
		    var s=a[i].split('|'); if(s[1]==''||s[0]=='') continue;
		    o+='<tr><td>'+h(s[0])+': </td><td>'+h(s[1])+'</td></tr>';
		}
		x.setAttribute('dataold',x.innerHTML);
		x.innerHTML=\"<div><i class='e_cancel'></i></div><table class='pod'>\"+o+'</table>';
	    }
    }); });


    doclass('alsumm',function(e){

	addEvent(e,'click',function(x){ x=x.target;
	    x=x.closest('table');
	    var u=x.querySelectorAll('.summ');
	    var seli='summ sel';
	    for(var i in u) { if(isNaN(i)) continue; if(u[i].className.indexOf('sel')>=0) { seli='summ'; break; }  }
	    for(var i in u) { if(isNaN(i)) continue; u[i].className=seli; }
	    countall();
	});
    });



    doclass('summ',function(e){

	addEvent(e,'click',function(x){ x=x.target;
	    if(x.className.indexOf('sel')>=0) {
		x.className='summ';
	    } else {
		x.className='summ sel';
	    }
	    countall();
	});
    });

countall();
}

");


    $GLOBALS['article']['opt']='a:3:{s:8:"template";s:7:"rasskaz";s:10:"autoformat";s:2:"pd";s:7:"autokaw";s:2:"no";}';

function printr($name,$u) {
	$o='';
	foreach($u as $n=>$l) {
	    $o.="<tr>";
	    $o.="<td>".h($l['Дата'])."</td>";
	    $o.="<td class='summ'>".h($l['Сумма'])."</td>";
	    $o.="<td class='podr' data=\"";
		foreach($l as $li=>$lu) $o.=h($li)."|".h($lu)."||";
	    $o.="\">".h($l['НазначениеПлатежа'])."<div class=br>".h($l['Плательщик1'])."</div></td>";
	    $o.="</tr>";
	}

	return "<center><div class=zg>".$name."</div><table>"
."<tr>"
    ."<th>Число</th>"
    ."<th class='alsumm'>Сумма</th>"
    ."<th>Назначение и отправитель</th>"
."</tr>".$o."</table></center>";
}

function NALOGI($e) {
$cf=array_merge(array(
'template'=>"<br><a href='{acc_link}'>{acc}</a> (<a href='{acc_link}contents'>{count}</a>)"
),parse_e_conf($e)); $s=$cf['body'];

$s=c0($s);
if(strstr($s,"\n\n")) list($tabs,$s) = explode("\n\n",$s,2);
else $tabs='';

/*
$tabs="
ДОХОД СЕБЕ НА КАРТУ: НазначениеПлатежа = собственных средств
Проценты от банка: НазначениеПлатежа = Уплачены проценты
Комиссия банка: НазначениеПлатежа = Комиссия за проведение операций
НАЛОГИ: Получатель1 = Управление Федерального казначейства
Вносил свои деньги: НазначениеПлатежа = внесение собственных
ВОЗВРАТ ИЗ НАЛОГОВОЙ: НазначениеПлатежа = Инспекция Федеральной
ВХОДЯЩИЕ: Получатель1 = Идивидуальный предприниматель Каганов Леонид Александрович
";
*/

$s=preg_replace("/\n*КонецДокумента\nКонецФайла\n*$/s",'',$s);
list(,$s)=explode("\nСекцияДокумент=",$s,2); $s="СекцияДокумент=".trim($s);

$a=explode("\nКонецДокумента",$s);

foreach($a as $n=>$u) { if(trim($u)=="") continue;
    if(strstr($u,"\nСекцияДокумент")) list(,$u)=explode("\nСекцияДокумент",$u,2); $u="\nСекцияДокумент".$u;

    $z=explode("\n",$u);
    $ara=array(); foreach($z as $l) { if(trim($l)=="") continue;
	list($x,$y)=explode('=',$l,2); $x=trim($x); $y=trim($y);
	$ara[$x]=$y;
    }
    $a[$n]=$ara;
}



    $otabs=array();

    if($tabs != '') {
	$tt=explode("\n",$tabs);
	foreach($tt as $t) {
	    if(empty($t=c0($t)) ) continue;

	    if(!strstr($t,':')) $name=h($t); else { list($name,$t)=explode(':',$t,2); $name=h($name)."<div class='zg1'>".h($t)."</div>"; }
	    if(empty($name=c0($name)) || !strstr($t,'=')) continue;

	    list($tag,$t)=explode('=',$t,2);
	    if( empty($tag=c0($tag)) || empty($t=c0($t)) ) continue;

	    $otabs[$name]=array($tag,$t);
	}
    }

$OOO=array();
foreach($a as $n=>$u) { if(!isset($u["Получатель1"])) continue;

    foreach($otabs as $name=>$p) {
	if(stristr($u[$p[0]],$p[1])) {
		if(!isset($OOO[$name])) $OOO[$name]=array();
		$OOO[$name][]=$u;
		unset($a[$n]);
	}
      }
}

$o="<div id='rezok'>"
."<div><i class='e_list-add'></i> <i class='e_list-remove'></i> ИТОГО: <span id='rezok_itogo'></span></div>"
."<div><i class='e_list-add'></i> <i class='e_list-remove'></i> 6%: <span id='rezok_6'></span></div>"
."<div><i class='e_list-add'></i> <i class='e_list-remove'></i> 1% свыше 300тыс: <span id='rezok_300'></span></div>"
."<div><input type=text id='clc' size=20 onchange='clc()');\"><i class='e_cancel' onclick=\"idd('clc').value='';\"></i><br>= <span id='clcr'></span></div></div>"
."</div>";
foreach($OOO as $n=>$O) $o.=printr($n,$O);
$o.=printr("ОСТАЛЬНЫЕ",$a);
return $o;

}

?>