<?php /* все страницы с этим тэгом

{_ALLTAG: гребаный стыд _}

*/

function GETALLTAGS($e) {
    $cf=array_merge(array(
        'sort'=>0,
        'serparator'=>"\n"
    ),parse_e_conf($e)); $e=$cf['body'];

if(strstr($e,',')) { $e=explode(',',$e); foreach($e as $n=>$l) $e[$n]=e(trim($l)); } else $e=array(e($e));

if(false!==($k=array_search('YYYY',$e))) { unset($e[$k]); $and=(empty($e)?'':"num IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag` IN ('".implode("','",$e)."')".ANDC().") AND ");
        $p=ms("SELECT DISTINCT `tag` FROM `dnevnik_tags` WHERE ".$and."(`tag` LIKE '19__' OR `tag` LIKE '20__')".ANDC(),"_a1");
	if($cf['sort']==0) sort($p); else rsort($p);
	return $o=implode($cf['serparator'],$p);
}

if(false!==($k=array_search('A',$e))) { unset($e[$k]); $and=(empty($e)?'':"num IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag` IN ('".implode("','",$e)."')".ANDC().") AND ");
        $p=ms("SELECT DISTINCT `tag` FROM `dnevnik_tags` WHERE ".$and."`tag` LIKE '_'".ANDC(),"_a1");
	sort($p);
	return $o=implode($cf['serparator'],$p);
}

if(false!==($k=array_search('1A',$e))) { unset($e[$k]); $and=(empty($e)?'':"num IN (SELECT `num` FROM `dnevnik_tags` WHERE `tag` IN ('".implode("','",$e)."')".ANDC().") AND ");
        $p=ms("SELECT DISTINCT `tag` FROM `dnevnik_tags` WHERE ".$and."`tag` LIKE '1_'".ANDC(),"_a1");
	sort($p);
	return $o=implode($cf['serparator'],$p);
}


return $o='ERR';


/*
	dier($p,$GLOBALS['msqe'].ANDC());

    if($e=='') { $e=gettags()[0]; }
    if(empty($e)) return;
    return "{_CENTER:<i><b>Все страницы по теме &laquo;".h($e)."&raquo;:</b></i>
{_ANONS:
template = <div style='margin-left:50pt'>{Y}-{M}-{D}: <a href='{link}'>{Header}</a></div>
tags = ".h($e)."
days = 0
_}_}";
*/
}
?>