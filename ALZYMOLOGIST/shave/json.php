<?php

$admin_logins=array("lleo","test1");

include "../../config.php";
$GLOBALS['workdir']=$GLOBALS['filehost'].'ALZYMOLOGIST/shave/';
$GLOBALS['msq_charset']='utf8';
include $include_sys."_msq.php";


function logi($s) { $f=$GLOBALS['workdir'].'log.txt'; $l=fopen($f,"a+"); fputs($l,$s); fclose($l); chmod($f,0666); }

// logi("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n=================== Start");







header("Content-type: application/json; charset=utf-8");
// header("Content-type: text/plain; charset=utf-8");

$j=file_get_contents('php://input');
$j=(array)json_decode($j);

// die(print_r($j,1));

if(isset($_GET['action'])) $action=$_GET['action'];
else $action=$j['action'];

function jdier($J) { if(gettype($J)!='array') $J=array();


$J['act']='dier'; jie($J); } // dier over JSON
function jidie($s) { jie(array('act'=>'idie','s'=>$s)); } // dier over JSON

function jie($J) { // JSON
    if(gettype($J)!='array') $J=array('response'=>$J);
    if($GLOBALS['msqe']!='') $J=array_merge($J,array('error'=>'mysql','message'=>$GLOBALS['msqe']));
    $J=array_merge($J,array('action'=>$GLOBALS['action']));
    die(json_encode($J));
    // if($DIE) exit; fastcgi_finish_request();
}

function ejie($J) { // JSON Error
    if(gettype($J)!='array') jie(array('error'=>$J));
    if(!isset($J['error'])) $J['error']='unknown';
    jie($J);
}

if($action=='comment_list') {
    $DOI=$j['DOI'];

    if(preg_match("/^\d+$/s",$DOI)) $id=intval($DOI);
    else $id=intval(ms("SELECT `id` FROM `shave-items` WHERE `blake3DOI`='".e(blake3DOI($DOI))."' LIMIT 1","_l"));

    if(!$id) return 'Error D01';
    $ppid=ms("SELECT COUNT(*) FROM `shave-tags` WHERE `toid`=".intval($id),'_a1');

    $pp=ms("SELECT * FROM `shave-items` WHERE `uid`!=0 AND `uid` IN (".implode(',',$ppid).")",'_a1');
// `DOI`,`Date`,`author`,`about`,`uid`,`raw`
    $au=array();
    $a=array(); foreach($pp as $p) {
	$aid=$p['author'];
	if(!isset($au[$aid])) $au[$aid]=ms("SELECT `login` FROM `shave_login` WHERE `uid`=".intval($aid),'_l');
	if(empty($au[$aid])) $au[$aid]='Unknown';
	$a[]=array('author'=>$au[$aid],'text'=>$p['about'],'time'=>date("Y-m-d H:i",$p['Date']));
    }
    die(json_encode($a));
}

//          _                _
//         | |    ___   __ _(_)_ __
//         | |   / _ \ / _` | | '_ \
//         | |__| (_) | (_| | | | | |
//         |_____\___/ \__, |_|_| |_|
//                     |___/

function isLoginer($cookie,$mode='login') { $c=isLogin($cookie,$mode); if($c!==false) return $c; ejie('login error'); }

function isLogin($cookie,$mode='login') {
    if(!strstr($cookie,'-')) return false;
    list($uid,$random,)=explode('-',$cookie,3);
    if($cookie!=cookieHash($uid,$random)) return false;
    if($mode=='login') return ms("SELECT `login` FROM `shave_login` WHERE `uid`=".intval($uid),'_l');
    return $uid;
}

function passHash($login,$password){ return blake3($login.$password.'i prosto sosiski',16); }
function blake3DOI($DOI) { return blake3($DOI,16); }

function cookieHash($uid,$random=false){
    if($random===false) $random=rand(0,99999999);
    return $uid.'-'.$random.'-'.blake3($uid.$random.'i nafig sosiski',16);
}

function cookieTest($c){ // test cc
    list($uid,$random,)=explode('-',$c,3);
    return ($c==cookieHash($uid,$random));
}

function login_uid($login,$password) {
    $hash=passHash($login,$password);
    return intval(ms("SELECT `uid` FROM `shave_login` WHERE `login`='".e($login)."' AND `password`='".e($hash)."'",'_l',0));
}


if($action=='listLogin') {
    $pp=ms("SELECT * FROM `shave_login`",'_a',0);
    jie($pp);
}

