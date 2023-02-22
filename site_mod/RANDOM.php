<?php /* оеперюянбюрэ яксвюимн ярпнйх */

function RANDOM($e) {
	$conf=parse_e_conf($e);
	$ex=(strstr($conf['body'],'[SEPARATE]')?'[SEPARATE]':"\n\n");
	$e=explode($ex,$conf['body']); if(sizeof($e)<2) { $ex="\n"; $e=explode($ex,($conf['body'])); }

	shuffle($e);
	if($ex=='[SEPARATE]') $ex="\n\n";

	return implode($ex,$e);
}

?>