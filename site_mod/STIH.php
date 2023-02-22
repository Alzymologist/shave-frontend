<?php /* оформление стиха

Удобный тег для оформления стихов вне зависимости от опций автоверстки заметки.
Если нужно дать строкам отступ (например для припева или написания лесенкой), в начале строки ставится 1,2,3... пробела - каждый пробел превратится в заметный отступ, равный 8 символам.

{_STIH:
Однажды Алексей Васильевич Крылов написал следующее стихотворение.

   из школьного

ХОЛОДИЛЬНИК

В трехдверный холодильник
я засунул кипятильник.
Не знаю, что получится —
пусть техника помучится!

предположим, февраль 1986, А.Крылов

_}

*/

STYLES(".prolog { font-style: italic; wwwwwwmax-width: 80%; font-size: 14px; } ");

function STIH($e) { global $cf;

// dier("@".$e."@");

$cf=array_merge(array(
'TAB'=>8,
'noheader'=>0,
'delheader'=>0,
'nopodp'=>0,
'noprolog'=>0,
'center'=>1,
'no'=>0
),parse_e_conf($e));

    if($cf['no']) { $cf['nopodp']=$cf['noprolog']=1; if(!$cf['delheader']) $cf['noheader']=1; }

    $e=$cf['body'];
    if(!preg_match("/^\s*(<div id='Body_\d+'>)(.+?)(<\/div>)\s*$/s",$e,$m)) $m=array('','',$e,'');
    $m[2]=str_replace(array('<br>','<p>'),array("\n","\n\n"),$m[2]);
    $a=STIH_razberi($m[2],$cf);
// dier($a);
    $a['body']=preg_replace_callback("/(^|\n)( +)/s",function($t){ return $t[1].str_repeat(chr(160), strlen($t[2])*$GLOBALS['cf']['TAB'] ); },$a['body']);
    if($cf['center']) $a['body']="{_CENTER:".str_replace("\n","<br>",trim($a['body']))."_}";
    else $a['body']=str_replace("\n","<br>",trim($a['body']));
    return $m[1].implode('',$a).$m[3];
}

function ree($e) { return explode("\n",trim(implode("\n",$e),"\n")); }
function STIH_razberi($s,$cf) { $s=trim($s,"\n");
    $len=0; $ilen=0;
    $e=explode("\n",$s);
    for($i=0;$i<sizeof($e);$i++) { $x=strlen($e[$i]); if($x) { $len+=$x; $ilen++; } }
    $sred=$len/$ilen;
    $a=array(
        'prolog'=>'',
//        'epigraf'=>'',
        'header'=>'',
        'body'=>'',
        'podp'=>''
    );

    // ищем подпись
    if( !$cf['nopodp'] && strlen($e[sizeof($e)-1]) && !strlen($e[sizeof($e)-2]) ) { $a['podp']="<p class=podp>".$e[sizeof($e)-1]."</p>"; unset($e[sizeof($e)-1]); $e=ree($e); } // подпись

    // ищем пролог (длинную ПЕРВУЮ фразу)
    if( !$cf['noprolog'] && !strlen($e[1]) && (strlen($e[0]) > $sred*1.5) && !preg_match("/^ {5,}/s",$e[0])) { $a['prolog']="<p class=prolog>".trim($e[0])."</p>"; unset($e[0]); $e=ree($e); } // пролог - первая фраза длиннее средней строк

// dier($e);

    // ищем заголовок
 if(!$cf['noheader']) {
    if( mb_strtoupper($e[0],'cp1251') == $e[0] ) { // если первая строка вся заглавная, то заголовок и все, что под ней
        $ep=array();
        for($i=0;$i<sizeof($e);$i++) {
            if(!strlen($e[$i])) break;
            $ep[]=trim($e[$i]); unset($e[$i]);
        }
        $a['header']="<p class=z>".implode("<br>",$ep)."</p>";
        $e=ree($e);
    } elseif( strlen($e[0]) && !strlen($e[1]) ) { // если первая строка отдельностоящая, то она заголовок
        $a['header']=$e[0];
        unset($e[0]); unset($e[1]);
        $e=ree($e);
    }
 }
    // заменяем все эпиграфы
    $last=0;
    for($i=0;$i<sizeof($e);$i++) {
	if(preg_match("/^( {5,})(.+?)$/s",$e[$i],$m)) {
	    $len=strlen($m[1]);
	    if(!$last) {
		$last=$len;
		$e[$i]="<p class='epigraf'>".$m[2];
	    } else {
		if($len>$last) {
		    $e[$i-1].="</p>";
		    $e[$i]="<p class='epigrafp'>".$m[2]."</p>";
		    $last=0;
		    	$e[$i]="[".$e[$i]."]";
		} else {
		    	$e[$i]=$m[2];
		}

	    }
	} elseif($last) { // закрыть
	    $e[$i-1].="</p>";
	    $last=0;
		$e[$i]=$e[$i];
	}
    }

   if($cf['delheader']) unset($a['header']);

    $a['body']=implode("\n",$e);

//    $a['body']=preg_replace("/(^|\n) {5,}([^\n]+)\n {6,}([^\n]+)/s","$1<p class=epigraf>$2</p><p class=epigrafp>$3</p>",$a['body']);
//    $a['body']=preg_replace("/(^|\n) {5,}([^\n]+)/s","$1<p class=epigraf>$2</p>",$a['body']);

// dier($a);

    return $a;
}


?>