//          _____     _                _
//         |  ___| __(_) ___ _ __   __| |___
//         | |_ | '__| |/ _ \ '_ \ / _` / __|
//         |  _|| |  | |  __/ | | | (_| \__ \
//         |_|  |_|  |_|\___|_| |_|\__,_|___/
//


if($action=='listFriends') {
    $uid=isLoginer($j['cookie'],'uid');
    $pp=ms("SELECT `friend` FROM `shave_friends` WHERE `uid`='".intval($uid)."'",'_a1');
    if(sizeof($pp)) $pp=ms("SELECT `login`,`uid` FROM `shave_login` WHERE `uid` IN (".implode(',',$pp).")",'_a');
    jie($pp);
}

if($action=='setFriend') {
    $uid=isLoginer($j['cookie'],'uid');
    // What is a Friend you does mean?
    $fuid=0; if(isset($j['uid'])) $fuid=intval($j['uid']);
    elseif(isset($j['login'])) $fuid=intval(ms("SELECT `uid` FROM `shave_login` WHERE `login`='".e($j['login'])."'",'_l'));
    if(!$fuid) ejie("Friend=0");

    $value=intval($j['value']);
    if($value) {
	if(!intval(ms("SELECT COUNT(*) FROM `shave_friends` WHERE `uid`=".intval($uid)." AND `friend`=".intval($fuid),"_l",0))
	) msq("INSERT INTO `shave_friends` SET `uid`=".intval($uid).",`friend`=".intval($fuid) );
    }
    else msq("DELETE FROM `shave_friends` WHERE `uid`=".intval($uid)." AND `friend`=".intval($fuid) );
    jie('OK');
}

if($action=='FindLogin') {
    $txt=$j['txt'];
    if($txt=='') $pp=ms("SELECT `login` FROM `shave_login`",'_a1');
    else $pp=ms("SELECT `login` FROM `shave_login` WHERE `login` LIKE '".e($txt)."%'",'_a1');
    jie(implode("\n",$pp));
}

if($action=='newLogin') {
    $login=$j['login']; if(empty($login)) ejie('login empty');
    $password=$j['password']; if(empty($password)) ejie('password empty');
     $uid=login_uid($login,$password);
    if($uid) ejie('Login already exist');
    msq_add('shave_login',arae(array('login'=>$login,'password'=>passHash($login,$password))));
    $uid=intval(msq_id());
    if(!$uid) ejie('mysql no uid');
    jie(array(
	'login'=>$login,
	'uid'=>$uid,
	'cookie'=>cookieHash($uid)
    ));
}

if($action=='Login') {
    $login=$j['login'];
    $password=$j['password'];
    $uid=login_uid($login,$password);
    if(!$uid) ejie(404);
    jie(array(
	'login'=>$login,
	'uid'=>$uid,
	'cookie'=>cookieHash($uid)
    ));
}

//          _____ _           _
//         |  ___(_)_ __   __| |
//         | |_  | | '_ \ / _` |
//         |  _| | | | | | (_| |
//         |_|   |_|_| |_|\__,_|
//


function getItems($uids,$query,$mode='id') {
    if(!sizeof($uids)) jie(array()); // no items

    $PERPAGE=20;
    $offset=(isset($GLOBALS['j']['offset'])?intval($GLOBALS['j']['offset']):0);
    $msq="FROM `shave-items` WHERE `".e($mode)."` IN (".e(implode(',',$uids)).")";
    $total=intval(ms("SELECT COUNT(*) ".$msq,'_l'));
    $pp=ms("SELECT * ".$msq." ORDER BY `Date` DESC LIMIT ".intval($offset).",".intval($PERPAGE),'_a');

    if($pp===false) jie(array()); // My Friends have no posts since last $time sec
    $pp['total']=$total;
    $pp['query']=$query;
    $pp['start']=$offset;
    $pp['perpage']=$PERPAGE;

    jie($pp);
}


