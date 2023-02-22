<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// rss заметок дневника

$GLOBALS['PUBL']=1; // флаг для модулей, что материал идет во внешнюю публикацию

// if($GLOBALS['unic']==4) dier($GLOBALS['IS']);

/*
// добавить авторизацию
function rss_h($u,$ran) { return substr(sha1($u.$GLOBALS['hashrss'].$ran),4,8); }
function rss_uset($u) { if(!isset($GLOBALS['hashrss'])) return false; $ran=substr(sha1($time.rand(0,99999999).$GLOBALS['hashrss']),2,4); return $u."-".$ran."-".rss_h($u,$ran); }
function rss_ucheck($up) { if(!strstr($up,'-')||!isset($GLOBALS['hashrss'])) return 0; list($u,$ran,$p)=explode('-',$up,3); return ($p==rss_h($u,$ran)?$u:0); }

if(isset($_GET['lajax'])) { // аякс-запрос

    if(RE('a')=='getrsslink') { // для получения парольного линка RSS по запросу <a href="javascript:majax('{acc_link}rss',{a:'getrsslink'})">RSS</a>
    if($GLOBALS['admin'] && !isset($GLOBALS['hashrss'])) otprav("salert(\"Not set \$hashrss in your /config.php!<p>Please add to your <b>/config.php</b> the line with hash like this:<p><b>\$hashrss='my secret phrase blablabla';</b>\",10000)");
    $link=$GLOBALS['httphost'].'rss?r='.rss_uset($GLOBALS['unic']);
    otprav("ohelpc('rss','rss',\"Ваш аккаунт #".$GLOBALS['unic']
    .($GLOBALS['podzamok']?" и вы имеете <b>подзамочный доступ</b>.<p>Чтобы видеть подзамочные посты, ":'')
    ."для чтения RSS-ленты используйте личную ссылку:<p><a href='".h($link)."'>".h($link)."</a>\")");
    }

}
*/

$GLOBALS['rssmode']=1;

include_once $include_sys."_onetext.php"; // обработка заметки

$subst1=array("{foto_www_preview}");
$subst2=array($foto_www_preview);

$RSSZ_skip = 20;
//$RSSZ_mode = 1;

ob_clean();
header("Content-Type: text/xml; charset='".$wwwcharset."'");
$skip=intval($_GET['skip']);

/*
if(isset($_GET['r'])&&false!=($unic=rss_ucheck($_GET['r']))) {
    getis_global(($GLOBALS['unic']=$unic));
    if($GLOBALS['podzamok']) logi('rss-podzamok.log',date("Y-m-d H:i:s")." ".$GLOBALS['IS']['realname'].": ".($skip?"(skip:".$skip.") ":'').$GLOBALS['IP']." ".$GLOBALS['BRO']."\n");
}
*/

