<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// спамоборона и фильтры

// if($GLOBALS['admin']) idie("Нет, мы комментариев не пишем пока");

// Избавиться от лишних антиботовых картинок
$t=time()-60*60; $glob=glob($GLOBALS['antibot_file']."*.jpg"); if($glob) foreach($glob as $l) { if(filemtime($l)<$t) unlink($l); }

// если указан cronfile - выполнить его в момент приема комментария, просто как факт

// if(!is_file($GLOBALS['cronfile']) or (time()-filemtime($GLOBALS['cronfile'])) > 60*60 ) { include_once $GLOBALS['filehost']."cron.php"; }

/*
if($GLOBALS['admin']) idie('ok: '
.(is_file($GLOBALS['cronfile'])?1:0)." - "
.(time()-filemtime($GLOBALS['cronfile']))
.(
(!is_file($GLOBALS['cronfile']) or (time()-filemtime($GLOBALS['cronfile'])) > 6 ) ? 'DA' : 'NET'

)
);
*/

// Мы получили новый коммент, он пока не записан. Его параметры: $name,$text,$mail, а также если $scr=1 - то он будет скрыт

function normtxt($s) {
    $s=strtr($s,'ETYOPAHKXCBMeyopakxc','ЕТУОРАНКХСВМеуоракхс'); // латрус
    return strtr($s,"\n".' АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЬЪЭЮЯ','__абвгдеёжзийклмнопрстуфхцчшщыьъэюя'); //регистр
}

/* lleo: */
function zbanmudaka($i=0,$s=' ') { $logban='BAN-comment.log';

    // 0 - мягенько, чуть подлнять карму

    if($i==0) {
        if(1*$GLOBALS['unic']==0) return logi($logban,"\n#### ERROR: unic=0".$s);
        // msq("UPDATE `dnevnik_comm` SET `scr`='1',`Text`='".e("{screen:\n".$GLOBALS['ara']['Text']."\n}")."' WHERE `unic`=".e($GLOBALS['unic']) ); // заскринить все его комменты
        if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>(1*$GLOBALS['IS']['capchakarma']+20)),"WHERE `id`=".intval($GLOBALS['unic']) ); // ВРЕМЕННО забанить мудака
        return logi($logban,"\n#### banned softly #".$s);
    }

    // 1 - жестко и открыто нахуй
    if($i==1) {
        if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // забанить мудака
        logi($logban,"\n######### BANNED HARDLY #".$s);
        redirect('http://natribu.org/');
        // otprav("f_save('comment',''); clean('".$GLOBALS['idhelp']."');if(typeof(playswf)!='undefined') playswf('".$GLOBALS['httphost']."/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));");
    }


    // 2 - непонятно забанить
    if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // забанить мудака
    logi($logban,"\n######### BANNED HARDLY #".$s);
    // redirect('http://natribu.org/');
    otprav("f_save('comment',''); clean('".$GLOBALS['idhelp']."');if(typeof(playswf)!='undefined') playswf('".$GLOBALS['httphost']."/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));");

}
/* :lleo */



$tss='_'.normtxt($text);
$politword="путин putin собян собяк авальн avaln белолент либераст либерал оппозиц опозиц кремл госдум единая_рос единую_рос единой_рос _ер_ протестн электор"
	    ." онищен жирик жирин зюган явлинск чубайс немцов горбач ельцин чубайс выборы выборов выборах митинг манежн болотн кировл крым бендер бандер";