if($action=='Find') {
    $txt=trim($j['txt'],"\r\n\t ");
    if($txt=='') ejie("Empty txt");

    $offset=(isset($j['offset'])?intval($j['offset']):0);

    if($txt=='MyFriends') {
	$uids=get_friends_uids();
	if(!sizeof($uids)) jie(array());
	$PERPAGE=20;
        getItems($uids,$txt,'uid',array('LIKES'=>$pp)); // return;
    }

    if($txt=='MyLikes') {
	$uids=get_friends_uids();
	if(!sizeof($uids)) jie(array());
	$PERPAGE=20;
	$sql="SELECT L.`id`,L.`uid`,L.`like`,L.`Date`, 'like' as `MODE`,
S.`DOI`,S.`author`,S.`about`
FROM `shave-like` as L
LEFT JOIN `shave-items` as S
ON S.`id`=L.`id`
WHERE L.`uid` IN (".e(implode(',',$uids)).")
ORDER BY L.`Date` DESC LIMIT ".intval($offset).",".intval($PERPAGE);
	$pp=ms($sql,'_a');
        if($pp===false) jie(array()); // No Friend's likes
	// foreach($pp as $n=>$d) { $pp[$n]=count_likes($d); $pp[$n]=count_FROM_TO($d); } // добавить лайки и комменты
	$pp['total']=sizeof($pp);
	$pp['query']=$txt;
	$pp['start']=$offset;
	$pp['perpage']=$PERPAGE;
	jie($pp);
    }

// eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
    // NEW ENGINE
    $blake3txt=blake3DOI($txt);

  // *** поиск DOI *** //

    // сперва ищем в нашей базе как DOI
    if(!empty($D=ms("SELECT * FROM `shave-items` WHERE `blake3DOI`='".e($blake3txt)."'","_1"))) {
	$D=count_likes($D);
	$D=count_FROM_TO($D);
        jie(array(0=>$D, "total"=>1, "perpage"=>1, "query"=>"DOI", "start"=>0));
    }
    // в базе DOI не нашли, но вдруг это и правда был DOI? Тогда просто скачать его через crossref.org например.
    if( preg_match("/^(https*\:\/\/[^ ]+?\/|)(\d+\.\d+\/[^ \/]+)$/s",$txt,$m) ) { $txt=$m[2];
        $DD=crossref_curl('https://api.crossref.org/works/'.urlencode($txt).'?mailto=lleo@lleo.me');
	$DD=parseDOIs($DD);
	if(isset($DD[0])) { // добавить
	    $DD[0]=count_likes($DD[0]);
	    $DD[0]=count_FROM_TO($DD[0]);
	}
        jie($D);
    }

  // *** поиск text *** //

    // затем ищем в нашей базе по совпадению
    $sql="FROM `shave-items` WHERE `author` LIKE '%".e($txt)."%' OR `about` LIKE '%".e($txt)."%'  OR `raw` LIKE '%".e($txt)."%'";
    if($total=intval(ms("SELECT COUNT(*) ".$sql,"_l"))) {
        $D=ms("SELECT * ".$sql." ORDER BY `id` LIMIT ".$offset.",20","_a");
	foreach($D as $n=>$d) { $D[$n]=count_likes($d); $d[$n]=count_FROM_TO($d); } // добавить лайки и комменты
        jie(array_merge($D,array("total"=>$total, "perpage"=>sizeof($D), "query"=>$txt, "start"=>$offset)));
    } else { // не найдено в нашей базе
	jie(array("total"=>0, "perpage"=>0, "query"=>$txt, "start"=>$offset));
    }

    // и в любом случае поискать ещё на внешних ресурсах
    // и тут уж нечего добавлять лайки и комменты - их пока нет
    // extended_ask($txt,$blake3txt,$offset);
}



//          _       _   _                     ____    _         _   _   _
//         | |     (_) | | __   ___          |  _ \  (_)  ___  | | (_) | | __   ___
//         | |     | | | |/ /  / _ \  _____  | | | | | | / __| | | | | | |/ /  / _ \
//         | |___  | | |   <  |  __/ |_____| | |_| | | | \__ \ | | | | |   <  |  __/
//         |_____| |_| |_|\_\  \___|         |____/  |_| |___/ |_| |_| |_|\_\  \___|
//

if($action=='Like') {
    $uid=isLoginer($j['cookie'],'uid'); // User id
    $pid=intval($j['pid']); if(!$pid) ejie('pid=0'); // Item id
    $like=(intval($j['like'])?'like':'dislike');

    // Если оценивает свой
    if( $uid != ms("SELECT `uid` FROM `shave-items` WHERE `id`=".e($pid),"_l",0) ) {
	if(false===($l=ms("SELECT `like` FROM `shave-like` WHERE `id`=".intval($pid)." AND `uid`=".intval($uid),"_l",0))) { // Голосовал ли сей?
	    // не голосовал - добавить голос
    	    msq("INSERT INTO `shave-like` SET `id`=".intval($pid).",`uid`=".intval($uid).",`like`='".e($like)."',`Date`=".time() );
        } else {
	    // уже голосовал - поменять мнение если поменялось, иначе удалить
    	    if($l != $like) msq("UPDATE `shave-like` SET `like`='".e($like)."',`Date`=".time()." WHERE `id`=".intval($pid)." AND `uid`=".intval($uid) );
	    else msq("DELETE FROM `shave-like` WHERE `id`=".intval($pid)." AND `uid`=".intval($uid) );
	}
    }
    // Теперь все голоса подсчитать
    $p=count_likes(array('id'=>$pid));
    jie($p);
}

