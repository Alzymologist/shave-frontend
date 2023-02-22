<?php // почта пользователей

include "../config.php"; $ajax=1; include $include_sys."_autorize.php";

$a=RE('a'); ADH();

/*
  `id` int(10) unsigned NOT NULL auto_increment,
  `answerid` int(10) unsigned NOT NULL default '0' COMMENT 'ответ на',
  `unicfrom` int(10) unsigned NOT NULL default '0' COMMENT 'id отправител€',
  `unicto` int(10) unsigned NOT NULL default '0' COMMENT 'id получател€',
  `timecreate` int(11) unsigned NOT NULL default '0' COMMENT '¬рем€ создани€',
  `timeview` int(11) unsigned NOT NULL default '0' COMMENT '¬рем€ первого прочтени€',
  `timeread` int(11) unsigned NOT NULL default '0' COMMENT '¬рем€ подтверждени€ прочтени€',
  `text` text NOT NULL COMMENT '“екст письма',
  `IPN` int(10) unsigned NOT NULL default '0' COMMENT 'IP в цифре',
  `BRO` varchar(1024) NOT NULL COMMENT 'Ѕраузер все-таки запишем?',
  `whois` varchar(128) NOT NULL  COMMENT 'ќпредел€лка страны',
  PRIMARY KEY  (`id`),
  KEY `new` (`timeread`,`unicto`)
*/


//========================================================================================================================
if($a=='newform') { // окно письма
    ifloginl();
    $u=RE0('unic'); if(!$u) idie(LL('error:unic0'),'Error: unic=0');
    $is=getis($u,'#');
    if(!($tmpl=RE('tmpl'))) $tmpl="new"; $tmpl=preg_replace("/[^0-9a-z\-\_]+/si",'',$tmpl);
    $answerid=RE0('answerid');
    $s=mpers(get_sys_tmp("mailbox_".$tmpl.".htm"),array('hid'=>'newmail_'.$u,'unicto'=>$u,'to'=>$is['user'],'text'=>'','mytext'=>'','answerid'=>$answerid,'id'=>0));
    otprav($s);
}


if($a=='write') { // окно письма
    ifloginl();
    $u=RE0('unic'); if(!$u) idie(LL('error:unic0'),'Error: unic=0');
    $is=getis($u,'#');
    $answerid=RE0('answerid');
    $s=mpers(get_sys_tmp("mailbox_write.htm"),array('hid'=>'newmail_'.$u,'unicto'=>$u,'to'=>$is['user'],'text'=>'','mytext'=>'','answerid'=>$answerid,'id'=>0));
    otprav($s);
}


if($a=='editmail') { // окно письма
    ifloginl(); // доступно только авторизованным
    $id=RE0('id'); if(!$id) idie(LL('error:id0'),'Error: id=0');
    $r=ms("SELECT * FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND `unicfrom`='".intval($unic)."'","_1",0);
    if($r==false) otprav('');
    $u=$r['unicto']; $is=getis($u,'#');
    if(!($tmpl=RE('tmpl'))) $tmpl="new"; $tmpl=preg_replace("/[^0-9a-z\-\_]+/si",'',$tmpl);
    $s=mpers(get_sys_tmp("mailbox_".$tmpl.".htm"),array('hid'=>'editmail_'.$u,'unicto'=>$u,'to'=>$is['user'],'text'=>'','mytext'=>$r['text'],'answerid'=>$r['answerid'],'id'=>$id));
    otprav($s);
}

