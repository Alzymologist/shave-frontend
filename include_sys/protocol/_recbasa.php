<?php // Протоколы внешних соцсетей


function recbasa(){

$twitter_net='twitter:lleokaganov';
$lj_net='lj:lleo-kaganov';
$fb_net='facebook:lleokaganov';
$vk_net='vk:4350243';

//    msq("DELETE FROM `socialmedias`");

    $k='0 ';

// все socialmedia
$rr=ms("SELECT * FROM `socialmedia`","_a",0);
foreach($rr as $r) {
    if($r['num']==0) continue;
    if($r['net']=='lj') $r['net']=$lj_net;
    if($r['net']=='twitter') $r['net']=$twitter_net;
msq_add("socialmedias",arae(array('acn'=>0,
    'cap_sha1'=>'',
    'num'=>$r['num'],
    'net'=>$r['net'],
    'url'=>'',
    'id'=>$r['url'],
    'type'=>'post'
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} //dier($rr,$GLOBALS['msqe']);

$k.='1 ';

// все фотки socialmedia_fotos
$rr=ms("SELECT * FROM `socialmedia_fotos`","_a",0);
foreach($rr as $r) {
    if($r['net']=='' AND $r['type']='fb_foto') $r['net']=$fb_net;
    if($r['type']=='') idie('#');
msq_add("socialmedias",arae(array('acn'=>0,
    'cap_sha1'=>$r['cap_sha1'],
    'num'=>$r['num'],
    'net'=>$r['net'],
    'url'=>$r['url'],
    'id'=>$r['id'],
    'type'=>$r['type'],
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} //dier($rr,$GLOBALS['msqe']);

$k.='2 ';

// все фотки vk
$rr=ms("SELECT * FROM `vk_fotos` WHERE `url` LIKE 'http%'","_a",0);
foreach($rr as $r) {
msq_add("socialmedias",arae(array('acn'=>0,
'cap_sha1'=>$r['cap_sha1'],
'num'=>$r['num'],
'net'=>$vk_net, // $r['net'],
'url'=>$r['url'],
'id'=>$r['id'],
'type'=>'vk_foto',
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} //dier($rr,$GLOBALS['msqe']);

$k.='3 ';

// все альбомы fb
$rr=ms("SELECT * FROM `vk_fotos` WHERE `url` LIKE 'f:%'","_a",0);
foreach($rr as $r) {
msq_add("socialmedias",arae(array('acn'=>0,
    'cap_sha1'=>$r['cap_sha1'],
    'num'=>$r['num'],
    'net'=>$fb_net, // $r['net'],
    'url'=>substr($r['url'],2),
    'id'=>$r['id'],
    'type'=>'fb_album',
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} //dier($rr,$GLOBALS['msqe']);

$k.='4 ';

// все альбомы VK
$rr=ms("SELECT * FROM `vk_fotos` WHERE `url` LIKE ':%'","_a",0);
foreach($rr as $r) {
msq_add("socialmedias",arae(array('acn'=>0,
    'cap_sha1'=>$r['cap_sha1'],
    'num'=>$r['num'],
    'net'=>$vk_net, // $r['net'],
    'url'=>substr($r['url'],1),
    'id'=>$r['id'],
    'type'=>'vk_album',
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} //dier($rr,$GLOBALS['msqe']);

$k.='5 ';

// все заметки VK
$rr=ms("SELECT * FROM `vk_fotos` WHERE `url` LIKE '#%'","_a",0);
//idie(sizeof($rr));  dier($rr,$GLOBALS['msqe']);
foreach($rr as $r) {
msq_add("socialmedias",arae(array('acn'=>0,
    'cap_sha1'=>$r['cap_sha1'],
    'num'=>$r['num'],
    'net'=>$vk_net, // $r['net'],
    'url'=>substr($r['url'],1),
    'id'=>$r['id'],
    'type'=>'vk_note',
))); //if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']." - ".$k);
} dier($rr,$GLOBALS['msqe']." - ".$k);

}
?>