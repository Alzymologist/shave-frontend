<?php // ��������������� �������

include "../config.php"; include $include_sys."_autorize.php";
$a=RE('a');
$GLOBALS['json_return']=RE0('json');

if($a=='unsubscribe' || $a=='ban_immediately') { $ajax=0; $mail=RE('mail'); // ��������� ���������� ��� ��� � ��������
    if(RE('confirm') != substr(sha1(RE('date').'|'.$mail.'|'.$newhash_user),1,16) ) { sleep(5); idie($a.': Error confirm'); }
    $r=ms("SELECT * FROM ".$db_unic." WHERE (`mail`='!".e($mail)."' OR `mailw`='!".e($mail)."'
 OR `mail`='".e($mail)."' OR `mailw`='".e($mail)."')","_a",0);

    if($r==false) idie($a.': mail <b>'.h($mail).'</b> is not subscribed!'); // `mail_comment`=1 AND 

    $tmpl="<br>unic:#{id} last time: {timelast} user: {imgicourl} (realname:`{#realname}` login:`{#login}`)";
    $o=""; foreach($r as $x) { $p=get_ISi($x); $o.=mpers($tmpl,$p);
	msq("UPDATE ".$db_unic." SET `mail_comment`='0'"
.($a=='ban_immediately'?",`capchakarma`=253":'')
." WHERE `id`=".intval($x['id']) ); if($msqe!='') { $o.=$msqe; break; }
	$o.=" � <font color=green>UNSUBSCRIBED</font>";
    }
    idie($o);
}

// if($admin) idie('���������� ������ - ������ ��� ���������� �����!');

$erorrs=array();
 ADH();

$id=RE('id'); if(strstr($id,'_')) { list($aaa,$id,)=explode('_',$id,3); } $id=intval($id); // RE0 scc_317901

$comnu=RE0('comnu');
$idhelp='cm'.$comnu;
$lev=RE0('lev');
$dat=RE0('dat');
include $include_sys."_onecomm.php";


//=====================================================================================================================


$select_color_file=$GLOBALS['filehost'].'unic-colors.txt';

function select_color_getbasa() { global $select_color_file; $R=array();
    $s=(is_file($select_color_file)?fileget($select_color_file):''); $s=explode("\n",$s);
    foreach($s as $l) { if(c($l)=='' || !strstr($l,'|')) continue; list($u,$c,$n)=explode('|',$l); $R[c($u)]=array(c($c),c($n)); }
    if(!isset($R['default'])) $R=array_merge(array('default'=>array('EEEEEE','default messages color')),$R);
    if(!isset($R['screen'])) $R=array_merge(array('screen'=>array('CADFEF','hidden messages color')),$R);
    return $R;
}

function select_color_setbasa($R) { global $select_color_file; $s=array();
    foreach($R as $u=>$x) $s[]=$u.'|'.$x[0].'|'.$x[1];
    fileput($select_color_file,implode("\n",$s));
    fileput($select_color_file.".dat",serialize($R));
}

