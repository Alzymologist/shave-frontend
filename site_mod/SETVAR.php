<?php /* set language

{_SCRIPTS: var commenttmpl='comment_ej.htm';_}
{_SETVAR: comment_tmpl=comment_ej.htm _}

*/

function SETVAR($e) {
#    // запрос ранее установленной переменной
#    if(false===strpos($e,'=')) { $e=c($e); return (isset($article['VAR'])&&isset($article['VAR'][$e])?$article['VAR'][$e]:''); }
    // установка переменной
    $c=parse_e_conf($e);
    foreach($c as $n=>$l) {
	if(in_array($n,array(
'comment_tmpl'
	))) $GLOBALS[$n]=$l;
	else if(in_array($n,array(
// 'comment_tmpl'
	))) $GLOBALS['article'][$n]=$l;
    }
    return $c['body'];

//    <script>/*lleo*/var commenttmpl='{_PHPEVAL:$o=$GLOBALS['comment_tmpl']='comment_ej.htm';_}';</script>
//     $GLOBALS['mylang']=c0($e);
//     SCRIPTS('SETLANG',"var mylang='".hh(c0($e))."';");
//    return '';
}
?>