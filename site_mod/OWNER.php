<?php /* ��������� ������ ��������� ��������
acc - ��� �������� = ��� ��������� = ����� ���������
acn - ����� ��������
img - ������ �� �������
realname - ������ ���
site - ��������� ���������� ����� ������-�� ��� �����
birth - ���� �������� ���������, ���� �������
domain - ������� �����
time - ������ ���� �����������
data - ���� �����������
*/

function OWNER($e) {
    if($e=='acc') return $GLOBALS['acc'];
    if($e=='acn') return $GLOBALS['acn'];
    if($e=='unics') return $GLOBALS['ounics'];
    if($e=='unicslist') {
	    if(!strstr($GLOBALS['ounics'],',')) return '';
	    $a=explode(',',$GLOBALS['ounics']); unset($a[0]);
	    $s=array(); foreach($a as $u) {
		    $w=getis($u);
		    $s[]="<span class=ll alt='������� ��������' onclick=\"majax('login.php',{a:'getinfo',unic:".$u."})\"><b>".$w['imgicourl']."</b></span>";
	    }
	    return implode(", ",$s);
    }
    if($e=='unic') { if(!strstr($GLOBALS['ounics'],',')) return $GLOBALS['ounics']; $a=explode(',',$GLOBALS['ounics']); return $a[0]; }
    if($e=='time') return $GLOBALS['otime'];
    if($e=='date') { list($a,)=explode(' ',$GLOBALS['otime'],2); return $a; }
    if($e=='domain') return $GLOBALS['odomain'];

    if(!isset($GLOBALS['OWNER'])) {
	if(!strstr($GLOBALS['ounics'],',')) $GLOBALS['ounic']=$GLOBALS['ounics']; else { $a=explode(',',$GLOBALS['ounics']); $GLOBALS['ounic']=$a[0]; }
	$GLOBALS['OWNER']=getis($GLOBALS['ounic']); // ��������� �������������� ������ � ������� ���������
    }

    if(in_array($e,array('realname','site','img','imgicourl'))) return $GLOBALS['OWNER'][$e];
    if($e=='birth') return ($GLOBALS['OWNER'][$e]=='0000-00-00'?'':$GLOBALS['OWNER'][$e]);

    return '';
}
?>