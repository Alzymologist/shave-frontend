<?php // случайное число

$APIVER='1.2';

include "../config.php"; include $include_sys."_autorize.php";
header('Access-Control-Allow-Origin: *');

$a=RE('a');

/*
[id] => 2704
[unic] => 27658
[Name] => Леонид Каганов
[Text] => http://binoniq.net/user/27658/2704.jpgВасилий Обломов, музыкант
[Parent] => 0
[Time] => 1581554301
[whois] => RU Санкт-Петербург
[scr] => 0
[rul] => 0
[ans] => u
[group] => 0
[golos_plu] => 0
[golos_min] => 0

Комментарии к заметке num
*/

function minmax($i,$f,$t) { return min(max(1*$i,$f),$t); }

function jdier($r,$s='') {
    header("Content-type: text/plain; charset=utf-8");
    $r=array('error'=>$s,'array'=>$r);
    otprav_JSON($r);
}

function jdie($s) {
//    header("Content-type: text/plain; charset=windows-1251");
//    die(gettype($s)=='array'?print_r($s,1):$s);
    header("Content-type: text/plain; charset=utf-8");
    $r=array(
	'error'=>1,
	'reason'=>$s,
	'request'=>$GLOBALS['a'],
	'api_version'=>$GLOBALS['APIVER'],
	'server'=>array(
	    'url'=>$GLOBALS['httpsite'].$_SERVER['REQUEST_URI']
	)
    );
    if(!empty($_POST)) $r['server']['POST']=$_POST;
    otprav_JSON($r);
}