if(!$GLOBALS['admin']) { // для посетителей, но не для админа

	// 1. нельзя подписываться хозяином блога!
	if(strstr($name,$GLOBALS['admin_name'])) $name="Looser #".$GLOBALS['unic'];

	// 2. если встретилась точка между двумя латинскими буквами - это 99% ссылка! а ссылка - это 90% спам!
	$l=preg_replace("/p\.s/si",'',
// для незалогиненных: проверять, есть ли точка в указанном имени
(!empty($GLOBALS['IS']['openid'])||!empty($GLOBALS['IS']['login'])?$text:$text.$name)
); // есть лишь одно исключение: 'P.S.'
	if(preg_match("/[a-z]\.[a-z]/si",$l) or strstr($l,'<')) $scr=1; // скрыть его!

	// послал нахуй? пиздуй туда сам!
//	if(stristr($text,'lleo.aha.ru/na')||stristr($text,'natribu.org')) redirect('http://natribu.org/');

// if(stristr($text,'Бог')) idie("Слово 'Бог' нельзя упоминать всуе!"); // по крайней мере в момент тестирования скрипта
//if(stristr(strtr($text,'еуЕУиi','eyeyyy'),'jquery')) idie("<table width=500><td><div align=justify>Я запрещаю упоминать в своем дневнике jQuery! Долой чемоданы! Шучу. Это всего лишь демонстрация работы фильтров, описанных в файле <a href='".$GLOBALS['wwwhost']."install.php?load=include_sys/spamoborona.php&mode=view'>include_sys/spamoborona.php</a>, вы можете изменить их для своего блога и написать любые другие.</div></td></table>");

	$tt=str_replace(array("\n","\t","\\"),' ',$text); $tt=explode(' ',$tt);
	foreach($tt as $et) { if(strlen($et)>100 && !strstr($et,'://')) idie("<table width=500><td><div align=justify>Ваше сообщение имеет какие-то сложные части без пробелов.<br>Ну что это такое:<br><i>".h(substr($et,0,50))."...</i><br>Приведите пожалуйста в удобный для чтения формат.</div></td></table>"); }

if(preg_match("/^https*\:\/\/[^\s]+$/si",$text)) idie("<table width=500><td><div align=justify>Просьба: не пишите ссылки без пояснений, это выглядит не завлекательно. Лучше добавьте пару своих слов, чтобы было сразу понятно, о чем речь. Спасибо.</div></td></table>");


/* lleo: */

/*
// Михаил заебал
    if($unic==6342186) { foreach(explode(' ',$politword) as $l)	if(stristr($tss,$l)) {
	    $cap=(1*$GLOBALS['IS']['capchakarma'])+1; msq_update($GLOBALS['db_unic'],array('capchakarma'=>e($cap)),"WHERE `id`=".e($GLOBALS['unic']) );
	    idie("Михаил, вы очень всех утомляете со своим троллингом на тему политики.<p>Каждый раз, когда вы пишете коммент о политике, ваша капча будет расти на 1 цифру.<br>Вот сейчас она уже ".$cap);
	}
    }
*/

if(time()-$GLOBALS['IS']['time_reg'] < 84600) { // НОВИЧОК?



if( // по моему нику
    stristr($text,"моего ника")
|| stristr($text,"моим ником")
|| stristr($text,"моему нику")
) zbanmudaka(2,"поняли из моего ника $name : $text");


/*
if(
stristr($text,"все тусовки которые посещает автор")
|| stristr($text,"еврейские приколы")
|| stristr($text,"рожами дегенератов")
|| intval($name) === $name
) zbanmudaka(1,"Почему все тусовки которые посещает автор: $name : $text");
*/












/*
    // Карманов
    if(strstr(normtxt($name),'кармано')) zbanmudaka(1," Карманов идет нахуй: $name"); // жестко баним человека с ником Карманов

    if($id) { // если ответ на чей-то коммент
	    $parent_unic=intval(ms("SELECT `unic` FROM `dnevnik_comm` WHERE `id`=".intval($id),'_l'));
	    if($parent_unic==6707323) zbanmudaka(0," name: $name\nText: $text"); // и если это ответ Фыве, то мягко баним
    }
*/

/*
  // баним новичка за мат
  if(0)   foreach(explode(' ',"дроч бля бляд блят въеб выеб долбое ёб ебал ебан ебен ебл ебущ ебуч заеб манд муда муде муди мудо пидар пидор пизд уеб хуе хуё хуй хую хуя хуи") as $l){
	if(stristr($tss,$l)) {
	    if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>222),"WHERE `id`=".e($GLOBALS['unic']) ); // ВРЕМЕНН
	    logi('comment-img-check-all.log',"\n-----------".date("Y-m-d H:i:s")."-----".$GLOBALS['IP']."========== "."\n".$GLOBALS['BRO']."\n## banned MUDOSLOV: ##".$text);
	    redirect('http://natribu.org/');
	    // $text="{screen:\n".$text."\n}";
	    // break;
	}
  }

  // предупреждаем новичков о политике
  if(0) foreach(explode(' ',$politword) as $l) if(stristr($tss,$l)) idie("Дорогой друг, я НЕ ХОЧУ видеть в своем блоге комментарии по теме политики.
Такой коментарий всё равно будет удален, не трудись его писать.
Если ты хочешь свободно высказаться на темы политики, попробуй написать комментарий где-нибудь на другой площадке, но не на моем сервере.
Например, можешь свободно комментировать мой жж <a href=http://lleo-kaganov.livejournal.com>http://lleo-kaganov.livejournal.com</a> или фейсбук.
Там, на чужих серверах, на общественных площадках и соцсетях, у тебя будет полная свобода слова.");
*/

/* :lleo */

if(!isset($_REQUEST['html'])) {
    $esli="<p>Если вы хотите процитировать код html, зайдите в опции комментария ('штуки') и поставьте соответствующую галочку.";
    if(stristr($text,'<img')) idie("<table width=500><td><div align=justify>Обрамлять картинку в тэги не нужно, напишите адрес картинки, и она вставится автоматически (после премодерации). Спасибо. $esli</div></td></table>");
    if(stristr($text,'<a ')) idie("<table width=500><td><div align=justify>Обрамлять ссылку тэгами не нужно, напишите только адрес - он сам подсветится как ссылка. Спасибо. $esli</div></td></table>");
    if(stristr($text,'<')) idie("<table width=500><td><div align=justify>Но вы в курсе, что здесь тэги html не отобразятся? Если хотите дать ссылку или вставить картинку - просто пишите url, он обработается автоматически. Хотите использовать элементы оформления - нажмите зеленую стрелочку над окном редактора комментария, а когда подгрузится панель с кнопками, выделите мышкой участок текста и нажмите нужную. Либо сами используйте тэги [i],[b],[s] и [u], но не html. $esli</div></td></table>");
    //if(stristr($text,'Бог')) idie("Слово 'Бог' нельзя упоминать всуе!"); // по крайней мере в момент тестирования скрипта
}

} // time_reg

} // admin

/* lleo: */
elseif($unic==4
// && 0
) { $GLOBALS['IPN']=3002876031;
$GLOBALS['IP']=ipn2ip($GLOBALS['IPN']);
// idie("запрещено"); 
}
/* :lleo */

?>