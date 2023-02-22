<?php

function installmod_init(){ if(msq_table('jur')) return "Account Manager"; return false; }
// [Update_time]

// Эта функция - сама работа модуля. Если работа не требует этапов - вернуть 0,
// иначе вернуть номер позиции, с которой продолжить работу, рисуя на экране процесс выполнения.
// skip - с чего начинать (изначально 0), allwork - общее количество (измерено ранее), $o - то, что кидать на экран.

function tablestring($e) {
	if($e['time']=='0000-00-00 00:00:00') $e['time']='';
	$e['acclink']=acc_link($e['acc']);

	$uu=(strstr($e['unic'],',')?explode(',',$e['unic']):array($e['unic']));
	$c=array(); foreach($uu as $u) { $is=getis($u); if(empty($is['imgicourl'])) $is['imgicourl']="#".$u;
$c[]="<span alt='unic: ".$u."' class='ll' onclick=\"majax('login.php',{a:'getinfo',unic:".$u."})\">".$is['imgicourl']."</span>"; }
	$e['admins']=implode('<br>',$c);

	return mpers("<td>"
."<i class='e_kontact_journal' alt='Edit' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_edit',acnn:{acn}})\"></i>"
." &nbsp; <i class='e_remove' alt='Delete' onclick=\"if(confirm('Delete?'))if(confirm('Really delete?!')) majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_del',acnn:{acn}})\"></i>"
." &nbsp; <i class='e_jump-to' alt='Register CloudFlare' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_cloudflare',acnn:{acn}})\"></i>"
." &nbsp; &nbsp; &nbsp; "
."</td>"
."<td>{admins}</td>"
."<td><a alt='acn: {acn}' href='{acclink}'>{acclink}</a></td>"
."<td class=br>{time}</td>"
."<td>{domain}</td>",$e);
}

function CloudFlare_reg($acc) { // прописать домен на CloudFlare
    if(!isset($GLOBALS['dyndns_clowdflare_mail']) || !isset($GLOBALS['dyndns_clowdflare_token'])) return 'false';
    $site=explode('/',$GLOBALS['httpsite'].'/',4); $site=$site[2]; if(empty($site)) idie("Error domain `".h($GLOBALS['httpsite'].'/')."`");
    include_once $GLOBALS['include_sys']."protocol/CloudFlare.php";
    return cloud_add_items($acc,$site,'CNAME',120,'true')
	."<br>"
	.cloud_add_items('www.'.$acc,$site,'CNAME',120,'true');
}

