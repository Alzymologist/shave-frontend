<?php /* ???? ??? ???????????? ??

???? ?????????? ???????????? ??? ???????????? ??, ?? ?????? ?????, ??????? ? ??? ?????????? '$lju' ?? ??? ???. ???? ?? ??????????? - ?????? ?? ????????.

{_lju: ????? ? ???, ?? ???? ?? ????? $lju ?????? ??????, ????? ? ?????? ????? ???? ?? $lju.livejournal.com ???? ???????????. _}

*/

function lju($e) { global $lju; if($lju=='') return ''; else return str_replace('$lju',$lju,$e); }

?>