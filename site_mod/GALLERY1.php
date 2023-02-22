<?php

SCRIPT_ADD("//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");

/*
body,html,div,blockquote,img,label,p,h1,h2,h3,h4,h5,h6,pre,ul,ol,li,dl,dt,dd,form,a,fieldset,input,th,td{border:0;outline:none;margin:0;padding:0;}
body{height:100%;background:#fff;color:#1f1f1f;font-family:"Lucida Sans Unicode","Lucida Grande",Arial,Verdana,sans-serif;font-size:13px;padding:7px 0;}
ul{list-style:none;}
.text-center {text-align: center; padding: 10px 0;}
*/

STYLES("
.gallery1 {width: 660px; margin: 0 auto 20px auto;}
.gallery1 ul {padding-left: 10px;}
.gallery1 li {display: inline; margin-right: 3px;}
.gallery1_main-img {background: url(".$GLOBALS['wwwhost']."/extended/gallery1/bg_img.png) no-repeat 0 0; padding: 26px;}
");


SCRIPTS('

function gallery1() {

$.fn.preload = function() {
    this.each(function(){
        $("<img/>")[0].src = this;
    });
}

	var galleryClass = ".gallery1";
	$(galleryClass+" li img").hover(function(){
		var $gallery = $(this).parents(galleryClass);
		$(".gallery1_main-img",$gallery).attr("src",$(this).attr("src").replace("pre/",""));
	});
	var imgSwap = [];

	$(galleryClass+" li img").each(function(){
		imgUrl = this.src.replace("pre/", "");
		imgSwap.push(imgUrl);
	});
	$(imgSwap).preload();
}

page_onstart.push("gallery1()");
');



function GALLERY1($e) {

$o='';
$p=explode("\n",$e);
$img1=''; foreach($p as $l) { if(empty($l)) continue;
    if(empty($img1)) $img1=$l;
    $ll=dirname($l)."/pre/".basename($l);
    $o.="<li><img src='".h($ll)."' /></li>";
}

return "<div class='gallery1'>
	<img src='".h($img1)."' class='gallery1_main-img' />
	<ul>
".$o."
	</ul>
</div>";

}

?>