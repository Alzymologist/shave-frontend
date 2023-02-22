<?php /* Вставить окно с роликом Ютуба

{_PLAYTUBE: https://www.youtube.com/watch?v=Cu1KlT-dyRo _}
{_PLAYTUBE: https://youtu.be/BAi6sJ1TJ6k Ролик _}
{_PLAYTUBE: <iframe width="560" height="315" src="https://www.youtube.com/embed/i13GaWLqpzk" frameborder="0" allowfullscreen></iframe> | Вот ролик _}

*/

function PLAYTUBE($e) {

$cf=array_merge(array(
    'width'=>853,
    'height'=>480
),parse_e_conf($e)); $e=$cf['body'];

    $txt=''; if($e=='') return;
    if(strstr($e,'<iframe')) $txt='';
    else list($e,$txt)=explode(strstr($e,'|')?'|':' ',$e,2); $txt=c0($txt); $e=c($e);
    $e=urldecode($e);

    if(isset($GLOBALS['nett'])) { // SOCIAL NETWORK
        $e=fulllink($e,$GLOBALS['r']['url']);

/*
   [r] => Array
        (
            [0] => lj
            [1] => template.ljpost
            [2] => lleo-kaganov
            [3] => forest5LJ_lleo_k
            [conf] => Array
                (
                    [body] => <small><i>© взято отсюда: <a href='{url}'>{url}</a></i></small><p>{text}
                )

        )

    [net] => lj
    [nett] => lj:lleo-kaganov
*/
//	dier($GLOBALS['r'],"WWWWWWWWWWW".$GLOBALS['nett']."WWWWWWWWWww:".$GLOBALS['r']['url']);

// dier("<figure><iframe src=\"/embed/youtube?url=".urlencode($e)."\"></iframe></figure>");

    if($GLOBALS['net']=="telegraph") {


//        if(preg_match("/(www\.youtube\.com|youtu\.be|m\.youtube\.com)/s",$e)) { // вставка роликов с ютуба
//            preg_match("/(v=|youtu\.be\/)([0-9a-z\_\-]+)/si",$e,$m);
	    // return "<figure><iframe src=\"/embed/youtube?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D".$m[2]."\"></iframe></figure>";
// return "<figure><iframe src=\"https://www.youtube.com/watch?v=".$m[2]."\"></iframe></figure>";
//	    return '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$m[2].'" frameborder="0" allowfullscreen></iframe>';
	    // dier($m[2]);
//	}
// https://youtu.be/etqbYjxoRrI

// if()

// /embed/youtube?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DtrHMLJpxG7o"

//	return "<figure><iframe src=\"/embed/youtube?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DtrHMLJpxG7o\"></iframe></figure>";
//	return "<figure><iframe src=\"/embed/youtube?url=".urlencode($e)."\"></iframe></figure>";
//	return "<iframe src=\"/embed/youtube?url=".urlencode($e)."\"></iframe>";
 	return "<a href=\"".$e."\">".$e."</a>"
// ."<iframe src=\"/embed/youtube?url=".urlencode($e)."\"></iframe>"
// ."<iframe src=\"".urlencode($e)."\"></iframe>"
;

}

        if(in_array($GLOBALS['net'],array('telegram','11telegraph','fb','facebook','vk'))) {
	    if(substr($txt,0,1)=='#') { $txt=substr($txt,1); return " ".($txt==''?'':$txt.": ").$e." "; }
	    return "\n".($txt==''?'':$txt.": ").$e."\n";
	}
    }

    $media=array('mp4','flv','avi','mkv','mov');

    if( preg_match("/([0-9a-z]{8}\-[0-9a-z]{4}\-[0-9a-z]{4}\-[0-9a-z]{4}\-[0-9a-z]{12})/si",$e,$m)) {
     $PL="<iframe width='".$cf['width']."' height='".$cf['height']."' sandbox='allow-same-origin allow-scripts allow-popups'"." src=\"".h($e)."\" frameborder='0' allowfullscreen>".h($e)."</iframe>";
    } elseif( preg_match("/(v=|youtu\.be\/|\/embed\/)([0-9a-z\_\-]+)/si",$e,$m) ) {
     $link="https://www.youtube.com/embed/".h($m[2])."?rel=0&showinfo=0";
     $PL="<iframe width='".$cf['width']."' height='".$cf['height']."' src='$link' frameborder='0' allowfullscreen>$link</iframe>";
    } elseif(in_array(getras($e),$media)) {
        $e=fulllink($e, getlink($GLOBALS['article']['Date']) );
	$PL="<video src='".h($e)."' controls>Play: <a href='".h($e)."'>".h($e)."</a></video>";
    } else $PL="<font color=red>UNKNOWN MEDIA. Use Youtube' link or hash or media type: ".implode(", ",$media)."</font>";

    return "<center><div style='max-width: 90% !important;width:".$cf['width']."px;border: 1px solid #ccc; box-shadow: 0px 15px 15px 15px rgba(0,0,0,0.6); border-radius: 7px 7px 7px 7px; padding: 15px 15px 15px 15px;'>"
    .($txt!=''?"<div style='margin-bottom:10px;align:center;font-size:20px;font-weight:bold;'>".$txt."</div>":'').$PL."</div></center>";

}

?>