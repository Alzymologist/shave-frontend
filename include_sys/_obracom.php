<?php

//SCRIPTS("cot","function cot(e){e.style.display='none';e.nextSibling.style.display='inline';};");

/*
$commentary=nl2br(htmlspecialchars($commentary));
$commentary=AddBB($commentary);
$commentary="\n$commentary\n";
$commentary=hyperlink($commentary);
$commentary=trim($commentary,"\n");
*/

// die("1");


function link_lj_var($t) {
	$t1=str_ireplace('&'.'quot;','"',$t[1]);
	$t2=str_ireplace('&'.'quot;','"',$t[2]);
	$t1=trim($t1,"'\"\n ");
	$t2=str_ireplace('&'.'lt;wbr&'.'gt;&'.'lt;/wbr&'.'gt;','',trim($t2,"'\"\n "));
	if($t2==$t1) return $t1;
	return $t2." (".$t1.")";
}

function AddBB($s) {
        $s=preg_replace_callback("/&"."lt;a href=(.*?)&"."gt;(.*?)&"."lt;\/a&"."gt;/si","link_lj_var",$s);
	$s = str_replace('&'.'quot;','"', $s);

        $search = array(
                '/\[big\](.*?)\[\/big\]/is',

                '/\[h\](.*?)\[\/h\]/is',
                '/\[b\](.*?)\[\/b\]/is',
                '/&'.'lt;b&'.'gt;(.*?)&'.'lt;\/b&'.'gt;/is',
                '/&'.'lt;strong&'.'gt;(.*?)&'.'lt;\/strong&'.'gt;/is',

                '/\[i\](.*?)\[\/i\]/is',
                '/&'.'lt;i&'.'gt;(.*?)&'.'lt;\/i&'.'gt;/is',
                '/&'.'lt;em&'.'gt;(.*?)&'.'lt;\/em&'.'gt;/is',

                '/\[u\](.*?)\[\/u\]/is',
                '/&'.'lt;u&'.'gt;(.*?)&'.'lt;\/u&'.'gt;/is',

                '/\[s\](.*?)\[\/s\]/is',
                '/&'.'lt;s&'.'gt;(.*?)&'.'lt;\/s&'.'gt;/is',

                '/&'.'lt;quote&'.'gt;(.*?)&'.'lt;\/quote&'.'gt;/is',
                '/&'.'lt;cite&'.'gt;(.*?)&'.'lt;\/cite&'.'gt;/is',

		'/&'.'gt;([^\&\n<]+)/is',

                '/\[img\](.*?)\[\/img\]/is',
                '/\[url\](.*?)\[\/url\]/is',

                '/\[tab\](.*?)\[\/tab\]/is', // табличка

                '/\[url\=([^\>\<\'\"\=\)\(\;\#]*?)\](.*?)\[\/url\]/is'
                );

        $replace = array(
		"<font size='+2'>$1</font>",
		"<div class=ll onclick='cot(this)'>[...]</div><div style='display:none'>$1</div>", // '/\[h\](.*?)\[\/h\]/is',
		'<b>$1</b>', //  '/\[b\](.*?)\[\/b\]/is',
                '<b>$1</b>', //  '/<b>(.*?)<\/b>/is',
                '<b>$1</b>', //  '/<strong>(.*?)<\/strong>/is',

                '<i>$1</i>', //  '/\[i\](.*?)\[\/i\]/is',
                '<i>$1</i>', //  '/<i>(.*?)<\/i>/is',
                '<i>$1</i>', //  '/<em>(.*?)<\/em>/is',

                '<u>$1</u>', //  '/\[u\](.*?)\[\/u\]/is',
                '<u>$1</u>', //  '/<u>(.*?)<\/u>/is',

                '<s>$1</s>', //  '/\[s\](.*?)\[\/s\]/is',
                '<s>$1</s>', //  '/<s>(.*?)<\/s>/is',

                '<i><font color=gray>$1</font></i>', // '/<quote>(.*?)<\/quote>/is',
                '<i><font color=gray>$1</font></i>', // '/<cite>(.*?)<\/cite>/is',

                '<font color=gray>&'.'gt;$1</font>', // '/>([^\&\n<]+)/is',

                ' $1 ',		// '<img src="$1" />',    '/\[img\](.*?)\[\/img\]/is',
                ' $1 ',		// '<a href="$1">$1</a>', '/\[url\](.*?)\[\/url\]/is',

                '<div class=rama>$1</div>', // '/\[tab\](.*?)\[\/tab\]/is', // табличка

		'<a href=\'$1\'>$2</a>' // '/\[url\=([^\>\<\'\"\=\:\)\(\;\#]*?)\](.*?)\[\/url\]/is'
                );
        $s = preg_replace($search, $replace, $s);
	$s = preg_replace_callback('/\[code\](.*?)\[\/code\]/is','CODE_comm', $s);
	$s = str_replace('"','&'.'quot;',$s);
        return $s;
}


