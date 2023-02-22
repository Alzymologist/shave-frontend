<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// robots.txt

// if($GLOBALS['unic']==27658) dier($GLOBALS);

ob_clean();
header('Content-Type: text/plain; charset=UTF-8');
header('Connection: close');

// Disallow: /".$GLOBALS['blogdir']."imbload
// Disallow: /".$GLOBALS['blogdir']."friends
// Disallow: /".$GLOBALS['blogdir']."stat


$s="User-agent: *
Host: ".$GLOBALS['_SERVER']['HTTP_HOST']."
Disallow: /".$GLOBALS['blogdir']."admlogin$

Disallow: /".$GLOBALS['blogdir']."login$
Disallow: /".$GLOBALS['blogdir']."login?

Disallow: /".$GLOBALS['blogdir']."upload$
Disallow: /".$GLOBALS['blogdir']."upload?

Disallow: /".$GLOBALS['blogdir']."backup$
Disallow: /".$GLOBALS['blogdir']."backup?

Disallow: /".$GLOBALS['blogdir']."comm$
Disallow: /".$GLOBALS['blogdir']."comm?
Disallow: /".$GLOBALS['blogdir']."comms$
Disallow: /".$GLOBALS['blogdir']."comms?
Disallow: /".$GLOBALS['blogdir']."comment$
Disallow: /".$GLOBALS['blogdir']."comment?
Disallow: /".$GLOBALS['blogdir']."comments$
Disallow: /".$GLOBALS['blogdir']."comments?

Disallow: /".$GLOBALS['blogdir']."bot$
Disallow: /".$GLOBALS['blogdir']."bot?
Disallow: /".$GLOBALS['blogdir']."bot/

Disallow: /".$GLOBALS['blogdir']."pravki$
Disallow: /".$GLOBALS['blogdir']."pravki?

Disallow: /".$GLOBALS['blogdir']."*?hash=*&before=*

Disallow: /".$GLOBALS['blogdir']."tmp/
Disallow: /".$GLOBALS['blogdir']."ajax/
Disallow: /".$GLOBALS['blogdir']."userdata/
Disallow: /".$GLOBALS['blogdir']."design/
Disallow: /".$GLOBALS['blogdir']."extended/
Disallow: /".$GLOBALS['blogdir']."hidden/
Disallow: /".$GLOBALS['blogdir']."include_sys/
Disallow: /".$GLOBALS['blogdir']."log/
Disallow: /".$GLOBALS['blogdir']."module/
Disallow: /".$GLOBALS['blogdir']."site_mod/
Disallow: /".$GLOBALS['blogdir']."site_module/
Disallow: /".$GLOBALS['blogdir']."template/
Disallow: /".$GLOBALS['blogdir']."user/
";

if($GLOBALS['acc']!='') $s.="sitemap: ".$GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO']."://".$GLOBALS['_SERVER']['HTTP_HOST']."/sitemap.xml\n";
elseif($GLOBALS['mnogouser']) {
    $s.="sitemap: ".$GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO']."://".$GLOBALS['_SERVER']['HTTP_HOST']."/".$GLOBALS['blogdir']."sitemap.xml\n";
    $jj=ms("SELECT `acc` from `jur`","_a1");
    foreach($jj as $a) {
	$f1=substr($a,0,1); $fn=substr($a,-1,1); if($f1=='-'||$f1=='_'||$fn=='-'||$fn=='_') continue;
	$s.="sitemap: ".$GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO']."://".$a.".".$GLOBALS['_SERVER']['HTTP_HOST']."/".$GLOBALS['blogdir']."sitemap.xml\n";
    }
}

header('Content-Length: '.strlen($s));
die($s);

?>