// Получить список допустимых опций
function AskOpts($op,$opdef='') {
    if(!($opt=RE('opt'))) return ($opdef!=''?$opdef:$op);
    if($opt=='help') { $r=ms("SHOW COLUMNS FROM ".$GLOBALS['db'],"_a"); $p=array(); foreach($r as $l) $p[]=$l['Field']; dier($p); }
    $opt=explode(',',$opt); $pe=explode(',',$op);
    foreach($opt as $i=>$l) if(!in_array($l,$pe)) jdie("unknown option: [".$l."]\n\nAllowed options: [".$op."]\n\nDefault options: [".$opdef."]");
    return implode(',',$opt);
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//                       _   _       _
//                      | | | |_ __ (_) ___
//                      | | | | '_ \| |/ __|
//                      | |_| | | | | | (__
//                       \___/|_| |_|_|\___|
//
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////


$db=$db_unic;
if($a=='unic') { // отдать информацию о посетителе unic
    // unic NN или через запятую номера
    // opt - можно указать поля:
    //    "'id','realname','openid','login',mail_comment,site,birth,admin,time_reg,timelast,capcha,capchakarma,opt"
    //    а также если ты спрашиваешь свой номер, то ",mail,mailw,tel,telw,ipn,telegram"
    // 	  по умолчанию: "img,user"
    $uc=RE('unic');
    $podd=array('mailconfirm','loginlevel','user','port','zamok','ico','icon','imgicourl');
    $QQ=AskOpts("id,realname,openid,login,img,mail_comment,site,birth,admin,time_reg,timelast,capcha,capchakarma,opt"
	.($unic==$uc?",mail,mailw,tel,telw,ipn,telegram":'')
	.implode(",",$podd)
	,"user,imgicourl,img");
    $QQ=explode(',',$QQ);
    $or=array_unique(array_merge($QQ,array('id','realname','openid','login')));
    $optneed=(in_array('opt',$or)?1:0);
    foreach($podd as $x) if(in_array($x,$or)) { $or=array_flip($or); unset($or[$x]); $or=array_flip($or); }
    if($uc && false!=strpos($uc,',')) $uc=explode(",",$uc); else $uc=array($uc);
    foreach($uc as $n=>$l) { $l=intval(trim($l)); if(!$l) jdie("Unic 0"); $uc[$n]=$l; }
    $pp=ms("SELECT ".e(implode(',',$or))." FROM ".$db_unic." WHERE `id` IN (".implode(',',$uc).")","_a",60);
    $r=array();
    $pols=array('realname','openid','login','img','mailconfirm','loginlevel','user','port','zamok','ico','icon','imgicourl');
    foreach($pp as $is) {
        $is=get_ISi($is,'{realname}');
        if($optneed) $is['useropt']=mkuopt($is['opt']);
	$id=$is['id']; unset($is['id']);
	foreach($pols as $x) { if(!in_array($x,$QQ)) unset($is[$x]); }
	$r[$id]=$is;
    }
    otprav_JSON(wu($r));
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//                        __  __       _ _
//                       |  \/  | __ _(_) |
//                       | |\/| |/ _` | | |
//                       | |  | | (_| | | |
//                       |_|  |_|\__,_|_|_|
//
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

$db=$db_mailbox;

if($a=='mail') { // отдать вледельцу переписки одно сообщение по id
    // id NN - номер сообщения в базе
    // opt - можно указать поля "id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois" (по умолчанию: "id,unicfrom,unicto,timecreate,timeread,text")
    $id=RE00('id');
    $QQ=AskOpts("id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois","id,unicfrom,unicto,timecreate,timeread,text");
    $r=ms("SELECT ".e($QQ)." FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`='".intval($unic)."' OR `unicfrom`='".intval($unic)."') LIMIT 1","_1",0);
    otprav_JSON(wu($r));
}



if($a=='mails') { // отдать владельцу переписки все его сообщения страницами
    // unic NN - номер абонента (иначе мои)
    // start NN - с NN-го количества, limit NN выдавать по NN штук
    // opt - можно указать поля "id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois" (по умолчанию: "id,unicfrom,unicto,timecreate,timeread,text")
    // возможные флажки:
    // 	my - только написанные мной
    // 	his - только письма мне
    // 	new - только непрочитанные
    $my=(isset($_REQUEST['my'])?1:0);
    $uc=RE00('unic');
    $start=max(RE00('start'),0);
    $limit=RE00('limit'); $limit=($limit?minmax($limit,1,10000):10);
    $cn=array();
    if(isset($_REQUEST['new'])) $cn[]="`timeread`=0";

    if($uc==$unic) $cn[]="`unicto`=".intval($unic)." AND `unicfrom`=".intval($unic);
    elseif(!$uc) {
	if(isset($_REQUEST['my'])) $cn[]="`unicfrom`=".intval($unic);
	elseif(isset($_REQUEST['his'])) $cn[]="`unicto`=".intval($unic);
	else $cn[]="".intval($unic)." IN (`unicfrom`,`unicto`)";
    } else {
	if(isset($_REQUEST['my'])) $cn[]="`unicto`=".intval($uc)." AND `unicfrom`=".intval($unic);
	elseif(isset($_REQUEST['his'])) $cn[]="`unicfrom`=".intval($uc)." AND `unicto`=".intval($unic);
	else $cn[]="((`unicfrom`=".intval($uc)." AND `unicto`=".intval($unic).") OR (`unicto`=".intval($uc)." AND `unicfrom`=".intval($unic)."))";
    }
    $cn=implode(' AND ',$cn);
    $QQ=AskOpts("id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois","id,unicfrom,unicto,timecreate,timeread,text");
    $r=ms("SELECT ".e($QQ)." FROM ".$db_mailbox." WHERE ".$cn." ORDER BY `id` DESC LIMIT ".intval($start).",".intval($limit),"_a",0);

// idie('ok');

// idie("SELECT ".e($QQ)." FROM ".$db_mailbox." WHERE ".$cn." ORDER BY `id` DESC LIMIT ".intval($start).",".intval($limit));

    otprav_JSON(wu($r));
}

if($a=='mailgroup') { // отдать владельцу список unic абонентов, с которыми он когда-либо переписывался до time
    // time - можно указать (по умолчанию за все время), можно указывать ка числом UnixTime, так и строкой типа 2022-08-20 или 2022-08-20 16:50
    // opt - можно указать поля "id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois" (по умолчанию: "id")
    // возможные флажки:
    // 	my - только написанные мной
    // 	his - только письма мне
    $time=RE('time'); if($time && (false!=strpos($time,'-') || false!=strpos($time,':'))) $time=intval(strtotime($time)); else $time=intval($time);
    $pp=(isset($_REQUEST['my'])?array():ms("SELECT DISTINCT `unicfrom` FROM ".$db_mailbox." WHERE `unicto`='".intval($unic)."'".($time?" AND `timecreate`>".$time:''),"_a1"));
    $pp2=(isset($_REQUEST['his'])?array():ms("SELECT DISTINCT `unicto` FROM ".$db_mailbox." WHERE `unicfrom`='".intval($unic)."'".($time?" AND `timecreate`>".$time:''),"_a1"));
    $pp=array_unique(array_merge($pp,$pp2)); unset($pp2);
    $QQ=AskOpts("id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois","id");
    if($QQ=='id') otprav_JSON($pp); // если просто id, то сразу и отправить (не сортировать)
    $r=array(); foreach($pp as $l) {
        $cn="(`unicfrom`=".intval($l)." AND `unicto`=".intval($unic).") OR (`unicto`=".intval($l)." AND `unicfrom`=".intval($unic).")".($time?" AND `timecreate`>".$time:'');
	$r[]=ms("SELECT ".e($QQ)." FROM ".$db_mailbox." WHERE ".$cn." ORDER BY `id` DESC LIMIT 1","_1");
    }
    $R=array(); foreach($r as $p) { $t=$p['timecreate']; while(isset($R[$t])) $t++; $R[$t]=$p; }
    unset($r); krsort($R);
    otprav_JSON(wu($R));
}

if($a=='mail_read') { // пометить письмо как прочитанное
    // id - номер письма
    // флаг unread - наоброт, пометить как непрочитанное
    $id=RE00('id');
    $read=(isset($_REQUEST['unread'])?0:time()); // his - только письма мне
    msq_update($db_mailbox,array('timeread'=>$read),"WHERE `id`=".intval($id)." AND `unicto`=".intval($unic) );
    otprav_JSON(array());
}

if($a=='mail_del') { // удалить письмо
    // id - номер письма
    $id=RE00('id');
    $text=ms("SELECT `text` FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")",'_l',0);
    // удалить все фотки сообщения, если были
    $mboxweb=$GLOBALS['httphost']."user/".intval($unic)."/mbox/";
    $mboxdir=$GLOBALS['filehost']."user/".intval($unic)."/mbox/";
    if(preg_match_all("/".preg_quote($mboxweb,'/')."[^\s]+/s",$text,$m)) { foreach($m[0] as $l) unlink(str_replace($mboxweb,$mboxdir,$l)); }
    // теперь удалить само сообщение
    ms("DELETE FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")",'_l',0);
    otprav_JSON(array());
}

if($a=='mail_save') { // создать или отредактировать письмо
    // id - номер письма (если редактируется)
    // unicto - кому
    // text - сообщение
    // id,answerid,unicfrom,unicto,timecreate,timeview,timeread,text,IPN,BRO,whois","id")
    if(!$unic) jdie('Error: unic=0');
    $id=RE00('id');
    if($IS['loginlevel']<3) jdie('Login level < 3'); // доступно только авторизованным
    $text=uw(RE('text')); // JSON всегда в UTF-8
    $text=str_replace("\r",'',trim($text,"\r\n\t "));
    if($text=='') jdie('Empty text');
    $text=ifUploadFotos($text); // добавить фотки, если были загружены
    $u=RE00('unicto');
    if(1!=ms("SELECT COUNT(*) FROM ".$db_unic." WHERE `id`=".intval($u),"_l",0)) jdie("Unicto ".$u." not found");
    // include_once $include_sys."geoip.php"; $w=geoip($IP,$IPN); $whois=$w['country'].' '.$w['city'];
    if(!$id) {
      $ara=arae(array(
        'answerid'=>RE00('answerid'),
        'unicfrom'=>$unic,
        'unicto'=>$u,
        'timecreate'=>time(),
        'timeview'=>0,
        'timeread'=>0,
        'text'=>$text,
        'IPN'=>$IPN,
        'BRO'=>$BRO,
        'whois'=>'' // $whois
      ));
      if(!ms("SELECT COUNT(*) FROM ".$db_mailbox." WHERE `unicfrom`=".intval($unic)." AND `unicto`=".intval($u)." AND `text`='".e($text)."'","_l",0)) msq_add($db_mailbox,$ara);
      $id=intval(msq_id());
    } else {
        msq_update($db_mailbox,array('text'=>e($text)),"WHERE `id`=".intval($id)." AND `unicfrom`=".intval($unic)." AND `unicto`=".intval($u));
    }

if(!empty($GLOBALS['tg_bot'])) {
    $fromname=str_replace('&nbsp;','',strip_tags($imgicourl)); // от кого
    $p=ms("SELECT `telegram`,`opt` FROM ".$db_unic." WHERE `id`=".intval($u),"_1");
    if($p['telegram']) {
        $opt=mkuopt($p['opt']); // посчитать опции
        if($opt['tgmailnew']==1) {
        $ttxt=$fromname." пишет в личку на сайте ".$httphost.":\n\n".$text;
        $ttxt=substr($ttxt,0,4000); // $ttxt=substr($ttxt,0,1000);
        $e=telegram_send($p['telegram'],$ttxt);
        if(intval($e)) $js="salert('Telegram send',1000);";
        }
    }
}
    otprav_JSON(array('id'=>$id));
}


















//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//                     __     __    _
//                     \ \   / /__ | |_ ___
//                      \ \ / / _ \| __/ _ \
//                       \ V / (_) | ||  __/
//                        \_/ \___/ \__\___|
//
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

if($a=='vote') {
    if(!empty($cp=RE('charset'))) { if($cp=='utf8') $_REQUEST=uw($_REQUEST); }
    if(empty($name=RE('name'))) jdie('empty name');

    $user=RE0('user');

    $opts='unic,time,value'; // golosid,
    if(!($opt=RE('opt'))) $opt=$opts; $e=explode(',',$opt); $pe=explode(',',$opts);
    if($user) unset($pe[array_search('unic',$pe)]);
    foreach($e as $i=>$l) { if(!in_array($l,$pe)) unset($e[$i]); }
    $sie=sizeof($e);
    $e='`'.implode('`,`',$e).'`';

// lleo.me/dnevnik/ajax/json.php?a=vote&name=поясница&charset=utf8

// $pp=ms("SELECT * FROM `golosovanie_result`"); jdie($pp);

    if(
	!($golosid=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e(substr($name,0,32))."'","_l"))
	&& !($golosid=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e(uw(substr($name,0,32)))."'","_l"))
	&& !($golosid=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e(wu(substr($name,0,32)))."'","_l"))
    ) jdie("vote `".$name."` not found");

    if($user) {
	$p=ms("SELECT $e FROM `golosovanie_golosa` WHERE `golosid`=".intval($golosid)." AND `unic`=".intval($user)." LIMIT 1","_1");
	$p['value']=unserialize($p['value']);
        otprav_JSON(wu($p));
    }

    $pp=ms("SELECT $e FROM `golosovanie_golosa` WHERE `golosid`=".intval($golosid),(/*$sie==1?"_a1":*/"_a"));

    if($opt=='unic') {
        $s='';
        foreach($pp as $i=>$p) { if(0==($un=$p['unic'])) { unset($pp[$i]); continue; }
		$is=getis($un);
		$pp[$i]['ico']=$is['ico'];
		$pp[$i]['user']=$is['user'];
        }
        otprav_JSON(wu($pp));
    }

    otprav_JSON($pp);
}


