function can_i_edit($p) { // ��������� �� �������������?
    if($GLOBALS['admin']) return 1;
    if($GLOBALS['unic'] != $p['unic']) {
	// ����� acn �������, � ������� ���� �������
	if(false==($p['acn']=ms("SELECT `acn` FROM `dnevnik_zapisi` WHERE `num` = ".e($p['DateID']),"_l"))) idie("Error not_own #1");
	// ����� ���� ���������� ��������, � �������� ��������� �������
	if(false==($p['unics']=ms("SELECT `unic` FROM `jur` WHERE `acn`=".e($p['acn']),"_l"))) idie("Error not_own #2");
	// � ���������, ������ �� �������� � ����� ������� ���� ��������
	if(!is_unics($GLOBALS['unic'],$p['unics'])) idie('Comments:not_own you unic #'.h($GLOBALS['unic'])." != comment unic #".h($p['unic']));
	// � ��� ������ ������ ����, ������ �� ����� ���� ��� ����� ��������
	return 1;
    }
    if($GLOBALS['comment_time_edit_sec'] && (time()-$p['Time'] > $GLOBALS['comment_time_edit_sec'])) idie("������������� ����� ������ � ������� ".floor($GLOBALS['comment_time_edit_sec']/60)." �����.");
    if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`=".e($p['id']),"_l",0)) idie("������������� ������ - ��� ���� ������.");
    if($GLOBALS['IS']['capchakarma']>9) idie("���, �������, ���� ������������� ���� ����������� ������:<br>��� ��������, �� � ���������, ����� ��� �����.");
    return 1;
}

if($a=='select_color_save') { AD();
    $R=select_color_getbasa();
    $c=RE('color');
    if(!preg_match("/^[0-9A-F]{6}$/s",$c)) idie("Error color format: `".h($c)."`");
    $rn=h(str_replace('|','#',$IS['realname']));
    $R[$unic]=array($c,$rn);
    $R=select_color_setbasa($R);
    otprav("clean('select_color');salert('Saved',500);");
}

if($a=='select_color_form') { AD();
    $r=md5(time().rand(0,10000));
	$N=intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `unic`=".e($unic),"_l",0)); if(!$N) idie('Comments:'.$N);
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `unic`=".e($unic)." LIMIT ".(rand(0,$N-1)).",1","_1",0))===false) idie('Comments:not_found');
        $opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($p['DateID']),"_1"); $GLOBALS['opt']=mkzopt($opt);
    $R=select_color_getbasa(); $c=(isset($R[$unic])?$R[$unic][0]:'FFFFFF');

	$O=comment_one(ppu($p),1);

	$O="<div id='out_color' unic=0 name=0 style='background-color:#".h($c).";position:relative;"
	    ."border: 1px solid #bbb; box-shadow: #888 5px 5px 5px; border-radius: 0.8em 0.8em 0.8em 0.8em; overflow: auto; padding: 0.4em 0.4em 0.4em 0.4em; margin: 0.4em 0 0 0.4em;"
	    ."margin-left:0px'>$O</div>";
otprav("
ohelpc('select_color','Select Color',\"".njsn($O."<p>

<div style='position:relative;margin-top:10px;height:250px;'>

<div class='picker' id='primary_block'>

    <div id='line'><div id='arrows'><div class='left_arrow'></div><div class='right_arrow'></div></div></div>

    <div id='block_picker'>
        <img src='".$www_design."select_color/select_color_bgGradient.png' class='bk_img'>
        <div class='circle' id='circle'></div>
    </div>

    <p><input type='button' value='SET' onclick=\"majax('comment.php',{a:'select_color_save',color:document.getElementById('txt_color').value})\"> � <input type='text' id='txt_color' size=6 maxlength=6 value='000000'>

</div>

</div>
")."\");

idd('out_color').style.width=((getWinW()-50)*0.8)+'px';
center('select_color');

LOADS(['".$www_design."select_color/select_color.js?$r','".$www_design."select_color/select_color.css?rand=$r'],function(){picker.init('".h($c)."')});
");
// 'alert('unic: ".$unic."\\n(".$IS['realname'].") \\ncolor:')

}

//========================================================================================================================
if($a=='why_hidden_comm') { // ������ �������?
    $u=RE0('unic');
    $e=RE0('e');


    $p=ms("SELECT id,login,openid,admin,realname,mail FROM ".$GLOBALS['db_unic']." WHERE `id`=".e($u),"_1");
    $p=get_ISi($p,'<small><i>{name}</i></small>');

$o="������ ���� ����������� �������?<p>������ ��� ��� ����� "
."<span class=l onclick=\"majax('login.php',{a:'getinfo',unic:".$p['id']."})\">".$p['imgicourl']."</span>"

."<p>�� ������:"

."<br>� ���������, � ����� ����������� ��������: "
."<i alt='���������' class='e_ledred' onclick=\"var s,r=f5_read('ueban'),p=r?r.split(','):[],e=in_array(".$p['id'].",p);if(e){delete(p[e]);s='e_ledgreen';}else{p.push(".$p['id'].");s='e_ledred';}this.className=s;f5_save('ueban',p.join(','));\"></i>"
."<br>� ��� ����� <span class=l onclick=\"clean('whyban');var i=idd(".h($e).").id.replace(/scc_/g,''),s='scc_'+i;otkryl(s); removeEvent(e.target,'click',restore_comm); comhif5(i,0);\">��������</span> ���� ������ �����������"
."<br>� ������ <span class=l onclick=\"majax('comment.php',{a:'my_ueban',ueban:f5_read('ueban')})\">���� ����������</span>"
."<br>� <span class=l onclick=\"clean('whyban');clean(".h($e).");removeEvent(e.target,'click',restore_comm);\">�������</span> ���� � ������ � ���� �����"
;

otprav("ohelpc('whyban','������ ���� ����������� �������?',\"".njs($o)."\")");
}

if($a=='my_ueban') { // ��� ����������
    $ueban=trim(RE('ueban'),','); if($ueban!=preg_replace("/[^\d\,]+/",'',$ueban)) idie('Error u-ban Cookie');
    if($ueban=='') otprav("idie('��� ������ ���������� ����.<p>����� �� ������ ����������� ����������� �����������,<br>�������� ��� ������ �������� � �������� ��� `��������`."
."<p>������, ��� ������ ���������� ��������� � ��������� ������ �������� ��� ������ ����� � �� ������� �� ������.<br>���� �� �������� ��������� ��� �������������� ������ ���������, ������ ����� ����� ����."
."');");
    $pp=ms("SELECT id,login,openid,admin,realname,mail FROM ".$GLOBALS['db_unic']." WHERE `id` IN (".e($ueban).")","_a");
    $o=''; foreach($pp as $p) { $p=get_ISi($p,'<small><i>{name}</i></small>');

$o.="<div>"
."<i alt='���������' class='e_ledred' onclick=\"var s,r=f5_read('ueban'),p=r?r.split(','):[],e=in_array(".$p['id'].",p);if(e){delete(p[e]);s='e_ledgreen';}else{p.push(".$p['id'].");s='e_ledred';}this.className=s;f5_save('ueban',p.join(','));\"></i>"
."�"
."<span class=l onclick=\"majax('login.php',{a:'getinfo',unic:".$p['id']."})\">".$p['imgicourl']."</span>"
."</div>"; }

    otprav("ohelpc('my_ueban','��� ����������',\"".njs($o)."\")");
}
//========================================================================================================================
if($a=='mucapcha') { $muka=RE0('muka'); if(!$muka) $muka=$IS['capchakarma'];

    put_last_tmp('');
    $allmu=sizeof(glob($GLOBALS['filehost']."design/sociologia-kapcha/*.jpg"));
    if(!$allmu) idie("sociologia-kapcha not found");

    $click=($muka-60)*15+10;
    $secund=($muka-60)*15+10;

otprav("var mudat=[],muD,mucli=0;

if(!user_opt.ani && !user_opt.anicss) { LOADS(www_css+'animate.css',function(){user_opt.anicss=1;}); }

mudnew=function(i){ var k=1,j,l; while(k){k=0;l=Math.floor(Math.random()*".$allmu."+1);if(l<10)l='00'+l;else if(l<100)l='0'+l;for(j=1;j<=9;j++)if(1*l==mudat[j])k++} mudat[i]=1*l;return l;};

mudclick=function(i){ mucli++; i=i.id.replace(/^mud/,'')*1; clean('mud'+i,'zoomOut'); setTimeout('mudseti('+i+')',1600);
plays(www_design+'sound/'+(Math.random()>0.5?'bbm_end_call':'voice_recording_stop')+'.mp3');
};
mudseti=function(i){ zabil('mu'+i,\"<img id='mud\"+i+\"' onclick='mudclick(this)' style='width:100px !important;height:100px !important;' src='\"+www_design+\"sociologia-kapcha/\"+mudnew(i)+\".jpg'>\");};

mudsubmit=function(){
var x=".$click."-mucli;
if(x>0) { plays(www_design+'sound/bbm_tone.mp3'); return salert('�����������!<br>���������������, � ��� �������� '+x+' �������!',1000); }
var x=muD-Math.floor(new Date().getTime()/1000);
if(x>0) { plays(www_design+'sound/bbm_tone.mp3'); return salert('�����������!<br>������ �����������, � ��� �������� '+x+' ������!',1000); }
salert('�� ������� ����� ������, �� ���� ������!',1000);
capchaspec=0;
clean('mucapcha','zoomOut');
plays(www_design+'sound/bbm_outgoing_call.mp3');
};

plays(www_design+'sound/bbm_incoming_call.mp3');

ohelpc('mucapcha','��������, ��� �� �� �����',\"".njsn("<table border=0 cellspacing=5 width=300>"
."<tr><td colspan=3 style='background-color:#4990E2; color:white; text-weight:bold; font-size:13px; padding:20px;'>�������� ��� �����������,<br>�� ������� ������������<div style='font-size:38px'>������</div></td></tr>"
."<tr><td id='mu1' style='width:100px;height:100px;'></td><td id='mu2' style='width:100px;height:100px;'></td><td id='mu3' style='width:100px;height:100px;'></td></tr>"
."<tr><td id='mu4' style='width:100px;height:100px;'></td><td id='mu5' style='width:100px;height:100px;'></td><td id='mu6' style='width:100px;height:100px;'></td></tr>"
."<tr><td id='mu7' style='width:100px;height:100px;'></td><td id='mu8' style='width:100px;height:100px;'></td><td id='mu9' style='width:100px;height:100px;'></td></tr>"
."<tr><td colspan=3><input type='button' value='������' onclick='mudsubmit()'></tr></table>")."\");

muD=Math.floor(new Date().getTime()/1000+".$secund.");
for(var i=1;i<=9;i++)mudseti(i);");

}



//========================================================================================================================




if($a=='imgban') { AD(); // ������� � �������� ��������
    $u=RE0('u');
    $img=RE('img');
    $file=rpath($GLOBALS['filehost']."user/".$u."/".$img);
    $www=$GLOBALS['httphost']."user/".$u."/".h($img);
    $md5=md5(fileget($file));
    $mm=''; $aa=file($GLOBALS["host_log"]."comment_foto_banned.log"); foreach($aa as $l) { $l=c0($l); if(empty($l)||!strstr($l,' ')) continue;
	list($m5,$f5)=explode(' ',$l,2); $m5=c0($m5); $f5=c0($f5);
	if($m5==$md5) $mm.="<br>".$md5." : `".h($f5)."` `".h($file)."`";
    }
    if(!empty($mm)) otprav("salert(\"banned\",200)");

    if(!is_file($file)) idie("Error: file not found: `".h($file)."`");
    logi("comment_foto_banned.log",$md5.' '.$file."\n");
    if(!unlink($file)||is_file($file)) idie("Error: file NOT DELETE: `".h($file)."`");
    $d=''; $dir=dirname($file);
    if(!sizeof(glob($dir."/*"))) { if(!rmdir($dir)||is_dir($dir)) idie("Error: DIR NOT DELETE: `".h($dir)."`"); else $d="<div>DIR DELETED: `".h($dir)."`</div>"; }

    otprav("salert(\"$file [$md5]$d<div><img src='$www'></div>\",12000);");
}

if($a=='deltroll') { // ������� ������

    if(!$GLOBALS['podzamok']) idie("error");
    if(false===($u=ms("SELECT `unic` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0))) idie("error");
    if(false===($t=ms("SELECT `time_reg` FROM ".$db_unic." WHERE `id`=".e($u),"_l",0))) idie("error");
    if(time()-$t >= 84600) idie("timeout");

    logi("comment_deltroll.log",date("Y-m-d_H:i:s")." admin: ".$unic." ".$IS['realname']." id=".$id." u=".$u."\n");

//  idie("����� �������� ���������");


if($admin) { // ����� ����� ������
    $banned=array(); foreach(file($GLOBALS["host_log"]."comment_foto_banned.log") as $l) { $l=c0($l); if(empty($l)) continue; list($m5,)=@explode(' ',$l,2); $banned[]=$m5; }

    // ����� ������� �� ������ ������ �����
/*
    $pp=ms("SELECT `id` FROM ".$db_unic." WHERE `capchakarma`='255'","_a",0);
    $r=array(); $s='';
    foreach($pp as $n=>$u) { $u=$u['id'];
	$a=array();
	if(!is_dir($GLOBALS['filehost']."user/".$u)) continue;
	    foreach(glob(rpath($GLOBALS['filehost']."user/".$u."/*")) as $l) {
		$a[]=basename($l);
		$md5=md5(fileget($l));
		$w=h($GLOBALS['httphost']."user/".$u."/".basename($l));

		$d='';
		if(in_array($md5,$banned)) {
			if(!unlink($l)||is_file($l)) idie("Error: file NOT DELETE: `".h($l)."`");
			$dir=dirname($l); if(empty(glob($dir."/*"))) { if(!rmdir($dir)||is_dir($dir)) idie("Error: DIR NOT DELETE: `".h($dir)."`"); else $d="DIR DELETED: `".h($dir)."`"; }

			$s.="<div><big>".$u."</big>: $w [$md5] - <font color=red>BANNED $d</font></div>";
		} else $s.="<div><big>".$u."</big>: $w [<span class=ll  onclick=\"if(confirm('ban img?'))"
."majax('comment.php',{a:'imgban',u:'".h($u)."',img:'".h(basename($l))."'})"
."\">$md5</span>]<br><img style='max-width=500px;max-height=500px;' src='$w'></div>";
	    }
	    if(!empty($a)) $r[$u]=$a;
    }
    unset($pp);

    idie($s);
*/

    $p=ms("SELECT `id` FROM `dnevnik_comm` WHERE `unic`=".e($u),"_a");
    $r=''; foreach($p as $i) { $i=$i['id'];

	// $s='';
	foreach(glob($GLOBALS['filehost']."user/".$u."/".$i.".*") as $fot) {
	    $md5=md5(fileget($fot));
	    $s.="<br>".h($fot)." ".$md5;
	    if(!in_array($md5,$banned)) { $s.=" - new"; logi("comment_foto_banned.log",$md5.' '.$fot.' '.date("Y-m-d_H:i:s")."\n"); $banned[]=$md5; }
	    // else { /*$i=array_search($md5,$banned); $s.=" - old";*/  }
	}

//	idie($s);
	logi("comment_ban.log",date("Y-m-d_H:i:s")." user: ".$u." deleted comment: ".$i."\n");
        $r.=del_comm($i)."if(idd('$i'))clean('$i');";
	// $r.="alert('$i');if(idd('$i'))clean('$i');";
    }
//    idie(nl2br(h(fileget($GLOBALS["host_log"]."comment_foto_banned.log"))));

    msq_update($db_unic,array('capchakarma'=>255),"WHERE `id`=".e($u)); // ��������
    otprav($r);
}

    // ��� ��������� ������ ��������
    otprav("salert('��, ��������, ������ ����� ��� �����',2000);");






    $count=ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `unic`=".e($u),'_l',0); // ���������� ��� ��� ��������`")
    msq("UPDATE `dnevnik_comm` SET `scr`='1' WHERE `unic`=".e($u)); // ���������� ��� ��� ��������
    msq("UPDATE `dnevnik_comm` SET `Text`=CONCAT(`Text`,'".e("\n\n{scr:"
."[b]".date("H:i")." ���������� by ".$IS['user']."[/b]"
.($count>1?" ����� ������������: ".$count:" �� ���� ������������ �����������")
."}")."') WHERE `unic`=".e($u));
if($count>1) msq("UPDATE `dnevnik_comm` SET `Text`=CONCAT(`Text`,'".e("{scr:[i]���������� ������ �� ���� �����������[/i]}")."') WHERE `id`=".e($id)); // ���������

    cache_rm(comment_cachename(ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l")));
    otprav("salert('������ ������������: ".$count."<br>����� ������� ���������, ������ ��������',2000);");
}




//========================================================================================================================
if($a=='whois') { // ������������� ����������
        if(false===($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))) otprav("salert('error ID: ".$id."',500);");
	if(!$p['IPN']) otprav("salert('error: 0.0.0.0',500);");
	if(($f=RE0('f')) || trim($p['whois'])=='') {
	    $ip=ipn2ip($p['IPN']); if(empty($ip)) idie("Whois: IP error");
	    include_once $include_sys."geoip.php";
	    if(!RE0('f')) $w=geoip($ip,$p['IPN']); else { $w=ipgeobase($ip); if(!$w || $r['city']=='') $w=geoip_whois($ip); }
	    if(!$w || $r['city']=='') idie('Whois IP error');
	    $p['whois']=$w['country'].' '.$w['city'];
	    msq("UPDATE `dnevnik_comm` SET `whois`='".e($p['whois'])."' WHERE `IPN`=".e($p['IPN'])." AND (`whois`='' OR `whois`=' ')");
	}
	otprav_comment($p);
}
//========================================================================================================================
/*
if($a=='sendmemail') { // ���������� ���������� ����� ����� ������ ����
    list($mail,$confirm)=var_confirmed($IS['mail']);
    if($mail=='') idie("Your mail not defined, set in <a href=\"javascript:majax('login.php',{a:'getinfo'})\">usercard</a>");
    include_once $include_sys."_sendmail.php"; // send_mail_confirm($mail,$name);
    sendmail('my mail',$mail,$mail,$mail,RE('subj'),RE('text'));
    otprav("salert('Read your mail ".$mail."',5000);");
}
*/


// ��������� �������� ������������ � �������
if($a=='loadcomments') { // ,commenttmpl:(typeof(commenttmpl)=='string'?commenttmpl:'')
	$art=ms("SELECT `opt`,`num` FROM `dnevnik_zapisi` ".WHERE( "`num`=".e($dat) ),"_1");
	$art=mkzopt($art);
	$_GET['screen']=RE("mode");
	$comments_pagenum=RE("page");

	$GLOBALS['comment_tmpl']=h(RE('commenttmpl'));

	otprav("
	zabil('0',\"".njs(load_comments($art))."\");
	var c=gethash_c(); if(c && idd(c)) { kl(idd(c)); c=document.location.href; document.location.href=c; }
");
}

// ��������� �� �������� ���������, ��� ������� id
if($a=='loadpage_with_id') { // $id=RE0('id'); $dat
	if(!$id) otprav('');
	$do="if(idd(".$id.")) { kl(idd(".$id.")); var c=document.location.href; document.location.href=c; }";
	$pages=($comments_on_page?ceil(get_idzan1($dat)/$comments_on_page)-1:0); // ����� ������� ���������
//	if(!$pages) otprav($do); ����� ��������� - ������ ���������
	if(($mas=load_mas($dat))===false) idie("err num: $id in $dat");
	$i=0; $n=0; while(isset($mas[$i]) && ($mas[$i]['level']!=1 || ++$n) && $mas[$i++]['id']!=$id){}
	$n=ceil($n/$comments_on_page)-1;
//	if($n==RE0('page')) otprav($do); ����� ��������� - ������ ���������
	otprav("majax('comment.php',{a:'loadcomments',dat:$dat,page:$n,commenttmpl:(typeof(commenttmpl)=='string'?commenttmpl:'')})");
}

// ======================================================
if(!$unic) {

/*
idie_error("������� ������ ����� ������ ������������.
������������ ��������� ���, ��� ����������� ��������,
���� - �������� � ������ � ������ �������� �����/������,
� ����� ���������� email, ������� ������ � ������ �� ������.
<p><center><input type='button' value='������������' onclick=\"majax('login.php',{a:'do_login'})\"></center>");
*/

idie_error("�� ������� �� ���� �����?

����� ������ ��� ��� �������� ��������, ���� ��� ��������,
� ������ �� ������� ������ ����� ��, ��� ��������.

���� �� ��� �������� �������� � ��� ����� ��������� ��� ������ -
������, � ��� � �������� ��������� ����, ������� ��� �����������
�����-�� �����������.");

}


//========================================================================================================================
// ������ ������������ ��������� ������������ ����������� � ���������� �� ������������ � ����
if($a=='autosave') { put_last_tmp(RE('text')); otprav(''); }

//========================================================================================================================
// ��������� ���������� �������������� ��������
if($a=='loadpanel') { $idhelp=h(RE('idhelp')); $idhelp0=substr($idhelp,0,strlen($idhelp)-1);
        $id=$idhelp0."_textarea";
        $panel=nort(mpers(get_sys_tmp("panel_comm.htm"),
array('id'=>$id,'idhelp'=>$idhelp)));
        otprav("
zabil('".$idhelp."','".njs($panel)."');
idd('".$idhelp."').onclick=function(){return true};
idd('".$id."').focus();");
}

// show_url
if($a=='show_url') { $t=RE('type'); $u=h(rpath(RE('url'))); $s='Error media type';
	switch($t) {
	        case 'mp3': include_once $site_mod."MP3.php"; $s=MP3($u); break;
	        case 'youtub': include_once $site_mod."YOUTUB.php"; $s=YOUTUB($u.",480,385,autoplay"); break;
	        case 'img': $s='<img src="'.$u.'" hspace="10">'; break;
        }
	otprav("zabil('".RE('media_id')."',\"".njs($s)."\")");
}
//========================================================================================================================
// ��������� ���������� �������������� �������� ����
/*
if($a=='loadfoto') {
	$id=h(RE("id"));
	$idh=str_replace("_textarea","",$id);
	$panel="<br><input name='foto' type='file' onchange=\"idd('$id').value=idd('$id').value.replace(/\[IMG\]/gi,'')+'[IMG]'\">";
        otprav("clean('".$id."loadfoto');zabil('".$idh."p',vzyal('".$idh."p')+\"".njsn($panel)."\");");
}
*/

//========================================================================================================================
if($a=='pokazat') { // ��������
	$oid=RE("oid"); $id=intval(substr($oid,1));
	$level=($lev/$comment_otstup)+1;

	if(!$id /*or !$dat*/ or substr($oid,0,1)!='o') oalert("WTF?! oid:'".h($oid)."' id:'$id' dat:'$dat'");

$maxcommlevel=$level+2;
        $mas=load_mas($dat); if($mas===false) otprav("clean('$oid')");

$mojnocom=getmojno_comm($dat);

$r=''; $rr="clean('$oid');";

function otdalcomm($p,$id,$mojnocom){ return "
mkdiv(".$p['id'].",\"".njs(comment_one(ppu($p['p']),$mojnocom))."\",'".commclass($p['p'])."',idd(0),idd($id));
var e=idd(".$p['id'].");
e.style.marginLeft='".($p['level']*$GLOBALS['comment_otstup'])."px';
e.style.backgroundColor='#".commcolor($ara)."';
otkryl(".$p['id'].");
";
}
	for($i=0,$max=sizeof($mas);$i<$max;$i++){if($mas[$i]['p']['Parent']==$id){
		$rr.=otdalcomm($mas[$i],$id,$mojnocom);
		$i++; for(;$i<$max;$i++) { if($mas[$i]['level']<$level) break; $r=otdalcomm($mas[$i],$id,$mojnocom).$r; }
	}}

otprav($r.$rr);
}
//========================================================================================================================

if($a=='paren') { // �������� �������
	if(!$id) otprav('');
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0);
        $opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($p['DateID']),"_1"); $GLOBALS['opt']=mkzopt($opt);
otprav("
if(idd('show_parent')) clean('show_parent');
else {
mkdiv('show_parent',\"".njs(comment_one(ppu($p),getmojno_comm($p['DateID']),0 ))."\",'popup');
posdiv('show_parent',mouse_x+10,mouse_y);
}
");
}


if($a=='paren1') { // �������� �������
	if(!$id) otprav('');
	// �������� parent
	$parent=ms("SELECT `parent` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0);
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($parent),"_1",0);
        $opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($p['DateID']),"_1"); $GLOBALS['opt']=mkzopt($opt);

	otprav("
	if(!idd('$parent')) {
		var d=idd('".$id."');
		var l=1*d.style.marginLeft.replace(/px/g,'');
		d.style.marginLeft=(l+50)+'px';
	        var p=document.createElement('DIV');
		p.id=p.name='$parent';
		p.style.marginLeft=l+'px';
		p.style.backgroundColor='#".commcolor($p)."';
		p.innerHTML=\"".njs(comment_one(ppu($p),getmojno_comm($p['DateID']) ))."\";
		idd(0).insertBefore(p,d);
	} else {
		idd('$parent').style.border=(idd('$parent').style.border==''?'5px dotted green':'');
		}
	");
// p.className='".commclass($p)."';

}


if($a=='paren2') { if(!$id) otprav(''); // �������� �������
	$parent=ms("SELECT `parent` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0); // �������� parent
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($parent),"_1",0);
        $opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($p['DateID']),"_1"); $GLOBALS['opt']=mkzopt($opt);

	otprav("
		var i=".$parent.";
		if(idd(i)) { var e='delme_'+i; idd(i).id=e; clean(e); }
		var d=idd('".$id."');
		var l=1*d.style.marginLeft.replace(/px/g,'');
		d.style.marginLeft=(l+50)+'px';

	        var p=document.createElement('DIV');
		p.id=p.name=i; p.style.marginLeft=l+'px';
		p.style.backgroundColor='#".commcolor($p)."';
		p.innerHTML=\"".njs(comment_one(ppu($p),getmojno_comm($p['DateID']) ))."\";
		idd(0).insertBefore(p,d);
		idd(i).style.border='3px dotted #ccc';
		idd(".$id.").style.border='3px dotted green';
		clean('sp".$id."');
	"); // p.className='".commclass($p)."'; 
}


//========================================================================================================================
if($a=='otprav_comment') { otprav_comment(ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0)); } // ������ ��������� �������
if($a=='otprav_comment1') { $r=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0);

$r=comment_prep($r,1,0);

otprav("zabil('p".$id."',\"".njsn(h(print_r($r,1)))."\");"); } // ������ ��������� �������
//========================================================================================================================

if($a=='plus' || $a=='minus') { // ��������� ������ ��� �������
//	if(!$unic) otprav("� � ��� ������ ���� ���������?");

	if($IS['loginlevel']<3) idie_error("������� ������ ����� ������ ������������.
������������ ��������� ���, ��� ����������� ��������,
���� - �������� � ������ � ������ �������� �����/������,
� ����� ���������� email, ������� ������ � ������ �� ������.
<p><center><input type='button' value='������������' onclick=\"majax('login.php',{a:'do_login'})\"></center>");

        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0); if($p['unic']==$unic) ktogadilminusom($id,'self');

/*
	if($plain) {
	    ktogadilminusom($id,'self');
	    $p['golos_plu']=8;
	    otprav_JSON(wu($p)); // eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
	}
*/

	$golos='golos_'.($a=='plus'?'plu':'min');
	if(false!==msq_add('dnevnik_plusiki',array('commentID'=>$id,'unic'=>$unic,'var'=>$a))) {
	    msq("UPDATE `dnevnik_comm` SET `".$golos."`=`".$golos."`+1 WHERE `id`=".e($id) ); $p[$golos]++; otprav_comment($p);
	}
	$msqe=''; ob_clean(); // �������� ������
	if($a==ms("SELECT `var` FROM `dnevnik_plusiki` WHERE `commentID`=".e($id)." AND `unic`=".e($unic),'_l',0)) ktogadilminusom($id,'double'); // ��� ����
	msq_update('dnevnik_plusiki',array('var'=>$a),"WHERE `commentID`=".e($id)." AND `unic`=".e($unic) );
	$golos2='golos_'.($a!='plus'?'plu':'min');
	msq("UPDATE `dnevnik_comm` SET `".$golos."`=`".$golos."`+1,`".$golos2."`=`".$golos2."`-1 WHERE `id`=".e($id) );
	$p[$golos]++; $p[$golos2]--;
	otprav_comment($p);
}






//---------- ��� ����� ----------------------
function ktogadilminusom($id,$json_reason) {
	if($GLOBALS['json_return']) otprav_JSON(wu(array('error'=>1,'text'=>$json_reason)));

        $pp=ms("
SELECT r.var,r.unic,a.login,a.openid,a.admin,a.realname,a.mail
FROM `dnevnik_plusiki` AS r, ".$GLOBALS['db_unic']." AS a
WHERE r.commentID=".intval($id)." AND a.id=r.unic LIMIT 20000","_a");

// $s=$s0='';
$smin=$splu=$spmin=$spplu=''; $km=$kp=0;
foreach($pp as $p) { $p=get_ISi($p,'<small><i>{name}</i></small>'); $c=$p['imgicourl'];
        $c="<span onmouseover='kus(".$p['unic'].")'>$c</span>, ";
        if(($GLOBALS['admin']||$GLOBALS['podzamok']) and $p["admin"]=="podzamok") { if($p['var']=='plus') { $kp++; $spplu.=$c; } else { $km++; $spmin.=$c; } }
        else { if($p['var']=='plus') { $kp++; $splu.=$c;} else { $km++; $smin.=$c; } }
}
otprav("ohelpc('ktominusil','��� ������ ����/�����',\""
."<table><tr valign=top><td width=50%><i><b>����� $kp</b></i><p><small>".njs(str_replace(', ','<br>',trim($spplu.$splu,', ')))."</small></td>"
."<td width=50%><i><b>������ $km</b></i><p><small>".njs(str_replace(', ','<br>',trim($spmin.$smin,', ')))."</small></td></tr></table>"
."\")");
}
//========================================================================================================================
if($a=='editsend') { // ������� ����������������� �����������

	$text=RE("text"); $text=trim($text,"\n\r\t "); $text=str_replace("\r","",$text);
	if($text=='') $a='del'; // ���� ������� ������ ������� - ������������ ��� ��������
	else { // � ����� ���������� ��������������

		if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found id:'.h($id));
		can_i_edit($p); // � ����� �� �������������?


		if($text==$p['Text']) {
			if($GLOBALS['json_return']) idie_error('','not_change'.$text);
	        $ara=$p; $ic=$GLOBALS['include_sys']."onComment.php"; if(is_file($ic)) include_once $ic; // �������� $ara � $p
			otprav("clean('$idhelp');"); // ���� ����� �� ��������� - ������ �������
		}

		$scr=$p['scr']; include_once $GLOBALS['include_sys']."spamoborona.php";

		msq_update('dnevnik_comm',array('Text'=>e($text),'scr'=>$scr),"WHERE `id`=".e($id) );

		$p['Text']=$text;
	        $ara=$p; $ic=$GLOBALS['include_sys']."onComment.php"; if(is_file($ic)) include_once $ic; // �������� $ara � $p
		otprav_comment($p,onComm('edit',$p)."clean('$idhelp');");
         }
}
//========================================================================================================================
if($a=='del') { // id ������� �����������
    if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found');
    can_i_edit($p); // ��������� �� �������������/�������?
    cache_rm(comment_cachename(ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l")));
    otprav(onComm('del',$p).del_comm($id) );
    // otprav("clean($id)");
}
//========================================================================================================================
if($a=='edit') { // id ������������� �����������

	if(($p=ms("SELECT `id`,`unic`,`Text`,`Time`,`Name` FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found');
	can_i_edit($p);

$s="<form name='sendcomment_".$comnu."' onsubmit='cmsend_edit(this,".$comnu.",".$id."); return false;'><div id='co_$comnu'></div>"
."<textarea onkeyup='while(this.scrollTop)this.rows++' id='textarea_".$comnu."' style='border: 1px dotted #ccc; margin: 0; padding: 0;' name='txt' cols=50 rows="
.max(3,page(h($p['Text']),50)).">".h(str_replace("\n",'\\n',$p['Text']))."</textarea>"
."<div><input title='Ctrl+Enter' id='edit\comsend_".$comnu."' type=submit value='send'></div>"
."</form>";

if($comment_time_edit_sec && !$admin){
	$delta=$comment_time_edit_sec-(time()-$p['Time']); $dmin=date("i",$delta); $dsec=date("s",$delta);
	$o.="
var comm_red_timeout=function(id,n){ if(!idd('editcomsend_'+id)) return;
        if(!n) { idd('textarea_'+id).style.color='#AAAAAA'; return zakryl('editcomsend_'+id); }
        var N=new Date(); N.setTime(n*1000);
	var sec=N.getSeconds(); if(sec<10) sec='0'+sec;
        idd('editcomsend_'+id).value='Send before: '+N.getMinutes()+':'+sec;
        setTimeout('comm_red_timeout('+id+','+(--n)+')',1000);
}; comm_red_timeout(".$comnu.",".($dmin*60+$dsec-5).");";
} else $o='';

$s="

    setkey('Enter','ctrl',function(){idd('editcomsend_".$comnu."').click()},false,1);
    setkey('Digit8','ctrl',function(e){ ti('textarea_".$comnu."','".chr(171)."{select}".chr(187)."') },true,1);
    setkey('Comma','ctrl',function(e){ tin('textarea_".$comnu."','".chr(171)."')},true,1);
    setkey('Period','ctrl',function(e){ tin('textarea_".$comnu."','".chr(187)."')},true,1);
    setkey('Space','ctrl',function(e){ tin('textarea_".$comnu."','".chr(160)."')},true,1);
    setkey('Digit6','ctrl',function(e){ tin('textarea_".$comnu."','".chr(151)."')},true,1);
    setkey('Minus','alt',function(e){ tin('textarea_".$comnu."','".chr(151)."')},true,1);

    setkey('Comma','ctrl shift',function(e){ tin('textarea_".$comnu."','�')},true,1);
    setkey('Period','ctrl shift',function(e){ tin('textarea_".$comnu."','�')},true,1);


/*
    setkey('Escape','',function(e){ if( (e.target && e.target.value.length<2)||confirm('������� ����������� ��� ����������?')) clean('{idhelp}'); },false,1);
*/

comnum++; ohelpc('".$idhelp."',\"".($admin?h($p['Name']):"��������������")."\",\"<div id='commentform_".$comnu."'>"
.$s."</div>\"); idd('textarea_".$comnu."').focus();
".$o;

otprav("LOADS(www_css+'commentform.css');
cm_mail_validate=function(p) { var l=p.value; return l; };

cmsend_edit=function(t,comnu,id) { majax('comment.php',{a:'editsend',text:t['txt'].value,comnu:comnu,id:id,commenttmpl:commenttmpl}); return false; };

cmsend=function(t,comnu,id,dat,lev) {
    var ara={a:'comsend',comnu:comnu,id:id,dat:dat,lev:lev,commenttmpl:commenttmpl};
    if(t['mail']) ara['mail']=t['mail'].value;
    if(t['nam']) ara['name']=t['nam'].value;
    if(t['txt']) ara['text']=t['txt'].value;
    if(t['capcha']) ara['capcha']=t['capcha'].value;
    if(t['capcha_hash']) ara['capcha_hash']=t['capcha_hash'].value;
    majax('comment.php',ara);
    return false;
};".$s);
}

//========================================================================================================================
if($a=='ans') { // ���������-��������� ������ �� ���� �����������
	AD();
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found');
	$p['ans']=($p['ans']=='u'?'0':($p['ans']=='0'?'1':'u'));
	msq_update('dnevnik_comm',array('ans'=>$p['ans']),"WHERE `id`=".e($id) );
	otprav_comment($p); // ,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
if($a=='scr') { // ������-�������� ���� �����������
	if( !( ($GLOBALS['comment_friend_scr'] && $podzamok || $admin) ) ) oalert("� ���� ��� ���� ������ ���.");
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found');
	$p['scr']=($p['scr']==1?0:1);
	msq_update('dnevnik_comm',array('scr'=>$p['scr']),"WHERE `id`=".e($id) );
	otprav_comment($p,"idd('".$id."').style.backgroundColor='#".commcolor($p)."';");
}
//========================================================================================================================
if($a=='rul') { // ����������/����� ������ ����� �� ���� �����������
	AD();
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($id),"_1",0))===false) idie('Comments:not_found');
	$p['rul']=($p['rul']==1?0:1);
	msq_update('dnevnik_comm',array('rul'=>$p['rul']),"WHERE `id`=".e($id) );
	otprav_comment($p); // ,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================

if($a=='comsend') { razreshi_comm();
$text=str_replace("\r",'',trim(RE('text'),"\r\n\t ")); if($text=='') $erorrs[]=LL('Comments:empty_comm');

if($IS['user']!=''&&$IS['user_noname']!='noname') $name=$IS['user'];
else {
    $name=trim(RE("name")); $name=preg_replace("/\s+/si",' ',$name);
    if($name=='') $erorrs[]=LL('Comments:empty_name');
    else { // ������� ����� ���
	$p=ms("SELECT `id` FROM ".$db_unic." WHERE `realname`='".e($name)."'");
	if(sizeof($p)) {
	    $o=''; foreach($p as $l) { $l=intval($l['id']); $o.=" � <span class=ll onclick='kus(".$l.")'>".$l."</span>"; }
	    $erorrs[]='������������ � ����� ������ ��� ���������������:'.$o.'<br>������������ ��� �������� ���.';
	}
    }
}

$mail=mail_validate(RE('mail'));



//=====

if(count($_FILES)>0) {

	$opt=mkzopt(ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($dat),"_1")); unset($opt['opt']);
//	dier($opt);
//    [Comment_foto_sign] => ����� ����� ���������
//    [Comment_foto_x] => 40
//    [Comment_foto_q] => 75

	foreach($_FILES as $n=>$FILE) if(is_uploaded_file($FILE["tmp_name"])){

// idie("UPL: ".is_uploaded_file($FILE["tmp_name"]));

	$foto_replace_resize=1; require_once $include_sys."_fotolib.php";
        list($W,$H,$itype)=getimagesize($FILE["tmp_name"]);

// idie("under construction, wait 5 min: ".count($_FILES)."W=$W, H=$H, itype=$itype");

$img=openimg($FILE["tmp_name"],$itype);
        if($img===false) idie(LL('Comments:foto:musor',implode(', ',$GLOBALS['foto_rash']))); // �� �� �����?
	$imgs=obrajpeg_sam($img,$opt['Comment_foto_x'],$W,$H,$itype,str_ireplace('{name}',$name,$opt['Comment_foto_logo']));
	imagedestroy($img);
	} // else idie("File upload error: ".nl2br(h(print_r($_FILES,1))));
}

// $imgs=array();
// $fname=h($FILE["name"]);
//	$frash=end(explode(".",strtolower($FILE['name'])));
//        if(!preg_match("/\.(jpe*g|gif|png)$/si",$fname)) idie("��� ����� �����?");
//        if(preg_match("/^\./si",$fname)) idie("��� � �����, ��? ��������!");
//        if(strstr($fname,'..') or strstr($fname,'/') or strstr($fname,"\\") ) idie("�����������, �������?");
//	elseif(is_file($fotodir.$fname)){$fname.='_'; $k=0; while(is_file($fotodir.$fname.(++$k))){} $fname.=$k;}
//        closeimg($img2,$to,$itype); imagedestroy($img);
//	if(false===obrajpeg($FILE["tmp_name"],$fotodir.$fname,$fotouser_x,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo))) idie("��� � �� �� ����� ������ ��� �����?");
//	$text=str_ireplace('[IMG]',"\n".$httphost."user/".$unic."/{comment_id}.".(3)."\n",$text);
//$imgs[]=array(obrajpeg_sam($img,$fotouser_x,$W,$H,$itype,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo)),$itype);
//        closeimg($img2,$to,$itype); imagedestroy($img);
//	if(false===obrajpeg($FILE["tmp_name"],$fotodir.$fname,$fotouser_x,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo))) idie("��� � �� �� ����� ������ ��� �����?");
//	$text=str_ireplace('[IMG]',"\n".$httphost."user/".$unic."/".urlencode($fname)."\n",$text);



//===

if(!sizeof($erorrs)) {

	$ara_kartochka=array(); // ���� ����� ��������� ������ � ��������

// ============ ���� ����� �������� ����� ==============
// 0 - �� ��������� ������ ����������, ��� ���� ������ ����� ���� ���
// 1 - ����� ���� ������� ���� ���, ����� �� ��������� �� ����
// 2 ... 255 - ��������� ����� � ���� ����������� ����
if($IS['capchakarma']!=1) {

    if($IS['capchakarma']>=60 && $IS['capchakarma']<100) { // ���������
	$mu=ms("SELECT `timelast` FROM `unictemp` WHERE `unic`=".intval($GLOBALS['unic']),"_l",0);
	if(time() < $mu+($IS['capchakarma']-59)*10 ) otprav("salert('������! �� ������ ������ ���� ������!',1000);majax('comment.php',{a:'mucapcha',e:'start'})");
    } else {

	$karma=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
	include_once $GLOBALS['include_sys']."_antibot.php";
	if(RE('capcha')=='') otprav_error("������� ����� � �������� � ��������.");
        if(!antibot_check(RE('capcha'),RE('capcha_hash'))) otprav_error("�������� ����� � ��������, ���������!",
"zabil('ozcapcha_".$comnu."',\"".njs("<table><tr valign=center><td>
<input maxlength=".$karma." class='capcha' type=text name='capcha'>
<input type=hidden name='capcha_hash' value='".antibot_make($karma)."'></td><td>".antibot_img()."</td></tr></table>")."\");");
	if($IS['capchakarma']==0) $ara_kartochka['capchakarma']=(isset($GLOBALS['capchafirst'])?$GLOBALS['capchafirst']:1); // �������� � ����, ��� ����� ������� ����
    }
}
// ============ // ���� ����� �������� ����� ==============
	$scr=0; include_once $GLOBALS['include_sys']."spamoborona.php";

	// $c=ms("SELECT `Comment_screen` FROM `dnevnik_zapisi` WHERE `num`=".e($dat),"_l");
	$po=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($dat),"_1");
	$po=mkzopt($po); $c=$po['Comment_screen'];

	if($c=='screen' or (!$podzamok && $c=='friends-open')) $scr=1;

ADMA(1);
// if(isset($admin_colors[$unic])) $group=$admin_colors[$unic]; else $group=$ADM?1:0;

	$ara=array(
		'Text'=>$text,
			'Mail'=>$mail!=''?$mail:$IS['mail'],
			'Name'=>$name,
//		'group'=>$group, // $admin?3:0,
		'IPN'=>$IPN,
		'BRO'=>$BRO,
// 'whois'
		'DateID'=>$dat,
		'unic'=>$unic,
		'Time'=>time(),
		'scr'=>$scr,
		'Parent'=>$id );

//dier($ara,'####');

// � ����� �� �� ����� ���������� ���� ����?
	$ans=($id==0?'u':ms("SELECT `ans` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l"));
	if(!$ADM and $ans=='0') idie('����� �������� �������� �� ���� �����������.');
	if($ans=='u') { $e=getmojno_comm($dat);
		if(!$ADM and $e===false) idie('� ���� ������� �������� ������.');
		if(!$ADM and $e=='root' and $id!=0) idie('� ���� ������� ��������� �����������, �� �� ������ �� ���.');
	}
// ------------------------------------------

// $IP='83.151.5.155';

    include_once $include_sys."geoip.php"; $w=geoip($GLOBALS['IP'],$GLOBALS['IPN']);
    if(!$w || $w['country']=='') $ara['whois']='';  else $ara['whois']=$w['country'].' '.$w['city'];

/*
    include_once $include_sys."_files.php";
    function gettg($s,$n) { return (!preg_match("/<".$n.">([^<>]+)<\/".$n.">/si",$s,$l)?false:$l[1]); }
    $g=fileget_timeout('http://ipgeobase.ru:7020/geo?ip='.$IP,1);
    if(($c=gettg($g,'country'))===false) $ara['whois']='';
    else $ara['whois']=$c."|".gettg($g,'city')."|".gettg($g,'region').", ".gettg($g,'district');
*/
//    idie("<pre>".nl2br(h($ara['whois'])).strlen($ara['whois'])."</pre>".h($g));
// whois: varchar(128) NOT NULL
// 
// <ip-answer><ip value="83.151.5.155"><inetnum>83.151.0.0 - 83.151.15.255</inetnum><country>RU</country><city>������</city><region>���������� ���������</region><district>����������� ����������� �����</district><lat>55.796539</lat><lng>49.108200</lng></ip></ip-answer>
// dier($ara);

	msq_add('dnevnik_comm',arae($ara)); $newid=msq_id(); $ara['id']=$newid;
	del_last_tmp(); // ������� ���

//===================

if(isset($imgs)) { // ���� ���� ��������� �����
	$fotodir=$filehost."user/".$unic."/";
	if(!is_dir($fotodir)){ if(mkdir($fotodir)===false) idie("mkdir `".h($fotodir)."`"); chmod($fotodir,0777); }
	$to="user/$unic/$newid".".".$GLOBALS['foto_rash'][$itype];
	closeimg($imgs,$filehost.$to,$itype,$opt['Comment_foto_q']);

    // ������ �� ������ ��� ����� ����� ������ �������
	$ara['Text']=$ara_text_save=str_ireplace("[IMG]","\n".$httphost.$to."\n",$text);
	if(is_file($GLOBALS['include_sys']."spamoborona2.php")) include_once $GLOBALS['include_sys']."spamoborona2.php";
	msq_update('dnevnik_comm',array('Text'=>e($ara_text_save)),"WHERE `id`=".e($newid) );
}

//===================
//	$ara=ms("SELECT * FROM `dnevnik_comm` WHERE `id`=".e($newid'","_1",0);
//	$c=njs(comment_one($ara,getmojno_comm($ara['DateID'])));
	$ara['whois']=''; $ara['rul']=$ara['golos_plu']=$ara['golos_min']=0; $ara['ans']='u';

	$GLOBALS['comment_tmpl']=h(RE('commenttmpl')); // ��������

	$c=njs(comment_one(ppu($ara),getmojno_comm($ara['DateID'])));

// ================= ��������� ������ � �������� =================
	if($IS['realname']=='') { $ara_kartochka['realname']=$IS['realname']=e($name); }

// ================= ��������� ������ � �������� =================
	if($mail!='' && $IS['mail']=='') { $ara_kartochka['mail']=$IS['mail']=e($mail);
		include_once $include_sys."_sendmail.php"; send_mail_confirm($mail,$name,$ara);
	}
	if(sizeof($ara_kartochka)) msq_update($db_unic,$ara_kartochka,"WHERE `id`=".e($unic) );
// ================= ��������� ������ � �������� =================

	cache_rm(comment_cachename($dat));

// ================ ��������� ������ =============================

$js=onComm('new',$ara);

if($id && !isset($GLOBALS['ara_nomail'])) { $p=get_user_toans($ara,$id); if($ara['unic']!=$p['id']) { // ���� ��� ����� (�� � ����� �������), ��������� �������� ���� ���������

    // mail
    if($p['mail_comment']==1 && ($m=get_workmail($p)) ){ // � �� ����� �������� ������ � � ���� ������ � ����������� mail
	include_once $include_sys."_sendmail.php";
	if(0!==($sys=mail_answer($id,$ara,$p,$m))) $js.="salert('mail send: ".njsn($sys['name_parent'])."',1000);";
    }

    $p['opt']=mkuopt($p['opt']); // ��������� �����

/*
    // TeddyId
    if(!empty($GLOBALS['teddyid_nodeid']) && $p['teddyid']  // ���� ��� teddyid
    && ($p['opt']['ttcom'] || $p['opt']['ttcom1'] && $p['group']==1) // � ��������� ���������� ��� ��������� ���� ��� ������
    ) { // ���� ��� teddyid � ��������� ����������
    $ttxt="".$ara['Name']." �������� � �������:
".$p['Date']." - ".$p['Header']."

".$ara['Text'];
    // $ttxt=substr($ttxt,0,1000);

    $ttxt=substr($ttxt,0,400);
	$e=teddyid_opovest($p['teddyid'],$ttxt);
	if(intval($e)) $js.="salert('Teddy send',1000);";
	// else idie('Error: '.h($e));
    }
*/


    // Telegram

// if($unic==4) { msq("UPDATE ".$GLOBALS['db_unic']." SET teddyid=0 WHERE id=4 LIMIT 1"); dier($p); }

    if(!empty($GLOBALS['tg_bot']) && $p['telegram']  // ���� ��� ���������� ��� telegram
    && ($p['opt']['tgcom'] || $p['opt']['tgcom1'] && $p['group']==1) // � ��������� ���������� ��� ��������� ���� ��� ������
    ) { // ���� ��� telegram � ��������� ����������
    $ttxt="".$ara['Name']." �������� ��� � ������� ".getlink($p['Date'])
."\n".$p['Date']." - ".$p['Header']
."\n\n".$ara['Text'];
    $ttxt=substr($ttxt,0,4000);
	$e=telegram_send($p['telegram'],$ttxt);
	if(intval($e)) $js.="salert('Telegram send',1000);";
	// else idie('Error: '.h($e));
    }

}}
// ===============================================================

    $ic=$GLOBALS['include_sys']."onComment.php"; if(is_file($ic)) include_once $ic; // �������� $ara � $p


otprav("/*WWW*/f_save('comment',''); clean('$idhelp');
".($id?"mkdiv($newid,\"$c\",'".commclass($ara)."',idd(0),idd($id));":"mkdiv($newid,\"$c\",'".commclass($ara)."',idd(0));")."
var e=idd($newid);
e.style.marginLeft='".($lev+25)."px';
e.name='$newid';
e.style.backgroundColor='#".commcolor($ara)."';
otkryl(e);
".($id?'':"if(typeof(onComm)=='undefined')window.location=mypage.replace(/#[^#]+$/g,'')+'#$newid';")."
$js
if(typeof(plays)!='undefined') plays('".$httphost."design/kladez/'+((Math.floor(Math.random()*100)+1)%27)+'.mp3');
"
);

} else { otprav_error(implode('<br>',$erorrs)); }

}

//=================================== ��������� ����� ===================================================================

if($a=='comform') { // a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum; ��������

// idie("����������� � ���� �������� � ��������� ��� ���������, ������� ������. ������� ��������� �� ����������.");

 razreshi_comm();

if($dat==0) $dat=ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0); if($dat===false) idie("��������� ����.");

$capchakarma=$IS['capchakarma'];
if($capchakarma>=60 && $capchakarma<100) { $capchakarma=1; $capchaspec=1; } else $capchaspec=0; // ������������ �������

$ar=array(
'comnu'=>$comnu,'id'=>$id,'dat'=>$dat,'lev'=>$lev,'idhelp'=>$idhelp,
'imgicourl'=>$imgicourl,
'httphost'=>$httphost,
'is_name'=>($IS['user']!=''&&(!isset($IS['user_noname'])||$IS['user_noname']!='noname')),
'capchakarma'=>$capchakarma,'capchaspec'=>$capchaspec
); list($ar['mail'],$ar['mail_confirm'])=var_confirmed($IS['mail']);

if($capchakarma!=1) { include_once $include_sys."_antibot.php";
    $ar['antibot_karma']=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
    $ar['antibot_hash']=antibot_make($ar['antibot_karma']);
    $ar['antibot_img']=antibot_img();
}

    $tmpl=rpath(RE('tmpl')); if(empty($tmpl)) $tmpl="comment_new.htm";

// idie(RE('acc'));

    $tmp=get_sys_tmp($tmpl,RE('acc'));
    if(empty($tmp)) idie('COMM-Template not found: '.h($tmpl));

    otprav(nor(mpers($tmp,$ar)));
}

//=================================== ������� ����������� ===================================================================

function del_comm($id,$l=1,$delcache=1) { $id=intval($id); if(!$id) return " alert('id=0?!');";
	// ��� ������ �������� ��� ���� �������, �� ������ � ������ ���
	if($delcache) cache_rm(comment_cachename(ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l")));

	if($l and ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`=".e($id),"_l",0) ) { // ���� � ���� ���� ������� - ������ ��������
		msq_update('dnevnik_comm',array(
			'Time'=>0,'unic'=>0,'Name'=>'','Mail'=>'','Text'=>'','IPN'=>0,
			'BRO'=>'','whois'=>'','rul'=>'no','ans'=>'0','golos_plu'=>0,
			'golos_min'=>0 ),"WHERE `id`=".e($id) );

		return "idd($id).innerHTML=''; idd($id).className='cdel';";
	}
	// ����� ������� ������
	
	$Parent=intval(ms("SELECT `Parent` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0)); // ������ ��������� �������
	$unic=intval(ms("SELECT `unic` FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0)); // ������ unic

	// ����� �������
	ms("DELETE FROM `dnevnik_comm` WHERE `id`=".e($id),"_l",0);

	// ����� ��� �������� � ����� ��������
	//if($unic) { $glob=glob($GLOBALS['filehost']."user/".$unic."/".$id.".*"); foreach($glob as $f) { unlink($f); $d=dirname($f); $glob2=glob($d."/*"); if(empty($glob2)) rmdir($d); } }
	if($unic) {
	    $glo=glob($GLOBALS['filehost']."user/".$unic."/".$id.".*");
	    if(!empty($glo)) foreach($glo as $f) { unlink($f); $d=dirname($f); $kglo=glob($d."/*"); if(!empty($kglo)) rmdir($d); }
	}

	$r=" clean($id);";

	if( ! $Parent // ���� �� ��� � �����
		or ms("SELECT `Time` FROM `dnevnik_comm` WHERE `id`=".e($Parent),"_l",0) // ��� ��� ������� �� ������
		or ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`=".e($Parent),"_l",0) // ���� � �������� ���� ������ �������
	) return $r; // �� ������ ��������� � ������� ����

	return $r.del_comm($Parent,0,0); // ����� ��������� �������� � ���
}

function otprav_comment($p,$r='') { if($GLOBALS['json_return']) otprav_JSON(wu($p));
	$GLOBALS['comment_tmpl']=h(RE('commenttmpl'));
	cache_rm(comment_cachename($p['DateID'])); // �������� ��� �������� ���� ������
	$opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`=".e($p['DateID']),"_1");
	$GLOBALS['opt']=mkzopt($opt);
	otprav("idd(".$p['id'].").innerHTML=\"".njs(comment_one(ppu($p),getmojno_comm($p['DateID']) ))."\"; ".$r);
}

function ppu($p) {
$pu=ms("SELECT `capchakarma`,`mail`,`admin`,`openid`,`realname`,`login`,`img` FROM ".$GLOBALS['db_unic']." WHERE `id`=".e($p['unic']),"_1",0);
return get_ISi(array_merge($pu,$p));
}

function getmojno_comm($num) {
	$p=ms("SELECT `opt`,`DateDatetime`,`num` FROM `dnevnik_zapisi` WHERE `num`=".e($num),"_1");
	$p=mkzopt($p);
	$p['counter']=get_counter($p);
	return mojno_comment($p);
}

function otprav_error($s,$p='') { global $comnu; otprav("zabil('co_".$comnu."',\"<div class=e>".njs($s)."</div>\");".$p); }

//=================================== ��������� ����� ===================================================================
function send_comment_form($text,$id,$lev,$comnu) { // {a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

razreshi_comm();

$s="<form name='sendcomment' onsubmit='cmsend(this,".$comnu.",".$id.",".$dat.",".$lev."); return false;'><div id='co_$comnu'></div>";

$s.= "<div><div class=l1>"
.($IS['user']!=''&&$IS['user_noname']!='noname'?$imgicourl:"���: <input name='name' class='in' type='text'>")."
<div id='".$idhelp."p' style='display:inline; margin-left: 3px;'><i onclick=\"majax('comment.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" class='e_finish' alt='panel'></i></div>
</div><div class=l2>"
.($IS['mail']!=''?"<acronym title='������ ������ �� ".h($IS['mail'])."'><i class='e_mail' align=right></i></acronym>"
:"mail: <input name='mail' class=in type=text onkeyup='this.value=cm_mail_validate(this)'>"
)."</div>
<br class=q /></div>";

if($IS['capchakarma']!=1) { include_once $include_sys."_antibot.php";
	$karma=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
$s.="<div><div class=l1>".($IS['capchakarma']==0?"�� ������� �� �����<br>":'')."�����������, ��� �� �� �����:</div>
<div class=l2 id='ozcapcha_$comnu'><table><tr valign=center><td><input maxlength=$karma class='capcha'
type=text name='capcha'><input type=hidden name='capcha_hash' value='".antibot_make($karma)."'></td>
<td>".antibot_img()."</td></tr></table></div><br class=q /></div>";
}

}

function razreshi_comm() { global $max_comperday,$unic,$admin;
// if($GLOBALS['podzamok']) return; // ������� ����� ������ ����������� ������������
    if($admin) return; // ������ ����� ������ ����������� ������������
	if(!$max_comperday) return;
	$time=time();
	$p=ms("SELECT `Time` FROM `dnevnik_comm` WHERE `unic`=".e($unic)." AND `Time`>".($time-86400)." ORDER BY `Time` LIMIT ".e($max_comperday)."","_a",0);
	if(sizeof($p)<$max_comperday) return;

$to=$p[0]['Time']+86400;
idie("���������� ���������� ������������ �� �������� � ����� � $max_comperday
<br>������ ".date("H:i",$time).", ����� ����������� ����� �������� "
.(date("d",$time)!=date("d",$to)?"������":"�������")." ����� ".(date("H:i",$to)) );
}


function get_user_toans($ara,$id) {
$p=ms("SELECT z.`Header`,z.`Date`,c.Time,c.Text,u.login,u.telegram,u.openid,u.realname,u.img,u.mail,u.mailw,u.mail_comment,u.id,u.opt,c.*
 FROM `dnevnik_zapisi` AS z, `dnevnik_comm` AS c
 LEFT JOIN ".$GLOBALS['db_unic']." AS u ON c.`unic`=u.`id`
 WHERE z.`num`=".e($ara['DateID'])." AND c.`id`=".e($id),"_1",0);

// dier($p);

return get_ISi($p);
}

idie(nl2br(h(__FILE__.": unknown action `".$a."`")));



function idie_error($s,$json=false) {
	if($GLOBALS['json_return']) otprav_JSON(wu(array('error'=>1,'text'=>($json===false?$s:$json))));
	idie(nl2br($s));
}

function onComm($name,$ara) {
    $js=''; foreach($ara as $n=>$l) $js.=h($n).":\"".str_replace(array("\"","\r","\n"),array("\\n",'',"\\n"),$l)."\",";
    return "if(typeof(onComm)=='function') onComm({".$js."ACT:'$name'});";
}

?>