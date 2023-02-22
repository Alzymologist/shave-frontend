<?php

$GLOBALS['BACKUP_MYSQL_DIR']=$GLOBALS['filehost']."hidden/backup-mysql/";

function msq_all_tables($schema='') {
    return ms("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".e($schema==''?$GLOBALS['msq_basa']:$schema)."'",'_a1',0);
// $t=array(); $p=ms("SHOW TABLES","_a"); foreach($p as $l) foreach($l as $l1) $t[$l1]=$l1; return $t;
}
function msq_info_table($table,$n='',$schema='') { // $a=ms("show table status where name='".e($table)."'",'_1');
    $a=ms("SELECT * FROM information_schema.tables WHERE table_schema='".e($schema==''?$GLOBALS['msq_basa']:$schema)."' AND table_name='".e($table)."'",'_1',0);
    return ($n==''?$a:$a[strtoupper($n)]);
/*
    [Name] => [TABLE_NAME] => dnevnik_comm
    [Rows] => [TABLE_ROWS] => 300107
    [Collation] => [TABLE_COLLATION] => cp1251_general_ci
    [Comment] => [TABLE_COMMENT] => Комментарии посетителей
     + [TABLE_CATALOG] => def
     + [TABLE_SCHEMA] => dnev
     + [TABLE_TYPE] => BASE TABLE
*/
}

function msq_table_polya($ta) { $s=parse_msqtxt($GLOBALS['msq_txt'],$ta); preg_match_all("/\n\s*`([^`]+)`/si",$s,$m); return '`'.implode('`,`',$m[1]).'`'; }

function sizer($x,$color=1) { for($i=0;$x>=1024;$x/=1024,$i++); $x=round($x,2).array('b','Kb','Mb','Gb','Tb','Pb')[$i];
    return ($color ? "<font color=".array('#878','#C77','#F00','#F3F','#0CF','#B70')[$i].">".$x."</font>" : $x);
}

/*
        $s="DELETE FROM $tb WHERE $a $u";
// if(msq_field($table,$pole)===false) msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$s
msq_field($tb,$pole) // проверить, существует ли такое поле в таблице $tb
 // проверить, существует ли такая таблица
msq_index($tb,$index) // проверить, существует ли такой индекс
// изменить поле в таблице	function msq_change_field($table,$pole,$s)
// добавить поле таблицы	function msq_add_field($table,$pole,$s)
// удалить поле из таблицы	function msq_del_field($table,$pole)
// добавить ИНДЕКС в таблицу	function msq_add_index($table,$pole,$s)
// удалить ИНДЕКС из таблицы	function msq_del_index($table,$pole)
// создать таблицу		function msq_add_table($table,$s)
// удалить таблицу		function msq_del_table($table,$text)
*/

// Эта функция возвращает 0, если выполнять этот модуль не требуется (напр. работа уже сделана)
// Либо - строку для отображения кнопки запуска работы.

//$GLOBALS['msq_txt']=$GLOBALS['filehost']."module/upgrade/sql.txt";
$GLOBALS['msq_txt']=$GLOBALS['filehost']."module/upgrade/*.sql";


function installmod_init(){ if(RE('a')=='testmod') return $o=parse_mytables('',''); return ''; }
// [Update_time]

function msqtableknop($schema,$a,$table,$pole='',$new='',$old='') {
	$tabb=$schema."-".$table;
	$at=array(
		'del_field'=>'red','add_field'=>'green','change_field'=>'blue',
		'del_index'=>'red','add_index'=>'green','change_index'=>'blue',
		'add_table'=>'green');

	$onclick="majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."'"
.",act:'$a'"
.",table:'$table'"
.",schema:'$schema'"
.($pole==''?'':",pole:'$pole'")
.($new==''?'':",new:'".njs($new)."'")
// .($old==''?'':",old:'$old'")
."})";

	$s='' // "<blink><span style='color:red; font-size:12px; text-decoration:bold;'>*</span></blink> "
	."<input style='color:".$at[$a].";text-decoration:bold' type='button' value='$a' onclick=\"$onclick\">";
	if($new!='') {
		if($old=='') { $s.=" <tt><b>`$pole`:</b> $new</tt>"; }
		else { $s="<table><tr><td valign='center'>$s &nbsp; <b>`$pole`</b>: </td><td><tt><s>$old</s><br>$new</tt></td></tr></table>"; }
	} else $s.=" <tt><b>$pole</b></tt>";
	return "<div id='msqch_".$a."_".$tabb."_".$pole."'>".$s."</div>";
}