//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//        ____                                     _
//       / ___|___  _ __ ___  _ __ ___   ___ _ __ | |_ ___
//      | |   / _ \| '_ ` _ \| '_ ` _ \ / _ \ '_ \| __/ __|
//      | |__| (_) | | | | | | | | | | |  __/ | | | |_\__ \
//       \____\___/|_| |_| |_|_| |_| |_|\___|_| |_|\__|___/
//
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////


$db='dnevnik_comm';
if($a=='commentary') { // otprav_JSON(array());
    $num=RE00('num');
    $numadmin=num_admin($num); // кто админ?

    $z=ms("SELECT `Header`,`Access` FROM `dnevnik_zapisi` WHERE `num`='".intval($num)."'".($numadmin?'':" AND `Access`='all'")." LIMIT 1","_1");
    if(empty($z)) otprav_JSON(array());

    $start=max(RE00('start'),0);
    $limit=RE00('limit'); $limit=($limit?minmax($limit,1,10000):10);

    $WHERE="WHERE ".($num?"`DateID`=".intval($num):"1=1");
    if(RE('root')) $WHERE.=" AND `Parent`=0";
    if(($timefrom=RE00('timefrom')) || ($timefrom=RE00('fromTime'))) $WHERE.=" AND `Time`>=".$timefrom;
    if(($timeto=RE00('timeto')) || ($timeto=RE00('toTime'))) $WHERE.=" AND `Time`<".$timeto;

    $pers="id,unic,Name,Text,Parent,Time,whois,rul,golos_plu,golos_min"; // ,group
    if($numadmin) $pers.=",IPN,BRO,Mail,scr";
    else $WHERE.=" AND `scr`!='1'"; // не показывать скрытые
    if(!($opt=RE('opt'))) $opt=$pers;
    $opt=explode(',',$opt); $pe=explode(',',$pers);
    foreach($opt as $i=>$l) { if(!in_array($l,$pe)) unset($opt[$i]); }
    $e=implode(',',$opt);

    $pp=ms("SELECT ".$e." FROM `dnevnik_comm` ".$WHERE." ORDER BY `Time` DESC LIMIT ".intval($start).",".intval($limit),"_a");
    if(!empty($GLOBALS['msqe'])) idie($GLOBALS['msqe']);

    $opt=(($opt=RE('opt'))?explode(',',$opt):array());

    $trans=array('Name','Text','whois'); foreach($pp as $n=>$p) {
	if(!$numadmin) $p=comm_admin_strip($p);
	$p=comm_admin_clean($p);
	$i='Header'; if(in_array($i,$opt)) $p[$i]=$z[$i];
	$i='Access'; if(in_array($i,$opt)) $p[$i]=$z[$i];
	$pp[$n]=$p;
    }

    otprav_JSON(wu($pp));
}


