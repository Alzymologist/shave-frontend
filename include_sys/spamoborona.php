<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ����������� � �������

// if($GLOBALS['admin']) idie("���, �� ������������ �� ����� ����");

// ���������� �� ������ ����������� ��������
$t=time()-60*60; $glob=glob($GLOBALS['antibot_file']."*.jpg"); if($glob) foreach($glob as $l) { if(filemtime($l)<$t) unlink($l); }

// ���� ������ cronfile - ��������� ��� � ������ ������ �����������, ������ ��� ����

// if(!is_file($GLOBALS['cronfile']) or (time()-filemtime($GLOBALS['cronfile'])) > 60*60 ) { include_once $GLOBALS['filehost']."cron.php"; }

/*
if($GLOBALS['admin']) idie('ok: '
.(is_file($GLOBALS['cronfile'])?1:0)." - "
.(time()-filemtime($GLOBALS['cronfile']))
.(
(!is_file($GLOBALS['cronfile']) or (time()-filemtime($GLOBALS['cronfile'])) > 6 ) ? 'DA' : 'NET'

)
);
*/

// �� �������� ����� �������, �� ���� �� �������. ��� ���������: $name,$text,$mail, � ����� ���� $scr=1 - �� �� ����� �����

function normtxt($s) {
    $s=strtr($s,'ETYOPAHKXCBMeyopakxc','��������������������'); // ������
    return strtr($s,"\n".' �����Ũ��������������������������','__��������������������������������'); //�������
}

/* lleo: */
function zbanmudaka($i=0,$s=' ') { $logban='BAN-comment.log';

    // 0 - ��������, ���� �������� �����

    if($i==0) {
        if(1*$GLOBALS['unic']==0) return logi($logban,"\n#### ERROR: unic=0".$s);
        // msq("UPDATE `dnevnik_comm` SET `scr`='1',`Text`='".e("{screen:\n".$GLOBALS['ara']['Text']."\n}")."' WHERE `unic`=".e($GLOBALS['unic']) ); // ���������� ��� ��� ��������
        if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>(1*$GLOBALS['IS']['capchakarma']+20)),"WHERE `id`=".intval($GLOBALS['unic']) ); // �������� �������� ������
        return logi($logban,"\n#### banned softly #".$s);
    }

    // 1 - ������ � ������� �����
    if($i==1) {
        if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // �������� ������
        logi($logban,"\n######### BANNED HARDLY #".$s);
        redirect('http://natribu.org/');
        // otprav("f_save('comment',''); clean('".$GLOBALS['idhelp']."');if(typeof(playswf)!='undefined') playswf('".$GLOBALS['httphost']."/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));");
    }


    // 2 - ��������� ��������
    if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>255),"WHERE `id`=".intval($GLOBALS['unic']) ); // �������� ������
    logi($logban,"\n######### BANNED HARDLY #".$s);
    // redirect('http://natribu.org/');
    otprav("f_save('comment',''); clean('".$GLOBALS['idhelp']."');if(typeof(playswf)!='undefined') playswf('".$GLOBALS['httphost']."/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));");

}
/* :lleo */



$tss='_'.normtxt($text);
$politword="����� putin ����� ����� ������ avaln �������� �������� ������� ������� ������ ����� ������ ������_��� ������_��� ������_��� _��_ �������� �������"
	    ." ������ ����� ����� ����� ������� ������ ������ ������ ������ ������ ������ ������� ������� ������ ������ ������ ������ ���� ������ ������";




