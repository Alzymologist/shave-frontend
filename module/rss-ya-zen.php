<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// rss ������� ��������

$GLOBALS['PUBL']=1; // ���� ��� �������, ��� �������� ���� �� ������� ����������

// if($GLOBALS['unic']==4) dier($GLOBALS['IS']);

/*
// �������� �����������
function rss_h($u,$ran) { return substr(sha1($u.$GLOBALS['hashrss'].$ran),4,8); }
function rss_uset($u) { if(!isset($GLOBALS['hashrss'])) return false; $ran=substr(sha1($time.rand(0,99999999).$GLOBALS['hashrss']),2,4); return $u."-".$ran."-".rss_h($u,$ran); }
function rss_ucheck($up) { if(!strstr($up,'-')||!isset($GLOBALS['hashrss'])) return 0; list($u,$ran,$p)=explode('-',$up,3); return ($p==rss_h($u,$ran)?$u:0); }

if(isset($_GET['lajax'])) { // ����-������

    if(RE('a')=='getrsslink') { // ��� ��������� ���������� ����� RSS �� ������� <a href="javascript:majax('{acc_link}rss',{a:'getrsslink'})">RSS</a>
    if($GLOBALS['admin'] && !isset($GLOBALS['hashrss'])) otprav("salert(\"Not set \$hashrss in your /config.php!<p>Please add to your <b>/config.php</b> the line with hash like this:<p><b>\$hashrss='my secret phrase blablabla';</b>\",10000)");
    $link=$GLOBALS['httphost'].'rss?r='.rss_uset($GLOBALS['unic']);
    otprav("ohelpc('rss','rss',\"��� ������� #".$GLOBALS['unic']
    .($GLOBALS['podzamok']?" � �� ������ <b>����������� ������</b>.<p>����� ������ ����������� �����, ":'')
    ."��� ������ RSS-����� ����������� ������ ������:<p><a href='".h($link)."'>".h($link)."</a>\")");
    }

}
*/

$GLOBALS['rssmode']=1;

