<?php /* Вставка фоток из фотоальбома

Указываем имя фотки в альбоме или url (автоопределение). Также можно поставить запятую и указать дополнительный аргумент выравнивание:
center - по центру экрана
left - дальнейший текст будет обтекать фотку слева
right -  дальнейший текст будет обтекать фотку справа

<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/1.jpg _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/2.jpg, center _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/3.jpg, left _} Все остальное будет огибать эту фотку слева.  Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева.  Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева.</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/4.jpg, right _} Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа.</div>

*/


function IMG($e) {
    $cf=array_merge(array(
	'WIDTH'=>false,
	'CENTER'=>false
    ),parse_e_conf($e)); $e=$cf['body'];

    $c=(strstr($e,',')?',':' ');
    if(strstr($e,$c)) { $g=explode($c,$e);
	$e=$g[0]; unset($g[0]);
	foreach($g as $n=>$l) { $l=strtolower($l);
	    if($l=='center') $cf['CENTER']=1;
	    elseif(intval($l) != 0)
 $cf['WIDTH']=intval($l);
//	    else continue;
//	    unset($g[$n]);
	}
//	if(1*$g[1]==0) list($e,$o)=$g;
    }


// if($GLOBALS['ADM']) dier($cf,$e);

	$e=(strstr($e,'/')?$e:$GLOBALS['foto_www_small'].$e); $e=c($e);// $o=c($o);
	$mn='foto-'.md5($e);

	if($GLOBALS['ADM']) {

// idie( "nc=" .$_GET['nocache'] );

		$s1="<div id='".$mn."' style='position:relative;display:inline-block'>"."<div style='font-size:10px;position:absolute;top:10px;left:10px;'>";
		$s2="</div>";
		$s3="onload=\""
."var w=this.width,h=this.height;"
."idd('".$mn."_w').value=w;"
."zabil('".$mn."_h',h);"
// ."idd('".$mn."_w').size=1+0*(''+w).length;"
."zabil('".$mn."_ww',w);"
."zabil('".$mn."_hh',h);"
."\" ";

if(!strstr($e,'://') || substr($e,0,strlen($GLOBALS['httphost']))==$GLOBALS['httphost'] ) {

    if(time()-$GLOBALS['article']['DateUpdate'] < 5*60 || isset($_GET['nocache']) ) {

    if(!strstr($e,'://')) $e=$GLOBALS['httpsite'].h($e);

    $s1.="<div>"
."<i class='knop e_remove' title='Delete' onclick=\"if(confirm('Delete?')) majax('foto.php',{a:'fot_del',img:'".$e."'})\"></i>"
."&nbsp;<i class='knop e_rotate_left' title='270' onclick=\"if(confirm('Rotate 270?')) majax('foto.php',{a:'fot_rotate',degree:270,img:'".$e."'})\"></i>"
."&nbsp;<i class='knop e_rotate_right' title='90' onclick=\"if(confirm('Rotate 90?')) majax('foto.php',{a:'fot_rotate',degree:90,img:'".$e."'})\"></i>"
."&nbsp;<i class='knop e_reload_page' title='180' onclick=\"if(confirm('Rotate 180?')) majax('foto.php',{a:'fot_rotate',degree:180,img:'".$e."'})\"></i>"
."&nbsp;<i class='knop e_crop' title='crop' onclick=\"majax('foto.php',{a:'fot_crop_engine',img:'".$e."'})\"></i>"
."&nbsp;<i class='knop e_kontact_journal' title='Album Edit' onclick=\"majax('foto.php',{a:'album_edit',p:'".RE('p')."',num:num})\"></i>"
."</div>";

}

} else {

SCRIPTS("IMG_scr","

function IMGreview(e) {
    var x=1*e.value,id=e.id.split('_')[0],w=1*vzyal(id+'_ww'),h=1*vzyal(id+'_hh');
    if(x>w) e.value=x=w; 
    var y=Math.floor(x*(h/w));
    e=idd(id+'_i'); e.width=x; e.height=y;
    zabil(id+'_h',y);
}

function IMGrz(id,i) {
    var x=1*idd(id+'_w').value,y=vzyal(id+'_h'),x0=1*vzyal(id+'_ww');
    if(i>0 && x>=x0 || i<0 && x<=10) { salert('limit: '+x0,500); return; }

    var X=x+i,Y=Math.floor(X*y/x),e=idd(id+'_i'); e.width=X; e.height=Y;

    idd(id+'_w').value=X;
    zabil(id+'_h',Y); /*idd(id+'_h').value=Y;*/
}

");

    $id=$mn.'_i';

    $s1.="<form onsubmit=\"return send_this_form(this,'foto.php',{a:'download',r:3,num:".$GLOBALS['article']['num'].",url:'".h($e)."'})\">"
."<div style='padding:2px;display:inline-block;color:black;background-color:white;border:1px solid green;'>"

."<div class='br l' title='подпись' onclick=\"otkryl('".$mn."_txt');\"><span id='".$mn."_ww'>0</span>x<span id='".$mn."_hh'>0</span>"
// ."&nbsp;".$e
."</div>"

."<i class='knop e_viewmagm' onclick=\"IMGrz('".$mn."',-10)\"></i> &nbsp; "
."<i class='knop e_viewmagp' onclick=\"IMGrz('".$mn."',+10)\"></i> &nbsp; "
."<input type=text name='newsize' size=3 id='".$mn."_w' value='' onchange=\"IMGreview(this);\">x<span id='".$mn."_h'></span>"
// .selecto('newsize','1024',array('50'=>'50','100'=>'100','150'=>'150','200'=>'200','300'=>'300','400'=>'400','500'=>'500','600'=>'600','700'=>'700','900'=>'900','1024'=>'1024'),"class='r' name")
."<div id='".$mn."_txt' style='display:none;'>подпись:&nbsp;<input title='Signature' style='font-size:10px' type='text' size='25' name='sign' value=''></div>"
."<input style='font-size:10px' type=button value='View' onclick=\"\"IMGreview(idd('".$mn."_w'))\"> &nbsp; "
."<input style='font-size:10px' type=submit value='Download'>"
."</div>"
."</form>";

    $s3="id='".$id."' ".$s3;

}


$s1.="</div>";


} else $s1=$s2=$s3='';

	if($cf['WIDTH']!=false) $s3="width='".$cf['WIDTH']."' ".$s3;

	if(isset($GLOBALS['IMG_template'])&& !empty($GLOBALS['IMG_template'])) return mpers($GLOBALS['IMG_template'],array('text'=>$e,'pre'=>$s1,'post'=>$s2,'html'=>$s3,'align'=>$cf['CENTER']));

	$x=$s1."<img class='foto' ".$s3."src='".h($e)."' border='0'>".$s2;
	if($cf['CENTER']) $x="<center>".$x."</center>";

//if($GLOBALS['ADM']) dier($cf); // return h($x);

	return $x;
}

?>