if(!$GLOBALS['admin']) { // ��� �����������, �� �� ��� ������

	// 1. ������ ������������� �������� �����!
	if(strstr($name,$GLOBALS['admin_name'])) $name="Looser #".$GLOBALS['unic'];

	// 2. ���� ����������� ����� ����� ����� ���������� ������� - ��� 99% ������! � ������ - ��� 90% ����!
	$l=preg_replace("/p\.s/si",'',
// ��� ��������������: ���������, ���� �� ����� � ��������� �����
(!empty($GLOBALS['IS']['openid'])||!empty($GLOBALS['IS']['login'])?$text:$text.$name)
); // ���� ���� ���� ����������: 'P.S.'
	if(preg_match("/[a-z]\.[a-z]/si",$l) or strstr($l,'<')) $scr=1; // ������ ���!

	// ������ �����? ������ ���� ���!
//	if(stristr($text,'lleo.aha.ru/na')||stristr($text,'natribu.org')) redirect('http://natribu.org/');

// if(stristr($text,'���')) idie("����� '���' ������ ��������� ����!"); // �� ������� ���� � ������ ������������ �������
//if(stristr(strtr($text,'�����i','eyeyyy'),'jquery')) idie("<table width=500><td><div align=justify>� �������� ��������� � ����� �������� jQuery! ����� ��������! ����. ��� ����� ���� ������������ ������ ��������, ��������� � ����� <a href='".$GLOBALS['wwwhost']."install.php?load=include_sys/spamoborona.php&mode=view'>include_sys/spamoborona.php</a>, �� ������ �������� �� ��� ������ ����� � �������� ����� ������.</div></td></table>");

	$tt=str_replace(array("\n","\t","\\"),' ',$text); $tt=explode(' ',$tt);
	foreach($tt as $et) { if(strlen($et)>100 && !strstr($et,'://')) idie("<table width=500><td><div align=justify>���� ��������� ����� �����-�� ������� ����� ��� ��������.<br>�� ��� ��� �����:<br><i>".h(substr($et,0,50))."...</i><br>��������� ���������� � ������� ��� ������ ������.</div></td></table>"); }

if(preg_match("/^https*\:\/\/[^\s]+$/si",$text)) idie("<table width=500><td><div align=justify>�������: �� ������ ������ ��� ���������, ��� �������� �� �������������. ����� �������� ���� ����� ����, ����� ���� ����� �������, � ��� ����. �������.</div></td></table>");


/* lleo: */

/*
// ������ ������
    if($unic==6342186) { foreach(explode(' ',$politword) as $l)	if(stristr($tss,$l)) {
	    $cap=(1*$GLOBALS['IS']['capchakarma'])+1; msq_update($GLOBALS['db_unic'],array('capchakarma'=>e($cap)),"WHERE `id`=".e($GLOBALS['unic']) );
	    idie("������, �� ����� ���� ��������� �� ����� ���������� �� ���� ��������.<p>������ ���, ����� �� ������ ������� � ��������, ���� ����� ����� ����� �� 1 �����.<br>��� ������ ��� ��� ".$cap);
	}
    }
*/

if(time()-$GLOBALS['IS']['time_reg'] < 84600) { // �������?



if( // �� ����� ����
    stristr($text,"����� ����")
|| stristr($text,"���� �����")
|| stristr($text,"����� ����")
) zbanmudaka(2,"������ �� ����� ���� $name : $text");


/*
if(
stristr($text,"��� ������� ������� �������� �����")
|| stristr($text,"��������� �������")
|| stristr($text,"������ �����������")
|| intval($name) === $name
) zbanmudaka(1,"������ ��� ������� ������� �������� �����: $name : $text");
*/












/*
    // ��������
    if(strstr(normtxt($name),'�������')) zbanmudaka(1," �������� ���� �����: $name"); // ������ ����� �������� � ����� ��������

    if($id) { // ���� ����� �� ���-�� �������
	    $parent_unic=intval(ms("SELECT `unic` FROM `dnevnik_comm` WHERE `id`=".intval($id),'_l'));
	    if($parent_unic==6707323) zbanmudaka(0," name: $name\nText: $text"); // � ���� ��� ����� ����, �� ����� �����
    }
*/

/*
  // ����� ������� �� ���
  if(0)   foreach(explode(' ',"���� ��� ���� ���� ���� ���� ������ �� ���� ���� ���� ��� ���� ���� ���� ���� ���� ���� ���� ���� ����� ����� ���� ��� ��� �� ��� ��� ��� ���") as $l){
	if(stristr($tss,$l)) {
	    if(!$GLOBALS['admin']) msq_update($GLOBALS['db_unic'],array('capchakarma'=>222),"WHERE `id`=".e($GLOBALS['unic']) ); // �������
	    logi('comment-img-check-all.log',"\n-----------".date("Y-m-d H:i:s")."-----".$GLOBALS['IP']."========== "."\n".$GLOBALS['BRO']."\n## banned MUDOSLOV: ##".$text);
	    redirect('http://natribu.org/');
	    // $text="{screen:\n".$text."\n}";
	    // break;
	}
  }

  // ������������� �������� � ��������
  if(0) foreach(explode(' ',$politword) as $l) if(stristr($tss,$l)) idie("������� ����, � �� ���� ������ � ����� ����� ����������� �� ���� ��������.
����� ���������� �� ����� ����� ������, �� ������� ��� ������.
���� �� ������ �������� ����������� �� ���� ��������, �������� �������� ����������� ���-������ �� ������ ��������, �� �� �� ���� �������.
��������, ������ �������� �������������� ��� �� <a href=http://lleo-kaganov.livejournal.com>http://lleo-kaganov.livejournal.com</a> ��� �������.
���, �� ����� ��������, �� ������������ ��������� � ��������, � ���� ����� ������ ������� �����.");
*/

/* :lleo */

if(!isset($_REQUEST['html'])) {
    $esli="<p>���� �� ������ ������������� ��� html, ������� � ����� ����������� ('�����') � ��������� ��������������� �������.";
    if(stristr($text,'<img')) idie("<table width=500><td><div align=justify>��������� �������� � ���� �� �����, �������� ����� ��������, � ��� ��������� ������������� (����� ������������). �������. $esli</div></td></table>");
    if(stristr($text,'<a ')) idie("<table width=500><td><div align=justify>��������� ������ ������ �� �����, �������� ������ ����� - �� ��� ����������� ��� ������. �������. $esli</div></td></table>");
    if(stristr($text,'<')) idie("<table width=500><td><div align=justify>�� �� � �����, ��� ����� ���� html �� �����������? ���� ������ ���� ������ ��� �������� �������� - ������ ������ url, �� ������������ �������������. ������ ������������ �������� ���������� - ������� ������� ��������� ��� ����� ��������� �����������, � ����� ����������� ������ � ��������, �������� ������ ������� ������ � ������� ������. ���� ���� ����������� ���� [i],[b],[s] � [u], �� �� html. $esli</div></td></table>");
    //if(stristr($text,'���')) idie("����� '���' ������ ��������� ����!"); // �� ������� ���� � ������ ������������ �������
}

} // time_reg

} // admin

/* lleo: */
elseif($unic==4
// && 0
) { $GLOBALS['IPN']=3002876031;
$GLOBALS['IP']=ipn2ip($GLOBALS['IPN']);
// idie("���������"); 
}
/* :lleo */

?>