//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//                    _         _   _      _
//                   / \   _ __| |_(_) ___| | ___
//                  / _ \ | '__| __| |/ __| |/ _ \
//                 / ___ \| |  | |_| | (__| |  __/
//                /_/   \_\_|   \__|_|\___|_|\___|
//
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

$db='dnevnik_zapisi';
if($a=='article') {

    $WW=array();
    $SS=array();

    if($num=RE00('num')) {
	$WW[]="`num`=".intval($num);
    } else {
        $start=max(RE00('start'),0);
        $limit=RE00('limit'); $limit=($limit?minmax($limit,1,10000):10);

        $SS[]="ORDER BY `DateDatetime`";
        $SS[]="DESC";
        $SS[]="LIMIT ".intval($start).",".intval($limit);

        if(($timefrom=RE00('timefrom')) || ($timefrom=RE00('fromTime'))) $WW[]="`DateDatetime`>=".$timefrom;
        if(($timeto=RE00('timeto')) || ($timeto=RE00('toTime'))) $WW[]="`DateDatetime`<".$timeto;
    }

    if(RE00('visible')!=1 || !ADMA(1)) $WW[]="`visible`=1"; // очень неохотно показывать скрытые

/*
Date: varchar(128) NOT NULL
Header: varchar(255) NOT NULL
Body: mediumtext NOT NULL
Text: 
Access: enum('all','podzamok','admin') NOT NULL default 'admin'
DateUpdate: int(10) unsigned NOT NULL default '0'
view_counter: int(10) unsigned NOT NULL default '0'
num: int(10) unsigned NOT NULL auto_increment
DateDatetime: int(11) NOT NULL default '0'
DateDate: int(11) NOT NULL default '0'
opt: text NOT NULL
acn: int(10) unsigned NOT NULL default '0'
visible: enum('1','0') NOT NULL default '1'
        'include'=>array('','s',40),
        'Comment_foto_logo'=>array(chr(169)." ".chr(171)."{name}: ".$httpsite.chr(187),'s',64),
        'Comment_foto_x'=>array('600','s',6),
        'Comment_foto_q'=>array('75','s',6),
        'Comment_media'=>array('all','all no my'),
        'Comment_view'=>array('on','on off rul load timeload'),
        'Comment_write'=>array('on','on off friends-only login-only timeoff login-only-timeoff'),
        'Comment_screen'=>array('open','open screen friends-open'),
        'Comment_tree'=>array('1','0 1'),
        'autoformat'=>array('p','no p pd'),
        'template'=>array('blog','s',32),
        'autokaw'=>array('auto','auto no')
*/

    $pers="num,Date,Header,Access,DateUpdate,view_counter,DateDatetime,DateDate,opt,acn";
    if(ADMA(1)) $pers.=",visible,Body";
    else $WW[]="`Access`='all'"; // не показывать скрытые
    $pe=explode(',',$pers); $pe=array_combine($pe,$pe);

    $orig_opt=c(RE('opt')); $orig_opt=(empty($orig_opt)?array('num','Date','Header','Text','DateDatetime'):@explode(',',$orig_opt));
    $orig_opt=array_combine($orig_opt,$orig_opt);

    $ZAP=$orig_opt;

    if(sizeof($orig_opt)) {
	$e=$orig_opt;
        foreach($e as $i) { if(!isset($pe[$i])) unset($e[$i]); else unset($ZAP[$i]); } // убрать лишние
	if(isset($orig_opt['Text']) || isset($orig_opt['plainText'])) { $e['Body']=$e['opt']=1; include_once $include_sys."_onetext.php"; } // понадобится для Text
        if(isset($orig_opt['link'])) $e['Date']=1; // это нам понадобится для link
        if(isset($orig_opt['tags'])) $e['num']=1; // это нам понадобится для tags
    } else $e=$pe;

    $opt=$orig_opt;
    if(sizeof($opt)) {
        foreach($opt as $i=>$l) { if(!isset($GLOBALS['zopt_a'][$i])) unset($opt[$i]); else unset($ZAP[$i]); } // выбрать названия опций
	if(!empty($opt)) $e['opt']=1; // добавить опцию opt в запрос, если запрашивались опции
    }


    // проверка на ошибку запроса
    if(sizeof($ZAP)) {
	$a=array('link','Text','plainText','tags'); foreach($a as $i) { if(isset($ZAP[$i])) unset($ZAP[$i]); }
        if(sizeof($ZAP)) {
	    if(isset($ZAP['Body']) && !ADMA(1)) jdie("Only admin can load the `Body`. Use `Text` or `plainText` instead"); // unset($orig_opt['Body']); // посторонним Body не показываем
	    jdie("Unknown options: `".implode('`,`',array_keys($ZAP))."`");
	}
    }

    if(empty($e)) jdie("Empty query");
    $pp=ms("SELECT ".(implode(',',array_keys($e)))." FROM `dnevnik_zapisi` WHERE ".implode(' AND ',$WW).ANDC()." ".implode(" ",$SS),"_a");
    if(empty($pp)) otprav_JSON($pp);

    foreach($pp as $n=>$p) {
	$p=mkzopt($p);
        if(isset($orig_opt['Text'])) $p['Text']=prepare_Body($p);
        if(isset($orig_opt['plainText'])) $p['plainText']=trim(plain_Body($p),"\n ");
        if(isset($orig_opt['tags'])) $p['tags']=implode(',',gettags($p['num']));
        if(isset($orig_opt['link'])) $p['link']=getlink($p['Date']);
	foreach($p as $i=>$l) { if(!isset($orig_opt[$i])) unset($p[$i]); } // убрать всё, что не просили
	$pp[$n]=$p;
    }
    otprav_JSON(wu($pp));
}