if($action=='LikeList') {
    $pid=intval($j['pid']); if(!$pid) ejie('pid=0');
    $Like = ms("SELECT `uid` FROM `shave-like` WHERE `id`=".intval($pid)." AND `like`='like'","_a1",0);
    $LikeList = (empty($Like) ? array() : ms("SELECT `login` FROM `shave_login` WHERE `uid` IN (".implode(",",$Like).")","_a1") );
    $Dislike = ms("SELECT `uid` FROM `shave-like` WHERE `id`=".intval($pid)." AND `like`='dislike'","_a1",0);
    $DislikeList = (empty($Dislike)? array() : ms("SELECT `login` FROM `shave_login` WHERE `uid` IN (".implode(",",$Dislike).")","_a1") );
    jie(array(
	    'Like'=>sizeof($Like),
	    'Dislike'=>sizeof($Dislike),
	    'LikeList'=>$LikeList,
	    'DislikeList'=>$DislikeList
    ));
}

//           ____                                     _
//          / ___|___  _ __ ___  _ __ ___   ___ _ __ | |_
//         | |   / _ \| '_ ` _ \| '_ ` _ \ / _ \ '_ \| __|
//         | |__| (_) | | | | | | | | | | |  __/ | | | |_
//          \____\___/|_| |_| |_|_| |_| |_|\___|_| |_|\__|
//

if($action=='CommDel') {
    $author=isLoginer($j['cookie'],'login');
    if(!in_array($author,$admin_logins)) ejie('Admin only');
    // pid exist?
    $id=intval($j['pid']); // номер коммента, который удаляем
    if(1!=ms("SELECT COUNT(*) FROM `shave-items` WHERE `id`=".intval($id),'_l',0)) ejie('Not exist');
    // Is any comments? Если к этому комменту есть другие комменты - пошлем удалить сперва их.
    if( $l=intval(ms("SELECT COUNT(*) FROM `shave-tags` WHERE `toid`=".intval($id),'_l',0)) ) ejie('Delete '.$l.' subcomments first');
    // Is a comment - Delete from tags
    // заметки toid, которые комментировал удаляемый id
    $p=ms("SELECT `toid` FROM `shave-tags` WHERE `id`=".intval($id),'_a1',0);
    if(sizeof($p)) {
	msq("DELETE FROM `shave-tags` WHERE `id`=".intval($id));
	// foreach($p as $i) recount_TO($i); // пересчитываем число комментов для заметок, на которые был этот коммент $id (их может быть более 1)
    }
    // Delete from base
    msq("DELETE FROM `shave-items` WHERE `id`=".intval($id));
    jie('OK');
}


if($action=='newComment') {
    $author=isLoginer($j['cookie'],'login');
    $uid=isLoginer($j['cookie'],'uid');
    $text=$j['text']; // $text=wu($ara['text']);
    $toid=intval($j['id']); if(!$toid) ejie('id=0');
    jie( New_engine($text,$author,$toid,$uid) );
}


if($action=='CommentsTo') {
    // msq("TRUNCATE `shave-tags`");
//    uid = 1   toid = 1    id = 724
//    $ii=ms("SELECT * FROM `shave-tags`","_a"); jdier($ii);
//        $ii=ms("SELECT * FROM `shave-items` WHERE `raw`=''","_a");    jdier($ii);
//        $ii=ms("SELECT * FROM `shave-items`","_a");    jdier($ii);
    //    msq("DELETE FROM `shave-items` WHERE id=261");
    $pid=intval($j['pid']); if(!$pid) ejie('pid=0');
    $ii=ms("SELECT `id` FROM `shave-tags` WHERE `toid`=".intval($pid),'_a1',0);
    getItems($ii,'','id');
}

if($action=='CommentsFrom') {
    $pid=intval($j['pid']); if(!$pid) ejie('pid=0');
    $ii=ms("SELECT `toid` FROM `shave-tags` WHERE `id`=".intval($pid),'_a1',0);
    getItems($ii,'','id');
}



// Всякий тестовый мусор

