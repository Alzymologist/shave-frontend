<?php // hashdata v2

// if(!$GLOBALS['admin']) 
include $GLOBALS['include_sys']."_hashdata2.php";

SCRIPTS("hashpresent","var hashpresent=2;");

function hashme($e) {
        $conf=array_merge(array('template'=>'u{unic} t{time} {ip} {bro}'),parse_e_conf($e));

//	if(substr($e,0,1)=='#') return hashflash($e);
//	if($GLOBALS['admin']) return $e;

	return hashdata($conf['body'],mper(" ".$conf['template']." ",array(
		'ip'=>$GLOBALS['IP'],
//		'ipx'=>$GLOBALS['IPX'],
		'bro'=>$GLOBALS['BRO'],
		'time'=>time(),
		'unic'=>$GLOBALS['unic']
	)));
}

//$metka="Vot takoi tekstik ja zakodiroval. IP=10.8.0.100"; // ������ ������� �����
//$text=file_get_contents("santa.html"); // ����� �����
//print hashflash($text); // �������� ��� ������� ��� ������ � ���� ������
//$text2 = hashdata($text,$metka); // ������������ ����� � �����
//print datahash($text2); // ������ ����� �� ������

?>