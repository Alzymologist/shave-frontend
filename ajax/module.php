<?php // ???????? ???????

include "../config.php"; include $include_sys."_autorize.php"; ADH();

$mod=preg_replace("/[^a-z0-9\_]/si",'',RE('mod'));

$file=$site_mod.$mod.'.php'; if(!file_exists($file)) {
	$file=$site_module.$mod.'.php'; if(!file_exists($file)) idie("Module not found: $mod");
} include_once($file);
if(!function_exists($mod.'_ajax')) idie("Function not found: ".$mod."_ajax");
otprav(call_user_func($mod.'_ajax'));

?>