if($action=='Test_recount_all') {

// eeeeeeeeeeeeeeeeeeeeeeee
/*
    msq("UPDATE `shave-items` SET `commTO`=0,`commFROM`=0");
    $pp=ms("SELECT DISTINCT `id` FROM `shave-tags`",'_a1',0);
    foreach($pp as $i) recount_FROM($i);
    $pp=ms("SELECT DISTINCT `toid` FROM `shave-tags`",'_a1',0);
    foreach($pp as $i) recount_TO($i);
*/
    jie('OK');
}

/*
if($action=='CommentTest') {
    $pp=ms("SELECT * FROM `shave-items` WHERE `raw`=''",'_a',0);

    foreach($pp as $n=>$p) {
        $pp[$n]['commFROM']=intval(ms("SELECT COUNT(*) FROM `shave-tags` WHERE `id`=".intval($p['id']),'_l'));
        $pp[$n]['commTO']=intval(ms("SELECT COUNT(*) FROM `shave-tags` WHERE `toid`=".intval($p['id']),'_l'));
        $pp[$n]['toid']=implode(',',ms("SELECT `id` FROM `shave-tags` WHERE `toid`=".intval($p['id']),'_a1'));
        $pp[$n]['fromid']=implode(',',ms("SELECT `toid` FROM `shave-tags` WHERE `id`=".intval($p['id']),'_a1'));
    }

    jie($pp);
}

if($action=='testLogin') {
    msq("TRUNCATE TABLE shave_login");
    // msq_add('shave_login',array('login'=>'BRED','password'=>"iobliko"));
    // $pp=ms("SELECT * FROM shave_login",'_a',0);
    jie('OK');
}

if($action=='Toids') {
    $ii=$j['pid']; //if(empty($ii)) ejie('Empty toids');
    $ii=ms("SELECT `id` FROM `shave-tags` WHERE `toid`=".intval($pid),'_a1');
ejie($ii);
    getItems($ii,'','id');
}

*/













jdier($j);





//          ____                       ____   ___ ___
//         |  _ \ __ _ _ __ ___  ___  |  _ \ / _ \_ _|___
//         | |_) / _` | '__/ __|/ _ \ | | | | | | | |/ __|
//         |  __/ (_| | |  \__ \  __/ | |_| | |_| | |\__ \
//         |_|   \__,_|_|  |___/\___| |____/ \___/___|___/
//

function grepFam($l) {
    if( !isset($l['family']) && !isset($l['given']) ) return false;
    $s = ( isset($l['family']) ? $l['family'] : '' );
    $s.= ( isset($l['given'])  ? ($s==''?'':' ').$l['given'] : '' );
    return ($s==''?false:$s);
}