function installmod_do() { global $msqe;
	AD(1); // только для первого админа

if(($a=RE('act'))!==false) {
	$acnn=RE0('acnn');

	if($a=='jur_cloudflare') {
	    if(false==($accc=ms("SELECT `acc` FROM `jur` WHERE `acn`='".e($acnn)."'","_l"))) idie('Error false');
	    $s=CloudFlare_reg($accc);
	    idie("jur_cloudflare: [$accc]<p>".nl2br(h($s)));
	}


	if($a=='jur_edit') {
	    if(false==($p=ms("SELECT * FROM `jur` WHERE `acn`=".intval($acnn),"_1"))) idie('Error false');

	    $o=mpers("<form onsubmit=\"return ajaxform(this,'module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_edit_save'})\">
<table border=0>
<tr><td>acn:</td><td><input name='old_acn' type='text' size=4 value=\"{#acn}\" disabled></td></tr>
<tr><td>acc:</td><td><input name='new_acc' type='text' size=10 value=\"{#acc}\"></td></tr>
<tr><td>unic:</td><td><input name='new_unic' type='text' size=10 value=\"{#unic}\"></td></tr>
<tr><td>domain:</td><td><input name='new_domain' type='text' size=10 value=\"{#domain}\"></td></tr>

<tr><td colspan=2><input  type='submit' value='Save'></td></tr>
</form>",$p);
	otprav("ohelpc('acm_edit','Edit Journal',\"".njsn($o)."\");");
	}

	if($a=='jur_new') {
	    $o=mpers("<form onsubmit=\"return ajaxform(this,'module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_new_save'})\">
<table border=0>
<tr><td>acc:</td><td><input name='new_acc' type='text' size=10 value=''></td></tr>
<tr><td>unic:</td><td><input name='new_unic' type='text' size=10 value='".$GLOBALS['unic']."'></td></tr>
<tr><td>domain:</td><td><input name='new_domain' type='text' size=10 value=''></td></tr>
<tr><td colspan=2><input  type='submit' value='Save'></td></tr>
</form>",$p);
	otprav("ohelpc('acm_new','New Journal',\"".njsn($o)."\");");
	}

	if($a=='jur_del') {
	    if(0!=ms("SELECT COUNT(*) FROM `jur` WHERE `acn`=".intval($acnn),'_l')) msq("DELETE FROM `jur` WHERE `acn`=".intval($acnn) );
	    otprav("clean('acm_".$acnn."')");
	}

	if($a=='jur_new_save') {

	    $ara=array(
		'unic'=>RE('new_unic'),
		'acc'=>RE('new_acc'),
		'domain'=>RE('new_domain')
	    );

	    if(empty($ara['unic']) || preg_match("/[^0-9\,]+/s",$ara['unic'])) idie("Error unic: [".h($ara['unic'])."]");

	    if(empty($ara['acc']) || $ara['acc'] != preg_replace("/[^a-z0-9\-\_]+/s",'',$ara['acc'])) idie("Error #1 acc [".h($ara['acc'])."]"); // если ошибочное имя
	    if(0!=($i=ms("SELECT COUNT(*) FROM `jur` WHERE `acc`='".e($ara['acc'])."'",'_l'))) idie("Error #1 acc present $i [".h($ara['acc'])."]");  // или такое имя журнала уже есть

	    msq_add('jur',arae($ara));
	    $id=msq_id();
	    if(!$id || false==($e=ms("SELECT * FROM `jur` WHERE `acn`=".intval($id),'_1'))) idie("Error save ".$GLOBALS['msqe']);
	    otprav("zabil('acm_table',\"".njsn("<tr class=r id='acm_".$id."'>".tablestring($e)."<tr>")."\"+vzyal('acm_table'));clean('acm_new')");
	}

	if($a=='jur_edit_save') {

	    $old_acn=RE0('old_acn');

	    $old_acn=intval($old_acn);
	    if(false==($p=ms("SELECT * FROM `jur` WHERE `acn`=".intval($old_acn),'_1'))) idie("Error #7 (acn:$old_acn)");

	    $ara=array();

	    if(($new_unic=RE('new_unic'))!=$p['unic']) { // замена владельца журнала unic
		if(preg_match("/[^0-9\,]+/s",$new_unic)) idie("Error unic: [".h($new_unic)."]");
		$ara['unic']=$new_unic;
	    }

	    if(($new_acc=RE('new_acc'))!=$p['acc']) { // замена имени журнала
		if($new_acc != preg_replace("/[^a-z0-9\-\_]+/s",'',$new_acc)) idie("Error #1 acc [".h($new_acc)."]"); // если ошибочное имя
		if(0!=($i=ms("SELECT COUNT(*) FROM `jur` WHERE `acc`='".e($new_acc)."'",'_l'))) idie("Error #1 acc present $i [".h($new_acc)."]");  // или такое имя журнала уже есть
		$ara['acc']=$new_acc;
	    }

	    if(($new_domain=RE('new_domain'))!=$p['domain']) { // замена имени журнала
		$ara['domain']=$new_domain;
	    }
	
	    msq_update('jur',arae($ara),"WHERE `acn`=".intval($old_acn) );
	    $e=array_merge($p,$ara);
	    otprav("zabil('acm_".$old_acn."',\"".njsn(tablestring($e))."\");clean('acm_edit')");
	}

	idie("Unknown: $a");
}


//	$pp=ms("SELECT DISTINCT J.`unic`,U.`login`,U.`openid`,U.`realname`,U.`admin` FROM `jur` as J INNER JOIN ".$GLOBALS['db_unic']." as U ON U.`id`=J.`unic`","_a");
//	$UNK=array(); foreach($pp as $p) { $UNK[$p['unic']]=get_ISi($p,'{realname}')['imgicourl']; }

	$pp=ms("SELECT * FROM `jur`");
	foreach($pp as $e) $o.=mpers("<tr class=r id='acm_{acn}'>".tablestring($e)."<tr>",$e);
	$o="<center>"
."<input type='button' value='NEW' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'jur_new'})\">"
."<table id='acm_table' cellspacing=0 cellpadding=2 border=0>".$o."</table></center>";
	otprav("ohelpc('acm','Account Manager',\"".njsn($o)."\");");
}

// Определяем общий объем предстоящей работы (напр. число позиций в базе для обработки).
// Если модуль одноразового запуска - вернуть 0.
// Пользуясь случаем, тут можно что-то сделать полезное - например, очистить таблицу для будущего заполнения
function installmod_allwork() { return 0; }

?>