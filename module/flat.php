<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// поддержка протокола автопостинга flat ver 1.0

ob_clean();

function erflat($s) { die("errmsg\n".$s."\nsuccess\nFAIL\n"); }
header("Content-Type: text/plain; charset=utf-8"); // header("Content-Type: text/xml; charset='".$wwwcharset."'");
foreach($_REQUEST as $i=>$l) $_REQUEST[$i]=uw($l);

//

// if(!isset($_REQUEST['ver'])||$_REQUEST['ver']!=1) erflat("unknown protocol (only ver.1 supported)"."[$acc]");

$flat_user=RE('user');

if($acc=='' && empty($flat_user)) { // патч для прошлого, где логин прописывался в конциг для стэндалона
    $flat_user=$flatlogin;
    $_REQUEST['password']=$flatpassword;
}


    if(empty($flat_user)) $flat_user=$acc;
    if(empty($flat_user)) erflat('Login needed');


// ========================================================================
if(!empty($acc)) {
  if(!empty($xdomain)&&$xdomain==$acc) erflat('E-XDM: '.$GLOBALS['MYPAGE']); // нехуй на x.domain ломиться
  if(!$mnogouser) erflat("Error 404#: Subdomain <b>".h($acc)."</b> not exist on http[s]://".$MYHOST,"HTTP/1.1 404 Not Found"); // однопользовательский блог не понимает доменов
    if(substr($acc,0,4)=='www.') { $acc=substr($acc,4); // www.
        if(isset($redirect_www)) redirect($HTTPS."://".(false===strpos($acc,'.')?($acc==''?'':$acc.'.').$MYHOST:$acc).$_SERVER["REQUEST_URI"]);
    }
  if(false===strpos($acc,'.')) { // если без точки, то это поддомен пользователя
        if(false==($p=ms("SELECT `domain`,`time`,`acn`,`unic` FROM `jur` WHERE `acc`='".e($acc)."'","_1"))) { $acn=-1; $ADM=$otime=0; $odomain=$ounics=''; }
        else { $acn=$p['acn']; $odomain=$p['domain']; $otime=$p['time']; $ounics=$p['unic']; if(!isset($unic)) $unic=0; $ADM=is_unics($unic,$ounics); if($ADM) $ttl=0; }
  } else { // заход как бы с внешнего домена, но в поддомен пользователя
        if(false==($p=ms("SELECT `acn`,`acc`,`unic`,`time` FROM `jur` WHERE `domain` IN ('".e($acc)."','www.".e($acc)."')","_1"))) { $acn=-1; $ADM=$otime=0; $ounics=$odomain=''; }
        else { $acn=$p['acn']; $odomain=$acc; $acc=$p['acc']; $otime=$p['time']; $ounics=$p['unic']; $ADM=is_unics($unic,$ounics); if($ADM) $ttl=0; }
  }
} else { $acc=$odomain=''; $acn=$otime=0; $ADM=$admin; $ounics=$admin_unics; $acn=$otime=0; } // нет работы с поддоменами

    $ucs=(strstr($ounics,',')?explode(',',$ounics):array($ounics));
    $is=ms("SELECT * FROM ".$db_unic." WHERE `login`='".e($flat_user)."'",'_1');
    if(!in_array($is['id'],$ucs)) erflat("You are not admin for $acc");

    if(
	(!empty($pass=RE('hpassword')) && $is['password']!=$pass)
	|| (!empty($pass=RE('password')) && $is['password']!=md5($pass.$hashlogin))
    ) erflat('Password error');

//    $is['password']='***';
//    $W=print_r($is,1);
    // $is=getis($acn,'a');
    // if($is===false) erflat("unknown user ".h($acc)." #".$acn." ".$ounics);

//    erflat('FLAT not set: '.$W);
// ========================================================================


//    if($flat_user!=$flatlogin || $flat_pass!=$flatpassword) { sleep(3); erflat('wrong Login/Password'); }

 $mode=RE('mode');

