<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// перевод крупные буквы

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>"Буквы figlet",
'title'=>"Буквы figlet",
'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);






$txt=$_POST['txt'];

$o="

<script>
cpbuf=function(e){ if(typeof(e)=='object') e=e.innerHTML;
    var area = document.createElement('textarea');
    document.body.appendChild(area);
    area.value = e;
    area.select();
    document.execCommand('copy');
    document.body.removeChild(area);
    document.querySelector('#cp').style.display='inline-block';
    // alert('Copypasted:<p><b>'+e+'</b>',500);
};
</script>

<center>
<form action=".$mypage." method=post>
<p><input size=10 name=txt class=t value='".htmlspecialchars($txt)."'> <input type='submit' name='go' value='Do'>
</form></center>";

if($txt=='') die($o);
unset($q); exec("/usr/bin/figlet -t -C utf8 -f standard \"".h($txt)."\"",$q);

$p="\n//         ";

$o.= "<p><center>"
."<div id='cp' style='display:none;color:red;'>Copypasted to clipboard</div>"
."<table border=0><td>"
."<pre style='border:1px solid #ccc; cursor:pointer;' onclick=\"cpbuf(this.innerHTML)\">".trim($p.h(implode($p,$q)),"\n\t ")."</pre>"
."</td></table></center>";
die($o);

?>