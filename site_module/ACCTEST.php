<?php

/*  */

function ACCTEST_ajax(){ $a=RE('a');

    if($a=='addadmin') { global $acc,$acn; ADMA();
	$un=RE0('un'); if(!$un) idie("Error unic #".$un." (\"<b>".h(RE('un'))."</b>\") Numeric only.");
	if(false==($is=getis($un))) idie("Error: unic #".$un." not found!");
	$name="<div class=ll onclick=\"majax('login.php',{a:'getinfo',unic:".$unic."})\">".$is['imgicourl']."</div>";
	if($is['loginlevel']<2) idie("������������ ".$name." ������������ �������� ���� �������");
	if(false===($unics=ms("SELECT `unic` FROM `jur` WHERE `acc`='".e($acc)."'",'_l',0))) idie("��� �� ���c�� �������� ������");
	if(is_unics($un,$unics)) idieok("Already admin");
	msq_update('jur',array('unic'=>$unics.",".$un),"WHERE `acn`=".intval($acn) );
	idieok("Congratulations!<br>Unic #".$un." now admin in ".h($acc)."!");
    }

    if($a=='deladmin') { global $acc,$acn; ADMA();
	$un=RE0('un'); if(!$un) idie("Error unic #".$un." (\"<b>".h(RE('un'))."</b>\") Numeric only.");
	if(false===($unics=ms("SELECT `unic` FROM `jur` WHERE `acc`='".e($acc)."'",'_l',0))) idie("��� �� ���c�� �������� ������");
	if(!is_unics($un,$unics)) idie("���� ������� ��� �� �����");
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

	if($acc=='') return $o."<p>������� ������ �������.";

	$p=ms("SELECT * FROM `jur` WHERE `acc`='".e($acc)."'","_1",0);
	if(!sizeof($p)) return $o."<p>�������� `".h($acc)."` �� ������� �� ����������.
	<br><br>�� ������ ��� �������: ��������� � ����� <span class=ll onclick=\"majax('login.php',{a:'getinfo'})\">������ ��������</span> login: ".h($acc).", ����� ���� ����� �� <a href='".$GLOBALS['wwwhost']."acc'>".$GLOBALS['wwwhost']."acc</a> � ������� ���� ����������� �������.";

	$ow=''; $a=(strstr($p['unic'],',')?explode(',',$p['unic']):array($p['unic']));
	    $g=array(); foreach($a as $n=>$u) { $w=getis($u);
		$oww="<span class=ll alt='������� ��������' onclick=\"majax('login.php',{a:'getinfo',unic:".$u."})\"><b>".$w['imgicourl']."</b></span>";
		if($n) $oww="<div id='admunic".intval($u)."'><i class='e_remove' onclick=\"if(confirm('Remove admin ".$w['realname']."?'))majax('module.php',{mod:'ACCTEST',a:'deladmin',un:".intval($u)."})\"></i> &nbsp; ".$oww."</div>";
		$ow.=$oww;
	    }

return "
{_LISTIH:
<img src='{_OWNER:img_}'>
<h2><a href='//{_OWNER:acc_}.".$GLOBALS['rootdomain']."'>https://{_OWNER:acc_}.".$GLOBALS['rootdomain']."</a></h2>

{_TABLE: BORDER=0
������� �:|".$p['acn']."
�����:|<input type='text' size=10 value=\"{_OWNER:domain_}\" onchange=\"majax('module.php',{mod:'ACCTEST',a:'adddomain',domain:this.value})\">
�����������:|<b>{_OWNER:date_}</b>
unic �:|".h($p['unic'])."
��� unic:|".$GLOBALS['unic']."
������:|<div align=left>".$ow."</div>
��:|".(is_unics($GLOBALS['unic'],$p['unic'])?"�����
�������� ������:| unic: <input id='addunic' type=text size=10 value=''> <input type=button value='add admin' onclick=\"majax('module.php',{mod:'ACCTEST',a:'addadmin',un:idd('addunic').value})\"><p>���� �� ������ �����, <span class=ll onclick=\"majax('okno.php',{a:'unics'})\">��������</span>"
:"����� �� ����� ��� �� ������������")."
_}
_}

{_CENTER:
<div style='max-width:600px'>
��� �������: ��� ����� ���������� � ���� ����������� unic = ".$GLOBALS['unic']."
<p>�� � ��� �� �������, ��� ��� ������� ��� ��� ���� <a href='".$httphost."/login'>��������������</a> ��� ����������, ����� �������� � ���� ������?
<p>���������: ���� � ����� ������� ����� ���� �������� �� �� ������ ���������� ������, ������ �� �� ���������� ������� �������� ".h($acc).". ����� �������� �� ����� �������� ������ �������� ����� ��������� ����� � ����� ������� ����.
</div>
_}";

}
?>