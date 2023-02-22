<?php /* set language

{_SETLANG: en_}

*/

function SETLANG($e) {
    $GLOBALS['mylang']=c0($e);
    SCRIPTS('SETLANG',"var mylang='".hh(c0($e))."';");
    return '';
}
?>