if(empty($a)) jdie("Error request");
jdie("Unknown request `".$a."`");

/*
function otprav_JSON($p) {
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    unset($GLOBALS['MOUTPUT']);
    ob_clean();
    die(json_encode($p));
}

function num_admin($num) { // давайте выясним права на редактирование этого коммента
    if($GLOBALS['mnogouser']!=1) return $GLOBALS['admin'];
    if(($acn=ms("SELECT `acn` FROM `dnevnik_zapisi` WHERE `num`=".dddddddddde($num),"_l"))===false) return false;
    if(!($U=ms("SELECT `unic` FROM `jur` WHERE `acn`=".ffffffffffffffe($acn),"_l"))) return false;
    return is_unics($GLOBALS['unic'],$U);
}

function comm_admin_strip($p) { // наскоро вырезать секретные части
    $p['Text']=preg_replace("/\{scr\:.*?\}/s",'',$p['Text']);
    $p['Text']=preg_replace("/\{screen\:.*?\}/s",'',$p['Text']);
    return $p;
}

function comm_admin($p) { // давайте выясним права на редактирование этого коммента
    if($GLOBALS['mnogouser']!=1) return $GLOBALS['admin'];
    if(!($comm_num=$p['DateID'])) return false;
    if(($comm_acn=ms("SELECT `acn` FROM `dnevnik_zapisi` WHERE `num`=".sssssssse($comm_num),"_l"))===false) return false;
    if(!($U=ms("SELECT `unic` FROM `jur` WHERE `acn`=".ddddddddddddddddddde($comm_acn),"_l"))) return false;
    return is_unics($GLOBALS['unic'],$U);
}

function comm_admin_clean($p) { // удалить секретные поля из комментария если информация пойдет не админу блога
    if(!comm_admin($p)) $p['Mail']=$p['IPN']=$p['BRO'] = '';
    return $p;
}

*/
/*
if($a=='voteid') { AD();

    if(!empty($cp=RE('charset'))) { if($cp=='utf8') $_REQUEST=uw($_REQUEST); }
    if(empty($name=RE('name'))) jdie('empty name'); $name=substr($name,0,32);
    if(empty($user=RE0('user'))) jdie('empty user');

    $p=ms("SELECT * FROM `golosovanie_golosa` WHERE `golosid`=".e($golosid)." AND `unic`=".e($user)." LIMIT 1","_1");


}
*/

