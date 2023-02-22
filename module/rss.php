<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// rss ������� ��������

$GLOBALS['PUBL']=1; // ���� ��� �������, ��� �������� ���� �� ������� ����������

// if($GLOBALS['unic']==4) dier($GLOBALS['IS']);

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

$GLOBALS['rssmode']=1;

include_once $include_sys."_onetext.php"; // ��������� �������

$subst1=array("{foto_www_preview}");
$subst2=array($foto_www_preview);

//$RSSZ_skip = 10;
//$RSSZ_mode = 1;

ob_clean();
header("Content-Type: text/xml; charset='".$wwwcharset."'");
// header("Content-Type: application/xml; windsows-1251");

$skip=intval($_GET['skip']);

if(isset($_GET['r'])&&false!=($unic=rss_ucheck($_GET['r']))) {
    getis_global(($GLOBALS['unic']=$unic));
    if($GLOBALS['podzamok']) logi('rss-podzamok.log',date("Y-m-d H:i:s")." ".$GLOBALS['IS']['realname'].": ".($skip?"(skip:".$skip.") ":'').$GLOBALS['IP']." ".$GLOBALS['BRO']."\n");
}

$pp=ms("SELECT `Date`,`Body`,`Header`,`DateUpdate`,
`DateDate`,
`DateDatetime`,
`Access`,`num` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0").ANDC()." ORDER BY `Date` DESC LIMIT ".$skip.",".$RSSZ_skip,"_a");

$RSS_TEMPLATE="<?xml version='1.0' encoding='{wwwcharset}'?>
<rss version='2.0' xmlns:ya='http://blogs.yandex.ru/yarss/' xmlns:wfw='http://wellformedweb.org/CommentAPI/'>

<channel>
  <title>{#:admin_name}</title>
  <lastBuildDate>{#:lastupdate}</lastBuildDate>
  <link>{#httphost}</link>
  <description>{#:admin_name}: ����</description>
  <generator>Binoniq 3.0</generator>
  <wfw:commentRss>{#:httphost}rssc</wfw:commentRss>
  <ya:more>{#httpsite}{#mypage}?skip={#SKIP}</ya:more>
  <image>
    <url>{#httphost}design/userpick.jpg</url>
    <width>120</width>
    <height>155</height>
  </image>

{ITEMS}

</channel>
</rss>";

//	<DateDate>{#DateDate}</DateDate>
//	<DateDatetime>{#DateDatetime}</DateDatetime>

$ITEM_TEMPLATE="\n<item>
	<title>{#:Header}</title>
	<link>{#link}</link>
	<comments>{#link}</comments>
	<pubDate>{#DateDatetime}</pubDate>
	<lastBuildDate>{#DateUpdate}</lastBuildDate>
	<guid isPermaLink='true'>{#link}</guid>
	<author>{#httphost}</author>
        <description><![CDATA[{text}]]></description>
</item>\n"; // {#:text}


$s=''; $lastupdate=0; foreach($pp as $p) {
	$lastupdate = max($lastupdate,$p["DateUpdate"]);
	$p['link']=$httphost.$p["Date"]; // ������ ������ �� ������

	//    $p['Body']=RSS_zaban($p['Body']); // ���������� ����������
	    $p['text']=onetext($p,1); // ���������� ����� ������� ��� ��������
	    if($RSSZ_mode==1) $p['text']=RSSZ_mode1($p['text'],$p['link']); // ���� � ���������� ������� �� ������ ������ RSS
//		$text=zamok($p['Access']).$text; // �������� �������� ���������
//		$text=str_replace($subst1,$subst2,$text);

	$p['Header']=$p["Date"].($p["Header"]?" - ".$p["Header"]:"");

	$s.=mpers($ITEM_TEMPLATE,array_merge($p,array(
	    "DateUpdate" => date("r", $p["DateUpdate"]),
	    "DateDatetime" => date("r", $p["DateDatetime"]),
	    "DateDate" => date("r", $p["DateDate"]),
	    "httphost" => $httphost,
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

?>