<?php
function SCRIPT($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_SCRIPT'][c($n)]=addm(c($s)); return ''; }
function STYLE($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_STYLE'][c($n)]=addm(c($s)); return ''; }
function addm($e) { return (strstr($e,"\n")?$e:ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."'","_l")); }

if(!function_exists('SCRIPTS')) { function SCRIPTS($s,$l=0) { if(!$l) $GLOBALS['_SCRIPT'][]=$s; else $GLOBALS['_SCRIPT'][$s]=$l; } }
function STYLES($s,$l=0) { if(!$l) $GLOBALS['_STYLE'][]=$s; else $GLOBALS['_STYLE'][$s]=$l; }

function add_mtime($s) {
    if(strstr($s,'://')) return $s; // �� ����
    if(false!==($p=strpos($s,'?'))) $s=substr($s,0,$p); // ������ ����� "?..." ���� ��� ���� ��� �����
    if( is_file(($f=rpath($GLOBALS['host'].ltrim($s,'/')))) ) return $s.'?'.filemtime($f);
    return $s;
}

function SCRIPT_ADD($s) { $GLOBALS['_SCRIPT_ADD'][$s]=add_mtime(hh($s)); }

function STYLE_ADD($s) { $s=str_replace('{www_css}',$GLOBALS['www_css'],add_mtime(hh($s)));
	$s="link href='".add_mtime(hh($s))."' rel='stylesheet' type='text/css' charset='".$GLOBALS['wwwcharset']."'";
	$GLOBALS['_HEADD'][$s]=$s;
}

//if(isset($_GET['module'])) { // ��������� � ������� ����� GET
//	$a=array("onload:'yes'"); foreach($_GET as $n=>$l) { if($n!='module') $a[]="'$n':'$l'"; }
//	SCRIPTS("var page_onstart=[\"majax('".h($_GET['module']).".php',{".implode(',',$a)."})\"]");
//}

function mpr($s,$ara) { $s=$ara[$s]; foreach($ara as $n=>$l) $s=str_replace('{'.$n.'}',$l,$s); return $s; }

function ekr($s) { return str_replace(array('{','}'),array("\003","\004"),$s); }

function modul_n($t) { global $arap;
    if(isset($arap[$t[1]])) {
	if(false===strpos($arap[$t[1]],$t[0])) return $arap[$t[1]]; // ���� �� �������� ���
	return str_replace($t[0],"<font color='red'>&#123;recursion:".$t[1]."&#125;</font>",$arap[$t[1]] );
    }
    if($t[1]=='help') { $s=''; foreach($arap as $n=>$l) $s.="<div>".h($n)." = ".h($l)."</div>"; return $s; }
    return $t[0];
}

function modul_hn($t) { global $arap; if(isset($arap[$t[1]])) return h($arap[$t[1]]); return $t[0]; }

function modul_hun($t) { global $arap;
	if(isset($arap[$t[2]])) { $s=$arap[$t[2]]; switch($t[1]) { // ���� ���� ������
		case '#': // str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$s); // ������������ HTML-�������� ������ <>& {#:<����>} --> &lt;����&gt;// return h($s); 
		case 'njs': return njs($s); // ������������ ������� ��� JS
		case 'url': return urlencode($s); // ������������ url
		case 'wurl': return urlencode(wu($s)); // ������������ url � �������������� �� 1251 � UTF-8
		case 'njsn': return njsn($s); // ������������ ������� ��� JS � ������ \n
		case '#njs': return h(njs($s)); // ������������ ������� ��� JS � ������������
		case '#njsn': return h(njsn($s)); // ������������ ������� ��� JS � ������ \n � ������������
		case '#njs#': return ekr(h(njs($s))); // ������������ ������� ��� JS � ������������
		case '#njsn#': return ekr(h(njsn($s))); // ������������ ������� ��� JS � ������ \n � ������������
		case 'c': return c($s); // ������ ������ � ����� ������� � �������
		case 'length': return strlen($s); // ����� �������� � ������
		case 'nl2br': return nl2br($s); // \n � <br/>
		case '#nl2br': return nl2br(h($s)); // \n � <br\> � ��� ������������ HTML-��������
		case 'date': return date("Y-m-d H:i:s",$s); // ����� � ����
	}
		if('0'==$t[1][0]) return sprintf("%".$t[1]."d",$s);  // ���� ������ ������ 0, �� ���������� ��� ����� � ������ ����������� ������ {06:id} 000012

	} else { $s=$t[2]; switch($t[1]) {
		case 'chr': return chr(intval($s)); // {chr:160}{chr:169}{chr:151}{chr:171}{chr:187}
		case 'LL': // {?LL@Editor:fidopost?} {LL@Editor:fidopost|arg1|arg2|...|argXX}
			if(!strstr($s,'|')) return LL($s);
			$a=explode('|',$s); $e=$a[0]; unset($a[0]); return LL($e,$a);
		case 'print': return ekr(h(print_r($arap,1))); // {print:all}

		case 'div': $a=explode('-',$s); return ($a[0]/$a[1]); // ������� {divm:{unic}-100}
		case 'divm': $a=explode('-',$s); return ($a[0]%$a[1]); // ������� �� ������� {divm:{unic}-100}
	}

	} return $t[0];
}


function modul_huns($t) { $s=$t[2]; switch($t[1]) {
//		case 'divm100': return "(100)"; // {#:<����>} --> &lt;����&gt;
		case '#': return h($s); // {#:<����>} --> &lt;����&gt;
		case 'njs': return njs($s); // ������������ ������� ��� JS
		case 'url': return urlencode($s); // ������������ url
		case 'wurl': return urlencode(wu($s)); // ������������ url � �������������� �� 1251
		case 'njss': return njss($s); // ������������ ������� ��� JS � ������ \n
		case 'njsn': return njsn($s); // idie(nl2br(h(njsn($s)))); // ������������ ������� ��� JS � ������ \n
		case '#njs': return h(njs($s)); // ������������ ������� ��� JS � ������������
		case '#njsn': return h(njsn($s)); // ������������ ������� ��� JS � ������ \n � ������������
		case '#njs#': return ekr(h(njs($s))); // ������������ ������� ��� JS � ������������
		case '#njsn#': return ekr(h(njsn($s))); // ������������ ������� ��� JS � ������ \n � ������������
		case 'c': return c($s); // ������ ������ � ����� ������� � �������
		case 'length': return strlen($s); // ����� ��������
    }
}

function modul_s($t) { global $arap; $n=$t[1]; $s=$t[3];

	if($t[2]=='?') { // {?per?html_true|html_false?}
		list($n,$a,$b)=explode(strstr($s,"\n")?"\n":'|',$s,3);
		return trim(($arap[$n]?$a:$b),"\n\r\t ");
	}

	elseif($n=='LL') { // {?LL@Editor:fidopost?} {?LL@Editor:fidopost|arg1|arg2|...|argXX?}
		if(!strstr($s,'|')) return LL($s);
		$a=explode('|',$s); $e=$a[0]; unset($a[0]);
		return LL($e,$a);
	}

	elseif($n=='chr') { // {?chr:160?}{?chr:169?}{?chr:151?}{?chr:171?}{?chr:187?}
		return chr(intval($s));
	}

	elseif($n=='njs')    return njs($s); // ������������ ������� ��� JS
	elseif($n=='url')    return urlencode($s); // ������������ url
	elseif($n=='wurl')   return urlencode(wu($s)); // ������������ url � �������������� �� 1251
	elseif($n=='njsn')   return njsn($s); // ������������ ������� ��� JS � ������ \n
	elseif($n=='#njs')   return h(njs($s)); // ������������ ������� ��� JS � ������������
	elseif($n=='#njsn')  return h(njsn($s)); // ������������ ������� ��� JS � ������ \n � ������������
	elseif($n=='#njs#')  return ekr(h(njs($s))); // ������������ ������� ��� JS � ������������
	elseif($n=='#njsn#') return ekr(h(njsn($s))); // ������������ ������� ��� JS � ������ \n � ������������

	elseif($t[2]==':') { if(isset($arap[$n])) $n=$arap[$n];
		foreach(explode((strstr($s,"\n")?"\n":'|'),$s) as $l) {
			if(!strstr($l,':')) continue;
			list($x,$c)=explode(':',$l,2);
			if(c($x)==$n) break;
	        } return trim($c,"\n\r\t ");
	}
}

function mper($s,$ara) { $ara['www_design']=$GLOBALS['www_design'];

// dier($s);

// if(isset($ara['helpmessage'])) return $ara['helpmessage'];
	$GLOBALS['arap']=$ara;
	$maxlen=10000000;
//	$oldlen=strlen($s);

	$s_old=''; $stop=1000; while($s!=$s_old && --$stop) { $s_old=$s;
		if(strlen($s)>$maxlen) idie("<font color=red>ERROR MAX SIZE (".strlen($s).") > $maxlen </font><hr>");

                $s=preg_replace_callback("/\{([0-9a-z_\-]+)\}/si","modul_n",$s); // {name}
                $s=preg_replace_callback("/\{#([0-9a-z_\-]+)\}/si","modul_hn",$s); // h({name})
                $s=preg_replace_callback("/\{([0-9a-z_\-\#]+)\:([0-9a-z_\-]+)\}/si","modul_hun",$s); // {opt:name}

		$s=str_replace(array("{?","?}"),array("\001","\002"),$s);
		$s=preg_replace_callback("/\001([0-9a-z_\-]+)([\?\:\@])([^\001\002]+)\002/si","modul_s",$s);

                $s=preg_replace_callback("/\{@@@([0-9a-z_\-\#]+)\:(.+)@@@\}/si","modul_huns",$s); // {opt:name}
	}
        return str_replace(array("\001","\002","\003","\004"),array('{','}','{','}'),$s);
}

function parse_e_conf($e) {

if($e=='?') { // help
	global $mod,$site_mod,$site_module;
        $m=$site_mod.$mod.".php"; if(file_exists($m)) $f=$m; else { $m=$site_module.$mod.".php"; if(file_exists($m2)) $f=$m2; else $f=''; }
	idie("<pre>".h($s));
}

	$a=array(); $p=explode("\n",$e); $i=0;
	for($ln=sizeof($p);$i<$ln;$i++) { $l=c($p[$i]);
	    if($l=='' or !strstr($l,'=')) break;
	    list($n,$v)=explode('=',$l,2); $n=c($n);
	    if(strstr($n,'?')) break;
	    if($n=='' or $n!=strtr($n,' <>-"\'','xxxxxx')) break;
	    $a[$n]=c0($v);
	}
	$a['body']=implode("\n",array_slice($p,$i));

/*
// $a=array('body'=>''); $dalee=0; $p=explode("\n",$e);
	foreach($p as $l) {
		if($dalee) { $a['body'].=$l."\n"; continue; }
		if(c($l)=='' or !strstr($l,'=')) { $dalee=1; $a['body'].=$l."\n"; continue; }
		list($n,$v)=explode('=',$l,2); $n=c($n);
		if($n=='' or $n!=strtr($n,' <>-"\'','xxxxxx')) { $dalee=1; $a['body'].=$l."\n"; continue; }
		$a[$n]=c($v);
	}*/

    return $a;
}

// ==============================================================================================
// ���������� ��� ��������� � �����

$GLOBALS['mainmod']=array('ADMINSET','ADMINPANEL','PRAVKA','PREVNEXT','TITLE','STATISTIC','TEXT','OEMBED','COUNTER','UNIC','ANOTHER_DATE','HEAD','HEAD_D','HEAD_N','HEAD_TXT','HEADERS');
$GLOBALS['norepeat_modules']=array('CONTENTER','ADMIN','PRAVKA','LAST','FIDO','INSTALL','RSS'); // 'UNIC11111',
$GLOBALS['repeat_modules']=array();
/*
function modules($s) { // $o='';
        $s_old=''; $stop=1000; while($s!=$s_old && --$stop) {
//                $s=str_replace("{_","\001",str_replace("_}","\002",$s));
                $s_old=$s;
//                $s=preg_replace_callback("/\001([^\001\002]+)\002/s","module",$s);
                $s=preg_replace_callback("/\{_(?!\{_)(.*?)_\}/s",'module',$s);
//		$o.='<hr>'.h($s);
// $o=preg_replace("/\{#(?!.*?\{#)(.*?)#\}/si",'TTT',$o);
//              $s=preg_replace_callback("/\{_(.+?)_\}/s","module",$s);
        }
       return $s;
//        return str_replace(array("\001","\002"),'############################',$s);
}
*/

function parss($s,$r1='{_',$r2='_}'){ $l1=strlen($r1); $l2=strlen($r2); $d=0; $stop=100; while($stop--) {
        if(($i=strpos($s,$r1))===false || ($j=strpos($s,$r2))===false) return false;
        if(($k=strpos(substr($s,$l1),$r1))===false || $j<$k) return array($d+$i,$j+$l2);
        $s=substr($s,$k+$l1); $d+=$k+$l1;
} return false;
}



function modules($s) { return md($s);
/*
 if($GLOBALS['unic']==4) return md($s);
        $s_old=''; $stop=500; while($s!=$s_old && --$stop) {
                $s=str_replace("{_","\001",str_replace("_}","\002",$s));
                $s_old=$s;
                $s=preg_replace_callback("/\001([^\001\002]+)\002/s","module",$s);
        }
        return $s;
*/
}

/*
function module($t) { global $mod; $s=$t[1]; // ��������� ������
        if(strstr($s,':')) { // ������������ ������
                list($mod,$arg)=explode(':',$s,2); $mod=rpath(c($mod));
	if(in_array($mod,$GLOBALS['norepeat_modules'])) { // ��������� ������� ��������� ����������� ������ 1 ����
	    if($GLOBALS['norepeat_modules'][$mod]==1) return "<font color=red>MODULE \"$mod\" can not repeat!</font>";
	    $GLOBALS['norepeat_modules'][$mod]=1;
	}

                if(!function_exists($mod) && !in_array($mod,$GLOBALS['mainmod'])) {
                        $modfile=$GLOBALS['site_mod'].'/test/'.$mod.".php";
                        $modfile2=$GLOBALS['site_module'].'/test/'.$mod.".php";
                        if(file_exists($modfile)) include_once($modfile);
			elseif(file_exists($modfile2)) include_once($modfile2);
			else return (in_array($mod,array('SIGNAL'))?'':"<font color=red>MODULE NOT FOUND: <b>".h($t[1])."</b></font>");

                        if(!function_exists($mod)) idie("Func not found: ".h($mod)
.($GLOBALS['admin']&&isset($GLOBALS['article']['Date'])?"
<p><a href=".$GLOBALS['httphost']."editor/?Date=".$GLOBALS['article']['Date'].">������������� ".h($GLOBALS['article']['Date'])."</a>
<p><div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$GLOBALS['article']['num']."'})\">������������� � ����</div>
":'')
);
                }

                return call_user_func($mod,c0($arg));
        }

        // ����� - ������ ������ �� ����
        $o=ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($s)."'","_l");
//        if(preg_replace("/\{_(SCRIPT\:|STYLE\:|SCRIPT_ADD\:|STYLE_ADD\:).*?_\}/si",'',c($o))=='') return '';
        return "<!--".$p['id']."-->".$o."<!--/".$p['id']."-->";
}
*/

// =========================================

function md($s,$stopi=500) {
    while(--$stopi && false!==strpos($s,'{_')) $s=md0($s);
    return $s;
}

function md0($s,$stopi=500) { if(!$stopi) return "<font color=red>ERROR MODULE LOOP</font>".$s;
    $start=0;
    if(false===($start=strpos($s,'{_',$start))) return $s;
    $d=1;
    $stop=500;
    $cu=$start;
    while(--$stop && $d) {
        if(false===($i_e=strpos($s,'_}',$cu+2))) return md($s."_}<font color=red><b>_</b><b>}</b></font>",--$stopi);
        $i_s=strpos($s,'{_',$cu+2);
        if($i_s===false || $i_s>$i_e) { $d--; $cu=$i_e; }
        elseif($i_s!==false) { $d++; $cu=$i_s; }
    }
    if(!$stop) return "<font color=red>ERROR MODULE LOOP2</font>".$s;
        $s0=domd(substr($s,$start+2,$i_e-$start-2),--$stopi);
    return substr($s,0,$start) .$s0 .substr($s,$i_e+2);
}

function domd($s) { // ��������� ������
    if(false!==strpos($s,':')) { // ������������ ������
                list($mod,$arg)=explode(':',$s,2); $mod=rpath(c($mod));
        if(in_array($mod,$GLOBALS['norepeat_modules'])) { // ��������� ������� ��������� ����������� ������ 1 ����
            if(@($GLOBALS['norepeat_modules'][$mod]==1)) return "<font color=red>MODULE \"$mod\" can not repeat!</font>";
            $GLOBALS['norepeat_modules'][$mod]=1;
        }

        if(!function_exists($mod) && !in_array($mod,$GLOBALS['mainmod'])) {

            $modfile0=$GLOBALS['site_mod'].'IFMOD/'.$mod.".php";
            $modfile=$GLOBALS['site_mod'].$mod.".php";
    	    $modfile2=$GLOBALS['site_module'].$mod.".php";
            if(file_exists($modfile0)) { include_once($modfile0); $GLOBALS[$mod.'_nomod_mode']=1; }
            elseif(file_exists($modfile)) include_once($modfile);
            elseif(file_exists($modfile2)) include_once($modfile2);
            else return (in_array($mod,array('SIGNAL'))?'':"<font color=red>MODULE NOT FOUND: <b>".h($mod)."</b></font>");

            if(!function_exists($mod)) {
                idie("Function not found: ".h($mod)
                .($GLOBALS['admin']&&isset($GLOBALS['article']['Date'])?
                    "<p><a href=".$GLOBALS['httphost']."editor/?Date=".$GLOBALS['article']['Date'].">������������� ".h($GLOBALS['article']['Date'])."</a>"
                    ."<p><div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$GLOBALS['article']['num']."'})\">������������� � ����</div>"
                :'')
                );
            }
        }

	if(!isset($GLOBALS[$mod.'_nomod_mode'])) $arg=md($arg);
        return call_user_func($mod,c0($arg));
    }

    // ����� - ������ ������ �� ����
    $o=ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e(md($s))."'","_l");
    return ($o===false?'':$o);
}



?>