if($a=='new') { // письмо готово
$js='';
while(1) {
    ifloginl(); // доступно только авторизованным
    $id=RE0('id');
    $text=str_replace("\r",'',trim(RE('text'),"\r\n\t "));
    if($text=='') { if($id) { $a='del_id'; break; } else otprav("salert('".LL('wherethetext')."',1000);"); }

    $text=ifUploadFotos($text); // добавить фотки, если были загружены

    $u=RE0('unicto');
    if(1!=ms("SELECT COUNT(*) FROM ".$db_unic." WHERE `id`=".intval($u),"_l",0)) idie("Unic ".$u." not found");
    if(!$unic) idie(LL('error:unic0'),'Error: unic=0');
    include_once $include_sys."geoip.php"; $w=geoip($IP,$IPN); $whois=$w['country'].' '.$w['city'];

    if(!$id) {
      $ara=arae(array(
    	'answerid'=>RE0('answerid'),
	'unicfrom'=>$unic,
	'unicto'=>$u,
	'timecreate'=>time(),
	'timeview'=>0,
	'timeread'=>0,
	'text'=>$text,
	'IPN'=>$IPN,
	'BRO'=>$BRO,
	'whois'=>$whois
      ));
      if(!ms("SELECT COUNT(*) FROM ".$db_mailbox." WHERE `unicfrom`=".intval($unic)." AND `unicto`=".intval($u)." AND `text`='".e($text)."'","_l",0)) msq_add($db_mailbox,$ara);
      $id=intval(msq_id());
    } else {
	msq_update($db_mailbox,array('text'=>e($text)),"WHERE `id`=".intval($id)." AND `unicfrom`=".intval($unic)." AND `unicto`=".intval($u));
    }

$js="if(typeof(onMailSend)=='function') onMailSend(".$id."); else salert('sent',500);";

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

/*
// ================ отправить почтой =============================
$js=''; if($id) { // если это ответ (не в корне коммент), остальное проверит сама процедура
<------>include_once $include_sys."_sendmail.php";
<------>if(0!==($sys=mail_answer($id,$ara))) $js.="salert('mail send: ".njsn($sys['name_parent'])."',1000);";
}
// ===============================================================
*/

otprav("clean('".RE('hid')."');".$js);
}}


//========================================================================================================================

