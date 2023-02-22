<?php // Работа с[M@!- фотоальбомом

include "../config.php"; include $include_sys."_autorize.php";

$usenames='html htm css txt text js lang';
function razre_ras($l){ return in_array(strtolower(substr(strrchr($l,'.'),1)),explode(' ',$GLOBALS['usenames'])); }
function REsel() { $sel=RE('sel'); if(!strstr($sel,'|')) return array($sel); return explode('|',$sel); }

function unlinkd($f) {
    unlink($f);
    $k=20; while(--$k) {
        $f=dirname($f);
	if(sizeof(glob($f.'/*'))) return;
	rmdir($f);
    }
}


if(!isset($TREEtmpADD)) $TREEtmpADD="template css"; // предустановленные папки

// idie('REMONT');

$hid=RE0("hid");
$a=RE('a'); ADH();

$lastphoto_file=$hosttmp."lastphoto.txt";

//=================================== album ===================================================================
if($a=='download') { ADMA(); $url=RE('url'); $num=RE0('num'); $r=RE0('r');

	if(!strstr($url,'://') || strstr($url,$httpsite)) idie('Error: '.h($url));
	if(false==($txt=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `num`=".intval($num),"_l",0))) idie("Error num: ".$num);
	if(!strstr($txt,$url)) idie("Error 1-".$url.'-'.$num.'<br>'.$txt);

	if(is_ras_image($url)) { $ras='.'.explode_last('.',$url); $u=substr($url,0,strlen($url)-strlen($ras)); }
	else { $ras='.jpg'; $u=$url; }
	$u=preg_replace("/(https|http|ftp)[\:\/w\.]+/si",'',$u);

	$i=(120-strlen($filehost));
	if(strlen($name)>$i) { $e=parse_url($u); $name=$e['host'].'-'.basename($e['path']); }
	$name=preg_replace("/[^0-9\.a-zA-Z\_\-]+/s","_",$u).$ras;

	    $url2=rpath(accd().date("Y/m/").$name);
	    if(!is_ras_image($url2)) $url2=trim($url2,'.').'.jpg'; // и добавить jpg если надо
	    $file=$filehost.$url2;

	if(strlen($file)>$i || is_file($file)) {
	    $name=rtrim(substr($name,0,($i-33)),'-_.').'_'.md5($name);
	    $url2=rpath(accd().date("Y/m/").$name);
	    if(!is_ras_image($url2)) $url2=trim($url2,'.').'.jpg'; // и добавить jpg если надо
	    $file=$filehost.$url2;
	}

	testdir(dirname($file));

	$r0r=RE0('r'); if($unic && ($r0r==0 || ($r0r==3 && !RE0('newsize')))) { // скачать просто
	    AD(); fileput($file,fileget($url));
	} else { // скопировать в tmp файл

	    $tmp=$hosttmp."foto_".md5($url2); if(is_file($tmp)) unlink($tmp); if(is_file($tmp)) idie('not delete!');


	    $fil=fileget($url);
	    if(strlen($fil)==0 && substr($url,0,8)=='https://') $fil=fileget(str_replace("https://","http://",$url));
	    if(strlen($fil)==0) idie('Error: empty file');
	    fileput($tmp,$fil); test_file($tmp,null,$fil); unset($fil);

	    require_once $include_sys."_fotolib.php"; $fotoset=loadset(); // обработать
	    if($r0r==3) obrajpeg($tmp,$file,RE0('newsize'),$fotoset['Q'],RE('sign'));
	    else obrajpeg($tmp,$file,$fotoset['X'],$fotoset['Q'],($r0r==1?$url:'')); //fotoset['logo']
	    unlink($tmp); // удалить
	}

	test_file($file,"Download error:<p><a href='".h($url)."'>`".h($url)."`</a>");

	$txt2=preg_replace("/(\{\_IMG\:\s*)".preg_quote($url,'/')."([\s\_$])/s","{_NO:".$url."_}"."$1".$wwwhost.$url2."$2",$txt);
	if($txt2==$txt) $txt2=str_replace($url,$httphost.$url2,$txt);

	msq_update('dnevnik_zapisi',array('Body'=>e($txt2)),"WHERE `num`=".intval($num));

	otprav("zabil('foto-".md5($url)."',\"<img src='".h($wwwhost.$url2)."'>\")");

}

//=================================== album ===================================================================
if($a=='album') { ADMA(); otprav("LOADS([www_js+'foto.js"
// ."?rand='+Math.random()+'"
."',www_css+'tree.css'],function(){".albumdir('/')."});"); }
//=================================== uploadfiles ===================================================================


if($a=='uploadnewfile') { ADMA(); $dir=RE('dir'); if($dir=='/') $dir='';
 $s="<form onsubmit=\"return ajaxform(this,'foto.php',{a:'uploadnewfile_do',dir:'".h($dir)."'})\">".$wwwhost.accd()
."<input id='uploadnewfile' title='загрузить один или несколько файлов' name='file1[]' size=30 type='file' multiple onchange=\"idd('uploadnewfile_submit').click()\">"
."&nbsp;<input id='uploadnewfile_submit' type='submit' value='send'></form>";
otprav("ohelpc('uploadnewfile','Upload Files',\"".njs($s)."\"); idd('uploadnewfile').focus();");
}
//--
if($a=='uploadnewfile_do') { ADMA();

    $dir=hackfile(RE('dir'));
    $ff_file=rpath($filehost.accd().$dir).'/'; testdir($ff_file);

    $k=0; // $s='';
    if(count($_FILES)>0) foreach($_FILES as $f) { if(gettype($f['name'])!='array') { foreach($f as $a=>$b) $f[$a]=array(0=>$b); }
	foreach($f['name'] as $a=>$b) {
	    if(is_uploaded_file($f["tmp_name"][$a])){ $l=h($f["name"][$a]);
		$l=hackfile($l);
		if(substr($l,0,1)=='.') idie("First letter `.`? ".h($l));
		if(strstr($l,'/')) idie("Unknown symbol /: ".h($l));
		if(!is_file($f["tmp_name"][$a])) idie("<font color=red>NOT FOUND: ".$f["tmp_name"][$a]."</font><br><pre>".nl2br(print_r($f,1))."<hr></pre>");
		$from=$f["tmp_name"][$a];
		$to=$ff_file.$l;
		rename($from,$to); test_file($to,"Error rename: ".h($from)); filechmod($to);
		$k++;
	    }
	}
    }

    if(!$k) idie("ERROR 2: ".nl2br(h(print_r($_FILES,1))));
    otprav("clean('uploadnewfile'); salert('DONE: $k',200); treereload();");
}
//=================================== editpanel ===================================================================
if($a=='createfile') { ADMA(); $dir=RE('dir'); if($dir=='/') $dir='';
$s=$wwwhost.accd()."<input onchange='createfile_go()' id='createfile_name' type='text' size='50' value=\"".h($dir)."\">&nbsp;<input type='button' value='Go' onclick='createfile_go()'>";
otprav("ohelpc('createfile','Create File',\"".njs($s)."\"); idd('createfile_name').focus();
createfile_go=function(){
	var l=idd('createfile_name').value,d=0;
	if(l.indexOf('.')<0 && confirm('Create folder?')) d=1;
	majax('foto.php',{a:'createfile_do',file:l,dir:d});
};
");
}
//--
if($a=='createfile_do') { ADMA(); $file=RE('file'); $f=rpath($filehost.accd().$file);
	if(is_file($f)||is_dir($f)) idie("Error! `".h($l)."` exist!");
	$s="clean('createfile');";
	if(RE0('dir')) { testdir($f); $s.="majax('foto.php',{a:'album'});"; }
	else { testdir(dirname($f)); $s.="treeid='".$file."'; majax('foto.php',{a:'edit_text',file:treeid});"; }
	otprav($s);
}

if($a=='uploadform') { ADMA(); $r=loadset(); $dir=$r['dir'];
$newd=date("Y/m"); $newd=($newd!=$dir && !is_dir($filehost.accd().$newd)?$newd:0);
$a=array('hid'=>$hid,'num'=>RE0($num),'wwwhost'=>$wwwhost,'dir'=>$dir,'id'=>$id,'idhelp'=>$idhelp,'www_design'=>$www_design,'newd'=>$newd);
otprav(mpers(str_replace(array("\n","\r","\t"),'',get_sys_tmp("foto_upload.htm")),$a));
}
//=================================== editpanel ===================================================================




//=================================== работа с нодами альбома ===================================================================
if($a=='formfotoset') { ADMA(); $idhelp='fotoset';
	$r=loadset();
otprav("
ohelpc('fotoset',\"<legend>Настройки фото\",\"".njsn("
<form onsubmit=\"return send_this_form(this,'foto.php',{a:'formfotoset_save'})\">
<table>
<tr><td>ширина картинки:</td><td><input id='fotoset_X' size='4' type='text' name='X' value='".h($r['X'])."'>px</td></tr>
<tr><td>качество картинки:</td><td><input size='3' type='text' name='Q' value='".h($r['Q'])."'>%</td></tr>
<tr><td>ширина превью:</td><td><input size='4' type='text' name='x' value='".h($r['x'])."'>px</td></tr>
<tr><td>качество превью:</td><td><input size='3' type='text' name='q' value='".h($r['q'])."'>%</td></tr>
<tr><td>папка:</td><td>".$wwwhost.accd()."<input size='15' type='text' name='dir' value='".h($r['dir'])."'></td></tr>
<tr><td>подпись:</td><td><input size=25 type=text name='logo' value='".h($r['logo'])."'></td></tr>
</table>
<p><input type='submit' value='Save'></form>
")."\");
idd('fotoset_X').focus();
");
}
//--
if($a=='formfotoset_save') { ADMA();
	$r=array(); foreach(explode(' ',trim(RE('names'))) as $l) $r[$l]=RE($l);
	$r=updset($r);
	otprav("zabilc('kudafoto','".h($r['dir'])."'); clean('fotoset');");
}
//=================================== работа с нодами альбома ===================================================================
if($a=='albumgo') { ADMA(); $id=RE('id'); 
  otprav(albumdir($id)." treeallimgicon(treeicon);".(RE0('tog')?"treetoggleNode(idd('$id'),1);":''));
}
//=================================== setdir ===================================================================
if($a=='setdir') { AD(); otprav("helps('foto_$hid',\"выбираем папку\",\"??\");"); }

if($a=='savedir') { AD(); $dir=RE("dir"); $dir=h(preg_replace("/\.+/s",'.',$dir));
	fileput($lastphoto_file,$dir);
	otprav("clean('foto'); majax('foto.php',{a:'uploadform'});");
}
//=================================== album-del ===================================================================
if($a=='filemove') { ADMA(); if(!sizeof($p=REsel())) idie('Select files,<br>then select folder<br>and then press button.');
$ndir=array(); $dir=RE('dir'); $to=rpath($filehost.accd().$dir); foreach($p as $l) {
		$f=rpath($filehost.accd().$l); $t=rpath($to.'/'.basename($l));
		if(is_file($f)&&!is_file($t)) { rename($f,$t); $ndir[dirname($l)]=1; }
	}
	$ndir[$dir]=1; $s=''; foreach($ndir as $l=>$n) $s.="treereload('".rtrim($l,'/')."/');";
//	idie($s);
	otprav($s."treeremove();salert('Moved!',800);");
}

// ----
if($a=='filecopy') { AD(); if(!sizeof($p=REsel())) idie('Select files,<br>then select folder<br>and then press button.');
$to=rpath($filehost.RE('dir')); foreach($p as $l) {
		$f=rpath($filehost.$l); $t=rpath($to.'/'.basename($l));
		if(is_file($f)&&!is_file($t)) copy($f,$t);
	}
	otprav("treeremove(); treereload(); salert('Copied!',800);");
}
// ----
if($a=='albumdel') { ADMA(); $s='';

	if(!sizeof($p=REsel())) {
		otprav("if(confirm('Delete '+treefolder+' ?')) {
			treeselected={}; treeselected[treefolder]=1;
			majax('foto.php',{a:'albumdel',sel:treeselected});
		}");
	}

	foreach($p as $l) { $f=rpath($filehost.accd().$l);
		$isf=is_file($f);

		if($isf) {
			$x=predir($f,'mic/'); if(is_file($x)) unlinkd($x);
			$x=predir($f,'pre/'); if(is_file($x)) unlinkd($x);
			unlinkd($f);
		} elseif(is_dir($f)) {
			if(!sizeof(glob($f.'/*'))) rmdir($f);
			else idie('Folder is not empty: <br>'.h($l)."<br>$f<br>".print_r(glob($f.'/*'),1));
		} else idie('Not found: '.h($l));

		if($isf&&is_ras_image($l)) $s.="clean('".$l."');"; // удалить фотку
		else {
    $soh=($acc!='' && is_file($filehost.$l)?1:0);

$s.="var e=idd('".$l."');"
.($soh // если есть такой же базовый - цвет серый
?"e.style.color='rgb(204,204,204)';" // иначе - удалить строку:
:"clean('".$l."');while(e.tagName!='LI'&&e.parentNode!=undefined)e=e.parentNode; e.parentNode.removeChild(e);"
// иначе удалить ноду
);
}
	}
	$s.="treeremove(); clean('fotooper'); treereload();";
	otprav($s);
}
//==================================================================================================
if($a=='lostfoto') { AD();
	$num=intval(str_replace('editor','',RE('num'))); if(!$num) idie('Error 0');
	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `num`=".$num,"_l"); if($Date===false) idie('Error 1');
	list($y,$m,)=explode('/',$Date,3); if(!intval($y)||!intval($m)) idie('Error 2');
	$nms="jpg|jpeg|png|gif|webp";
	$p=array(); $e=explode('|',$nms);
	foreach($e as $l) $p=array_merge($p, glob($filehost.$y."/".$m."/*.".$l), glob($filehost.$y."/".$m."/*.".strtoupper($l)) );
	$s=''; foreach($p as $l) {
		preg_match("/([^\/]+\.(".$nms."))/si",$l,$e); $e=$e[1];
		if(!ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date` LIKE '".e($y)."/".e($m)."/%' AND `Body` LIKE '%".e($e)."%'","_l"))
		$s.="\\n<center>{_IMG: ".$GLOBALS['wwwhost'].h($y)."/".h($m)."/".h($e)." _}</center>";
	}

	otprav("
		var v=idd('editor".$num."_Body');
		if(v) v.value=v.value+'".$s."';
	");
}
//=================================== editpanel ===================================================================
if($a=='edit_text') { ADMA(); $cff=$ff=RE('file'); // if(GLOBALS)
	if($GLOBALS['wwwcharset']=="windows-1251") $ff=wu($ff); // в кодировку UTF
	if($acc=='') { AD();
	    $f=rpath($filehost.$ff);
	    $fs=(is_file($f)?fileget($f):'');
	} else {
	    $f=rpath($filehost.accd().$ff);
	    if(!is_file($f)) $f=rpath($filehost."hidden/".accd().$ff); // еще поискать в скрытых
	    if(!is_file($f)) $f=rpath($filehost.$ff); // еще поискать в нормальных
	    allowfile($f,0);
	    $fs=(is_file($f)?fileget($f):'');
	}

otprav("
save_and_close=function(){save_no_close();clean('fotoset')};
save_no_close=function(){ if(idd('edit_text').value==idd('edit_text').defaultValue) return salert('".LL('save_not_need')."',500);
majax('foto.php',{a:'save_file',file:'".h($ff)."',text:idd('edit_text').value});
idd('edit_text').defaultValue=idd('edit_text').value;
};
ohelpc('fotoset','Edit: ".h($cff)."',\"<table><tr><td>"
."<textarea style='width:\"+(getWinW()-100)+\"px;height:\"+(getWinH()-100)+\"px;' id='edit_text'>".h(njsn($fs))."</textarea>"
."<br><input title='".LL('ctrl+Enter')."' type='button' value='".LL('Save+exit')."' onclick='save_and_close()'> <input title='".LL('shift+Enter')."' type='button' value='".LL('Save')."' onclick='save_no_close()'>"
."</td></tr></table>\");
idd('edit_text').focus();
setkey('Escape','',function(e){ if(idd('edit_text').value==idd('edit_text').defaultValue || confirm('".LL('exit_no_save')."')) clean('fotoset'); },false,1);
setkey('Enter','ctrl',save_and_close,false,1);
setkey('Enter','shift',save_no_close,false,1);
setkey('Tab','shift',function(){ti('edit_text','\\t{select}')},false,1);
");
}


if($a=='save_file'){ ADMA(); $s=RE('text'); $s=str_replace("\r",'',$s); $file=RE('file');
	if($admin && $acc=='') { $f=rpath($filehost.$file);}
	else { $f=$filehost.accd().$file; }
	allowfile($f,1); // проверка, что можно записывать, а что нет
	testdir(dirname($f));
	$js=''; if(!is_file($f)) $js.="treereload();";
	if($acc!=''&&!razre_ras($f)) idie("Name `".h($file)."` dedicated, sorry.<p>Use only: ".$usenames);
	fileput($f,$s); test_file($f,"Foto: error create file",$s);
	otprav($js."salert('".LL('saved')."',500)");
}


// eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee




//=================================== treeact ===================================================================

// Далее - процедуры фотообработки

require_once $include_sys."_fotolib.php";

//======================================
function refix_image_pre($s,$p=1) { // 0 - обычную 2 - мелкую 1 - обе
return ($p<2?preg_quote($s,'/'):'').($p>0?($p==1?'|':'').preg_quote(predir($s,'pre/'),'/'):''); }

function refix_image($img,$i,$fix,$p=1) { $i2=preg_replace("/^\d{4}\/\d{2}\//s",'',$i);
return "document.body.innerHTML=document.body.innerHTML.replace(/src=[\\'\\\"]*("
.refix_image_pre('/'.$GLOBALS['blogdir'].$i,$p)
.($i2==$i?'':'|'.refix_image_pre($i2,$p))
.'|'.refix_image_pre($img,$p)
.")[\\'\\\"]*/g,\"".$fix."\");";
}




if($a=='album_edit') { ADMA();

$p0=RE('p');

$js="

send_ftall=function(g,i){ if(i==undefined) i=0;
    var s='',e;
    for(;i<g;i++) {
	e=idd('ftarea".$p0."_'+i);
	if(e.value!=e.defaultValue) {
	    zabil('ftd',vzyal('ftd')+' *');
	    majax('editor.php',{a:'bigfotoedit_send',g:g,i:i,all:1,img:aft[i][0],num:".RE0('num').",i:i,p:".$p0.",txt:e.value});
	    break;
	}
    }
    if(i==g) clean('ftalledit');
};

var aft={};

make_input_fotos_area=function() {

    var g=0,e,i,s='',w=( idd('bigfostr') ? idd('bigfostr').style.width : Math.floor(getWinW()*0.9) );
    clean('bigfoto');

    while(e=idd('bigfot".$p0."_'+g)) {
	for(var t=0;t<g;t++) { if((''+aft[t][0])==(''+e)) return idie('Double foto: '+e); }
	aft[g]=[e,vzyal('bigfott".$p0."_'+g)]; g++;
    }

    for(i=0;i<g;i++) s=s+\"<p><div>"
    ."<div><textarea id='ftarea".$p0."_\"+i+\"' style='height:300px;width:\"+w+\";'>\"+aft[i][1]+\"</textarea></div>"
    ."<img src='\"+aft[i][0]+\"'>\"+i+\"</div>\";

    var bu=\"<input style='padding:50pt;' type='button' value='S E N D' onclick='send_ftall(\"+g+\")'>\";

    s='<center>'+bu+'<p>'+s+\"<br><span id='ftd'></span><p>\"+bu+'</center>';

    ohelpc('ftalledit','All Edit',s);
};

make_input_fotos_area();
";



otprav($js);


//zabil('bigfoto')
// var g=i; while(idd('bigfot'+p+'_'+g)) g++;
// idie();
}


if($a=='exif') { ADMA(); $img=RE('img'); $f=img_http2file($img);
    idie("<pre>".nl2br(h(uw(print_r(exif_read_data($f),1)))."</pre>"),"Exif");
}

if($a=='options') { ADMA(); $img=RE('img');
otprav("zabil('bigfoto_opt',\"".njsn("
<i class='knop e_system' title='Show EXIF' onclick=\"majax('foto.php',{a:'exif',img:'".$img."'})\"></i>
&nbsp;<i class='knop e_remove' title='Delete' onclick=\"if(confirm('Delete?')) majax('foto.php',{a:'fot_del',img:'".$img."'})\"></i>
&nbsp;<i class='knop e_rotate_left' title='270' onclick=\"if(confirm('Rotate 270?')) majax('foto.php',{a:'fot_rotate',degree:270,img:'".$img."'})\"></i>
&nbsp;<i class='knop e_rotate_right' title='90' onclick=\"if(confirm('Rotate 90?')) majax('foto.php',{a:'fot_rotate',degree:90,img:'".$img."'})\"></i>
&nbsp;<i class='knop e_blend' title='180' onclick=\"if(confirm('Rotate 180?')) majax('foto.php',{a:'fot_rotate',degree:180,img:'".$img."'})\"></i>
&nbsp;<i class='knop e_kontact_journal' title='Album Edit'onclick=\"majax('foto.php',{a:'album_edit',p:'".RE('p')."',num:num})\"></i>
")."\")");
}

if($a=='fot_del') { ADMA(); $img=RE('img'); $f=img_http2file($img);
	if(is_file($f)) {
            // $x=predir($f,'mic/'); if(is_file($x)) unlink($x);
            $x=predir($f,'pre/'); if(is_file($x)) unlink($x);
            unlink($f);
	    delfilecache($f);
	}
	otprav(refix_image($img,$i,'[x]')."clean('bigfoto');salert(\"File ".hh($i)." deleted\",1000);");
}

if($a=='fot_rotate') { ADMA(); $img=RE('img'); $f=img_http2file($img);
	$degree=RE0('degree'); $fotoset=loadset();
	rotatejpeg($f,$degree,$fotoset['Q']); delfilecache($f);
	$x=predir($f,'pre/'); if(is_file($x)) { rotatejpeg($x,$degree,$fotoset['q']); delfilecache($x); }
	otprav("window.location.reload(true);"
// .refix_image($img,$i,"src='".$img.'?'.filemtime($f)."'",0)
// .refix_image($img,$i,"src='".predir($img,'pre/').'?'.filemtime($f)."'",2)
."salert(\"File ".hh($i)." rotated\",1000);");
}

if($a=='fot_crop') { ADMA(); $img=RE('img'); $f=img_http2file($img); $web=img_http2www($img);
	crop_img( $f, $f,RE0('w'),RE0('h'),RE0('x'),RE0('y') );
	delfilecache($f);
	otprav("window.location.reload(true); salert(\"File ".hh($img)." crop\",1000);");
}

if($a=='fot_crop_engine') { ADMA(); $img=RE('img'); $f=img_http2file($img); $web=img_http2www($img);

/// canvas { display: block; width: 100%; height: auto; }
//     '".$GLOBALS['wwwhost']."extended/tinycrop/normalize.css',
//    '".$GLOBALS['wwwhost']."extended/tinycrop/style.css',

	otprav("

setInputsFrome=function(e) {
  idd('input-x').innerHTML = e.x;
  idd('input-y').innerHTML = e.y;
  idd('input-w').innerHTML = e.width;
  idd('input-h').innerHTML = e.height;
};

ohelpc('crop_img','Crop Image',\"".njsn("<div class='container'>
<div id='mount' class='mount'></div>
<div>X/Y: <span id='input-x'>X</span> / <span id='input-y'>Y</span> W/H: <span id='input-w'>W</span> / <span id='input-h'>H</span></div>
<div><center>
<button onclick='crop.setAspectRatio(1)'>Square</button>
<button onclick='crop.setAspectRatio(null)'>Free</button>
<button onclick=\"crop.setBounds({width:'100%',height:'100%'})\">100%</button>
<button onclick=\"crop.setBounds({width:'100%',height:'50%'})\">50%</button>
<button onclick=\"majax('foto.php',{a:'fot_crop',img:'".h($img)."',x:vzyal('input-x'),y:vzyal('input-y'),w:vzyal('input-w'),h:vzyal('input-h')})\">CROP</button>
</center></div>
</div>")."\");

idd('crop_img').style.width=Math.floor(getWinW()*0.9)+'px';
idd('crop_img').style.height=Math.floor(getWinH()*0.9)+'px';
center('crop_img');

LOADS(['".$GLOBALS['wwwhost']."extended/tinycrop.min.js'],function(e){

var crop = tinycrop.create({
	    parent: '#mount',
	    image: '".h($web)."',
            bounds: { width: '100%', height: '50%' },
	    backgroundColors: ['#fff', '#f0f0f0'],
            selection: {
        	color: 'red',
        	activeColor: 'orange',
/*
        	aspectRatio: 4 / 3,
        	minWidth: 200,
        	minHeight: 300,
        	width: 400,
        	height: 500,
        	x: 100,
        	y: 500
*/
    	    }
});

      crop
        .on('start', function(e) { setInputsFrome(e) })
        .on('move', function(e) { setInputsFrome(e) })
        .on('resize', function(e) { setInputsFrome(e) })
        .on('change', function(e) { setInputsFrome(e) })
        .on('end', function(e) { setInputsFrome(e) });

});

");
}




//======================================

if($a=='createpreview') { ADMA();
	$id=preg_replace("/\.+/s",'.',RE('id'));
	$l=$filehost.accd().$id.'/';
		$m=array(); $p=glob($l.'*'); foreach($p as $x){ if(is_ras_image($x)) $m[basename($x)]=1; } // вот шо надо
		if(is_dir($l.'pre')) { $p=glob($l.'pre/*'); foreach($p as $x) unset($m[basename($x)]); }
	if(!sizeof($m)) otprav("salert('Nothing to do!',1000);clean('wait');"); // idie('Error 04');

	$pre=$l.'pre'; if(!is_dir($pre)) { test_file($pre); chmod($pre,0777); } // создать там папку для превьюшек

	$r=loadset(); // взять настройки
	foreach($m as $x=>$n) {
		obrajpeg($l.$x, $l.'pre/'.$x,$r['x'],$r['q']); // сделать превьюшку
		$s.="idd('".$id."/".$x."').src='".$wwwhost.accd().$id."/pre/".$x."';"; // заменить на экране превьюшками
	}
	otprav($s."clean('wait');");
}

//=================================== treeact ===================================================================
if($a=='delpre') { AD(); if(!$admin) idie('admin only!');
	$dir=RE('dir'); if(strstr($dir,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(!sizeof($p=REsel())) { $sl=$filehost.$dir.'pre/'; $p=glob($sl.'*'); }
	else { $p=array(); foreach($p as $l) { if($n=='img') $p[]=$filehost.predir($l,'pre/'); } }

	$s=''; foreach($p as $x) if(is_ras_image($x)) { if(strstr($x,'..')) idie("Ошибка. Хакерствуем, бля?");
		if(is_file($x)){ $s.="idd('".predir_id($x,$filehost)."').src='".$www_design."img/foto.png';"; unlink($x); }
	}
	otprav($s."treeremove();clean('wait');");
}

if($a=='pre100x100') { AD(); if(!$admin) idie('admin only!');
	$rand=rand(0,100000);
	$fotoset=loadset();
	$dir=RE('dir'); if(strstr($dir,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(!sizeof($p=REsel())) { $sl=$filehost.$dir.'pre/'; $p=glob($sl.'*'); }
	else { $p=array(); foreach($p as $l) { if($n=='img') $p[]=$filehost.predir($l,'pre/'); } }

	$s=''; foreach($p as $x) if(is_ras_image($x)) { if(strstr($x,'..')) idie("Ошибка. Хакерствуем, бля?");
		if(is_file($x)){
		$s.="var i='".predir_id($x,$filehost)."'; idd(i).src=idd(i).src.replace(/\\?.*?$/g,'')+'?".$rand."';";
		pre100x100($x,$fotoset['q']);
		}
	}
	otprav($s."treeremove();clean('wait');");
}

//=================================== rotate ===================================================================
if($a=='rotate') { ADMA(); $s=''; $degree=RE0('degree'); $rand=rand(0,100000); $r=loadset();
	if(!sizeof($p=REsel())) idie('Empty rotate');
	foreach($p as $l) { $f=rpath($filehost.accd().$l);
		if(!is_file($f)) idie('Not found: '.h($l));
		if(!is_ras_image($l)) idie(h($l).' - is not image!');
			rotatejpeg($f,$degree,$r['Q']); delfilecache($f);
			$x=predir($f,'pre/'); if(is_file($x)) { rotatejpeg($x,$degree,$r['q']); delfilecache($f); }
			$s.="idd('$l').src=idd('$l').src.replace(/\\?.*?$/g,'')+'?".$rand."';";
	}
	otprav($s."clean('wait');");
}

//=================================== treeact ===================================================================
if($a=='htmlfile') {
	$l=preg_replace("/\.+/s",'.',RE('file')); if(stristr($l,'config.php')||stristr($l,'.htaccess')) AD();
	$f=find_myfile($l);
	allowfile($f,0);
	$s=fileget($f);

	if(($cs=(RE('charset')))) { $cs=strtolower($cs); if($cs=='utf'||$cs=='utf8') $s=uw($s); elseif($cs=='koi8') $s=kw($s); elseif($cs=='dos'||$cs=='cp866') $s=dw($s); }
	$id=h('file_'.$l);

	otprav("
setkey('Escape','',function(e){ clean('".$id."'); },false,1);
ohelpc('".$id."',\"".h($l)."\",\"<div style='display:inline-block;max-width:\"+(getWinW()*0.8)+\"px;word-wrap:break-word;'>".njsn($s)."</div>\");
");

}
//=================================== treeact ===================================================================
if($a=='treeact') { // ADMA();
	$l=preg_replace("/\.+/s",'.',RE('id'));

	if(stristr($l,'config.php')||stristr($l,'.htaccess')) AD(); // idie('Disable!');
	// idie(accd());

    if(is_ras_image($l)) {
	if(!is_file($filehost.accd().predir($l,'pre/'))) {
		otprav("if(confirm('Create previews for this folder?')) { ohelpc('wait','workind...',\"<img src='\"+www_design+\"img/ajax.gif'>\"); majax('foto.php',{a:'createpreview',id:'".h(dirname($l))."'});}");
	}
	otprav("var s='".$httphost.accd().$l."'; if(idd('bigfoto')&&idd('bigfotoimg').src==s) clean('bigfoto'); else bigfoto(s+'?".rand(0,10000)."')");
    }

    $f=find_myfile($l);
    allowfile($f,0);

// idie("$a:$l = $f");

	$ff=substr($f,strlen($filehost));
	$s=highlight_file($f,1);
	$s=str_replace("\r",'',$s);
//	$s=str_replace("\n",'<br>',$s);
//	$s=str_replace(' ','&nbsp;',$s);

	if(($cs=(RE('charset')))) { $cs=strtolower($cs);
	    if($cs=='utf'||$cs=='utf8') $s=uw($s);
	    elseif($cs=='koi8') $s=kw($s);
	    elseif($cs=='dos'||$cs=='cp866') $s=dw($s);
	    // elseif($cs=='windows'||$cs=='cp1251') $s=$s;
	}

if($ADM) otprav("starteditor=function(){majax('foto.php',{a:'edit_text',file:'".h($l)."'})};
setkey('Escape','',function(e){ clean('fotoset'); },false,1);
helpc('fotoset',\"<i class='knop e_kontact_journal' onclick='starteditor()'></i> ".h($wwwhost.$l)."\">,\"<div title='Edit this (press `E`)'><table id='fotoset_c'><tr>"
."<td style='max-width:\"+(getWinW()-50)+\"px;word-wrap:break-word;'>".njsn($s)."</td></tr></table></div>\");
setkey('KeyE','',starteditor,false);");
// idd('fotoset_c').onclick=function(e){ e=e||window.event;clean('fotoset'); }

otprav("setkey('Escape','',function(e){ clean('fotoset'); },false,1);
ohelpc('fotoset',\"".h($wwwhost.$l)."\",\"<table id='fotoset_c'><tr>"
."<td style='max-width:\"+(getWinW()-50)+\"px;word-wrap:break-word;'>".njsn($s)."</td></tr></table>\");");

}

//=================================== editpanel ===================================================================
if($a=='upload') {

ADMA(); $num=RE0("num"); $s=''; $r=loadset();
	$ff_file=rpath($filehost.accd().$r['dir']).'/'; testdir($ff_file); 
	$ff_file_pre=rpath($filehost.accd().$r['dir']).'/pre/'; testdir($ff_file_pre);
	$ff_www=rpath($wwwhost.accd().$r['dir']).'/';
	$ff_www_pre=rpath($wwwhost.accd().$r['dir']).'/pre/';

// dier($_FILES);

	if(count($_FILES)>0) foreach($_FILES as $f) { if(gettype($f['name'])!='array') { foreach($f as $a=>$b) $f[$a]=array(0=>$b); }

foreach($f['name'] as $a=>$b) {
	if(is_uploaded_file($f["tmp_name"][$a])){ $l=h($f["name"][$a]);
		if(!is_ras_image($l)) idie("Это разве фотка?");
		if(substr($l,0,1)=='.') idie("Имя с точки?");
		$l=hackfile($l);
		//--- фотоальбом Nokia ---
		if(preg_match("/^(\d\d)(\d\d)(\d{4})(\d+)\.jpg/si",$l,$m) && $m[3]."/".$m[2]==$r['dir']) $l=$m[1]."-".$m[4].".jpg";

		if(is_file($ff_file_pre.$l)) { $s.="<td><img onclick=\"foto('".$ff_www.$l."')\" src='".$fff_www_pre.$fl."'><div class=br><font color=red>".h($l)."</font></div></td>"; }
		else {
			if(!is_file($f["tmp_name"][$a])) idie("<font color=red>NOT FOUND: ".$f["tmp_name"][$a]."</font><br><pre>".nl2br(print_r($f,1))."<hr></pre>");
			obrajpeg($f["tmp_name"][$a],$ff_file.$l,$r['X'],$r['Q'],$r['logo']);
			obrajpeg($ff_file.$l,$ff_file_pre.$l,$r['x'],$r['q']);
			$s.="<td><img onclick=\"foto('".$ff_www.$l."')\" src='".$ff_www_pre.$l."'><div class=br>".h($l)."</div></td>";
			if($num) $ts.="\\n{_IMG: ".$l." _}";
	     	}
	}
}
}

	if($s=='') idie("Ошибка 2! ".nl2br(h(print_r($_FILES,1))));

	if($num && $ts!='') $ts="
		var v=idd('editor".$num."_Body'); if(v){
			v.value=v.value+'$ts';
			edit_polesend('Body',idd('editor".$num."_Body').value,".$num.");
		}
	";

otprav("
foto=function(f){
	helps('bigfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+f+\"'>\");
	idd('bigfoto').style.top = mouse_y+'px'; /*(getWinH()-imgy)/2+getScrollW()*/
	idd('bigfoto').style.left = (getWinW()-".$r['X'].")/2+getScrollH()+'px';
};

$ts

helps('foto_$hid',\"<table><tr align=center>".njsn($s)."</tr></table>\");
");

}

//==================================================================================================
/// убрать!
// function get_fotoset() { idie('EEEEEEEEEEEEEEERRRRRRRRRRRRRRRRRRRR get_fotoset!!!'); }

//===========================================================================================================
function adir($r,$fdir,$dir) { global $filehost;
    $a=glob($filehost.rpath($dir).'/*'); foreach($a as $l) {
	$name=rtrim(ltrim($fdir,"/").basename($l),"/");
	if(strstr($l,'.old---')) continue;
	if(isset($r[$name])) continue;
	$r[$name]=$l;
    }
    return $r;
}

function adirl($r,$dir) { $r[$dir]=$GLOBALS['filehost'].$dir; return $r; }

function albumdir($id) { global $acc,$filehost,$wwwhost,$www_design;
	$id=rtrim($id,'/').'/';

	$ud=accd();

	$r=array();
	$ADD=explode(" ",$GLOBALS['TREEtmpADD']);
	$i=rpath(rtrim($id,'/'));

	if($acc!='') {
	    if($i=='') {
		$r=adir($r,$i,$ud."/");
		foreach($ADD as $x) $r=adirl($r,$x);
	    } else {
	        $r=adir($r,$i."/",$ud."/".$i);
		if(in_array($i,$ADD)) $r=adir($r,$i."/",$i);
	    }

	} else {
	    $r=adir($r,$i."/",$id);
	}

	$dirs=$imgs=$files=''; foreach($r as $n=>$l) { $bn=basename($l);
		$bd=basename(dirname($l));
		$hvost=substr($l,0,strlen($l)-strlen("$bd/$bn"));
		$virt=($acc!=''&&$hvost==$filehost?1:0);

		if(is_dir($l)) { // все папки
			if($bn!='pre') $dirs.="['".hj($n)."/','<div class=ll".($virt?' style="color:#88d"':'').">".hj($bn)."</div>',1],"; // ."
		} else // все картинки
			if(is_ras_image($l)) {
			if(is_file(predir($l,'pre/'))) $pic=$wwwhost.$ud.predir($n,'pre/'); // pre preview
			else $pic=$www_design."img/foto.png"; // no preview
			$imgs.="<img src=\"".hj($pic)."\" onmouseover=\"treem(this)\" class=treef id=\"".hj($n)."\"> ";
		} else { // все остальное
			 $files.="['".hj($n)."','<div id=\"".hj($n)."\" class=le".($virt?' style="color:#ccc"':'').">".hj($bn)."</div>',0],";
		}

	}

return "
	treeonLoaded([".$dirs.($imgs!=''?"['','".$imgs."',0],":'').rtrim($files,',')."],'".hj($id)."');
	treeshowLoading(false,'".hj($id)."');
";
}

function is_ras_image($l){ return in_array(strtolower(explode_last('.',$l)),array('jpg','jpeg','gif','png','webp')); }
function predir($l,$pre){ $bn=basename($l); $bd=dirname($l).'/'; if($bd=='./') $bd=''; return $bd.$pre.$bn; }
function predir_id($l,$fh){ return preg_replace("/^.{".strlen($fh)."}(.*?)\/[^\/]+(\/[^\/]+)$/si","$1$2",$l); }

function hackfile($l) { if(rtrim($l,'/')==rpath($l)) return $l; hacklog($l); }
function hacklog($l) { logi('hack.txt',"\n".date("Y-m-d H:i:s")." acc: `".$GLOBALS['acc']."` loadfoto name: `".h($l)."`"); idie("Чо, хакер, бля? Щас нахуй отсюда вылетишь со своими `".h($l)."`"); }

function img_http_base($img) {
        if(strstr($img,'?')) list($img,)=explode('?',$img,2);
        if(!is_ras_image($img)) idie(h($img)." - is not image");
        $hst=$GLOBALS['httphost'].accd();
        if(strpos($img,$hst)!==0) idie("File `".h($img)."` not found!");
        return substr($img,strlen($hst));
}
function img_http2file($img) { $f=rpath($GLOBALS['filehost'].accd().img_http_base($img)); if(!is_file($f)) idie('Not found: '.h($f)); return $f; }
function img_http2www($img) { return rpath($GLOBALS['wwwhost'].accd().img_http_base($img)); }

idie(nl2br(h(__FILE__.": unknown action `".$a."`")));
?>