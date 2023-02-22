<?php

function mb_ucfirst($string, $encoding = 'cp1251') {
    $strlen     = mb_strlen($string, $encoding);
    $first_char = mb_substr($string, 0, 1, $encoding);
    $then       = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($first_char, $encoding) . mb_strtolower($then, $encoding);
}

function include_gadanie2008($e) {

$cf=array_merge(array(
    'len'=>false,
    'first'=>0,
    'template'=>'{b} ... {a}<br>'
),parse_e_conf($e));

$a=explode("\n","терроризм
преступность
наркомания
алкоголизм
инфляция рубля
коррупция
педерастия
безработица
жилищное строительство
дороги и транспорт
нищета
экология
мораль и нравственность
здравоохранение
энергетика
глупость
космическая программа
размер пенсий и стипендий
добыча нефти");

$b=explode("\n","усилятся проблемы в области, известной как
как-то постепенно наладится
неожиданно встанет в полный рост
резко уйдет в отрыв
изживет себя
безжалостно задавим такое понятие, как
наметятся улучшения в сфере
Россия выйдет на первое место по такой отрасли, как
совсем потеряет стыд
станет затейливей
утеряет последние остатки разума
впадет в маразм
с флагом и песней двинется вперед
круто обрушится
впадет в уныние
нездорово оживится
станет веселее
станет популярнее
сгинет с глаз долой");

shuffle($a);
shuffle($b);
$s='';

for($i=0;$i<sizeof($a);$i++) {
    $l=mpers($cf['template'],array('b'=>$b[$i],'a'=>$a[$i]));
    if($cf['first']==1) $l=mb_ucfirst($l,"Windows-1251");
    elseif($cf['first']=='all') $l=mb_strtoupper($l,"Windows-1251");
    $s.=$l; if($cf['len'] && $i>=($cf['len']-1)) break;
}

return $s;

}

?>