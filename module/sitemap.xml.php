<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// sitemap заметок дневника

ob_clean();
header("Content-Type: text/xml; charset=UTF-8");
// header("Content-Type: text/plain; charset=UTF-8");
header('Connection: close');

$s=''; $time=time();

if(!$GLOBALS['mnogouser'] || $GLOBALS['acc']!='') {
    $pp=ms("SELECT `Date`,`DateUpdate` FROM `dnevnik_zapisi` ".WHERE("`Access`='all'").ANDC()." ORDER BY `DateUpdate` DESC","_a");
    if(sizeof($pp)) {
        $tp=68400*60;
	$max_pri=0;
	$max_upd=0;
        foreach($pp as $p) {
		$t=$time-$p['DateUpdate'];
		$p['priority']=( $t >= $tp ? '0.0' : sprintf("%01.1f",(1-(1/$tp)*$t)) );
		$s.=elemdat($p);
		$max_pri=max($max_pri,$p['priority']);
		$max_upd=max($max_upd,$p['DateUpdate']);
        }
        $s.=elemdat(array( 'Date' => 'contents', 'DateUpdate' => $max_upd, 'priority'=>($max_pri?$max_pri:'0.0') ) );
    }

} else {

    $now=$time-86400*14;
    $jj=false; if($GLOBALS['mnogouser']) $jj=ms("SELECT `acn`,`acc` from `jur`","_a"); if($jj===false) $jj=array(0=>array('acn'=>0,'acc'=>''));
    foreach($jj as $j) {
	$pp=ms("SELECT `Date`,`DateUpdate` FROM `dnevnik_zapisi` WHERE `acn`=".intval($j['acn'])." AND `Access`='all' AND `DateUpdate`>".intval($now)." ORDER BY `DateUpdate` DESC","_a");
	if(!sizeof($pp)) continue;
	$s.=elemdat(array( 'Date' => 'contents', 'DateUpdate' => $pp[0]['DateUpdate'] ), $j['acn'], $j['acc'] );
	foreach($pp as $p) $s.=elemdat($p, $j['acn'], $j['acc'] );
    }

}

$s='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

'.$s.'
</urlset>';

header('Content-Length: '.strlen($s));
die($s);


function elemdat($ara,$acn=false,$acc=false) {

return '<url>
    <loc>'.getlink($ara['Date'],$acn,$acc).'</loc>
    <lastmod>'.date("Y-m-d",$ara['DateUpdate']).'</lastmod>
'.(empty($ara['priority'])?'':'    <priority>'.$ara['priority'].'</priority>')
.'</url>
';

}

?>