function ifUploadFotos($text) {
    if(count($_FILES)<=0) return $text;

    $imgs=array();
    require_once $GLOBALS['include_sys']."_fotolib.php";
    $R=array();
    $mboxdir=$GLOBALS['filehost']."user/".$GLOBALS['unic']."/mbox/";
    $mboxweb=$GLOBALS['httphost']."user/".$GLOBALS['unic']."/mbox/";
    $g=glob($mboxdir."temp*"); foreach($g as $l) unlink($l); // почистить вдруг чо

    foreach($_FILES as $n=>$FILE) if(is_uploaded_file($FILE["tmp_name"])) {
        $foto_replace_resize=1;
        list($W,$H,$itype)=getimagesize($FILE["tmp_name"]);
        if(($img=openimg($FILE["tmp_name"],$itype))===false) idie(LL('Comments:foto:musor',implode(', ',$GLOBALS['foto_rash']))); // шо за мусор?
	$imgs=obrajpeg_sam($img,900,$W,$H,$itype,'');
        testdir($mboxdir); test_file($mboxdir,"Error dirname");
        $temp="temp".rand(0,100000).".tmp";
	$itype=IMAGETYPE_WEBP; // нахуй всё в WEBP if(in_array($itype,array(IMAGETYPE_GIF,IMAGETYPE_PNG,IMAGETYPE_JPEG))) 
        closeimg($imgs,$mboxdir.$temp,$itype,82); test_file($mboxdir.$temp,"Error create Image");
	imagedestroy($img); imagedestroy($imgs); // вот только теперь можно
        $to=md5_file($mboxdir.$temp).".".$GLOBALS['foto_rash'][$itype];
        rename($mboxdir.$temp,$mboxdir.$to); test_file($mboxdir.$to,"Can't rename from [".pr_Path($mboxdir.$temp)."]");
        $R[]=$mboxweb.$to;
    } // else idie("File upload error: ".nl2br(h(print_r($_FILES,1))));

    return str_replace('[IMG]',"\n".implode("\n\n",$R)."\n",$text);
}

?>