$pp=ms("SELECT `Date`,`Body`,`Header`,`DateUpdate`,
`DateDate`,
`DateDatetime`,
`Access`,`num` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0").ANDC()." ORDER BY `Date` DESC LIMIT ".$skip.",".$RSSZ_skip,"_a");

$RSS_TEMPLATE="<?xml version=\"1.0\" encoding=\"{wwwcharset}\"?>
<rss version=\"2.0\"
    xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
    xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
    xmlns:media=\"http://search.yahoo.com/mrss/\"
    xmlns:atom=\"http://www.w3.org/2005/Atom\"
    xmlns:georss=\"http://www.georss.org/georss\">
    <channel>
        <title>{#:admin_name}</title>
        <link>{#httphost}</link>
        <description>{#:admin_name}: дневник</description>
		<generator>Binoniq 3.0</generator>
        <language>ru</language>

{ITEMS}

    </channel>
</rss>"; //                 <lastBuildDate>{#lastupdate}</lastBuildDate>


//	<DateDate>{#DateDate}</DateDate>
//	<DateDatetime>{#DateDatetime}</DateDatetime>
//           <enclosure url="http://example.com/2023/07/04/pic1.jpg" type="image/jpeg"/>
//           <enclosure url="http://example.com/2023/07/04/pic2.jpg" type="image/jpeg"/>
//           <enclosure url="http://example.com/2023/07/04/video/420" type="video/x-ms-asf"/>
//           <pdalink>{#link}</pdalink>
//           <amplink>{#link}</amplink>
//           <description>{#text}</description>
//            <category>Технологии</category>
//           <media:rating scheme=\"urn:simple\">nonadult</media:rating>

// 	<lastBuildDate>{#DateUpdate}</lastBuildDate>



$ITEM_TEMPLATE="\n
        <item>
           <title>{#:Header}</title>
           <link>{#link}</link>
           <pubDate>{#DateDatetime}</pubDate>
           <author>{#:admin_name}</author>
           <guid isPermaLink='true'>{#link}</guid>{enclosure}

	<comments>{#link}</comments>
   <content:encoded>
        <![CDATA[
{text}
        ]]>
    </content:encoded>
        </item>\n";



/*

<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss">
    <channel>
        <title>Пастернак</title>
        <link>http://example.com/</link>
        <description>
Проект о фруктах и овощах. Рассказываем о том, как выращивать, готовить и правильно есть.
</description>
        <language>ru</language>



        <item>
           <title>Андроид восстановит ферму в Японии</title>
           <link>http://example.com/2023/07/04/android-happy-farmer</link>
           <pdalink>http://m.example.com/2023/07/04/android-happy-farmer</pdalink>
           <amplink>http://amp.example.com/2023/07/04/android-happy-farmer</amplink>
           <guid>2fd4e1c67a2d28fced849ee1bb76e7391b93eb12</guid>
           <pubDate>Tue, 4 Jul 2023 04:20:00 +0300</pubDate>
           <media:rating scheme="urn:simple">nonadult</media:rating>
           <author>Петр Стругацкий</author>
           <category>Технологии</category>
           <enclosure url="http://example.com/2023/07/04/pic1.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/pic2.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/video/420"
                      type="video/x-ms-asf"/>
           <description>
                <![CDATA[
Заброшенную землю рядом с токийским университетом Нисёгакуся передали андроиду
с внешностью известного японского хозяйственника.
]]>
            </description>
            <content:encoded>
                <![CDATA[

<p>Здесь находится полный текст статьи.
Его могут прерывать картинки, видео и другой медиаконтент.</p>
<figure>
    <img src="http://example.com/2023/07/04/pic1.jpg" width="1200" height="900">
        <figcaption>
Первый андроид-фермер смотрит на свои угодья

            <span class="copyright">Михаил Родченков</span>
        </figcaption>
    </figure>
    <p>Продолжение статьи после вставленной картинки. В статье рассказывается
о технологии вспахивании земли, которую использует японский андроид-фермер.
Поэтому в материале не обойтись без видеоролика. Пример видеоролика ниже.</p>
    <figure>
        <video width="1200" height="900">
            <source src="http://example.com/2023/07/04/video/42420" type="video/mp4">
            </video>
            <figcaption>
Андроид-фермер вспахивает землю при помощи собственного изобретения

                <span class="copyright">Михаил Родченков</span>
            </figcaption>
        </figure>
        <p>Статья продолжается после видео. Андроид копает картошку.
Фермы развиваются. Япония продолжает удивлять.</p>
]]>
            </content:encoded>
        </item>


    </channel>
</rss>

*/
























$s=''; $lastupdate=0; foreach($pp as $p) {
	$lastupdate = max($lastupdate,$p["DateUpdate"]);
	$p['link']=$httphost.$p["Date"]; // полная ссылка на статью

//	    $p['Body']=RSS_zaban($p['Body']); // обработать забаненных
	    $t=plain_Body($p);

$enclosure='';
$imgs = search_fotos($t);

// if(!empty($imgs[0])) dier($imgs);

/*
    $n_img=sizeof($imgs[1]); $n_obj=sizeof($objs[1]);
    if($n_img) {
<------>$img=$imgs[2][0]; // взяли первое
<------>foreach($imgs[1] as $n=>$l) if($l=='IMG') { $img=$imgs[2][$n]; break; } // если тэг заглавным - то его
    } else $img='';
    // $text=$head."\n".plain_Body($r['p']);
*/

// if(!empty($imgs[0])) $imgs[0]=array($GLOBALS['httphost']."design/userpick/userpick_big.jpg");

if(!empty($imgs[0])) {
	    foreach($imgs[0] as $im) {
		$ras=getras($im); if($ras=='jpg') $ras='jpeg';
		$enclosure.="\n<enclosure url=\"".$im."\" type=\"image/".$ras."\"/>";
		// $t=str_replace($im,"<figure><img src=\"".$im."\"></figure>",$t);
		$t=str_replace($im,'',$t);
		// break;
	    }

//	    $t=print_r($imgs[0],1)."\n".$t;
}
	    $t=str_replace(chr(160)," ",$t);
	    $t=trim($t,"\r\t\n ");
	    $t=preg_replace("/\n[ \t]+/s","\n",$t);
	    $t=preg_replace("/\n+/s","\n",$t);
//	    $t=str_replace("\n","<br>",$t);
	    $t=str_replace("\n","<br>\n",$t);
//onetext($p,1); // обработать текст заметки как положено



	    $p['text']=$t;

	    if($RSSZ_mode==1) $p['text']=RSSZ_mode1($p['text'],$p['link']); // если в настройках указано не давать полный RSS
//		$text=zamok($p['Access']).$text; // добавить картинки подзамков
//		$text=str_replace($subst1,$subst2,$text);

	    if(str_replace(array('-',' '),'',$p["Header"])=="") $p['Header']=$p["Date"];
	    // .($p["Header"]?" - ".$p["Header"]:"");

	$s.=mpers($ITEM_TEMPLATE,array_merge($p,array(
	    "enclosure" => $enclosure,
	    "DateUpdate" => date("r", $p["DateUpdate"]),
	    "DateDatetime" => date("r", $p["DateDatetime"]),
	    "DateDate" => date("r", $p["DateDate"]),
	    "httphost" => $httphost,

	    'admin_name' => $admin_name, // "Леонид Каганов";
	    'admin_mail' => $admin_mail, // "lleo@lleo.me"; 

	    "httpsite" => $httpsite,
	    "mypage" => $mypage,
	    "MYPAGE" => $MYPAGE,
	    "admin_name" => $admin_name,
	    "wwwcharset" => $wwwcharset
	)));

}

$s = mpers($RSS_TEMPLATE,array(
    'ITEMS' => $s,
    'SKIP' => ($skip+$RSSZ_skip),
    'lastupdate' => date("r", $lastupdate),
	    'admin_name' => $admin_name, // "Леонид Каганов";
	    'admin_mail' => $admin_mail, // "lleo@lleo.me"; 
	    "httphost" => $httphost,
	    "httpsite" => $httpsite,
	    "mypage" => $mypage,
	    "MYPAGE" => $MYPAGE,
	    "admin_name" => $admin_name,
	    "wwwcharset" => $wwwcharset,
));

check_if_modified($lastupdate,"$lastupdate"); // время последней модификации (оно же как ETag)

ob_end_clean();
die($s);

//$s1.date("r",$lastupdate).$s);

//=========================================================================================================

// процедура времени последней модификации
function check_if_modified($date, $etag = NULL) { $cache = NULL;
        if( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) $cache = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $date;
        if( $cache !== false && isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
                $cache = $_SERVER['HTTP_IF_NONE_MATCH'] == '*' || isset( $etag )
                        && in_array( '"'.$etag.'"', explode( ', ',$_SERVER['HTTP_IF_NONE_MATCH'] ) );
                }
        if($cache) { header('HTTP/1.1 304 Not Modified'); ob_clean(); exit(); }
        else {
                header( 'Last-Modified: '.date( DATE_RFC822, $date ) );
                if( isset( $etag ) ) header( 'ETag: "'.$etag.'"' );
        }
}

// функция подготовки RSS, если он неполный
/*
function RSS_zaban($s) { global $admin,$IP,$BRO; 	// если это забаненные мудаки, воры и роботы
	if( $IP=='78.46.74.53' // http://feedex.net/view/lleo.aha.ru/dnevnik/rss.xml
		|| strstr($BRO,'eedjack') // BRO='Feedjack 0.9.10 - http://www.feedjack.org/'
		// || $_SERVER["REMOTE_ADDR"]=='79.165.191.215' // Feed43.com? http://feeds.feedburner.com/lleo ?
//		|| strstr($_SERVER["HTTP_USER_AGENT"],'Wget/') // а нехуй вгетом качать!
		|| $IP=='140.113.88.218' || strstr($BRO,'Yahoo Pipes')
	) return "Для вас полный текст заметки <a href=".mkna("читатель RSS!",
"вы настолько обленились, что вам лень ткнуть в ссылку и вы пытаетесь читать RSS пиратскими способами.",
"ходите и читайте дневник по-человечески.").">находится здесь</a>";
	return $s;
}
*/

function RSSZ_mode1($s,$link) { global $admin,$BRO,$IP;
	$sim=strip_tags(html_entity_decode($s)); // удалить все теги
	// $sim=ereg_replace("{_[^}]*_}",'',$sim); // удалить все модули в фигурных скобках
        $sim=preg_replace("/\{_[^\}]+_\}/si",'',$sim); // удалить все модули в фигурных скобках
	$bukv=round(((strlen($sim))+99 )/100)*100;
	$sim=trim(preg_replace("/^(.{260}[^\.\?\!]*[\.\!\?]).*$/si","$1",$sim))
	."... [<a href='$link'>читать полностью: примерно $bukv символов</a>]\n\n";
	// if(strstr($s,'<img')) $sim .= " + картинки или фотки";
	// if(strstr($s,'<script')) $sim .= " + скрипты какие-то";
	// if(strstr($s,'<object') || strstr($s,'<OBJECT')) $sim .= " + флэш вставлен (может, ролик или музыка?)";
	// $sim .= ".";

	// если это Яндекс

	if( strstr($BRO,'Yandex') || $IP=='78.110.50.100' ) return $sim."
\n<p><b>Пытаетесь читать мой дневник через RSS-ленту Яндекса? Здесь лишь грубая текстовая выжимка
для индексации в поиске - с битыми абзацами, без фоток, картинок, верстки, роликов, скриптов, голосований и прочего.
Настоящую версию моего дневника вы можете прочесть только на моем сайте (причины описаны <a href=".$httphost."/about_rss>здесь</a>).</b>";

	// если это робот трансляции ЖЖ

	// 204.9.177.18 (): , 'LiveJournal.com (webmaster@livejournal.com; for http://www.livejournal.com/users/lleo_run/; 488 readers)'
	if(strstr($BRO,'LiveJournal.com') )
	return $sim."\nЧитатели ЖЖ-трансляции! Оставляйте комментарии только на моем сайте, иначе я их не увижу.";

	return $sim;
}

function mkna($name,$prichina,$delat) { // создать ссылку посыла нахуй
	$stroka=$name."%".$prichina."%".$delat; $stroka=base64_encode($stroka);
	$stroka=str_replace("=","",$stroka); $stroka=str_replace("/","-",$stroka);
	return "http://natribu.org/?".$stroka;
}


function search_fotos($text,$vnutr=1) { // ищем фотки

if($vnutr) preg_match_all("/".preg_quote($GLOBALS['httphost'],'/')."[^<>\s]+\.(jpg|png|jpeg|gif)/si",$text,$imgs,PREG_PATTERN_ORDER);
else preg_match_all("/https?\:\/\/[^<>\s]+\.(jpg|png|jpeg|gif)/si",$text,$imgs,PREG_PATTERN_ORDER);
unset($imgs[1]);


$fo=array();
    $t=$text; $imgs['txt']=array(); foreach($imgs[0] as $n=>$l) {
//if(!strstr($t,$l)) idie('err1');
    list($a,$b)=explode($l,$t,2); if($n) $imgs['txt'][$n-1]=c0(strip_tags(// '<'.
trim($a))); else $text=c0($a);
    $t=$b;
    if(in_array($l,$fo)) unset($imgs[0][$n]); else $fo[]=$l;
} $imgs['txt'][$n]=c0(strip_tags(// '<'.
trim($t)));

// и удалить все маленькие фотки-дубли с /pre/
foreach($imgs[0] as $n=>$l) { if(!strstr($l,'/pre/')) continue;
    $l=str_replace('/pre/','/',$l);
    if(false===($k=array_search($l,$imgs[0]))) continue;
    $imgs['txt'][$k]=trim($imgs['txt'][$k].' '.$imgs['txt'][$n]);
    unset($imgs['txt'][$n]);
    unset($imgs[0][$n]);
}

    return $imgs;
}


?>