if($a=='del_id') { // удалить
    $id=RE0('id');
    $text=ms("SELECT `text` FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")",'_l',0);
    // удалить все фотки сообщени€, если были
    $mboxweb=$GLOBALS['httphost']."user/".intval($unic)."/mbox/";
    $mboxdir=$GLOBALS['filehost']."user/".intval($unic)."/mbox/";
    if(preg_match_all("/".preg_quote($mboxweb,'/')."[^\s]+/s",$text,$m)) { foreach($m[0] as $l) unlink(str_replace($mboxweb,$mboxdir,$l)); }
    // теперь удалить само сообщение
    ms("DELETE FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")",'_l',0);
    $hid=RE('hid'); $js=($hid?"clean('".$hid."')":'');
    otprav("salert('deleted',250); clean('mail".$id."');
if(idd('mail_tread')) setTimeout(function(){if(idd('mailchat') && vzyal('mailchat')=='') {
    var uc=idd('mail_tread').getAttribute('uc'); clean('mail_tread'); clean('with'+uc);
}
},500);
".$js);
}

function mail_one($p) {
        if(!function_exists("AddBB")) include_once $GLOBALS['include_sys']."_obracom.php";
	$text=nl2br(h($p['text']));
	$text=AddBB($text);
	$text="\n$text\n";
	$text=hyperlink($text);
	$text=c($text);
	$text=preg_replace("/\{(\_.*?\_)\}/s","&#123;$1&#125;",$text); // удалить подстыковки нахуй из пользовательского текста!
	$text=preg_replace("/&amp;(#[\d]+;)/si","&$1",$text); // отображать спецсимволы и национальные кодировки
	$text=str_replace('{','&#123;',$text); // } чтоб модули не срабатывали
	// $p['text']=search_podsveti($text);
	return array(
	    "<div id='mail".intval($p['id'])."' inbound='".($p['dr']=='fr'?1:0)."' timeread='".($p['timeread']?1:0)."' class='chat0".$p['dr']."' onclick='chat(this)'>",

	    "<div class='chat".$p['dr']."'>"
	    ."<div style='font-size:10px'>".date('Y-m-d H:i',$p['timecreate'])
	    .($p['dr']=='fr'?" &nbsp; <span style='font-size:13px'>".h($p['user'])."</span>":'')
	    ."</div>"
	    ."<br>".$text."</div>",

	    "</div>"
	);
}

if($a=='mail_one') { // письма
    $id=RE0('id'); if(!$id) idie($a." error #0");
    $r=ms("SELECT * FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")","_1",0);
    $uc=($r['unicto']==$unic?$r['unicfrom']:$r['unicto']);
    $HI=getis($uc,'#'); $user=$HI['user'];
    $MO=mail_one(array_merge($r,array('user'=>$user,'dr'=>($r['unicto']==$unic?'fr':'to'))));
    otprav("
var MO=[\"".njs($MO[0])."\",\"".njs($MO[1])."\",\"".njs($MO[2])."\"];
var e=idd('mailchat');
if(e) { e=e.querySelector('#mail".$id."');
    if(e) zabil(e,MO[1]);
    else zabil('mailchat',MO.join('')+vzyal('mailchat'));
} else {
    LOADS([www_css+'chat.css?'+Math.random(),www_css+'chat.js?'+Math.random()],function(){
	ohelpc('mail_tread',\"ћо€ переписка с ".h($user)."\",MO.join(''));
	idd('mail_tread').setAttribute('uc','".$uc."');
	mail_button_new();
    });
}");
}

if($a=='mail_tread') { // письма
    $uc=RE0('u'); if(!$uc) idie($a." error #0");
    $r=ms("SELECT * FROM ".$db_mailbox." WHERE (`unicfrom`=".intval($uc)." AND `unicto`=".intval($unic).") OR (`unicto`=".intval($uc)." AND `unicfrom`=".intval($unic).")
ORDER BY `id` DESC","_a");
    $HI=getis($uc,'#'); $user=$HI['user'];
    $o=''; foreach($r as $p) $o.=implode('',mail_one(array_merge($p,array('user'=>$user,'dr'=>($p['unicto']==$unic?'fr':'to')))));
    $o="<div id='mailchat' class='chat' style='--chat-color:#F0F0EA;'>".$o."</div>";
    otprav("
LOADS([www_css+'chat.css?'+Math.random(),www_css+'chat.js?'+Math.random()],function(){
    ohelpc('mail_tread',\"ћо€ переписка с ".h($user)."\",\"".njs($o)."\");
    idd('mail_tread').setAttribute('uc','".$uc."');
    mail_button_new();
});
");
}
// ================================

if($a=='mail_list') { // письма

    $pp=ms("SELECT DISTINCT `unicfrom` FROM ".$db_mailbox." WHERE `unicto`=".intval($unic),"_a1");
    $pp2=ms("SELECT DISTINCT `unicto` FROM ".$db_mailbox." WHERE `unicfrom`=".intval($unic),"_a1");
    $pp=array_unique(array_merge($pp,$pp2)); unset($pp2);

    $r=array();
    foreach($pp as $l) {
	$r[]=ms("SELECT * FROM ".$db_mailbox." WHERE
(`unicfrom`=".intval($l)." AND `unicto`=".intval($unic).") OR (`unicto`=".intval($l)." AND `unicfrom`=".intval($unic).")
ORDER BY `id` DESC LIMIT 1","_1");
    }
    $R=array();
    $U=array();
    foreach($r as $p) {	$t=$p['timecreate']; while(isset($R[$t])) $t++; $R[$t]=$p; }
    unset($r);
    krsort($R);

    $U[$unic]=getis($unic,'#'); // свой
    $o='';

//    $icoR="<span style='font-size:26px'>&#8594;</span>"; // $icoL="<span style='font-size:16px'>&#8592;</span>";

    foreach($R as $p) {
	$my=($p['unicfrom']==$unic?1:0);
	$uc=intval($my?$p['unicto']:$p['unicfrom']);
	if(!isset($U[$uc])) $U[$uc]=getis( ($my?$p['unicto']:$p['unicfrom']) ,'#');

        $name=$U[$uc]['imgicourl'];

	$text=h($p['text']);
	$text=str_replace("\n"," ",$text);
	$text=substr($text,0,100);

	$name="<div onclick='kus(".$uc.")' class='chat_name'>".$name."</div>";
	$img=$U[$uc]['img'];
	if($img!='') $img="<img class='chat_img' src='".h($img)."'>";
	$img="<div class='chat_blockimg'>".$img."</div>";

	$date=date('Y-m-d H:i',$p['timecreate']);
	$date="<span style='font-size:7px'>".date('Y-m-d H:i',$p['timecreate'])."</span>";

	$o.="<div id='with".$uc."'>"
	    ."<div class='in chat_blockname'>".$img.$name."</div>" // ($my?$icoR." ".$name:
	    ."<div onclick='mail_tread(".$uc.")' class='in chat_blocktext'>".$date."<br>".$text."</div>"
	    ."</div>";
    }

    otprav("
LOADS([www_css+'chat.css?'+Math.random(),www_css+'chat.js?'+Math.random()],function(){
mail_tread=function(u){ majax('mailbox.php',{a:'mail_tread',u:u}); };
ohelpc('mail_list','ћои переписки',\"".njs($o)."\")
})");
}



if($a=='readed') { // прочитано
    $id=RE0('id'); msq_update($db_mailbox,array('timeread'=>time()),"WHERE `id`=".intval($id)." AND `unicto`=".intval($unic) );
    otprav("var i='mmsg".$id."'; clean(i); doclass(i,function(e,l){clean(e);});
setTimeout(\"var e=idd('newmail').getElementsByTagName('BLOCKQUOTE'); if(!e.length) clean('newmail');\",100);");
}



//========================================================================================================================

if($a=='mail') { // письма

    $ty='';

    $LIM=20;
    $nn=RE0('nn')|0; if($nn<0) $nn=0;
    $limit=RE0('limit')|($LIM+1); if($limit<0 ||  $limit>1000) $limit=0;
    $mode=RE('mode'); if($mode=='')$mode='new';
    $box=RE('box'); if($box=='')$box='in';

//    $uf=($box=='in'?'unicfrom':'unicto');

    $pp=ms("SELECT `answerid`,`id`,`timecreate`,`timeread`,`unicfrom`,`unicto`,`text`,`whois`,`IPN`,`BRO` FROM ".$db_mailbox." WHERE `"
.($box!='in'?'unicfrom':'unicto')."`='".e($unic)."'"
.($mode=='new'?" AND `timeread`='0'":'')
." ORDER BY `timecreate` DESC"
." LIMIT ".e($nn).",".e($limit)
,"_a",0); if(RE0('showbox')!=1 && !sizeof($pp)) otprav('');

$prevnext="<table width=100% border=0><tr><td width=50% align=left class=r>{prev}</td><td width=50% align=right class=r>{next}</td></tr></table>";

// idie(nl2br(h('###'.system("tail -50 /var/log/nginx/error.log"))));

    $s=''; foreach($pp as $n=>$p) { if($n>=$LIM) break; $s.=oneletter($p,$n); }

// idie('###44:'.$s);

    $pn=mpers($prevnext,array(
'next'=>(sizeof($pp)>$LIM?"<div class='r l' onclick=\"majax('mailbox.php',{a:'mail',nn:".($nn+$LIM).",limit:$limit,mode:'$mode'".$ty."})\">следующие&nbsp;-&gt;</div>":''),
'prev'=>($nn?"<div class='r l' onclick=\"majax('mailbox.php',{a:'mail',nn:".($nn-$LIM<0?0:$nn-$LIM).",limit:$limit,mode:'$mode'".$ty."})\">&lt;-&nbsp;предыдущие</div>":''),
));

$s=$pn.$s.$pn;

    otprav("
LOADS([www_css+'chat.css?'+Math.random()],function(){
ohelpc('newmail',\"".njsn(
"mailbox: <span title='change: INBOX / OUTBOX' class='l' onclick=\"majax('mailbox.php',{a:'mail',nn:0,limit:$limit,mode:'all',box:'".($box!='in'?'in':'out')."'".$ty."})\">".($box!='in'?'OUTBOX':'INBOX')."</span>"
." / message: "
."<span title='change: new message / all message' class='l' onclick=\"majax('mailbox.php',{a:'mail',nn:$nn,limit:$limit,box:'$box',mode:'".($mode!='new'?'new':'all')."'".$ty."})\">".($mode=='new'?'new':'all')."</span>"
)."\",\"".njsn($s)."\");
})");
}



if($a=='delete') { // удалить
    idie('deprecated');
    $id=RE0('id'); ms("DELETE FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND `unicto`=".intval($unic),'_l',0);
    otprav("salert('deleted',250); clean('mmsg".$id."');setTimeout(\"var e=idd('newmail').getElementsByTagName('BLOCKQUOTE'); if(!e.length) clean('newmail');\",100);");
}


if($a=='answer') { // ответить
    $id=RE0('id');
    if(false===($p=ms("SELECT `text`,`unicfrom` FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND `unicto`=".intval($unic),"_1",0))) otprav("salert('message not found',1000)");
    msq_update($db_mailbox,array('timeread'=>time()),"WHERE `id`=".intval($id)." AND `unicto`=".intval($unic) ); // дл€ начала - оно прочитано
    $u=$p['unicfrom'];
//    $text=preg_replace("/\n(>*)\s*"."/","\n$1> ","> ".$p['text']);
    $text=preg_replace("/\n\s*"."/"," ",h($p['text']));
//    $text=h($p['text']);
//    $text=preg_replace("/\n\s*"."/","\n<br>",h($p['text']));
    $is=getis($u,'#'); $to=$is['imgicourl'];
    $s=mpers(get_sys_tmp("mailbox_new.htm"),array('hid'=>'newmail_'.$u,'unicto'=>$u,'to'=>$to,'text'=>$text,'answerid'=>$id));
    otprav(
// "var i='mmsg".$id."'; clean(i); doclass(i,function(e,l){clean(e);});setTimeout(\"var e=idd('newmail').getElementsByTagName('BLOCKQUOTE'); if(!e.length) clean('newmail');\",100);".
$s);
}


if($a=='parent') { // верхнее
    $id=RE0('id');
    if(false===($parent=ms("SELECT `answerid` FROM ".$db_mailbox." WHERE `id`=".intval($id)." AND (`unicto`=".intval($unic)." OR `unicfrom`=".intval($unic).")","_l",0))) otprav("salert('message not found',1000)");
    if(false===($p=ms("SELECT * FROM ".$db_mailbox." WHERE `id`=".intval($parent),"_1",0))) otprav("salert('parent not found',1000)");

    // $uf=($p['unicfrom']==$id?'unicfrom':'unicto');

    $s=oneletter($p,000);

    otprav("
var i='mmsg".$id."',j='mmsg".$parent."';
    // if(idd(j)) clean(j);

    var c=idd(i).className; if(c=='') { c=i; idd(i).className=c; }
    doclass(c,function(e,l){e.style.marginLeft=(1*e.style.marginLeft.replace(/px/gi,'')+l)+'px';},50);

    var div=document.createElement('DIV');
    // div.className=c; div.id=j;
    div.innerHTML=\"".njsn($s)."\";

    var nodeIns=idd(i).closest('.chat0');
    nodeIns.parentNode.insertBefore(div,nodeIns);
");
}

function oneletter($p,$n) { global $box,$mode,$uf;

include_once $GLOBALS['include_sys']."_obracom.php";

	$id=$p['id'];

	    // $u=intval($p[$uf]);

	$my=($p['unicfrom']==$GLOBALS['unic']?1:0);

//		'unicto'=>$p['unicto'],
//		'unicfrom'=>$p['unicfrom'],

	$is=getis( ($my?$p['unicto']:$p['unicfrom']) ,'#');
	$name=$is['imgicourl'];

$text=nl2br(h($p['text']));
$text=AddBB($text);
$text="\n$text\n";
$text=hyperlink($text);
$text=c($text);
$text=preg_replace("/\{(\_.*?\_)\}/s","&#123;$1&#125;",$text); // удалить подстыковки нахуй из пользовательского текста!
$text=preg_replace("/&amp;(#[\d]+;)/si","&$1",$text); // отображать спецсимволы и национальные кодировки
$text=str_replace('{','&#123;',$text); // чтоб модули не срабатывали
// $p['text']=search_podsveti($text);

//  if($GLOBALS['admin']) dier($p,"n=$n");

	return mpers(get_sys_tmp("mailbox_letter.htm"),array(
		'my'=>$my, // ($p['unicfrom']==$GLOBALS['unic']?1:0),
		'unicto'=>$p['unicto'],
		'unicfrom'=>$p['unicfrom'],
		'n'=>(1+$n), // +$nn,
		'u'=>$id,
		'name'=>$name,
		'time'=>date("Y-m-d H:i:s",$p['timecreate']),
		'text'=>$text,
		'answerid'=>$p['answerid'],
		'img'=>$is['img'],
		'box'=>$box,'mode'=>$mode,
		'timeread0'=>$p['timeread'],
		'timeread'=>date("Y-m-d H:i",$p['timeread']),'id'=>$id,'whois'=>$p['whois'],
		'BRO'=>$p['BRO'],
		'IP'=>ipn2ip($p['IPN']))
	);
}

idie(nl2br(h(__FILE__.": unknown action `".$a."`")));

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
        imagedestroy($img);
	testdir($mboxdir); test_file($mboxdir,"Error dirname");
        $temp="temp".rand(0,100000).".tmp";
        closeimg($imgs,$mboxdir.$temp,$itype,82); test_file($mboxdir.$temp,"Error create Image");
	$to=md5_file($mboxdir.$temp).".".$GLOBALS['foto_rash'][$itype];
	rename($mboxdir.$temp,$mboxdir.$to); test_file($mboxdir.$to,"Can't rename from [".pr_Path($mboxdir.$temp)."]");
	$R[]=$mboxweb.$to;
    } // else idie("File upload error: ".nl2br(h(print_r($_FILES,1))));

    return str_replace('[IMG]',"\n".implode("\n\n",$R)."\n",$text);
}

function ifloginl() { if($GLOBALS['IS']['loginlevel']<3) idie(LL('mailbox:loginlevel3'),'Login level Error'); } // доступно только авторизованным

?>