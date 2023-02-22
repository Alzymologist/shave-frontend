<?php /* вставка из файла итли запуск, если файл .php)
(только для рутовых аккаунтов)

Имя дается абсолютным. Если первый символ в имени файла не / - то подставить корень веб-папки.

Через пробел для вставляенмых файлов (все, кроме php) можно указать преобразование кодировки двумя символами:
	uw: UTF-8 - Windows-1251
	uk: UTF-8 - KOI8-R

	wu: Windows-1251 - UTF-8
	wk: Windows-1251 - KOI8-R

	kw: KOI8-R - Windows-1251
	ku: KOI8-R - UTF-8

	dw: DOS cp866 - Windows-1251
	du: DOS cp866 - UTF-8


Идентично:

{_INCLUDE: template/system/unic.htm uw _}

{_INCLUDE: /var/www/dnevnik/template/system/unic.htm _}
*/

function INCLUD($e) {
    $uw=''; if(strstr($e,' ')) list($e,$uw)=explode(' ',$e);

    $file=ltrim(str_replace('hidden/','',$e),'/');
    $e=ltrim($e,'/');
    if($GLOBALS['acc']=='') $file=$GLOBALS['filehost'].$e;
    else $file=$GLOBALS['filehost'].'userdata/'.$GLOBALS['acc'].'/'.$e;
    $file=rpath($file);

    if(is_file($file)) {
	if(getras($file)=='php') {
	    if(($ur=onlyroot(__FUNCTION__.' '.h($file),1))) return $ur; // только для рутового аккаунта
	    $o=''; include_once $file; return $o;
	}

	if($uw=='') return fileget($file);
	if($uw=='uw') return uw(fileget($file));
	if($uw=='wu') return wu(fileget($file));
	if($uw=='wk') return wk(fileget($file));
	if($uw=='kw') return kw(fileget($file));
	if($uw=='ku') return ku(fileget($file));
	if($uw=='uk') return uk(fileget($file));
	if($uw=='dw') return dw(fileget($file));
	if($uw=='du') return du(fileget($file));
	// return "<>";
    }

    $ee=e(ltrim($e,'/'));
    if(false!=($p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".$ee."' OR `num`=".intval($ee)." LIMIT 1","_1"))) {
	include_once $GLOBALS['include_sys']."_onetext.php";
	$p=mkzopt($p); $GLOBALS['article']=$p;
	$body=onetext($p,0);
	return $body;
    }
    return '<hr><font color=red>Include not found: `'.h($e)."`</font>";
}
?>