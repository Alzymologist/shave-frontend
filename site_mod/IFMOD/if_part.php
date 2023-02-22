<?php /*

Выдача разных куской в зависимости от наличия подстроки в заданной mode:

mode=Date - по умолчанию в адресе заметки (полезно делать разные элементы дизайна в одном темплейте, но для разных разделов сайта)
mode=Header - в заголовке заметки
mode=MYPAGE - в полном адресе страницы
mode=QUERY_STRING - в опциях запроса страницы
mode=BRO - в имени браузера
mode=REF - в REFFERER запроса

Эта заметка относится к {_if_part: mode=Date
default=хрен знает какому старому

2020/ Две тыщи двадцатому

2021/ Две тыщи двадцать первому

2022/ Две тыщи двадцать второму

_} году.

*/

function if_part($e) {
    $cf=array_merge(array(
	    'sep'=>"\n",
	    'sepdat'=>' ',
	    'default'=>'',
	    'mode'=>'Date' // Date Header MYPAGE QUERY_STRING BRO REF
    ),parse_e_conf($e)); $e=$cf['body'];

    $pp=( false===strpos($e,$cf['sep']) ? array($e) : explode($cf['sep'],$e) );
    if($cf['mode']=='Date') $d=$GLOBALS['article']['Date'];
    elseif($cf['mode']=='Header') $d=$GLOBALS['article']['Header'];
    elseif($cf['mode']=='MYPAGE') $d=$GLOBALS['MYPAGE'];
    elseif($cf['mode']=='QUERY_STRING') $d=$_SERVER['QUERY_STRING'];
    elseif($cf['mode']=='BRO') $d=$GLOBALS['BRO'];
    elseif($cf['mode']=='REF') $d=$GLOBALS['REF'];
    else return "<font color=red>is_part: unknown mode=".$cf['mode']."</font>";

    foreach($pp as $p) {
	list($di,$s)=(false===strpos($p,$cf['sepdat'])?array($p,''):explode($cf['sepdat'],$p,2));
	if(empty($di=c0($di))) continue;
	if(false!==stripos($d,$di)) return md( str_replace("\\n","\n", c0($s) ) );
	if($di=='default') $cf['default']=md(str_replace("\\n","\n", c0($s) ) );
    }

    return $cf['default'];
}

?>