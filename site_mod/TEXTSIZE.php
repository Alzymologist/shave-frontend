<?php /* размер заметки

–азмер заметки (передаетс€ ее им€ или num)

{_TEXTSIZE: /arhive/stateika _}
*/

function TEXTSIZE($e) { $e=ltrim($e,'/');
    $n=ms("SELECT LENGTH(`Body`) FROM `dnevnik_zapisi` WHERE ".(intval($e)
	?"`num`=".intval($e)
	:"`num`='".e($e)."'"
    ),'_l');
    return ($n===false?"":$n);
}
?>