//$r=$_REQUEST; $r['password']='*******'; file_put_contents('module/flat.---.txt',print_r($r,1));
// erflat($tag."---".str_replace("\n","|",nl2br(print_r($opt,1))));

 if($mode=='postevent' || $mode=='editevent') {
        $p=array('acn'=>$acn);
	$p['Header']=RE('subject');
        $p['Body']="{_NO:autopost:FLAT:".RE('link')."_}".RE('event');

	if(false===($p['Date']=RE('Date'))) {
	    $p['Date']='';
	    $name='year'; if( false!==($x=RE($name)) ) $p['Date'].=D00($x,4,$name);
	    $name='mon'; if( false!==($x=RE($name)) ) $p['Date'].='/'.D00($x,2,$name);
	    $name='day'; if( false!==($x=RE($name)) ) $p['Date'].='/'.D00($x,2,$name);
	    $name='hour'; if( false!==($x=RE($name)) ) $p['Date'].='_'.D00($x,2,$name);
	    $name='min'; if( false!==($x=RE($name)) ) $p['Date'].='_'.D00($x,2,$name);
	    // $Date=RE('year').'/'.RE('mon').'/'.RE('day').'_'.RE('hour').'_'.RE('min');
	}

    // Access: enum('all','podzamok','admin') NOT NULL default 'admin'
    // Specifies who can read this post. Valid values are public (default), private and usemask. When value is usemask, viewability is controlled by the allowmask.
    if(empty($x==RE('security')) || $x=='public') $p['Access']='all';
    elseif($x=='private') $p['Access']='admin';
    elseif($x=='usemask') $p['Access']='podzamok';
    else erflat('Unknown security mode: '.$x);

        $opt=array();
	$tag=RE('prop_taglist');

	foreach($_REQUEST as $i=>$l) {
		$t='lleoopt_'; if(substr($i,0,strlen($t))==$t) {
			$i=substr($i,strlen($t));
			if($i=='tags') { $tag=$l; continue; } // опция opt_tags - принудительно становить тэги
			if($i=='addtags') { $tag=($tag==''?$l:$tag.','.$l); continue; } // опция opt_addtags - добавить новые тэги к существующим
			$opt[$i]=$l;
			continue;
		}
		$t='lleo_'; if(substr($i,0,strlen($t))==$t) $p[substr($i,strlen($t))]=$l;
        }
	$p['opt']=ser(cleanopt($opt)); // опции
//	unset($p['Date']); // нахуй, у нас дата будет своя, это же автопостинг

    $num=RE0('itemid');

 if($mode=='postevent') {
    // save new
    if(empty($p['Date'])) {
        $d=date("Y/m/d");
        $i=$d; $k=0; while($k<100 && 1==ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($d)."'".ANDCME(),"_l",0)) $d=$i.'_'.(++$k);
        $p['Date']=$d;
    }
    $t=getmaketime($d);
    $p['DateUpdate']=time();
    $p['DateDate']=$t[0];
    $p['DateDatetime']=$t[1];

// print_r($p);

    if(!empty($num) || false===($num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($p['Date'])."'".ANDCME(),'_l'))) {
	msq_add('dnevnik_zapisi',arae($p)); if($msqe) erflat('MySQL: '.$msqe);
	$num=msq_id();
	if($tag!='') tags_save($tag); // и тэги дописать
	die("itemid\n".$num."\n"."url\n".getlink($p['Date'])."\n"."success\nOK\n");
    } else {
	$mode='editevent';
    }


 }
 if($mode=='editevent') { if(empty($num)) erflat('itemid=0');

    if(false==($p0=ms("SELECT `Date`,`Body` FROM `dnevnik_zapisi` WHERE `num`='".$num."'".ANDCME(),'_1'))) erflat("issue #".$num." not exist");
    if(!strstr($p0['Body'],'{_NO:autopost:FLAT:')) erflat("This page did't loaded by FLAT");

    if($p['Header'].$p['Body']=='') { // delete
	erflat('DELETE');
	if(!zametka_del($num)) erflat('Delete Unknown Error');
	die("success\nOK\n");
    }

    $u=msq_update('dnevnik_zapisi',arae($p),"WHERE `num`='".$num."'".ANDCME()); if($msqe) erflat('MySQL: '.$msqe);
    if($tag!='') tags_save($tag); // и тэги дописать
    die("itemid\n".$num."\n"."url\n".getlink($p0['Date'])."\n"."success\nOK\n");
 }
 }

function D00($i,$n=2,$name) {
    if(strlen($i) > $n|| (1*$i==0 && $i!=1&$i && $i!=str_repeat('0',$n) ) ) erflat('Error Date '.$name);
    return sprintf("%0".intval($n)."d",$i);
}

function ANDCME($i="AND") { return " ".$i." `acn`=".$GLOBALS['acn']; }

?>