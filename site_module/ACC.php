<?php

/*  */

function CloudFlare_reg($acc) { // прописать домен на CloudFlare
    if(!isset($GLOBALS['dyndns_clowdflare_mail']) || !isset($GLOBALS['dyndns_clowdflare_token'])) return false;
    $site=explode('/',$GLOBALS['httpsite'].'/',4); $site=$site[2]; if(empty($site)) idie("Error domain `".h($GLOBALS['httpsite'].'/')."`");
    include_once $GLOBALS['include_sys']."protocol/CloudFlare.php";

//    $otv1="record already exists";

    return // "salert(\"".njsn(nl2br(h(
	cloud_add_items($acc,$site,'CNAME',120,'true')
	."<br>"
	.cloud_add_items('www.'.$acc,$site,'CNAME',120,'true')
    // )))."\",3000);"
    ;
}

function ACC_ajax(){ $a=RE('a');

	if($a=='editdomain') { AD(); $n=RE0('n'); if(!$n) idie('Error root domain');
	    if(false===($d=ms("SELECT `domain` FROM `jur` WHERE `acn`=".intval($n),"_l",0))) idie("Not found");
	    idie("<p><input onchange=\"majax('module.php',{mod:'ACC',a:'editdomain_',n:".$n.",d:this.value})\" type=text size=20 style='font-size:24px' value=\"".h($d)."\">"
		."<p><input class=br type=submit value='OK'>","Edit domain for n=".$n);
	}

	if($a=='editdomain_') { AD(); $n=RE0('n'); if(!$n) idie('Error root domain');
	    $d=c(RE('d')); if($d===false || preg_match("/[^a-z0-9\-\_\.]+/",$d) || !strstr($d,'.')) idie("Error domain: `".h($d)."`");
	    msq("UPDATE `jur` SET `domain`='".e($d)."' WHERE `acn`=".intval($n)." LIMIT 1");
	    if($GLOBALS['msqe']!='') idie($GLOBALS['msqe'],"MySQL error");
	    idie('DONE');
	}

	if($a!=false) idie("Wrong a=".h($a));

	if_iphash(RE('iphash'));
	$acc=RE('acc');
	if(preg_match("/[^a-z0-9\_\-]+/s",$l)) idie("В вашем логине `".h($acc)."` постороние символы!");
  if(($acn=ms("SELECT `acn` FROM `jur` WHERE `acc`='".e($acc)."' LIMIT 1","_l",0))!==false) {
    return "idie(\"Аккаунт уже существует."
.njsn($GLOBALS['IS']['login']==$acc?"<p>Написать <a href=\"javascript:majax('editor.php',{acn:".$acn.",a:'newform',hid:hid})\">новую заметку</a>":'')
."\");"
    .CloudFlare_reg($acc);
    }

  if(($u=ms("SELECT `id` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($acc)."'","_l",0))===false) idie("User `".h($acc)."` not found!");

    $o='';

    $max=1*ms("SELECT MAX(`acn`) AS `acn` FROM `jur`",'_l')+1; // найти наибольшее

  msq_add('jur',array('acc'=>e($acc),'unic'=>$u,'acn'=>$max));
  $acn=ms("SELECT `acn` FROM `jur` WHERE `acc`='".e($acc)."'","_l",0);

    $o.=CloudFlare_reg($acc);
    return $o."
idie(\"User: `".h($acc)."` unic=$u <font color=green>CREATED</font> with id=$acn"
."<p>Написать <a href=\"javascript:majax('editor.php',{acn:".$acn.",a:'newform',hid:hid})\">новую заметку</a>"
."\");";

}

function ACC($e) { global $admin,$acc,$acn,$ADM,$IS,$httphost;
$conf=array_merge(array(
'mode'=>"admin",
'sort'=>'',
'day'=>30,
'all'=>0,
'visible'=>1,
'maketwo'=>0,
'template'=>"<br><a href='{acc_link}'>{acc}</a> (<a href='{acc_link}contents'>{count}</a>)"
),parse_e_conf($e));

$conf['template']=str_replace('\n',"\n",$conf['template']);


if($conf['mode']=='accounts') { // Аккаунты

	AD();

	$conf['template']="<tr>"
."<td>"
    ."<i class=e_remove></i> &nbsp; &nbsp; "
    ."<i class=e_link alt='Edit Domain' onclick=\"majax('module.php',{mod:'ACC',a:'editdomain',n:{#acn}})\"></i> &nbsp; &nbsp; "
."</td>"
."<td class=br>{#tim}</td>"
."<td>{#acn}</td>"
."<td><a href='{acc_link}'>{#acc}</a></td>"
."<td><b>{#domain}<b></td>"
."<td><b>{#count}<b></td>"
."</tr>";

	$pp=ms("SELECT * FROM `jur` ".($conf['sort']!=''?" ORDER BY `".e($conf['sort'])."`":''),"_a",0);

// dier($pp);

	$o=''; foreach($pp as $n=>$p) {

	    /* чистим наросшее откуда-то говно
	    if(($l=ms("SELECT COUNT(*) FROM `jur` WHERE `acn`=".dddddddddddde($p['acn'])." AND `acc`='".e($p['acc'])."'","_l",0))!=1) {
		msq("DELETE FROM `jur` WHERE `acn`=".intval($p['acn'])." AND `acc`='".e($p['acc'])."' LIMIT 1");
		dier($p,"Count: $l DELETED 1, pls upload page ",$GLOBALS['msqe']);
	    }
	    */

	    $o.=mper($conf['template'],array_merge($p,array(
		'acc_link'=>acc_link($p['acc']),
		'tim'=>($p['time']=='0000-00-00 00:00:00'? '' : $p['time']),
		'count'=>(($l=ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `acn`=".intval($p['acn']),'_l'))?$l:'')
	    )));
	}
	return "<table border=0>".$o."</table>";
}

if($conf['mode']=='list') { // AND z.DateDate>".(time()-$conf['day']*86400) //,z.COUNT(*) as `count`
	$pp=ms("SELECT a.`acc`,a.`acn`,a.`domain`,(SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `acn`=a.`acn`"
.($conf['day']?(
	substr($conf['day'],0,1)=='-'?" AND `DateDate`<=".(time()-ltrim($conf['day'],'-')*86400)
	:" AND `DateDate`>".(time()-$conf['day']*86400)
):'')
.($conf['visible']?" AND `Access`='all'":'').") as count FROM `jur` as a".($conf['sort']!=''?" ORDER BY `".e(ltrim($conf['sort'],'-'))."`"
.(substr($conf['sort'],0,1)=='-'?" DESC":'')
:''),"_a");

// dier($pp);

	$x=strstr($conf['template'],'{count}');
	$o=''; foreach($pp as $p) {
		/*$count=		($x?ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `acn`=".intval($p['acn'])
		.($conf['day']?" AND `DateDate`>".(time()-$conf['day']*86400):'')
		.($conf['noday']?" AND `DateDate`<=".(time()-$conf['noday']*86400):'')
		.($conf['visible']?" AND `Access`='all'":'')
		,"_l"):0);
		*/
		if($p['count'] || $conf['all']) $o.=mper($conf['template'],array('acc'=>h($p['acc']),'acc_link'=>acc_link($p['acc']),'count'=>$p['count']));
	}
	return $o;
}

if($conf['mode']=='count') { return ms("SELECT COUNT(*) FROM `jur`","_l"); }

// if($conf['mode']=='admin' && !$admin) { if(empty($acc)) return "Admin only!"; /* redirect($httphost.'acc'); */ }

// return "admin: ".intval($admin);

	// админ зашел создать аккаунт:
//	if($admin&&!empty($acc)) return "<span class='ll' onclick=\"if(confirm('create?'))majax('module.php',{mod:'ACC',acc:'$acc',iphash:'".iphash()."'});\">Create '".h($acc)."'?</span>";

	// логин зашел создать аккаунт:
	if(empty($IS['login'])) return "У вас не заполнено поле `login` в <span class='ll' onclick=\"majax('login.php',{a:'getinfo'})\">карточке</span>";
	if(empty($IS['password'])&&empty($IS['openid'])) return "У вас не заполнено поле `password` в <a href=\"javascript:majax('login.php',{action:'openid_form'})\">карточке</a>. Как вы планируете вернуть свой аккаунт, когда авторизация браузера слетит?";
	$l=h($IS['login']);

	if(preg_match("/[^a-z0-9\_\-]+/s",$l)) return "В вашем логине `$l` постороние символы (допустимы строчные: a-z0-9_-). Сменить логин возможности нет. Только разлогиниться и завести новый аккаунт ;)";

	if($acc!='') return "Этот раздел работает по адресу: <a href='".$GLOBALS['httphost']."acc'>".$GLOBALS['httphost']."acc</a>";
//	    return "Хотите завести себе аккаунт <b>$l</b>? <input type='button' value='Create ".$l."' onclick=\"if(confirm('create?'))majax('module.php',{mod:'ACC',acc:'$l',iphash:'".iphash()."'});\">";
// то можно это сделать, зайдя по адресу <a href='".acc_link($l)."acc'>".acc_link($l)."acc</a>";

// return 'D';

	if(0!=ms("SELECT COUNT(*) FROM `jur` WHERE `acc`='".e($acc)."'","_l",0))
	    return "Аккаунт `$acc` уже создан, посмотрите информацию о нем: <a href='".h(acc_link($l))."acctest'>".h(acc_link($l))."acctest</a>";

	if($conf['maketwo']==0 && 0!=ms("SELECT COUNT(*) FROM `jur` WHERE `acc`='".e($l)."'","_l",0)) {
	    return CloudFlare_reg($l)."<p>Account <b>$l</b> already created, see more: <a href='".h(acc_link($l))."acctest'>".h(acc_link($l))."acctest</a>";
	}




//	.($acc!=''?"<p>Согласно настройкам именно этого сервера, здесь запрещено создавать множественные аккаунты для одного пользователя."
//	."<p>Если хотите завести себе аккаунт <b>$l</b>, то поменять логин в личной карточке сейчас возможности нет - можно только разлогиниться и завести новую карточку.":'');

/*
	if($conf['maketwo']==0 && $l!=$acc) return "В вашей <span class=ll onclick=\"majax('login.php',{a:getinfo})\">карточке</span> прописан логин <b>$l</b>, а вы почему-то пытаетесь создать аккаунт <b>$acc</b>. Согласно настройкам именно этого сервера, здесь запрещено создавать множественные аккаунты для одного пользователя."
."<p>Вы разберитесь, чего вы хотите:"
."<p>1. Если хотите завести себе аккаунт <b>$acc</b>, то можно это сделать, зайдя по адресу <a href='".acc_link($acc)."acc'>".acc_link($acc)."acc</a>"
."<p>2. Если хотите завести себе аккаунт <b>$l</b>, то поменять логин в личной карточке сейчас возможности нет - можно только разлогиниться и завести новую карточку ;)";
*/

	return "Да, здесь можно создать журнал для своего аккаунта <b>$l</b>:<p>
<center><input type='button' value='Создать журнал ".$l."' onclick=\"if(confirm('create?'))majax('module.php',{mod:'ACC',acc:'$l',iphash:'".iphash()."'});\"></center>";
}

?>