// Эта функция - сама работа модуля. Если работа не требует этапов - вернуть 0,
// иначе вернуть номер позиции, с которой продолжить работу, рисуя на экране професс выполнения.
// skip - с чего начинать (изначально 0), allwork - общее количество (измерено ранее), $o - то, что кидать на экран.
function installmod_do() { global $msqe;
	$a=RE('act');
	$table=RE('table');
	$pole=RE('pole');
	$new=RE('new');
	$old=RE('old');
	$schema=RE('schema'); if(empty($schema)) idie("Empty Schema! act=do a=[$a] ");
	$tbb="`".e($schema)."`.`".e($table)."`";
	$tabb=$schema."-".$table;

// === @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ===
	if($a=='view_other_schema') {

	    $pp=msq_all_tables($schema);
	    $o=''; foreach($pp as $tab) {

		$tbb="`".e($schema)."`.`".e($tab)."`";
		$tabb=$schema."-".$tab;

	        $ax=ms("SELECT * FROM information_schema.tables WHERE table_schema='$schema' AND table_name='$tab'",'_1',0);

		$o.=table_mutab($tab,$schema,array(
		    'gears'=>''.($ax['ENGINE']=='MyISAM'?"MyISAM <i class='e_kr_invert' onclick=\"idd('mysqlquerys').value='ALTER TABLE $tbb ENGINE=InnoDB'\"></i>":''),
		//    'TABLE_COMMENT'=>'', // $ext['TABLE_COMMENT'],
		    'TABLE_ROWS'=>$ax['TABLE_ROWS'], // $ext['TABLE_ROWS'],
		    'DATA_LENGTH'=>$ax['DATA_LENGTH'], // $ext['DATA_LENGTH'],
		    'INDEX_LENGTH'=>$ax['INDEX_LENGTH'], // $ext['INDEX_LENGTH'],
		    'realrows'=>ms("SELECT COUNT(*) FROM `".e($schema)."`.`".e($tab)."`","_l",0)
		));

		$pp=ms("SELECT * FROM information_schema.columns WHERE table_schema='$schema' AND table_name='$tab'",'_a',0);

		$a=array(); foreach($pp as $p) $a[$p['COLUMN_NAME']]=$p['COLUMN_TYPE']
					.($p['IS_NULLABLE']=="NO"?' NOT NULL':'')
					.(empty($p['COLUMN_DEFAULT'])?'':" default '".$p['COLUMN_DEFAULT']."'")
					.(empty($p['EXTRA'])?'':" ".$p['EXTRA'])
					.(empty($p['COLUMN_COMMENT'])?'':" COMMENT '".$p['COLUMN_COMMENT']."'");
		$o.="<div id='msqtok_".$tabb."' style='display:none;margin-left:30px;'>".stroki_mutab($tab,$schema,$a)
."<div class=br>"
."<div>Engine: ".$ax['ENGINE']
.($ax['ENGINE']=='MyISAM'?" <i class='e_kr_invert' onclick=\"idd('mysqlquerys').value='ALTER TABLE $tbb ENGINE=InnoDB'\"></i>":'')
."</div>"
."<div>Rows: ".$ax['TABLE_ROWS']."</div>"
."<div>Data: ".sizer($ax['DATA_LENGTH'])."</div>"
."<div>Index: ".sizer($ax['INDEX_LENGTH'])."</div>"
."<div>Update: ".$ax['UPDATE_TIME']."</div>"
."<div>Charset: ".$ax['TABLE_COLLATION']."</div>"
."</div>"

."</div>";
	}
	

	//    $s=parse_mytables('_msq_nesrav_',$schema);
	    otprav("ohelpc('schema_$schema','DataBase $schema',\"".njsn($o)."\")");
	}
// === @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ===


	if($a=='mysql_query') { $s=RE('string'); $p=ms($s,"_a",0); dier($p,$msqe); }





	if($a=='view_all_tables') {

	    $pp=ms("SELECT "
."*"
// ."ENGINE,TABLE_ROWS,DATA_LENGTH,INDEX_LENGTH,UPDATE_TIME,TABLE_COMMENT"
." FROM information_schema.tables"
." WHERE `TABLE_SCHEMA` NOT IN('information_schema','mysql','performance_schema')"
// ." WHERE TABLE_NAME = '".$tab."' AND TABLE_SCHEMA = '".$GLOBALS['msq_basa']."'"
,"_a",0);

// dier($pp);

$a=array();
foreach($pp as $p) {
	$sh=$p['TABLE_SCHEMA'];
	if(!isset($a[$sh])) $a[$sh]=array();
	$a[$sh][$p['TABLE_NAME']]=array(
            'ENGINE' => $p['ENGINE'],
            'TABLE_ROWS' => $p['TABLE_ROWS'], // ." (".ms("SELECT COUNT(*) FROM `".e($sh)."`.`".e($p['TABLE_NAME'])."`","_l").")",
            'DATA_LENGTH' => sizer($p['DATA_LENGTH']),
            'INDEX_LENGTH' => sizer($p['INDEX_LENGTH']),
            'UPDATE_TIME' => $p['UPDATE_TIME']
	);
}

$o='';
foreach($a as $sh=>$tab) {
    $o.="<div style='font-size:22px;text-decorate:bold;padding:30px;'>".$sh."</div>";

	foreach($tab as $tb=>$p) {
	    $o.="<div class=r><b>".$tb."</b> ".$p['ENGINE']." ~".$p['TABLE_ROWS']." ".$p['DATA_LENGTH']." / ".$p['INDEX_LENGTH']."</div>";
	}
//            'TABLE_ROWS' => $p['TABLE_ROWS']." (".ms("SELECT COUNT(*) FROM `".e($sh)."`.`".e($p['TABLE_NAME'])."`","_l").")",
//            'UPDATE_TIME' => $p['UPDATE_TIME']
}


    idie($o);
	}

//-----------------------------------------------------------------------------------------------------------------------

	if($a=='change_engine') {
	    msq("ALTER TABLE ".$tbb." ENGINE=".e($new));
	    if($msqe!='') idie("Error:<p>".$msqe);
	    otprav("salert('Engine changed',2000); clean('msqch_".$a."_".$tabb."_');");
	}

//-----------------------------------------------------------------------------------------------------------------------

	if($a=='add_field') {
		if(msq_pole($table,$pole,$schema)) idie("Error: `$pole` exist in table $tbb!");
		msq("ALTER TABLE ".$tbb." ADD `".$pole."` ".$new); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('added',800); clean('msqch_".$a."_".$tabb."_".$pole."')");
	}

	if($a=='change_field') { //idie('#2');
		if(($r=msq_pole($table,$pole,$schema))==$new) idie("Error: `$pole` already set:<br>$new");
		msq("ALTER TABLE ".$tbb." CHANGE `".$pole."` `".$pole."` ".$new); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('changed',800); clean('msqch_".$a."_".$tabb."_".$pole."');if(idd('okno')) clean('okno');");
	}

	if($a=='del_field') {
		if(!msq_pole($table,$pole,$schema)) idie("Error: `$pole` not found!");
		msq("ALTER TABLE ".$tbb." DROP `".$pole."`"); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('deleted',800); clean('msqch_".$a."_".$tabb."_".$pole."')");
	}

	// ====================================
// добавить ИНДЕКС в таблицу	function msq_add_index($table,$pole,$s)
// удалить ИНДЕКС из таблицы	function msq_del_index($table,$pole)
// function msq_index($tb,$index) { // проверить, существует ли такой индекс (результат: PRIMARY KEY - 1, KEY - true)

/*
ALTER TABLE имя_таблицы ADD PRIMARY KEY (список_столбцов);
ALTER TABLE имя_таблицы ADD UNIQUE имя_индекса (список_столбцов);
ALTER TABLE имя_таблицы ADD INDEX имя_индекса (список_столбцов);
ALTER TABLE имя_таблицы ADD FULLTEXT имя_индекса (список_столбцов);

ALTER TABLE имя_таблицы DROP PRIMARY KEY;
ALTER TABLE имя_таблицы DROP INDEX имя_индекса;
*/

// function msq1($s) { dier($s); }
// ALTER TABLE `dnev`.`dnevnik_posetil` ADD INDEX `date` (`date`)

function msq_addindex($tb,$ind,$s='') { if($s=='' || strstr($ind,' ')) list($in,$s)=explode(' ',$ind,2); else $in=$ind; $in=trim($in,'`');
	if(msq_index($tb,$in)) msq_del_index($tb,$in);
	if($in=='PRIMARY') msq("ALTER TABLE $tb drop primary key, ADD PRIMARY KEY ".$s);
	else if($in=='UNIQUE') msq("ALTER TABLE $tb ADD UNIQUE ".$s);
	else if($in=='FULLTEXT') msq("ALTER TABLE $tb ADD FULLTEXT ".$s);
	else msq("ALTER TABLE $tb ADD INDEX `".$in."` ".$s);
	if($msqe!='') idie("Error:<p>$msqe");
	return $in;
}

function msq_delindex($tb,$ind) { if(strstr($ind,' ')) list($ind,)=explode(' ',$ind,2); $ind=trim($ind,'`');
	if(!msq_index($tb,$ind)) idie("Error: index `".h($ind)."` not found in table `".h($tb)."`!");
	msq("ALTER TABLE $tb DROP ".($ind=='PRIMARY'?"PRIMARY KEY":"INDEX `".e($ind)."`"));
	if($msqe!='') idie("Error:<p>$msqe");
}

	if($a=='add_index') { msq_addindex($tbb,$pole); otprav("salert('index added',800); clean('msqch_".$a."_".$tabb."_".$pole."')"); }
	if($a=='del_index') { msq_delindex($tbb,$pole); otprav("salert('index deleted',800); clean('msqch_".$a."_".$tabb."_".$pole."')"); }
	if($a=='change_index') { $pole=msq_addindex($tbb,$pole,$new); otprav("salert('index changed',800); clean('msqch_".$a."_".$tabb."_".$pole."');"); }

	// ===================================



	if($a=='edit_field') {
		if(!($s=msq_pole($table,$pole,$schema))) idie("Error: `$pole` not found!");
		otprav("
helpc('okno',\"<fieldset><legend>Edit fileld `".h($pole)."` in $tbb</legend>"
."<input type='text' id='edit_pole' value=\\\"".h($s)."\\\" size='".strlen($s)."'>"
."<input type='button' value='submit' onclick=\\\""
."majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'change_field',table:'$table',schema:'$schema',pole:'$pole',re:1,new:idd('edit_pole').value})"
."\\\"></fieldset>\");
");
	}

	if($a=='create_table') {
		if(msq_table($table)) idie("Error: $tbb exist!");
		$s=parse_msqtxt($GLOBALS['msq_txt'],$table);
		msq($s); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('created ".$table."',1000);zabil('msqmktb_$tabb',\"".njsn(parse_mytables($table,$schema))."\");");
	}

	if($a=='delete_table') {
		if(!msq_table($table,$schema)) idie("Error: $tbb not exist!");
		msq("DROP TABLE $tbb"); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('deleted $tbb',1000);clean('$tabb');clean('msqtok_$tabb');clean('msqmktb_$tabb');");
	}

	if($a=='truncate_table') {
		if(!msq_table($table,$schema)) idie("Error: $tbb not exist!");
		msq("TRUNCATE TABLE $tbb"); if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('truncated',1000);");
	}

	if($a=='backup_table') { $table2=$table.'_old';

		if(msq_table($table2)) msq("RENAME TABLE `$table2` TO `$table2"."_"
		.strtr(msq_info_table($table2,'Update_time')," :-","___")."`");

		$s=parse_msqtxt($GLOBALS['msq_txt'],$table);
//		msq_del_table($table2);
		$s=str_replace("EXISTS `$table` (","EXISTS `$table2` (",$s);
 		msq($s);
//		idie(nl2br($s));

//	idie(msq_table_polya($table));



		msq("INSERT INTO `$schema`.`$table2` (".msq_table_polya($table).") SELECT ".msq_table_polya($table)." FROM `$schema`.`$table`");
		if($msqe!='') idie("Error:<p>$msqe");

		otprav("
salert('backuped',1000);
clean('".RE('id')."');
zabil('msqmktb_$tabb',\"".njsn(parse_mytables($table,$schema))."\");
zabil('_msq_nevbaze_ost_',\"".njsn(parse_mytables('_msq_nevbaze_ost_',$schema))."\");
");
	}

//---------------------
	if($a=='backup_file_table') { $lim=1000;
		$all=RE0('all'); if(!$all) $all=ms("SELECT COUNT(*) FROM $tbb","_l",0);
		$skip=1*RE0('skip'); if(!$all && !$skip) otprav("clean('".RE('id')."');");

		// $p=parse_msqtxt($GLOBALS['msq_txt'],$table); preg_match_all("/\n\s*\`([^\`]+)/si",$p,$m); $m=$m[1];
		$m=ms("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='$schema' AND table_name='$table'",'_a1',0);

		testdir($GLOBALS['BACKUP_MYSQL_DIR'].$schema); $file=rpath($GLOBALS['BACKUP_MYSQL_DIR'].$schema."/".$table.".txt");
		if(!$skip && is_file($file.".TEMP")) unlink($file.".TEMP");
		$fp=fopen($file.".TEMP","a+");
		if(!$skip) fputs($fp,"".implode(" | ",$m)."\n\n");
		$pp=ms("SELECT * FROM $tbb LIMIT $skip,$lim","_a",0); foreach($pp as $p) {
		    $s=array(); foreach($m as $l) $s[]=str_replace(array("\\","|","\n"),array("\\\\","\\|","\\n"),$p[$l]);
		    fputs($fp,implode("|",$s)."\n");
		}
		fclose($fp); // idie(nl2br(h(fileget($file))));
		$skip+=$lim;

		if($skip >= $all) { rename($file.".TEMP",$file); echmod($file,0666); otprav("clean('percent');clean('".RE('id')."');"); }

		otprav("helps('percent',\"<fieldset><legend>`".$table."` -&gt; ".$file." &nbsp; &nbsp; \"+parseInt((100/$all)*$skip)+\"% <span class='timet'></span></legend>"
		    ."<div style='width:\"+(getWinW()/2)+\"px;'><div style='width:\"+(((getWinW()/2)/$all)*$skip)+\"px;height:16px;background:red;'></div></div></fieldset>\");"
		    ."posdiv('percent',-1,-1);"
		    ."majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'".RE('act')."',table:'$table',schema:'$schema',id:'".RE('id')."',skip:".$skip.",all:".$all."});"
		);
	}


	if($a=='restore_file_table') { $lim=1000;

		$file=rpath($GLOBALS['BACKUP_MYSQL_DIR'].$schema."/".$table.".txt");
		// $table2=$table.'_NEW';

		$all=RE0('all'); if(!$all) { // первое
			if(!($all=intval(exec("wc -l \"".$file."\""))) || $all <= 2) idie("Error string 0: `$file`");
			$all -= 2; // первые две строки служебные
			// создадим таблицу если не было
			$s=parse_msqtxt($GLOBALS['msq_txt'],$table);
			// $s=str_replace("EXISTS `$table` (","EXISTS `$table2` (",$s);
			msq($s);
			if($msqe!='') idie($msqe);
		}
		$skip=1*RE0('skip'); if(!$all && !$skip) { idie('stop'); } // otprav("clean('".RE('id')."');");

		// открыли файл и служебные взяли
		if(!($handle=@fopen($file,"r"))) idie("Error open file `$file`");
		if(!($H=fgets($handle))) idie("Error read #1 file `$file`");
		$HH=explode('|',trim($H,"\n")); foreach($HH as $n=>$v) $HH[$n]=trim($v);
		if(!($H=fgets($handle)) || trim($H,"\n")!="") idie("Error read #2 file `$file`: [".h($H)."]".strlen($H));

		// отмотали нужные skip
		for($i=0;$i<$skip;$i++) {  if(!$handle || feof($handle) || fgets($handle)===false) idie("Error read file skip ".$i); }

		// теперь получаем данные
		$max=min($skip+$lim,$all);
		$o='';
		    for(;$skip<$max;$skip++) {
		    if(!$handle || feof($handle) || ($s=fgets($handle))===false) idie("Error read file data ".$skip);
		    // взяли данные
		    $s=str_replace(array("\\n","\\|","\\\\"),array("\n",'[pIpELinE]',"\\"),$a[$n]);
		    $a=explode('|',trim($s,"\n"));
		    $ara=array(); $qs=array(); foreach($HH as $n=>$v) $qs[]="`".e($v)."`='".e( str_replace('[pIpELinE]','|',$a[$n])  )."'";
		    if( ($col=ms("SELECT COUNT(*) FROM ".$tbb." WHERE ".implode(' AND ',$qs),'_l',0)) == 0) msq_add($tbb,arae($ara));
		    else $o.="<div>col=$col ".implode(' AND ',$qs)."</div>";
		    if($msqe!='') idie($msqe);
		}

		if($skip >= $all) {
		    if(!feof($handle)) if(!empty($s=fgets($handle))) idie('Err1');
		    if(!feof($handle)) { $s=fgets($handle); idie("Ошибка: fgets() неожиданно потерпел неудачу skip=$skip all=$all s=[$s] len=".strlen($s)); }
		    otprav("clean('percent');clean('".RE('id')."');"
		     .($o==''?'':"idie(\"".njsn($o)."\");")
		    );
		}

		fclose($handle);

		otprav("helps('percent',\"<fieldset><legend>`".$file."` -&gt; ".$table." &nbsp; &nbsp; \"+parseInt((100/$all)*$skip)+\"% <span class='timet'></span></legend>"
		    ."<div style='width:\"+(getWinW()/2)+\"px;'><div style='width:\"+(((getWinW()/2)/$all)*$skip)+\"px;height:16px;background:red;'></div></div></fieldset>\");"
		    ."posdiv('percent',-1,-1);"
		    ."majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'".RE('act')."',table:'$table',schema:'$schema',id:'".RE('id')."',skip:".$skip.",all:".$all."});"
		      .($o==''?'':"idie(\"".njsn($o)."\");")
		);
	}

//---------------------

	if($a=='restore_table') { $table2=$table.'_old';

		    dier($_REQUEST);
/*
		$s=parse_msqtxt($GLOBALS['msq_txt'],$table);
		msq_del_table($table); msq($s);


//		if(msq_table($table2)) msq("TRUNCATE `$table2`");
		msq("INSERT INTO $tbb SELECT * FROM `$table2`");
		if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('restored',1000); zabil('msqmktb_$tabb',\"".njsn(parse_mytables($table))."\");");
*/
	}

	if($a=='restore_table2') {

		$from=$tbb; $to=preg_replace("/_old`$/s","`",$from);
		$tabfrom=$table; $tabto=preg_replace("/_old$/s",'',$tabfrom);

		if(msq_table($to)) msq_del_table($to); // если была, то удалить
		$s=parse_msqtxt($GLOBALS['msq_txt'],$tabto); msq($s); // и создать заново
		if($msqe!='') idie("Error:<p>$msqe");

		// idie("tabfrom=[$tabfrom] tabto=[$tabto]<p>from=[$from] to=[$to]");
		// idie( "[$to] exist: ".(msq_table($to) ? 1 : 0)." size: ".intval(ms("SELECT COUNT(*) FROM $to",'_l',0)) );
		msq("INSERT INTO $to SELECT * FROM $from");
		if($msqe!='') idie("Error:<p>$msqe");
		otprav("salert('restored',1000); zabil('msqmktb_".$tabto."',\"".njsn(parse_mytables($tabto))."\");");
	}



	idie('error command');

//	$delknopka=1;
//	return 0;
}

// Определяем общий объем предстоящей работы (напр. число позиций в базе для обработки).
// Если модуль одноразового запуска - вернуть 0.
// Пользуясь случаем, тут можно что-то сделать полезное - например, очистить таблицу для будущего заполнения
function installmod_allwork() { return 0; }

//======================================================================================
// похвастаться успешной установкой
// function admin_pohvast() { return "<center><div id='soobshi'><input type=button value='Похвастаться успешной установкой' onclick=\"zabil('soobshi','<img src=http://lleo.aha.ru/blog/stat?link=".$GLOBALS['httphost'].">')\"></div></center>"; }
//======================================================================================


// Получить структуру таблиц движка из файла
function parse_msqtxt($txt,$ta='',$key=0) {
	$s=''; foreach(glob($txt) as $l) $s.="\n\n\n".fileget($l); // взять список баз на создание

	$s=preg_replace("/AUTO_INCREMENT=\d+/si","AUTO_INCREMENT=0",$s); // поправить сбитый автоинкремент
	$s=preg_replace("/\n-[^\n]+/si","","\n".$s); // убрать строки комментариев
	$s=preg_replace("/\n{2,}/si","\001",trim($s)); $a=explode("\001",$s); // разобрать

// dier($a[0]);


	$t=array(); foreach($a as $l) { // создание таблиц
		$l=c($l); if(!preg_match("/CREATE TABLE[^\n\`\(]+\`([^\`]+)\`/si",$l,$m)) continue;
		$table=$m[1];
			if($table==$ta) return $l;

			$t[$table]=array(); if($key) $t[$table]['KEY INDEX']=array();
			$t[$table]['TABLE OTHERDA']=array('engine'=>(preg_match("/ENGINE=(\w+)/s",$l,$m) && $m[1] ? $m[1] : '') );

			$lta=explode("\n",$l); // поля таблицы
			foreach($lta as $lt) {
				$lt=trim($lt,"\n\r\t ,");
				$lt=preg_replace("/[ ]+/s"," ",$lt);

				if($key && strstr($lt,'KEY ')) { $t[$table]['KEY INDEX'][]=str_replace('KEY ','',trim($lt,' ,')); continue; }

				$lt=preg_replace("/\s*COMMENT\s+[\'\"][^\'\"]+[\'\"]$/si","",$lt);
				$lt=preg_replace("/\s+default\s+\'\'/si","",$lt);
				$lt=preg_replace("/([ ])CURRENT_TIMESTAMP/si","$1'CURRENT_TIMESTAMP'",$lt);
				$lt=preg_replace("/\s+on update 'CURRENT_TIMESTAMP'/si","",$lt);

				if(preg_match("/^\`([^\`]+)\`/s",$lt,$mtmp)) { $t[$table][$mtmp[1]]=trim($lt); }
			} // SQL error: Unknown column 'DateDatetime' in 'where clause'
		// $t[$table]['COUNT(*)']=ms("SELECT COUNT(*) FROM `$table`","_l"); // число элементов
		// dier($t[$table],$m[1]);
	}
	return $t;
}





















//=============================================
function parse_mytables($ta='',$schema='') { global $clos; $o=''; // $i="<i class='e_";
// idie('@'.$schema);

	if($schema=='') $schema=$GLOBALS['msq_basa'];
	if($ta==''||$ta=='_msq_nevbaze_ost_'||$ta=='_msq_nesrav_') $rr=msq_all_tables($schema);//  dier($rr,$schema); }


//dier(parse_msqtxt($GLOBALS['msq_txt']));

	foreach(parse_msqtxt($GLOBALS['msq_txt'],'',1) as $tab=>$arr) { $clos='';


$KEY=$arr['KEY INDEX']; unset($arr['KEY INDEX']);
$OTHERDA=$arr['TABLE OTHERDA']; unset($arr['TABLE OTHERDA']);
//==================================== возьмем инфо о таблице
    $ext=ms("SELECT ENGINE,TABLE_ROWS,DATA_LENGTH,INDEX_LENGTH,UPDATE_TIME,TABLE_COMMENT FROM information_schema.tables WHERE TABLE_NAME = '".$tab."' AND TABLE_SCHEMA = '".$schema."'","_1");
	    $tbb=$schema.'-'.$tab;
// dier($arr);

		if($ta!='' && $ta!='_msq_nevbaze_ost_' && $tab!=$ta) continue;

		if($ta==''||$ta=='_msq_nevbaze_ost_') {
			$o.="<div id='msqmktb_".$tbb."'>";
			// if(isset($rr[$tab])) unset($rr[$tab]);
			if(($ky=array_search($tab,$rr)) !== false) unset($rr[$ky]);
		//	if(isset($rr[$tab.'_old'])) unset($rr[$tab.'_old']);

			if($ta=='_msq_nevbaze_ost_') { $o.='</div>'; continue; }
		}

		if(!msq_table($tab)) { $o.="<input style='color:red;text-decoration:bold:font-size:10px;padding:10px;' type='button' value='Create_Table'"
." onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'create_table',table:'".$tab."',schema:'".$schema."'})\">"
."<i class='e_expand_plus' id='msqPlus_".h($tbb)."'></i> <b><big>`".$tab."`</big></b>"; if($ta!='') return $o; $o.='</div>'; continue; } // создать

		$g=''; $arr_ok=$arr_del=$arr_add=$arr_change=array();

		$pp=ms("SHOW COLUMNS FROM `".e($schema)."`.`".e($tab)."`","_a",0); // взять все поля таблицы

		foreach($pp as $p) { $pole=$p['Field'];

			if($p['Extra']=='on update '.$p['Default']) $p['Extra']='';
			$str = trim("`$pole` ".$p['Type']." "
				.($p['Null']=='NO'?"NOT NULL ":"")
				.($p['Default']!=''&&$p['Default']!="''"?"default '".$p['Default']."' ":"")
				.($p['Extra']!=''?$p['Extra']." ":""));

			$str=preg_replace("/timestamp\(\d+\)/si","timestamp",$str);
			$str=str_ireplace('current_timestamp()','current_timestamp',$str);

			$str2 = substr($str,strlen($pole)+3);

			$strl=strtolower($str);
			$arrpolel=strtolower($arr[$pole]);

			if(!isset($arr[$pole])) $arr_del[$pole]=$str2; else { unset($arr[$pole]);

/*
			    $abylo=$arrpolel;
			    $anado=$strl;

			    $abylo=preg_replace("/ not null/si","<strike>$0</strike>",$abylo);
			    $anado=preg_replace("/ default \'[^\'\"]+\'/s",
"<strike>$0</strike>"
// "<b> not null</b>"
,$anado);

//			    if(preg_match("/^`.+` text$/si",$anado)) $anado=preg_match("/^`.+` varchar\(\d+\)/s",$arrpolel))
			    $abylo=str_ireplace(explode("\n","
 default null
 collate
 utf8_bin
 utf8_unicode_ci
 utf8
 'current_timestamp'
 character set
 ascii_bin
 ascii
 armscii8_bin
 armscii8
"),'[*]',$abylo);
*/

			    if($arrpolel==$strl
				|| preg_replace("/ not null/s","",$arrpolel)==$strl

|| str_replace("not null default '0000-00-00 00:00:00' on update current_timestamp","default '0000-00-00 00:00:00'",$strl) == $arrpolel

				|| preg_replace("/ default [\'\"][^\'\"]+[\'\"]/s"," not null",$strl)==$arrpolel
				|| (preg_match("/^`.+` text$/s",$strl) && preg_match("/^`.+` varchar\(\d+\)/s",$arrpolel))
				|| str_ireplace(explode("\n","
 default null
 collate
 utf8_bin
 utf8_unicode_ci
 utf8
 'current_timestamp'
 character set
 ascii_bin
 ascii
 armscii8_bin
 armscii8
"),'',$arrpolel)==$strl
			    ) $arr_ok[$pole]=$str2; // равно
			    else {  // изменить
/*
$ga=str_ireplace(explode("\n","
 default null
 collate
 utf8_bin
 utf8_unicode_ci
 utf8
 'current_timestamp'
 character set
 ascii_bin
 ascii
 armscii8_bin
 armscii8
"),'',$arrpolel);

idie("<p>".$ga."<br>".$arrpolel."<p>".$strl);
*/
				$ept=substr($arrpolel,strlen($pole)+3);
/*

			    $arr_test[$pole]=""
."<br><font color='magenta'>ept----&gt;".$ept."</font>"
."<br><font color='black'>str2: [".h($str2)."]</font>"


."<p>abylo(ept)=[$abylo]
<br>anado(str)=[$anado]
<hr>";
*/

				if($ept!=strtolower($str2)) $arr_change[$pole]=array($str2,$ept); else $arr_change[$pole]=array('ERROR: ['.$str2.']','['.$ept.']');
			    }
			}
		}

		foreach($arr as $pole=>$str) { $arr_add[$pole]=substr($str,strlen($pole)+3); } // и подсчитать те, что надо добавить

		//---
//		if(sizeof($arr_test)) { $s=''; foreach($arr_test as $n=>$v) $s.="<div style='background-color:#ccc'>".$n.": ".$v."</div>"; $g.="<div>$s</div>"; }

		if(sizeof($arr_ok)) $clos.=stroki_mutab($tab,$schema,$arr_ok); // добавим внутренности таблицы
		if(sizeof($arr_add)) { $s=''; foreach($arr_add as $n=>$v) $s.="<div>".msqtableknop($schema,'add_field',$tab,$n,$v)."</div>"; $g.="<div>$s</div>"; }
		if(sizeof($arr_change)) { $s=''; foreach($arr_change as $n=>$k) $s.="<div>".msqtableknop($schema,'change_field',$tab,$n,$k[1],$k[0])."</div>"; $g.="<div>$s</div>"; }
		// change ENGINE
		if($OTHERDA['engine'] != $ext['ENGINE']) $g.="<div>".msqtableknop($schema,'change_engine',$tab,'',$OTHERDA['engine'],$ext['ENGINE'])."</div>";
// sqtableknop($schema,$a,$table,$pole='',$new='',$old='') { eeeeeeeeeeeeeeeeeeeeeeeeeeee


		if(sizeof($arr_del)) { $s=''; foreach($arr_del as $n=>$v) $s.="<div>".msqtableknop($schema,'del_field',$tab,$n)."</div>"; $g.="<div>$s</div>"; }

//====================================

$o.=table_mutab($tab,$schema,array( // сами таблицы
	'gears'=>$g,
	'TABLE_COMMENT'=>$ext['TABLE_COMMENT'],
	'TABLE_ROWS'=>$ext['TABLE_ROWS'],
	'DATA_LENGTH'=>$ext['DATA_LENGTH'],
	'INDEX_LENGTH'=>$ext['INDEX_LENGTH']
	// ,'realrows'=> ms("SELECT COUNT(*) FROM `".e($schema)."`.`".e($tab)."`","_l",0)
));

// тут можно разместить индексы
list($toclos,$g) = indexex_mutab($KEY,$schema,$tab); $clos.=$toclos;


// ==================== ENGINE =======================

$chem=''; foreach($ext as $en_n=>$en_v) {
    if($en_n=='TABLE_COMMENT') continue;
    $chem.="<div>".$en_n.": ".(strstr($en_n,'_LENGTH')?sizer($en_v):$en_v)."</div>";
} $chem="<div style='font-size:8px;color:#444'>".$chem."</div>";

// $chom="<div style='font-size:8px;color:#3FF'>".$OTHERDA['engine']."</div>";

// ===================================================
$o.="<div id='msqtok_".$tbb."' style='display:none;margin-left:30px;'>".$clos.$chem."</div>".$g;

//-------------------

if($ta!=$tab) $o.="</div>"; else return $o;

	}




// Таблицы не из списка

$e=''; foreach($rr as $tab) { $tbb=$schema.'-'.$tab;
	$e.="<div id='msqmktb_$tbb' style='color: #779933; font-size:12px;'>$tab";

	if(strstr($tab,'_old')) { $t2=array_shift(explode('_old',$tab));
	$e.=" <i class='e_document-revert'"
	." onclick=\"if(confirm('Restore $t2?'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'restore_table2',table:'".$tab."',schema:'".$schema."'})\""
	." alt='Restore $t2 from ".$tbb."<br>count: "
	.ms("SELECT COUNT(*) FROM `".$schema."`.`".e($tab)."`","_l")
	." data: ".msq_info_table($tab,'Update_time',$schema)."'></i>"
	; }

	$e.=" <i class='e_remove'"
	." onclick=\"if(confirm('Delete $tbb?'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'delete_table',table:'".$tab."',schema:'".$schema."'})\""
	." alt='Delete Table'></i>"
	."</div>";
}

if($ta=='_msq_nevbaze_ost_') return $e;
$e="<div id='_msq_nevbaze_ost_'>".$e."</div>";


// взять все таблицы для заголовка:
$p=ms("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME NOT IN('information_schema','mysql','performance_schema')","_a1",0);
$scm=''; foreach($p as $l) { $scm.=" &nbsp; <span"
." class='ll' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'view_other_schema',schema:this.innerHTML})\""
.($l==$schema?" style='color:margenta !important'":'')
." alt='DataBase ".$l."'>".$l."</span>";
}

// dier($pp);

return "<table>
<tr><td colspan=3>
<i class='e_ledgreen' alt='All Tables' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'view_all_tables'})\"></i>
<b>DataBases: </b>".$scm."
</td></tr>
<tr><td colspan=3 align=center><input placeholder='DELETE FROM `fido`.`fidoecho` WHERE `AREAN` NOT IN (24,120,55,57,306,384,563)' id='mysqlquerys' type=text style='width:80%'>&nbsp;<input type='submit' value='MySQL Query' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'mysql_query',string:idd('mysqlquerys').value,schema:'$schema'})\"></td></tr>
<tr valign='top'><td width='50%'>$o</td><td>&nbsp; &nbsp;</td><td width='50%'><i>Other tables:</i><br>".$e."</td></tr>
</table>";
}


























// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


// тут можно разместить индексы
function indexex_mutab($KEY,$schema,$tab) { global $clos;

$clos.="<div class=r>index need:</div>";
foreach($KEY as $ke) { $clos.="<div class=br style='padding-left:20px'>$ke</div>"; }

$ee=ms("SELECT DISTINCT INDEX_NAME FROM `information_schema`.`STATISTICS` WHERE TABLE_SCHEMA = '".$schema."' AND TABLE_NAME = '".$tab."'" // ." AND TABLE_SCHEMA = '".$GLOBALS['msq_basa']."'"
,"_a1");
$KEY_DEL=$KEY_CH=array();

    $clos.="<div class=r>index present:</div>";

foreach($ee as $ind) {
    $u=ms("SELECT `INDEX_NAME`,`COLUMN_NAME`,`NON_UNIQUE` FROM `information_schema`.`STATISTICS` WHERE TABLE_NAME = '".$tab."' AND INDEX_NAME = '".$ind."'"." AND TABLE_SCHEMA = '".$schema."'","_a");

if(!sizeof($u)) continue;
    $cn=array(); foreach($u as $ui) $cn[]="`".$ui['COLUMN_NAME']."`"; $cn=implode(',',$cn);
    if($ind=='PRIMARY') $ky="PRIMARY";
    elseif($ui['NON_UNIQUE']==0) $ky="UNIQUE `".$ind."`";
    else $ky="`".$ind."`";
    $ky.=" (".$cn.")";
    $clos.="<div class=br style='padding-left:20px'>$ky</div>";
// $o.="<div class=br>".nl2br(h(print_r($u,1)))."</div>";

    list($ky1,$ch1)=explode(' ',$ky,2);
    foreach($KEY as $ki=>$kold) {
	if($ky==$kold) { unset($KEY[$ki]); $ky=''; break; }
	list($kold1,$ch2)=explode(' ',$kold,2);
	if($ky1==$kold1) { unset($KEY[$ki]); $KEY_CH[trim($ky1,'`')]=array($ch1,$ch2); $ky=''; break; }
    }
    if($ky!='') $KEY_DEL[]=$ky1;
}


$g='';
if(sizeof($KEY_DEL)) { $us=''; foreach($KEY_DEL as $n=>$v) $us.="<div>".msqtableknop($schema,'del_index',$tab,$v)."</div>"; if($us!='') $g.="<div>".$us."</div>"; }

if(sizeof($KEY_CH )) { $us=$clus=''; foreach($KEY_CH  as $n=>$k) {
	if($k[0]==preg_replace("/ *\(\d+\)/s",'',$k[1])) $clus.="<div>".msqtableknop($schema,'change_index',$tab,$n,$k[1],$k[0])."</div>";
	else $us.="<div>".msqtableknop($schema,'change_index',$tab,$n,$k[1],$k[0])."</div>";
} if($us!='') $g.="<div>".$us."</div>"; if($clus!='') $clos.="<div>".$clus."</div>";
}
if(sizeof($KEY    )) { $us=''; foreach($KEY     as $n=>$v) $us.="<div>".msqtableknop($schema,'add_index',$tab,$v)."</div>"; if($us!='') $g.="<div>".$us."</div>"; }

return array($clos,$g);

}




// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


// тут можно разместить еще чего-нибудь


function table_mutab($tab,$schema,$ara) { $tab=h($tab); $schema=h($schema);
/*
'gears'=>$g,
'TABLE_COMMENT'=>$ext['TABLE_COMMENT'],
'TABLE_ROWS'=>$ext['TABLE_ROWS'],
'DATA_LENGTH'=>$ext['DATA_LENGTH'],
'INDEX_LENGTH'=>$ext['INDEX_LENGTH'],
'realrows'=>ms("SELECT COUNT(*) FROM `".e($schema)."`.`".e($tab)."`","_l",0)
*/
$tbb=$schema.'-'.$tab;
// classList.toggle('класс');
return "<div id='$tbb'>"

."&nbsp;<i class='mv e_redo' id='msqBackup_".$tbb."' onclick=\"if(confirm('Backup ".$tbb." to ".$tbb."_old?'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'backup_table',table:'".$tab."',schema:'".$schema."',id:this.id})\" alt='Backup Table'></i>"
."&nbsp;<i class='mv e_jump-to' id='msqBackupFile_".$tbb."' onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'backup_file_table',table:'".$tab."',schema:'".$schema."',id:this.id})\" alt='Backup to File'></i>"
."&nbsp;<i class='mv e_remove' onclick=\"if(confirm('Delete ".$tbb."?'))if(confirm('Really delete `".$tbb."`?!'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'delete_table',table:'".$tab."',schema:'".$schema."'})\" title='Delete Table'></i>"
."&nbsp;<i class='mv e_edit-clear' onclick=\"if(confirm('Truncate (Clear Data) ".$tbb."?'))if(confirm('Really truncate `".$tbb."`?!'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'truncate_table',table:'".$tab."',schema:'".$schema."'})\" title='Truncate Table'></i>"
.(msq_table($tab.'_old',$schema)?"&nbsp;<i class='mv e_document-revert' onclick=\"if(confirm('restore ".$tbb."?'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'restore_table',table:'".$tab."',schema:'".$schema."'})\" title='Restore from ".$tbb."_old<br>count: "
.ms("SELECT COUNT(*) FROM `".$schema."`.`".e($tab.'_old')."`","_l")." data: ".msq_info_table($tab,'Update_time',$schema)."'></i>":'')
.(is_file($file=rpath($GLOBALS['BACKUP_MYSQL_DIR'].$schema."/".$tab.".txt"))?"&nbsp;<i alt='Restore from file ".sizer(filesize($file))."<br>".(substr($file,strlen($GLOBALS['filehost'])))."' class='mv e_ljvideo' onclick=\"if(confirm('restore ".$tbb."?'))majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'restore_file_table',table:'".$tab."',schema:'".$schema."'})\"></i>":"")

."<div class='mv0' style='display:inline-block;color: #779933; font-size:18px; text-decoration: bold; cursor:pointer;'"
." onclick=\"tudasuda('msqtok_".$tbb."'); var id=idd('msqplus_".$tbb."'),e=id.className;id.className=(e.indexOf('e_expand_plus')<0?e.replace(/e_expand_minus/g,'e_expand_plus'):e.replace(/e_expand_plus/g,'e_expand_minus'))\""
."><i class='e_expand_plus' id='msqplus_".$tbb."'></i>"
.$tab // САМО ИМЯ
." <span class='br'>"
.(isset($ara['TABLE_ROWS'])?$ara['TABLE_ROWS']:'')
.(isset($ara['realrows'])?" (".$ara['realrows'].")":'')
.(isset($ara['DATA_LENGTH'])?" ".sizer($ara['DATA_LENGTH']):'')
.(isset($ara['INDEX_LENGTH'])?" ".sizer($ara['INDEX_LENGTH'])."</span>":'')
."</div>" // число элементов

.(empty($ara['TABLE_COMMENT']||$ara['TABLE_COMMENT']==$tab)?'':"<div style='color:#779933;font-size:8px;margin-left:20px;'>".$ara['TABLE_COMMENT']."</div>")
.(isset($ara['gears'])?"<div style='font-size:16px;color:black;margin: 0 0 0 20px'>".$ara['gears']."</div>":'')
."</div>";
}


function stroki_mutab($tab,$schema,$a) { $tab=h($tab); $schema=h($schema);
/*
a=Array (
    [id] => int(10) unsigned NOT NULL auto_increment
    [Time] => int(11) unsigned NOT NULL default '0'
    [IPN] => int(10) unsigned NOT NULL
)
*/
    $o=''; foreach($a as $n=>$v) $o.="<div>" // СТРОКИ
    ."<span title='Edit ".$n."' style='color: #bbb; font-size:8px; cursor:pointer;'"
    ." onclick=\"majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',act:'edit_field',table:'".$tab."',pole:'".$n."',schema:'".$schema."'})\""
    ."><i class='mv e_kontact_journal'></i></span> "
    ."<b>".h($n).":</b> ".h($v)
    ."</div>";
    return $o;
}

?>