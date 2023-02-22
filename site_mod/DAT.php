<?php /* DAT

Удобное создание сущностей по темплейту. В каждой строке параметры разделяются |, первый
параметр называется {0}, второй {1} и т.д. Каждая строка заменяется на темплейт.

Опция {n} заменяется на порядковый номер данного элемента, опции вида {n2}, {n3} и т.п. - на порядковый номер с N нулей в начале (001, 0001)

{_DAT: template=\n<p>{n2}. <a href='/dnevnik/{1}.html'>{1} ? {2}</a><br>{@MP3: http://lleo.me/audio/f5/{0}@}
facebook.mp3	| 2011/10/17 | Ода социальным сетям
konoplya.mp3	| 2011/10/03 | Ода газетным новостям
china.mp3	| 2011/09/26 | Ода про рис и репу
shlagbaum.mp3	| 2011/09/19 | Ода шлагбауму
_}
*/

$GLOBALS['DAT_SUM_N']=0;

function DAT($e) { $GLOBALS['DAT_SUM_N']++;

	$cf=array_merge(array(
		'template'=>'{0} {1} {2} {3} {4} {5}',
		'SIZE'=>0,
		'SEP'=>' ', // если '', то не пытаться делить
		'SEPARATOR'=>"\n"
	),parse_e_conf($e));
	$cf['template']=str_replace(array("\\n","<space>"),array("\n"," "),$cf['template']);

// dier($cf);
	if($cf['SEPARATOR']!="\n") { $cs=$cf['SEPARATOR']; $cf['body']=trim($cf['body'],$cs); }
	elseif(strstr($cf['body'],"\n||\n")) { $cs="\n||\n"; $cf['body']=trim($cf['body'],"\n|"); }
	elseif(strstr($cf['body'],"\n\n")) { $cs="\n\n"; $cf['body']=trim($cf['body'],"\n"); }
	else { $cs="\n"; $cf['body']=trim($cf['body'],"\n"); }

	$s=''; $i=1;
	$G=explode($cs,$cf['body']);
	foreach($G as $l) { if(empty($l))continue;

		if($cf['SEP']=='') $a=array($l);
		else {
		    $cc = (strstr($l,"|") ? "|" : "\n");
		    $a=($cf['SIZE'] ? explode($cf['SEP'],$l,$cf['SIZE']) : explode($cc,$l));
		}

		foreach($a as $n=>$l) { $a[$n]=str_replace('\n',"\n",trim($l,"\t\r\n ")); }
		if(preg_match("/\{n(\d+)\}/s",$cf['template'],$m)) $a['n'.$m[1]]=sprintf("%0".$m[1]."d",$i);
		if(preg_match("/\{sum(\d+)\}/s",$cf['template'],$m)) { $is='DAT_SUM_'.$GLOBALS['DAT_SUM_N'].'_'.$m[1];
			if(!isset($GLOBALS[$is])) $GLOBALS[$is]=0;
			$GLOBALS[$is]+=$a[$m[1]];
			$a['sum'.$m[1]]=$GLOBALS[$is];
		    }
//		if(preg_match("/\{itogo(\d+)\}/s",$cf['template'],$m)) $a['itogo'.$m[1]]=(isset($GLOBALS['DAT_SUM_'.$m[1]])?$GLOBALS['DAT_SUM_'.$m[1]]:'[empty '.$m[1].']');
		$a['n']=($i++);
		$s.=mper($cf['template'],$a);
	}
	return str_replace(array('{@','@}'),array('{_','_}'),$s);
}
?>