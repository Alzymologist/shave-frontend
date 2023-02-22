<?php

function PHPEVAL($e) {

// if($GLOBALS['ADM']) return $e;

    if(($ur=onlyroot(__FUNCTION__.' '.h($e),1))) return $ur;
    try {
	$o=''; $result = eval($e);
    } catch (ParseError $e) {
	$o='Error PHPEVAL';
    }

    return $o;
}

?>