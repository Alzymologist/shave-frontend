<?php

$s="
Позанудствую, но в мире вообще не осталось места,
где бы md5 имел смысл, пользоваться им вообще никогда не надо.
Если нужна криптографически-стойкая хеш-функция, то надо брать blake3,
если нужна быстрая хеш-функция, то надо тоже брать blake3
(он где-то в 10 раз быстрее md5...)
";

function pf($start,$f) {
    echo sprintf("<br>%.5f seconds for %s", microtime(true)-$start, $f);
}

$L=10000;
$start=microtime(true); for($i=0; $i<$L; $i++) $str=md5($s.$i);    pf($start,"md5");
$start=microtime(true); for($i=0; $i<$L; $i++) $str=sha1($s.$i);   pf($start,"sha1");
$start=microtime(true); for($i=0; $i<$L; $i++) $str=hash("sha256",$s.$i); pf($start,"sha256");
$start=microtime(true); for($i=0; $i<$L; $i++) $str=hash("sha512",$s.$i); pf($start,"sha512");
$start=microtime(true); for($i=0; $i<$L; $i++) $str=blake3($s.$i); pf($start,"blake3");

?>