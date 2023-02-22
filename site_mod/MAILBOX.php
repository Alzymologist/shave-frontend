<?php /* Проверка новых входящих в почтовом ящике
*/

SCRIPTS('mailcheck',"page_onstart.push('Mbox.check()');");

function MAILBOX($e) { return $e;
/*
if(($n=ms("SELECT COUNT(*) FROM ".$GLOBALS['db_mailbox']." WHERE `unicto`=".intval($GLOBALS['unic'])." AND `timeread`=0","_l")))
    SCRIPTS("page_onstart.push(\"majax('mailbox.php',{a:'mail'})\");");

    if(empty($e)) return $n;
    return ($n?str_replace("{count}",$n,$e):'');
*/
}
?>