function parseDOIs($j) {
    if( 'ok' != $j['status'] || !isset($j['message-type']) ) { echo "Error"; return; }

    if( $j['message-type'] == 'work-list' ) {
        $j=$j['message'];
	$dois=array(
    	    'total'=>intval($j['total-results']),
    	    'perpage'=>intval($j['items-per-page']),
    	    'query'=>$j['query']['search-terms'],
    	    'start'=>intval($j['query']['start-index'])
	);
	$j=$j['items'];
    } elseif( $j['message-type'] == 'work' ) {
        $j=array($j['message']);
	$dois=array( 'total'=>1, 'perpage'=>1, 'query'=>"DOI", 'start'=>0 );
    } else return false;

    foreach($j as $e) { $D=array();
      // AUTHOR
        $c=array();
        if(isset($e['chair'])) foreach($e['chair'] as $l) if(false!=($fx=grepFam($l))) $c[]=$fx;
        if(isset($e['author'])) foreach($e['author'] as $l) if(false!=($fx=grepFam($l))) $c[]=$fx;
        $D['author']=implode(', ',$c);

      // TITLE
        $D['about']=(gettype($e['title'])=='array'?$e['title'][0]:$e['title']);
        if(isset($e['subtitle'])) $D['about'].=". ".(gettype($e['subtitle'])=='array'?$e['subtitle'][0]:$e['subtitle']);

//        if(isset($e['container-title'])) $D['about'].=". ".(gettype($e['container-title'])=='array'?$e['container-title'][0]:$e['container-title']);

        if(isset($e['abstract'])) $D['about'].=". ".preg_replace("/\s*<[^>]+>\s*"."/s",'',$e['abstract']);

        /*
        $m=explode(' ',"original-title container-title subject short-container-title");
        foreach($m as $l) {
	if(!isset($e[$l])) continue;
	if(gettype($e[$l])=='array') $e[$l]=$e[$l][0];
	$D[$l]=(gettype($e[$l])=='array'?$e[$l][0]:$e[$l]);
        }
        */
        // LINK
	// $D['URL']=$e['URL'];
        // Language
        // if(isset($e['language'])) $D['language']=$e['language'];

        // DATE
        // $olddat='';
        $m=explode(' ',"published issued deposited created indexed published-online published-print accepted posted");
        $tm=array('y'=>9999,'m'=>99,'d'=>99); $tname='';
        foreach($m as $l) {
	if(isset($e[$l])) foreach($e[$l]['date-parts'] as $ii=>$t) {
	    $t=array('y'=>(isset($t[0])?$t[0]:9999),'m'=>(isset($t[1])?$t[1]:99),'d'=>(isset($t[2])?$t[2]:99));
            if(                                             $tm['y']>$t['y']   // если год больше
             || ( $t['y']==$tm['y']                      && $tm['m']>$t['m'] ) // или год равен и месяц больше
             || ( $t['y']==$tm['y'] && $t['m']==$tm['m'] && $tm['d']>$t['d'] ) // или год и месяц равны и числo больше
            ) { $tm=$t; $tname=$l; }
	    //   olddat+="<div class='ss br'>"+l+": "+ddd(t)+" min:["+ddd(tm)+" "+tname+"]</div>";
          }
        }
	$D['Date']=$tm['y']
	    .( $tm['m']==99 ? '' : '-'.sprintf("%02d",$tm['m'])
		    .( $tm['d']==99 ? '' : '-'.sprintf("%02d",$tm['d']) )
	    );

        $D['Date_name']=$tname;

      // DOI
      $D['DOI']=$e['DOI'];
      $D['blake3DOI']=blake3DOI($D['DOI']);
      $D['raw']=json_encode($e);
      $D['id']=false;

    $D['id']=intval(ms("SELECT `id` FROM `shave-items` WHERE `blake3DOI`='".e($D['blake3DOI'])."'","_l"));
    if(!$D['id']) {
        msq("INSERT INTO `shave-items` SET
	    `DOI`='".e($D['DOI'])."',
	    `blake3DOI`='".e($D['blake3DOI'])."',
	    `Date`='".date($D['Date'])."',
	    `Date_created`=".time().",
	    `author`='".e($D['author'])."',
	    `about`='".e($D['about'])."',
	    `uid`=0,
	    `raw`='".e($D['raw'])."'
        ");
        $D['id']=msq_id();

	// FAKE! FAKE! FAKE!
	// if($D['id']) { FakeComment_add($D['id']); }
    }

      $dois[]=$D;
    }

    return $dois;
}







function crossref_curl($url) {
    if(!strstr($url,'?')) $url.='?mailto=lleo@lleo.me';
    $out=$GLOBALS['workdir'].'cache/'.blake3($url,10).'.json';
    if(is_file($out)) $html=file_get_contents($out);
    else {
        $addtime=120;
        $GLOBALS['starttime']=$GLOBALS['starttime']+$addtime;
        set_time_limit($addtime);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['User-Agent: TestHomePage/1.0 (https://lleo.me; mailto:lleo@lleo.me) DeepInScience']);
        $html = curl_exec($ch);
        curl_close($ch);
        file_put_contents($out,$html); chmod($out,0666);
    }
    $JJ=json_decode($html,true);
    if(!sizeof($JJ)) return false;
    return $JJ;
}



/*

//         __  __ ____
//         \ \/ /|_  /
//          >  <  / /
//         /_/\_\/___|
//

function rang($JJ) {
    if(!($uid=isLogin($GLOBALS['j']['cookie'],'uid'))) return $JJ; // Show all for non-login users

    $o=array();

    // Select all id: $IDS
    $IDS=array();
    $i=0; while(isset($JJ[$i])) {
	if( $JJ[$i]['commTO'] )
	{
	    $id=$JJ[$i]['id'];
 	    if(!$id) {
	        $id=ms("SELECT `id` FROM `shave-items` WHERE `blake3DOI`='".e(blake3DOI($JJ[$i]['DOI']))."'","_l");
	        if($id===false) $id='false';
	        $o[]="$i) id=0 for DOI: [".$JJ[$i]['DOI']."] = [$id]";
		$JJ[$i]['id']=$id;
	    }
	    $IDS[]=intval($id);
	}
	$i++;
    }
    $JJ['item_total']=$i;
//    $JJ['IDS']="commented item: (".implode(',',$IDS).")";
    if(empty($IDS)) return array();

    // Select all comments id: $CID
    $CID=ms("SELECT `id` FROM `shave-items`
	WHERE `id` IN (".implode(',',$IDS).")
	AND `id` IN ( SELECT `id` FROM `shave-tags`
	    WHERE `uid` IN (SELECT `friend` FROM `shave_friends` WHERE `uid`=".intval($uid).")
	)
    )",'_a1');
//    jidie(print_r($CID,1));
    $JJ['CID']="result: ".print_r($CID,1);

    // Del elements:
    $i=0; $k=0; while(isset($JJ[$i])) {
	if(!in_array($JJ[$i]['id'],$CID)) unset($JJ[$i]);
	else $k++;
	$i++;
    }
    $JJ['item_count']=$k;

    $JJ=array_merge(array('o'=>"<p>---------------------<br>".implode('<br>',$o)."<br>----------------"),$JJ);
//    jidie(print_r($JJ,1));
//    jdier($j);
    return $JJ;
}
*/




