<?php
// график биткоина


STYLE_ADD($GLOBALS['www_css']."animate.css");
SCRIPT_ADD($GLOBALS['wwwhost']."extended/rg.js");

SCRIPTS("
page_onstart.push('btcgraph1min();');

var dat,line,bstop=0;

function upal(e){ bstop=1; plays('/dnevnik/2017/12/00165.mp3',1); anim(e,'hinge',function(){zakryl('rgBTC1m');plays('/dnevnik/2017/12/00165.mp3'); zabil('btc','ой, биткоин упал!');}); }

function aminmax(a) { var max=0,min=99999999,c; for(var i in a) { c=a[i]; if(c===null) continue; if(1*c<min) min=c; if(c>max) max=c; } return [min,max]; }

function RUN(){ if(bstop) return;
var xhr=new XMLHttpRequest(); xhr.onreadystatechange=function(){
if(this.readyState==4 && this.status==200 && this.responseText!=null) {
var x=JSON.parse(this.responseText);
    RUR=Math.floor(1*(x.RUB.last)); zabilc('RUR',RUR); zabilc('RUB',RUR);
    USD=1*(x.USD.last); zabilc('USD',Math.floor(USD));
    STALOUSD=Math.floor(BTC*USD); zabilc('STALOUSD',STALOUSD);
    STALORUB=Math.floor(BTC*RUR); zabilc('STALORUB',STALORUB); zabilc('STALO',STALORUB);

var bs=Math.floor(STALORUB-BYLORUB); zabilc('ITOGO',(	bs>=0
	? PLUS.replace(/RUB/g,bs).replace(/USD/g,(STALOUSD-BYLOUSD))
	: MINUS.replace(/RUB/g,(-bs)).replace(/USD/g,-(STALOUSD-BYLOUSD))
	));

var x0=USD+Math.random()*(Math.random()<0.5?-5:5); // совсем чуть-чуть рандомизируем

if(line.originalData[0][0]===-1) {
    for(i=0;i<line.originalData[0].length;i++) line.originalData[0][i]=USD+Math.random()*(Math.random()<0.5?-5:5);
}

line.originalData[0].push(x0); line.originalData[0].shift();
var a=aminmax(line.originalData[0]); line.set({yaxisMin:a[0]}); line.set({yaxisMax:a[1]});
RGraph.SVG.redraw();

setTimeout('RUN()',1000);
} };
xhr.open('GET','https://blockchain.info/ru/ticker',true); xhr.send();
}

function btcgraph1min() { useropt.ani=1;

    KURSUSD=l_read('KURSUSD');
    KURSRUB=l_read('KURSRUB');
    BYLOUSD=l_read('BYLOUSD');
    BYLORUB=l_read('BYLORUB');
    BTC=l_read('BTC');
    BTC_re(0);

    if(!BTCDATA.length) { for(i=0;i<100;i++) BTCDATA.push(-1); }

    line=new RGraph.SVG.Line({id:'rgBTC1m',data:BTCDATA,options: {
gutterTop:8,gutterBottom:10,gutterLeft:40,gutterRight:20,backgroundGridVlinesCount:30,
xaxis: false, yaxis: false,linewidth:3,yaxisMax:0,yaxisMin:0,
textSize:'8px',attribution:false,shadow:true}}).draw();
RUN();
}

function BTC_re(e) { if(e && e.id) {
	if(e.id!='BTC') KURSUSD=KURSRUB=BYLOUSD=BYLORUB=0; window[e.id]=e.value;
    }
    KURSUSD=(typeof(KURSUSD)!='undefined' && 1*KURSUSD?1*KURSUSD:0);
    KURSRUB=(typeof(KURSRUB)!='undefined' && 1*KURSRUB?1*KURSRUB:0);
    BYLOUSD=(typeof(BYLOUSD)!='undefined' && 1*BYLOUSD?1*BYLOUSD:0);
    BYLORUB=(typeof(BYLORUB)!='undefined' && 1*BYLORUB?1*BYLORUB:0);
     USDRUB=(typeof(USDRUB) !='undefined' && 1*USDRUB ?1*USDRUB:0);
     BTC=(typeof(BTC)!='undefined' &&1*BTC?1*BTC:0);

    if(BTC && USDRUB) {

    if(KURSUSD || BYLOUSD) {
	if(!BYLOUSD) BYLOUSD=KURSUSD*BTC;
	if(!KURSUSD) KURSUSD=BYLOUSD/BTC;
		if(KURSRUB) BYLORUB=KURSRUB*BTC; else if(BYLORUB) KURSRUB=BYLORUB/BTC; else { KURSRUB=KURSUSD*USDRUB; BYLORUB=KURSRUB*BTC; }
    }
    else if(KURSRUB || BYLORUB) {
	if(!BYLORUB) BYLORUB=KURSRUB*BTC;
	if(!KURSRUB) KURSRUB=BYLORUB/BTC;
		if(KURSUSD) BYLOUSD=KURSUSD*BTC; else if(BYLOUSD) KURSUSD=BYLOUSD/BTC; else { KURSUSD=KURSRUB/USDRUB; BYLOUSD=KURSUSD*BTC; }
    }

    KURSUSD=Math.floor(KURSUSD);
    KURSRUB=Math.floor(KURSRUB);
    BYLOUSD=Math.floor(BYLOUSD);
    BYLORUB=Math.floor(BYLORUB);
	
    }

    if(idd('KURSUSD')) idd('KURSUSD').value=(KURSUSD?KURSUSD:'');
    if(idd('KURSRUB')) idd('KURSRUB').value=(KURSRUB?KURSRUB:'');
    if(idd('BYLOUSD')) idd('BYLOUSD').value=(BYLOUSD?BYLOUSD:'');
    if(idd('BYLORUB')) idd('BYLORUB').value=(BYLORUB?BYLORUB:'');
    if(idd('BTC')) idd('BTC').value=(BTC?BTC:'');

    zabilc('KURSUSD',KURSUSD);
    zabilc('KURSRUB',KURSRUB);
    zabilc('BYLOUSD',BYLOUSD);
    zabilc('BYLORUB',BYLORUB);
    zabilc('BTC',BTC);

    l_save('KURSUSD',KURSUSD);
    l_save('KURSRUB',KURSRUB);
    l_save('BYLOUSD',BYLOUSD);
    l_save('BYLORUB',BYLORUB);
    l_save('BTC',BTC);

}");

function BTCGRAPH($e){
    $cf=array_merge(array(
	'BTC'=>0,
	'KURSRUB'=>0,
	'KURSUSD'=>0,
	'BYLORUB'=>0,
	'BYLOUSD'=>0,
	'MINUS'=>'<font color=red>пропало примерно RUB руб или \$'.'USD!</font>',
	'PLUS'=>'<font color=green>добавилось примерно RUB руб или \$'.'USD!</font>',
	'DATE'=>date('d M Y в H:i:s'),
	'GRAPH'=>"<center>
<div class=r>график курса биткоина к доллару{_SNOSKA:ƒанные берутс€ с https://blockchain.info/ru/ticker_}</div>
<div alt='Ќе надо кликать!' style='display:inline-block;border:0px solid #ccc;width:100%;height:200px' id='rgBTC1m' onclick='upal(this)'></div>
</center>"
    ),parse_e_conf($e));


// dier($cf);

    if(empty($cf['body'])) $cf['body']="ƒопустим, у —таськи по€вилось 0.64 mBTC (<span class='BTC'>$BTC</span> биткоина).
<p>¬ тот день 21 Feb 2018 биткоин стоил 10419 USD, а в рубл€х 590292.38 руб, поэтому 0.00064*590292.38=<span class='BYLO'>$BYLO</span> руб.
<p>—ейчас ".date('d M Y в H:i:s')." цена биткоина <span class='USD'>$USD</span> USD или <span class='RUR'>$RUR</span> руб. Ёто значит, что <span class='BTC'>$BTC</span> BTC сейчас стоит <span class='BTC'>$BTC</span>*<span class='RUR'>$RUR</span>=<b><span class='STALO'>$STALO</span></b> руб.
“о есть, на данный момент <span class='ITOGO'>...</span>

{GRAPH}
"; else if(!strstr($cf['body'])) $cf['body'].="\n\n{GRAPH}";

    if(!$cf['BTC']) return "<font color=red>BTCPGPH: ошибка, вы должны задать сумму BTC</font>";
    if(!$cf['KURSUSD'] && !$cf['KURSRUB'] && !$cf['BYLOUSD'] && !$cf['BYLORUB']) return "<font color=red>BTCPGPH: ошибка, вы должны задать либо
курс на момент покупки в долларах KURSUSD, либо в рубл€х KURSRUB,
либо сколько было потрачено на тот момент рублей BYLORUB или долларов BYLOUSD</font>";

    include_once($GLOBALS['include_mod']."BTCINFO.php");
    $cf['USD']=BTCINFO('');
    $cf['RUR']=BTCINFO('val=RUB');
    $cf['STALORUB']=floor($cf['BTC']*$cf['RUR']);
    $cf['STALOUSD']=floor($cf['BTC']*$cf['USD']);
    $cf['USDRUB']=$cf['RUR']/$cf['USD'];

    // ладно, попробуем вычислить
    if($cf['KURSUSD'] || $cf['BYLOUSD']) {
	if(!$cf['BYLOUSD']) $cf['BYLOUSD']=$cf['KURSUSD']*$cf['BTC'];
	if(!$cf['KURSUSD']) $cf['KURSUSD']=$cf['BYLOUSD']/$cf['BTC'];

		if($cf['KURSRUB']) $cf['BYLORUB']=$cf['KURSRUB']*$cf['BTC'];
		elseif($cf['BYLORUB']) $cf['KURSRUB']=$cf['BYLORUB']/$cf['BTC'];
		else {
		    $cf['KURSRUB']=$cf['KURSUSD']*$cf['USDRUB'];
		    $cf['BYLORUB']=$cf['KURSRUB']*$cf['BTC'];
		}
    }
    elseif($cf['KURSRUB'] || $cf['BYLORUB']) {
	if(!$cf['BYLORUB']) $cf['BYLORUB']=$cf['KURSRUB']*$cf['BTC'];
	if(!$cf['KURSRUB']) $cf['KURSRUB']=$cf['BYLORUB']/$cf['BTC'];

		if($cf['KURSUSD']) $cf['BYLOUSD']=$cf['KURSUSD']*$cf['BTC'];
		elseif($cf['BYLOUSD']) $cf['KURSUSD']=$cf['BYLOUSD']/$cf['BTC'];
		else {
		    $cf['KURSUSD']=$cf['KURSRUB']/$cf['USDRUB'];
		    $cf['BYLOUSD']=$cf['KURSUSD']*$cf['BTC'];
		}
    }

//	$r=ms("SELECT `BTC` FROM `lleoaharu`.`bitcoin` ORDER BY `time` DESC LIMIT 100"); $o=''; foreach($r as $p) $o=$p['BTC'].','.$o;
//    $cf['BTCDATA']=trim($o,',');
// {BTCDATA}

    SCRIPTS( mpers(
"var "
."BTC={BTC},USDRUB={USDRUB},BYLORUB={BYLORUB},"
."BTCDATA=[],MINUS=\"{MINUS}\",PLUS=\"{PLUS}\";",$cf) );

    $cf['KURSUSD']=floor($cf['KURSUSD']);
    $cf['BYLOUSD']=floor($cf['BYLOUSD']);
    $cf['KURSRUB']=floor($cf['KURSRUB']);
    $cf['BYLORUB']=floor($cf['BYLORUB']);

    return mpers($cf['body'],$cf);

}
?>