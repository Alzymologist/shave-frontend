<?php // разный мусор

function wu($s) { $s=strtr($s,chr(152),'@'); return(iconv("windows-1251","utf-8//IGNORE",$s)); } // а знали ли вы, что блядский код 152 всё нахуй вешает?

include "../config.php"; include $include_sys."_msq.php";
// $a=RE('a');
//    $time=time();
    ms_connect();
//    msq_add('bot_event',arae(array('unit'=>$unit,'message'=>$Q)));
//if($a=='base') {
    $pp=ms("SELECT * FROM ".$GLOBALS['db_memory_visit']."",'_a',0);
    $o=''; foreach($pp as $p) $o.="\n".$p['unic'].' '.$p['time'].' '.(''==$p['user']?'':wu($p['user']));
    die(time().$o);
//
?>