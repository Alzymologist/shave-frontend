<?php // ������ �����

function wu($s) { $s=strtr($s,chr(152),'@'); return(iconv("windows-1251","utf-8//IGNORE",$s)); } // � ����� �� ��, ��� �������� ��� 152 �� ����� ������?

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