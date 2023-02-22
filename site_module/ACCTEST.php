<?php

/*  */

function ACCTEST_ajax(){ $a=RE('a');

    if($a=='addadmin') { global $acc,$acn; ADMA();
	$un=RE0('un'); if(!$un) idie("Error unic #".$un." (\"<b>".h(RE('un'))."</b>\") Numeric only.");
	if(false==($is=getis($un))) idie("Error: unic #".$un." not found!");
	$name="<div class=ll onclick=\"majax('login.php',{a:'getinfo',unic:".$unic."})\">".$is['imgicourl']."</div>";
	if($is['loginlevel']<2) idie("Пользователь ".$name." недостаточно заполнил свой профиль");
	if(false===($unics=ms("SELECT `unic` FROM `jur` WHERE `acc`='".e($acc)."'",'_l',0))) idie("Вот уж совcем странная ошибка");
	if(is_unics($un,$unics)) idieok("Already admin");
	msq_update('jur',array('unic'=>$unics.",".$un),"WHERE `acn`=".intval($acn) );
	idieok("Congratulations!<br>Unic #".$un." now admin in ".h($acc)."!");
    }

    if($a=='deladmin') { global $acc,$acn; ADMA();
	$un=RE0('un'); if(!$un) idie("Error unic #".$un." (\"<b>".h(RE('un'))."</b>\") Numeric only.");
	if(false===($unics=ms("SELECT `unic` FROM `jur` WHERE `acc`='".e($acc)."'",'_l',0))) idie("Вот уж совcем странная ошибка");
	if(!is_unics($un,$unics)) idie("Этот человек уже не админ");
	$a=(strstr($unics,',')?explode(',',$unics):array($unics));
	unset($a[array_search($un,$a)]);
	$unics=implode(',',$a);
	msq_update('jur',array('unic'=>$unics),"WHERE `acn`=".intval($acn) );
	otprav("clean('admunic".$un."');");
    }

    if($a=='adddomain') { global $acc,$acn; ADMA();
	$domain=RE('domain');
	if(!$domain || preg_match("/[^a-z0-9\.\-\_]+/s",$domain) || !strstr($domain,'.') ) idie("Error domain [".h($domain)."]");
	msq_update('jur',array('domain'=>$domain),"WHERE `acn`=".intval($acn) );
	if(!empty($GLOBALS['msqe'])) idie("MySQL Error: ".$GLOBALS['msqe']);
	otprav("salert('Domain saved',1000)");
    }


    idie("Unknown action `".h($a)."`");
}



SCRIPTS("nonav=1;");

function ACCTEST($e) { global $admin,$acc,$acn,$ADM,$IS,$httphost,$db_unic;
$conf=array_merge(array(
'template'=>"<br><a href='{acc_link}'>{acc}</a> (<a href='{acc_link}contents'>{count}</a>)"
),parse_e_conf($e));

$o='';

	if($acc=='') return $o."<p>Аккаунт админа сервера.";

	$p=ms("SELECT * FROM `jur` WHERE `acc`='".e($acc)."'","_1",0);
	if(!sizeof($p)) return $o."<p>Аккаунта `".h($acc)."` на сервере не существует.
	<br><br>Вы можете его завести: заполнить в своей <span class=ll onclick=\"majax('login.php',{a:'getinfo'})\">личной карточке</span> login: ".h($acc).", после чего зайти на <a href='".$GLOBALS['wwwhost']."acc'>".$GLOBALS['wwwhost']."acc</a> и создать себе одноименный аккаунт.";

	$ow=''; $a=(strstr($p['unic'],',')?explode(',',$p['unic']):array($p['unic']));
	    $g=array(); foreach($a as $n=>$u) { $w=getis($u);
		$oww="<span class=ll alt='открыть карточку' onclick=\"majax('login.php',{a:'getinfo',unic:".$u."})\"><b>".$w['imgicourl']."</b></span>";
		if($n) $oww="<div id='admunic".intval($u)."'><i class='e_remove' onclick=\"if(confirm('Remove admin ".$w['realname']."?'))majax('module.php',{mod:'ACCTEST',a:'deladmin',un:".intval($u)."})\"></i> &nbsp; ".$oww."</div>";
		$ow.=$oww;
	    }

return "
{_LISTIH:
<img src='{_OWNER:img_}'>
<h2><a href='//{_OWNER:acc_}.".$GLOBALS['rootdomain']."'>https://{_OWNER:acc_}.".$GLOBALS['rootdomain']."</a></h2>

{_TABLE: BORDER=0
аккаунт №:|".$p['acn']."
домен:|<input type='text' size=10 value=\"{_OWNER:domain_}\" onchange=\"majax('module.php',{mod:'ACCTEST',a:'adddomain',domain:this.value})\">
регистрация:|<b>{_OWNER:date_}</b>
unic №:|".h($p['unic'])."
ваш unic:|".$GLOBALS['unic']."
админы:|<div align=left>".$ow."</div>
вы:|".(is_unics($GLOBALS['unic'],$p['unic'])?"админ
добавить админа:| unic: <input id='addunic' type=text size=10 value=''> <input type=button value='add admin' onclick=\"majax('module.php',{mod:'ACCTEST',a:'addadmin',un:idd('addunic').value})\"><p>если не знаешь номер, <span class=ll onclick=\"majax('okno.php',{a:'unics'})\">поискать</span>"
:"здесь не админ или не залогинились")."
_}
_}

{_CENTER:
<div style='max-width:600px'>
для справки: ваш номер посетителя в базе посетителей unic = ".$GLOBALS['unic']."
<p>Ну и как вы думаете, это ваш аккаунт или вам надо <a href='".$httphost."/login'>перелогиниться</a> его владельцем, чтобы получить к нему доступ?
<p>Подсказка: если в самом верхнем левом углу страницы вы не видите оранжевого шарика, значит вы не залогинены админом аккаунта ".h($acc).". Админ аккаунта на любой странице своего аккаунта видит оранжевый шарик в левом верхнем углу.
</div>
_}";

}
?>