function CODE_comm($t) {
    $s=htmlspecialchars_decode($t[1]);
    $s=str_replace("<br>","\n",$s);
    $s=trim($s,"\n");
    $s=highlight_string("<?php\n".$s."\n?>",1);
    $s=str_replace("<br />","\n",$s);
    $s=str_replace("\n\n\n","\n",$s);
    $s=str_replace(array('<span style="','">'),array("<span style='","'>"),$s);
//if($GLOBALS['ADM']) idie(h(str_replace(array("\r","\n"),array('[r]','[n]'),$s)));
    $s=str_replace(array("<span style='color: #000000'>\n<span style='color: #0000BB'>&"."lt;?php\n","<span style='color: #0000BB'>?&"."gt;</span>\n</span>\n","?&"."gt;</span>\n</span>\n"),'',$s);
    $s=trim($s,"\n");
    $s=str_replace("\n","<br>",$s);
    $s=str_replace(array('<span style="','">'),array("<span style='","'>"),$s);
    return "<div style='width:90%;margin-left:20px;border:1px dotted #ccc;background-color:#eee;border-radius:5px;padding:5px;'>".$s."</div>";
}

function hypermail($s,$k=1) { return preg_replace("/"
."([\s".($k?">":'')."\(\:])" // символы перед [1]
."([0-9a-z\-\_\.]+\@[0-9a-z\-\_\.]+)" // http:// или www. [3]
."(" // символы после
."[\.\?\:][^a-zA-Z0-9\/]"
."|[\s".($k?"<>":'').",\)$]"
.")"
."/si","$1<a href='mailto:$2'>$2</a>$3", $s);
}


function hyperlink($s,$k=1) {
    $papki="[a-zA-Z0-9\!\#\$\%\(\)\*\+\,\-\.\/\:\;\=\[\]\\\^\_\`\{\}\|\~]+";
    $lastaz="[a-zA-Z0-9\/]";
    $quer="[a-zA-Z0-9\!\#\$\%\&\(\)\*\+\,\-\.\/\:\;\=\?\@\[\]\\\^\_\`\{\}\|\~]+"; // {
    $lastquer="[a-zA-Z0-9\#\$\&\(\)\*\/\=\@\]\\\^\_\`\}\|\~]";
    return preg_replace_callback("/"
	."([\s>"
	."\(\:])" // символы перед [1]
	."(" // [2]
	."([a-z]+:\/\/|(www\.))" // http:// или www. [3]
	."([0-9a-zA-Z][A-Za-z0-9_\.\-]*[A-Za-z]{2,6})" // aaa.bb-bb.c_c_c [4]
		."(\:\d{1,6}|)" // порт йопта блять или пустота [5]
		."("
		            ."\/".$papki.$lastaz."\?".$quer.$lastquer // /papka/papka.html?QUERY_STRING#HASH
			."|"."\?".$quer.$lastquer // ?QUERY_STRING#HASH
			."|"."\/".$papki.$lastaz // /papka/papka
		."|)"
	.")"
	."(" // символы после
	."[\.\?\:][^a-zA-Z0-9\/]"
	."|[\s"
	.($k?"<>":'')
	.",\)$]"
	.")"
	."/s","url_present", $s);
}