//          _____     _
//         |  ___|_ _| | _____
//         | |_ / _` | |/ / _ \
//         |  _| (_| |   <  __/
//         |_|  \__,_|_|\_\___|
//

function FakeComment_add($id) {
    if(rand(0,100)<10) FakeComm($id,'nudnik');
    if(rand(0,100)<10) FakeComm($id,'mikel');
    if(rand(0,100)<10) FakeComm($id,'score');
}

function FakeComm($id=0,$fn='') {
    if(!$id) return;
    if($fn=='nudnik') $login="Объединенный институт проблем. Минск.";
    else if($fn=='mikel') $login="Father O`Mikel"; // $fn='fake_mihail';
    else if($fn=='score') $login="Семен Львович Бакх, доцент";
    else return;
    $fn='fake_'.$fn;

    include_once($GLOBALS['workdir'].'fake/'.$fn.'.php');
    $s=call_user_func($fn);
    $a=New_engine($s,$login,$id);
    // jdier($a);
}

function New_engine($text='',$login='',$toid=0,$uid=0) {

    $uid=intval($uid); if(!$uid) {
	if(!($uid=ms("SELECT `uid` FROM `shave_login` WHERE `login`='".e($login)."'",'_l')))
	return array('error'=>'uid','message'=>'uid not found');
    }

    if(empty($login)) {
	if(!($login=ms("SELECT `login` FROM `shave_login` WHERE `uid`=".intval($uid),'_l')))
	return array('error'=>'login','message'=>'login not found');
    }

    $text=str_replace("\r",'',$text); // hate!!!
    $text=trim($text,"\n\t ");

    // Уже есть такая статья?
    if(ms("SELECT COUNT(*) FROM `shave-items` WHERE `uid`=".intval($toid)." AND `author`='".e($author)."' AND `about`='".e($text)."'",'_l',0)
    ) return array('error'=>'exist','message'=>'Comment exist');

    $t=time();
    msq("INSERT INTO `shave-items` SET
	    `DOI`='',
	    `blake3DOI`='".blake3DOI( "answer_to:".intval($toid)." date_created:".$t." author_uid:".e($uid) )."',
	    `Date`=".$t.",
	    `Date_created`=".$t.",
	    `author`='".e($login)."',
	    `about`='".e($text)."',
	    `uid`=".intval($uid).",
	    `raw`=''
    ");
    $id=intval(msq_id()); if(!$id) return array('error'=>'mysql','message'=>'Comment id=0');

    if($toid) { // если я был ответом на что-то
        // и пометить линк
        msq("INSERT INTO `shave-tags` SET `id`=".intval($id).",`toid`=".intval($toid).",`uid`='".intval($uid)."'");
	// recount_TO($toid); // пересчитываем число комментов у этого, кому ответили
	// recount_FROM($id); // и поправить себя, что у меня есть вышестоящий коммент (1 штука, но Саша говорил, что может быть и много)
    }
    return array('id'=>$id,'toid'=>$toid);
}















// eeeeeeeeeeeeeeeeeeeeeeeeeeee
function count_likes($p) {
    $uids=get_friends_uids();
    if(!sizeof($uids)) $Like=$Dislike=0;
    else {
	$sql="SELECT COUNT(*) FROM `shave-like` WHERE `id`=".intval($p['id'])." AND `uid` IN (".e(implode(',',$uids)).")";
	$Like = intval(ms($sql." AND `like`='like'","_l",0));
        $Dislike = intval(ms($sql." AND `like`='dislike'","_l",0));
    }
    return array_merge($p,array('Like'=>$Like,'Dislike'=>$Dislike));
}

function count_FROM_TO($p) { // Комменты подсчитать
    $uids=get_friends_uids();
    if(!sizeof($uids)) $commTO=$commFROM=0;
    else {
	$sql="SELECT COUNT(*) FROM `shave-tags` WHERE `uid` IN (".e(implode(',',$uids)).")";
        $commTO=intval(ms($sql." AND `toid`=".intval($p['id']),'_l',0));
	$commFROM=intval(ms($sql." AND `id`=".intval($p['id']),'_l',0));
    }
    return array_merge($p,array('commTO'=>$commTO,'commFROM'=>$commFROM));
}

function get_friends_uids() { global $UID,$UIDS;
    if(isset($UIDS)) return $UIDS; // кэшируем
    if(!isset($UID)) $UID=isLoginer($GLOBALS['j']['cookie'],'uid'); // кэшируем
    return $UIDS=ms("SELECT `friend` FROM `shave_friends` WHERE `uid`='".intval($UID)."'",'_a1');
}


//          _____      _                        _     _____ _           _
//         | ____|_  _| |_ ___ _ __ _ __   __ _| |   |  ___(_)_ __   __| |
//         |  _| \ \/ / __/ _ \ '__| '_ \ / _` | |   | |_  | | '_ \ / _` |
//         | |___ >  <| ||  __/ |  | | | | (_| | |   |  _| | | | | | (_| |
//         |_____/_/\_\\__\___|_|  |_| |_|\__,_|_|   |_|   |_|_| |_|\__,_|
//

function extended_ask($txt,$blake3txt,$offset) {
    // делали ли уже этот запрос по внешним базам?
    $ts=intval(ms("SELECT `total`,`Date` FROM `shave-extsearch` WHERE `blake3search`='".e($blake3txt)."'","_1"));
    if($ts['total']===false // если не было запроса
	|| $ts['Date'] < time()-30*86400 // или был слишком давно (более 30 дней)
	|| ( $ts['total'] < 0 && $ts['Date'] < time()-60*60 ) // или идет работа, да что-то зависла (1 час)
    ) { // то сделать внешний поиск
        logi("\n --> Внешний поиск: [".$txt."]");

    /*
	// пометить в базе, что такой поиск начат (total=-1)
        msq_add_update('shave-extsearch',arae(array(
	     'blake3search' => $blake3txt, 'total' => -1, 'Date' => time()
	)),"WHERE `blake3search`='".e($blake3txt)."'");
    */
	// через crossref.org
	wget_crossref($txt,$blake3txt,$offset);
	// через чего-нибудь ещё

    }

/*
    CREATE TABLE IF NOT EXISTS `shave-extsearch` (
      `blake3search` char(32) NOT NULL COMMENT 'blake3(search text)',
      `total` int(10) NOT NULL default '0' COMMENT 'Total',
      `Date` int(10) unsigned NOT NULL default '0' COMMENT 'Date of isseu',
      PRIMARY KEY (`blake3search`),
      KEY `total` (`total`),
      KEY `Date` (`Date`)
    ) ENGINE=InnoDB COMMENT='SHAVE external search';
*/


}

function wget_crossref($txt,$blake3txt,$offset=0) {
    $kon=0; while( ++$kon < 5000 ) {
	$url='https://api.crossref.org/works?filter=has-license:true,has-full-text:true&query='.urlencode($txt).'&mailto=lleo@lleo.me&rows=20&offset='.$offset;
        logi("\n --> $kon. ищем `".$txt."` offset=".$offset."\n".$url);
	// Find
        // http://api.crossref.org/works?query=fuck&rows=2&offset=20
        // $url='https://api.crossref.org/works?filter=has-license:true,has-full-text:true&query='.urlencode($txt).'&mailto=lleo@lleo.me'; // &rows=3';
        // $url='https://api.crossref.org/works?query='.urlencode($txt).'&mailto=lleo@lleo.me&rows=5&offset='
        $JJ=crossref_curl($url);
        logi("\n --> OK");
        $JJ=parseDOIs($JJ);
        logi(print_r($JJ,1));
        logi("\n\t\t total = ".$JJ['total']
	    ."\n\t\t perpage = ".$JJ['perpage']
	    ."\n\t\t query = ".$JJ['query']
	    ."\n\t\t start = ".$JJ['start']
	);

	if(empty($JJ) || !isset($JJ['total'])) { logi("\n --> Error"); break; }
	if($JJ['total'] < 20) { logi("\n --> End"); break; }

	$offset+=20;
    }

    logi("\nDONE.\n");
    exit;
    // return $JJ;
}

?>