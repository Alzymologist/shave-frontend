<?php // отладка

include "../config.php"; include $include_sys."_autorize.php";

$a=RE('a');

if($a=='adok') {
    $file=rpath($filehost."log/adok.txt"); testdir(dirname($file));
    $i=fopen($file,"a+"); fputs($i,"--- ".date("Y-m-d h:i:s")." ---\n".RE('text')."\n\n"); fclose($i); chmod($file,0666);
    otprav('');
}

ADH();

/*
	$file=rpath($filehost."log/ajax/".RE('f').".txt");
	testdir(dirname($file));
	$o=RE('o');

	$i=fopen($file,"a+"); fputs($i,"

------ ".date("Y-m-d h:i:s")." -------
".$o); fclose($i); chmod($file,0666);
*/
otprav("salert('save',100)");

?>