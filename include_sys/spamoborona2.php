<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// спамоборона 2 и фильтры
































// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ЭТОТ ФАЙЛ ТОЛЬКО ЕСЛИ ФОТКИ!!!! ЗАПОМНИ УЖЕ!!!






































/* lleo: */

function blokmudaka($i=0,$s=' ') { global $BANNEDCOMM;

// 0 - только скрыть SET `scr`='1',`Text`='".e("{screen:
// если 1:
// 

    if($i==0) {
	if(1*$GLOBALS['unic']==0) return logi('comment-img-check-all.log',"\n#### ERROR: unic=0".$s);
	msq("UPDATE `dnevnik_comm` SET `scr`='1',`Text`='".e("{screen:\n".$GLOBALS['ara']['Text']."\n}")."' WHERE `unic`=".intval($GLOBALS['unic']) ); // заскринить все его комменты
	if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>(1*$GLOBALS['IS']['capchakarma']+20)),"WHERE `id`=".intval($GLOBALS['unic']) ); // ВРЕМЕННО забанить мудака
	return logi('comment-img-check-all.log',"\n#### banned softly #".$s);
    }



// не помню, зачем это

    if($BANNEDCOMM!=1) { $BANNEDCOMM=1;
	// del_comm($GLOBALS['newid']); // удалить коммент, фотку оно само удалит
        $fimg=$GLOBALS['fotodir'].basename($GLOBALS['to']); if(is_file($fimg)) unlink($fimg);
        $GLOBALS['ara_text_save']=str_ireplace('[IMG]',"\nhttps://lleo.me/dnevnik/2021/03/kotiki.jpg\n",$GLOBALS['ara']['Text']);
        $GLOBALS['ara']['Text']=str_ireplace('[IMG]',"\nhttps://lleo.me/dnevnik/2021/03/kotiki0.jpg\n",$GLOBALS['ara']['Text']);
        $GLOBALS['ara_nomail']=1;
	msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // забанить мудака
        return logi('comment-img-check-all.log',"\n######### BANNED with change image #".$s);
    }


    if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // забанить мудака
    logi('comment-img-check-all.log',"\n######### BANNED HARDLY #".$s);

    otprav("f_save('comment',''); clean('".$GLOBALS['idhelp']."');
if(typeof(playswf)!='undefined') playswf('".$GLOBALS['httphost']."/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));
");

}
/* :lleo */


if(isset($imgs) && isset($to) && is_file($filehost.$to)) { // если были приложены фотки
	$md5=md5(fileget($filehost.$to));
/* lleo: */
	$parid=-1; $parids=array(2742036=>'брайнтуник',4999786=>'глеб');
	if(time()-$GLOBALS['IS']['time_reg'] < 84600) {
/* :lleo */

    if(1*$GLOBALS['ara']['Parent']!=0) { // кому отвечали?
	$parid=intval(ms("SELECT `unic` FROM `dnevnik_comm` WHERE `Parent`='".e($GLOBALS['ara']['Parent'])."'",'_l'));
	if(!$parid) $parid=-2;
    }

 logi('comment-img-check-all.log',"\n====================".date("Y-m-d H:i:s")."========".$GLOBALS['IP']."========== ".$parid." "
 .(isset($parids[$parid])?" [".$parids[$parid]."]":'')
 ."\n"
 .$GLOBALS['BRO']."\n"
 .print_r(array(
 	'FILE'=>$GLOBALS['FILE'],
 	'ara'=>$GLOBALS['ara'],
 	'newid'=>$GLOBALS['newid'],
 	'fotodir'=>$GLOBALS['fotodir'],
         'to'=>$GLOBALS['to'],
         'md5'=>$GLOBALS['md5']
    ),1));

/* lleo: */
  	$newmudak=1;
  } else $newmudak=0;


 if($newmudak==1 && ( trim($ara['Text'])=='[IMG]' || isset($parids[$parid]) ) ) blokmudaka(0); // пустой коммент с фоткой? поначалу забанить
 if($newmudak==1 && (stristr($GLOBALS['FILE']['name'],'hello_') || stristr($GLOBALS['ara']['Name'],'Дядька'))) blokmudaka(1);

 $mm=''; $aa=file($GLOBALS["host_log"]."comment_foto_banned.log"); foreach($aa as $l) { $l=c0($l); if(empty($l)||!strstr($l,' ')) continue;
    list($m5,)=explode(' ',$l,2); $m5=trim($m5);
    $qs.="<br> $m5 $md5 ";
    if($md5!=$m5) continue;
    blokmudaka(1,$m5);
    break;
 }

// if($GLOBALS['FILE']['name']=='gas.png') { del_comm($GLOBALS['newid']); // удалить коммент, фотку оно само удалит
//     idie('NOT '.$GLOBALS['BANNEDCOMM'].' '.$qs);
// }

/* :lleo */

} // конец блока "если были приложены фотки"


// if(strstr($ara['Text'],"У Стоуна в том посте странная аргументация")) blokmudaka(1); // нехуй гадить

// if(strstr($ara['Text'],"поняли из моего ника")) blokmudaka(1); // нехуй гадить

// Как все поняли из моего ника я из Украины и за Украину и я требую чтобы немедленно прекратили терроризировать в Украине таких же как я!


?>