$GLOBALS['media_id']=0;

function url_click($p,$s,$l=0) { $m='media_'.($GLOBALS['media_id']++);
    return $p[1]."<div id='$m'>"
	."<div title=\"".LL('obracom:click_this')."\" class='l' onclick=\"majax('comment.php',{a:'show_url',type:'$s',url:'".($l===0?$p[2]:$l)."',media_id:'$m'})\">".reduceurl($p[2],60)."</div>"
	."</div>".$p[8];
}

function url_present($p) { global $httpsite,$opt,$media_id,$site_mod;
	$o=( !isset($opt)
		or $opt['Comment_media']=='all'
		or $opt['Comment_media']=='my' && explode_last('://',$p[3].$p[5])==explode_last('://',$httpsite)
	?1:0);

	$r=urldecode($p[7]);
	if(!strstr($r,'.')) $r=''; else $r=strtolower(explode_last('.',$r));

	if($r=='mp3') { // вставка mp3
		if($o){ include_once $site_mod."MP3.php"; return $p[1].MP3($p[2]." | mp3").$p[8]; }
		else return url_click($p,'mp3');
	}

	if(in_array($p[5],array('www.youtube.com','youtu.be','m.youtube.com'))) { // вставка роликов с ютуба
	    preg_match("/(v=|youtu\.be\/)([0-9a-z\_\-]+)/si",$p[2],$m);

	    $t=0; $p[22]=str_replace('&'.'amp;','&',$p[2]); if( strstr($p[22],"?t=") || strstr($p[22],"&t=")) { // подсчитать время старта в секундах, если оно указано
		if(preg_match("/(\?|\&)t=[\dmsh]*?(\d+)h/si",$p[22],$i)) $t+=$i[2]*60*60;
		if(preg_match("/(\?|\&)t=[\dmsh]*?(\d+)m/si",$p[22],$i)) $t+=$i[2]*60;
		if(preg_match("/(\?|\&)t=[\dmsh]*?(\d+)s/si",$p[22],$i)) $t+=intval($i[2]);
	    }
	    $t=($t?"?start=".$t : '');

	    if($o) return "<div alt='play'>".h($m[2].$t)." "
		."<div style='border: 1px solid #ccc;box-shadow: 0px 5px 5px 5px rgba(0,0,0,0.6);"
		." position:relative;width:320px;height:180px;display:inline-block;background-image:url(https://img.youtube.com/vi/".h($m[2])."/mqdefault.jpg);'>"
		."<i style='position:absolute;top:70px;left:150px;' class='mv e_play-youtube'></i>"
		."</div>"
		."</div>";
	    return url_click($p,'youtub',$m[2]);
	}

	if($p[3]=='www.') $p[2]='http://'.$p[2];
	$l=$p[7];

	if(!strstr($l,'module=') && ( $r=='jpg' or $r=='gif' or $r=='jpeg' or $r=='png' or $r=='webp'
		or stristr($p[0],'https://pix2.blogs.yandex.net/getavatar')
		or stristr($p[0],'https://avatars.yandex.net')
		)
	) {
	    if($o) {
		if($GLOBALS['HTTPS']=='https') {  $p[2]=str_ireplace('http'.substr($GLOBALS['httpsite'],5),'',$p[2]); } // патчим для HTTPS
		return $p[1].'<img style="max-width:900px;max-height:800px" src="'.$p[2].'"'.(strstr($l,'&'.'amp;prefix=normal')?' align=left hspace=10':'').'>'.$p[8];
    	    }
	    return url_click($p,'img');
	}

	if($p[3]=='area://') return $p[1].'<a href="http://fghi.pp.ru/?'.$p[2].'">'.$p[3].$p[5].$l.'</a>'.$p[8];

	return $p[1].'<noindex><a href="'.$p[2].'" rel="nofollow">'.reduceurl(maybelink($p[3].$p[5].$l),60).'</a></noindex>'.$p[8];
}

function reduceurl($s,$l) { if(strlen($s) > $l) $s=substr($s,0,$l)."[...]"; return $s; }

?>