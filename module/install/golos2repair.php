<?php

// ��� ������� ���������� false, ���� ��������� ���� ������ �� ��������� (����. ������ ��� �������)
// ���� - ������ ��� ����������� ������ ������� ������.
function installmod_init() { return "GOLOS2 repair"; }

// ��� ������� - ���� ������ ������. ���� ������ �� ������� ������ - ������� 0,
// ����� ������� ����� �������, � ������� ���������� ������, ����� �� ������ ������� ����������.
// skip - � ���� ��������, allwork - ����� ���������� (�������� �����), $o - ��, ��� ������ �� �����.
function installmod_do() { global $o,$skip,$allwork,$delknopka; $starttime=time();

    // --------- stage 0

        msq("DELETE FROM `golosovanie_golosa` WHERE `unic`=0");

    // --------- stage 1
    // ��� ����� �����
    $pn=ms("SELECT DISTINCT `golosname` FROM `golosovanie_result`","_a1",0);
	$o.="<hr>";

    foreach($pn as $name) { // ��� ������� ����� ������� id
	$o.="<br><b>[$name]</b>";
        $pp=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e($name)."'","_a1",0);

        foreach($pp as $id) { // ��� ������� id ������� count � ���� �������
	    $skip++; if((time()-$starttime)>2) return $skip;

            $i=ms("SELECT COUNT(*) FROM `golosovanie_golosa` WHERE `golosid`=".intval($id),"_l",0);
            if($i==0) {
                msq("DELETE FROM `golosovanie_result` WHERE `golosid`=".intval($id) );
                $o.=" $id";
		continue;
            }

            if(''==($txt=ms("SELECT `text` FROM `golosovanie_result` WHERE `golosid`=".intval($id),"_l"))) {
                msq("DELETE FROM `golosovanie_result` WHERE `golosid`=".intval($id) );
                $o.=" $id";
            }
        }
    }

    // -------- stage 2
    $skip=0;

    $pn=ms("SELECT DISTINCT `golosid` FROM `golosovanie_result`","_a1"); $kk=array(); foreach($pn as $k) $kk[]=$k;
    $vv=ms("SELECT * FROM `golosovanie_golosa` WHERE `golosid` NOT IN (".implode(',',$kk).")","_a");
    foreach($vv as $v) { $skip++; if((time()-$starttime)>2) return $skip;
        msq("DELETE FROM `golosovanie_golosa` WHERE `golosid`=".intval($v['golosid']) );
        $o.=" ".$v['golosid'];
    }

    // -------- stage 3
    $skip=0;

    $pn=ms("SELECT DISTINCT `golosid` FROM `golosovanie_result`","_a1");
    foreach($pn as $id) { $skip++; if((time()-$starttime)>2) return $skip;
	if(''!=($si=golos_recalculate_id($id))) $o.=" $id : ".$si;
    }

    $delknopka=1;
    return 0;
}

// ���������� ����� ����� ����������� ������ (����. ����� ������� � ���� ��� ���������).
// ���� ������ ������������ ������� - ������� 0.
function installmod_allwork() { return ms("SELECT COUNT(*) FROM `golosovanie_result`","_l",0); }

function golos_recalculate_id($id) {
        $summ=0; // ����� �����
        $go=array(); // ��� ����������� ������ ��������
        $mes=''; // ������ ��� ��������� ���������

        $pp=ms("SELECT `value` FROM `golosovanie_golosa` WHERE `golosid`=".intval($id),"_a",0);
        foreach($pp as $p) {
            $g=unserialize($p['value']); if($g===false) { $mes.=' error 1'; continue; } // ���� ������ ������������ - ������
            foreach($g as $i=>$v) { if(!isset($go[$i])) $go[$i]=array(); $go[$i][$v]++; } // ������ ����� �������� �� ������� ������
            $summ++; // ������� ����� +1
        }

        $mmes=''; // ������ ��� �������������� ��������� ���������

        $p=ms("SELECT `n`,`text` FROM `golosovanie_result` WHERE `golosid`=".intval($id),'_1',0);
        $go0=unserialize($p['text']); // ���������� �������� ��������
        $summ0=$p['n']; // ����� �������� ��������

        if($summ!=$summ0) $mmes.="\n���! �� ������� ����� ������������: ".$summ0.", � ���������: ".$summ."\n";
        if(sizeof($go0)!=sizeof($go)) $mmes.="\n���! �� ����� �����: � ����: ".sizeof($go0).", � ���������: ".sizeof($go)."\n";

        if($mmes!='') { $mes.=$mmes;
                // ������������:
                $mes .= " UPD:".msq_add_update('golosovanie_result',arae(array( 'golosid'=>$id,'n'=>$summ,'text'=>serialize($go) )),'golosid');
        }
return $mes.$mmes;
}

?>