include_once $include_sys."_onetext.php"; // ��������� �������

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
        <description>{#:admin_name}: �������</description>
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
//            <category>����������</category>
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
        <title>���������</title>
        <link>http://example.com/</link>
        <description>
������ � ������� � ������. ������������ � ���, ��� ����������, �������� � ��������� ����.
</description>
        <language>ru</language>



        <item>
           <title>������� ����������� ����� � ������</title>
           <link>http://example.com/2023/07/04/android-happy-farmer</link>
           <pdalink>http://m.example.com/2023/07/04/android-happy-farmer</pdalink>
           <amplink>http://amp.example.com/2023/07/04/android-happy-farmer</amplink>
           <guid>2fd4e1c67a2d28fced849ee1bb76e7391b93eb12</guid>
           <pubDate>Tue, 4 Jul 2023 04:20:00 +0300</pubDate>
           <media:rating scheme="urn:simple">nonadult</media:rating>
           <author>���� ����������</author>
           <category>����������</category>
           <enclosure url="http://example.com/2023/07/04/pic1.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/pic2.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/video/420"
                      type="video/x-ms-asf"/>
           <description>
                <![CDATA[
����������� ����� ����� � ��������� ������������� ��������� �������� ��������
� ���������� ���������� ��������� ��������������.
]]>
            </description>
            <content:encoded>
                <![CDATA[

<p>����� ��������� ������ ����� ������.
��� ����� ��������� ��������, ����� � ������ ������������.</p>
<figure>
    <img src="http://example.com/2023/07/04/pic1.jpg" width="1200" height="900">
        <figcaption>
������ �������-������ ������� �� ���� ������

            <span class="copyright">������ ���������</span>
        </figcaption>
    </figure>
    <p>����������� ������ ����� ����������� ��������. � ������ ��������������
� ���������� ����������� �����, ������� ���������� �������� �������-������.
������� � ��������� �� �������� ��� �����������. ������ ����������� ����.</p>
    <figure>
        <video width="1200" height="900">
            <source src="http://example.com/2023/07/04/video/42420" type="video/mp4">
            </video>
            <figcaption>
�������-������ ���������� ����� ��� ������ ������������ �����������

                <span class="copyright">������ ���������</span>
            </figcaption>
        </figure>
        <p>������ ������������ ����� �����. ������� ������ ��������.
����� �����������. ������ ���������� ��������.</p>
]]>
            </content:encoded>
        </item>


    </channel>
</rss>

*/
























$s=''; $lastupdate=0; foreach($pp as $p) {
	$lastupdate = max($lastupdate,$p["DateUpdate"]);
	$p['link']=$httphost.$p["Date"]; // ������ ������ �� ������

//	    $p['Body']=RSS_zaban($p['Body']); // ���������� ����������
	    $t=plain_Body($p);

$enclosure='';
$imgs = search_fotos($t);

// if(!empty($imgs[0])) dier($imgs);

/*
    $n_img=sizeof($imgs[1]); $n_obj=sizeof($objs[1]);
    if($n_img) {
<------>$img=$imgs[2][0]; // ����� ������
<------>foreach($imgs[1] as $n=>$l) if($l=='IMG') { $img=$imgs[2][$n]; break; } // ���� ��� ��������� - �� ���
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
//onetext($p,1); // ���������� ����� ������� ��� ��������



	    $p['text']=$t;

	    if($RSSZ_mode==1) $p['text']=RSSZ_mode1($p['text'],$p['link']); // ���� � ���������� ������� �� ������ ������ RSS
//		$text=zamok($p['Access']).$text; // �������� �������� ���������
//		$text=str_replace($subst1,$subst2,$text);

	    if(str_replace(array('-',' '),'',$p["Header"])=="") $p['Header']=$p["Date"];
	    // .($p["Header"]?" - ".$p["Header"]:"");

	$s.=mpers($ITEM_TEMPLATE,array_merge($p,array(
	    "enclosure" => $enclosure,
	    "DateUpdate" => date("r", $p["DateUpdate"]),
	    "DateDatetime" => date("r", $p["DateDatetime"]),
	    "DateDate" => date("r", $p["DateDate"]),
	    "httphost" => $httphost,

	    'admin_name' => $admin_name, // "������ �������";
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
	    'admin_name' => $admin_name, // "������ �������";
	    'admin_mail' => $admin_mail, // "lleo@lleo.me"; 
	    "httphost" => $httphost,
	    "httpsite" => $httpsite,
	    "mypage" => $mypage,
	    "MYPAGE" => $MYPAGE,
	    "admin_name" => $admin_name,
	    "wwwcharset" => $wwwcharset,
));

check_if_modified($lastupdate,"$lastupdate"); // ����� ��������� ����������� (��� �� ��� ETag)

ob_end_clean();
die($s);

//$s1.date("r",$lastupdate).$s);

//=========================================================================================================

// ��������� ������� ��������� �����������
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

// ������� ���������� RSS, ���� �� ��������
/*
function RSS_zaban($s) { global $admin,$IP,$BRO; 	// ���� ��� ���������� ������, ���� � ������
	if( $IP=='78.46.74.53' // http://feedex.net/view/lleo.aha.ru/dnevnik/rss.xml
		|| strstr($BRO,'eedjack') // BRO='Feedjack 0.9.10 - http://www.feedjack.org/'
		// || $_SERVER["REMOTE_ADDR"]=='79.165.191.215' // Feed43.com? http://feeds.feedburner.com/lleo ?
//		|| strstr($_SERVER["HTTP_USER_AGENT"],'Wget/') // � ����� ������ ������!
		|| $IP=='140.113.88.218' || strstr($BRO,'Yahoo Pipes')
	) return "��� ��� ������ ����� ������� <a href=".mkna("�������� RSS!",
"�� ��������� ����������, ��� ��� ���� ������ � ������ � �� ��������� ������ RSS ���������� ���������.",
"������ � ������� ������� ��-�����������.").">��������� �����</a>";
	return $s;
}
*/

function RSSZ_mode1($s,$link) { global $admin,$BRO,$IP;
	$sim=strip_tags(html_entity_decode($s)); // ������� ��� ����
	// $sim=ereg_replace("{_[^}]*_}",'',$sim); // ������� ��� ������ � �������� �������
        $sim=preg_replace("/\{_[^\}]+_\}/si",'',$sim); // ������� ��� ������ � �������� �������
	$bukv=round(((strlen($sim))+99 )/100)*100;
	$sim=trim(preg_replace("/^(.{260}[^\.\?\!]*[\.\!\?]).*$/si","$1",$sim))
	."... [<a href='$link'>������ ���������: �������� $bukv ��������</a>]\n\n";
	// if(strstr($s,'<img')) $sim .= " + �������� ��� �����";
	// if(strstr($s,'<script')) $sim .= " + ������� �����-��";
	// if(strstr($s,'<object') || strstr($s,'<OBJECT')) $sim .= " + ���� �������� (�����, ����� ��� ������?)";
	// $sim .= ".";

	// ���� ��� ������

	if( strstr($BRO,'Yandex') || $IP=='78.110.50.100' ) return $sim."
\n<p><b>��������� ������ ��� ������� ����� RSS-����� �������? ����� ���� ������ ��������� �������
��� ���������� � ������ - � ������ ��������, ��� �����, ��������, �������, �������, ��������, ����������� � �������.
��������� ������ ����� �������� �� ������ �������� ������ �� ���� ����� (������� ������� <a href=".$httphost."/about_rss>�����</a>).</b>";

	// ���� ��� ����� ���������� ��

	// 204.9.177.18 (): , 'LiveJournal.com (webmaster@livejournal.com; for http://www.livejournal.com/users/lleo_run/; 488 readers)'
	if(strstr($BRO,'LiveJournal.com') )
	return $sim."\n�������� ��-����������! ���������� ����������� ������ �� ���� �����, ����� � �� �� �����.";

	return $sim;
}

function mkna($name,$prichina,$delat) { // ������� ������ ������ �����
	$stroka=$name."%".$prichina."%".$delat; $stroka=base64_encode($stroka);
	$stroka=str_replace("=","",$stroka); $stroka=str_replace("/","-",$stroka);
	return "http://natribu.org/?".$stroka;
}


function search_fotos($text,$vnutr=1) { // ���� �����

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

// � ������� ��� ��������� �����-����� � /pre/
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