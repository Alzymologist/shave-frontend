<?php /* index - индекс файлов папки

{_INDEX: design/img _}
*/

function INDEX($e) { $e=$GLOBALS['filehost'].trim($e,'/');

$o=''; foreach(glob($e.'/*') as $l) { $s='';
    $w=h($GLOBALS['wwwhost'].substr($l,strlen($GLOBALS['filehost'])));
    $ras=getras($w);
    $bn=basename($w);

    if(in_array($ras,array('mov','mpg','mpeg','mp4','avi','flv'))) {

	    SCRIPTS("playvid","changevidx=function(url,name){ var s='<div class=br>'+h(name)+'</div><div>"
	    ."<video controls id=\"videoidx\" src=\"'+h(url)+'\" width=\"320\" height=\"240\" autoplay>"
."<span style=\"border:1px dotted red\">Браузер не поддерживает MP3</span>"
."</video></div>';"
."if(idd('videosrcx')) zabil('videosrcx',s); else ohelpc('videosrcx_win','play','<div id=videosrcx>'+s+'</div>');};");
// " // <source src=\"'+h(url)+'\">" //  type=\"video/mpeg;\"

    $txt='play: '.h($bn);
    $s="<img src='".$GLOBALS['www_design']."img/play.png' title='play' onclick='changevidx(\"".h($w)."\",\"".h($txt)."\")'>&nbsp;"."<a href='$w'>".h($bn)."</a>";

    }

    else $s="<a href='$w'>".h($bn)."</a>";

    $o.="<div>".$s."</div>";
}

return $o;
}


/*


function MP3($e) {
    $e=urldecode($e);
    $f=$txt='';
    if(strstr($e,'|')) { list($e,$f)=explode('|',$e,2); $f=c0($f); $e=c($e);
        if(strstr($f,'|')) { list($txt,$f)=explode('|',$f,2); $txt=c0($txt); $f=c($f); }
    }

    if($f=='play') return "<img src='".$GLOBALS['www_design']."img/play.png' title='play' onclick='changemp3x(\"".h($e)."\",\"".h($txt)."\")'>";

// <div id='audiosrcx'></div>
// <script>changemp3x=function(url,name){ zabil('audiosrcx','<div class=br>'+name+'</div><div><audio controls autoplay id="audiidx"><source src="'+h(url)+'" type="audio/m

return "<center>"
."<audio controls><source src=\"".$e."\" type='audio/mpeg; codecs=mp3'>"
."<span style='border:1px dotted red'>ВАШ БРАУЗЕР НЕ ПОДДЕРЖИВАЕТ MP3, МЕНЯЙТЕ ЕГО</span>"
."</audio>"

.($f=='mp3'?"<p class=r><a title='Download file mp3' href=\"".h($e)."\">".h(basename($e))."</a>":'')
."</center>"
;
}
*/

?>