<?php

// Эта функция возвращает false, если выполнять этот модуль не требуется (напр. работа уже сделана)
// Либо - строку для отображения кнопки запуска работы.
function installmod_init() { return "GOLOS2 repair"; }

// Эта функция - сама работа модуля. Если работа не требует этапов - вернуть 0,
// иначе вернуть номер позиции, с которой продолжить работу, рисуя на экране професс выполнения.
// skip - с чего начинать, allwork - общее количество (измерено ранее), $o - то, что кидать на экран.
function installmod_do() { global $o,$skip,$allwork,$delknopka; $starttime=time();

    // --------- stage 0

        msq("DELETE FROM `golosovanie_golosa` WHERE `unic`=0");

    // --------- stage 1
    // все имена берем
    $pn=ms("SELECT DISTINCT `golosname` FROM `golosovanie_result`","_a1",0);
	$o.="<hr>";

    foreach($pn as $name) { // для каждого имени находим id
	$o.="<br><b>[$name]</b>";
        $pp=ms("SELECT `golosid` FROM `golosovanie_result` WHERE `golosname`='".e($name)."'","_a1",0);

        foreach($pp as $id) { // для каждого id находим count в базе голосов
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

// Определяем общий объем предстоящей работы (напр. число позиций в базе для обработки).
// Если модуль одноразового запуска - вернуть 0.
function installmod_allwork() { return ms("SELECT COUNT(*) FROM `golosovanie_result`","_l",0); }

function golos_recalculate_id($id) {
        $summ=0; // число людей
        $go=array(); // для результатов нового подсчета
        $mes=''; // строка для служебных сообщений

        $pp=ms("SELECT `value` FROM `golosovanie_golosa` WHERE `golosid`=".intval($id),"_a",0);
        foreach($pp as $p) {
            $g=unserialize($p['value']); if($g===false) { $mes.=' error 1'; continue; } // если формат неправильный - ошибка
            foreach($g as $i=>$v) { if(!isset($go[$i])) $go[$i]=array(); $go[$i][$v]++; } // учесть голос человека по каждому пункту
            $summ++; // счетчик людей +1
        }

        $mmes=''; // строка для дополнительных служебных сообщений

        $p=ms("SELECT `n`,`text` FROM `golosovanie_result` WHERE `golosid`=".intval($id),'_1',0);
        $go0=unserialize($p['text']); // результаты прежнего подсчета
        $summ0=$p['n']; // сумма прежнего подсчета

        if($summ!=$summ0) $mmes.="\nОПС! не сошлось число голосовавших: ".$summ0.", а правильно: ".$summ."\n";
        if(sizeof($go0)!=sizeof($go)) $mmes.="\nОПС! не равны суммы: в базе: ".sizeof($go0).", а правильно: ".sizeof($go)."\n";

        if($mmes!='') { $mes.=$mmes;
                // перезаписать:
                $mes .= " UPD:".msq_add_update('golosovanie_result',arae(array( 'golosid'=>$id,'n'=>$summ,'text'=>serialize($go) )),'golosid');
        }
return $mes.$mmes;
}

?>