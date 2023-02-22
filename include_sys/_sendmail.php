<?php // if=?(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

/*
// sendmail_service
if(isset($_POST['ssp'])) { include "../config.php";
die("Hole under construction");
if(isset($sendmail_gate_pass)) die(''.($_POST['ssp']==$sendmail_gate_pass?intval(
sendmail($_POST['from_name'],$_POST['from_adr'],$_POST['to_name'],$_POST['to_adr'],$_POST['subj'],$_POST['text'])):0));
    die(''.($sendmail_service_pass==$_POST['ssp']?intval(mail($_POST['to'],$_POST['subj'],$_POST['text'],$_POST['headers'],"-f".$_POST['from_addr'])):0));
}

Pavel Gulchuk <gul@gul.kiev.ua>

Не знаю, важно это или нет, но письмо о восстановлении пароля залетело в спам по таким критериям:

+ 1. Date in future (кривая timezone).
+ 2. Subject завёрнут в base64 без необходимости (там только латиница).
+ 3. From не завёрнут в MIME, хотя там кириллица, и по стандарту это необходимо.

----- Forwarded message from Леонид Каганов <lleo@lleo.me> -----
Date: Thu, 04 Jun 2020 11:23:51 UT
From: Леонид Каганов <lleo@lleo.me>
To: lleo@gul.kiev.ua
Subject: http://lleo.me/dnevnik - restore password
Message-Id: <20200604112353.Nr2GPp34@sas2-e7f6fb703652.qloud-c.yandex.net>

[...]
----- End forwarded message -----

Паша.

*/

function mail_answer($id,$ara,$p,$m) { //------------------- коммент по емайл

$sys=array(
    'unic'=>$GLOBALS['unic'],
    'httphost'=>$GLOBALS['httphost'],
    'mail_system'=>$GLOBALS['admin_mail'],
    'mail'=>get_workmail($GLOBALS['IS']),
    'mail_parent'=>$m,
    'name'=>$GLOBALS['imgicourl'],
    'name_parent'=>$p['imgicourl'],
    'img'=>hh($GLOBALS['IS']['img']),
    'img_parent'=>hh($p['img']),
    'head'=>h(strip_tags(strtr($p['Date'],'/','-')." ".$p['Header'])),
    'date'=>$p['Date'],
    'admin_name'=>$GLOBALS["admin_name"],
    'link'=>h(getlink($p['Date'])."#".$id),
    'date'=>date('Y-m-d H:i',$ara['Time']),
    'date_parent'=>date('Y-m-d H:i',$p['Time']),
    'text_parent'=>h($p['Text']),
    'text'=>h($ara['Text'])
); $sys['subj']=$sys['httphost'].": ".imgicourl_text($sys['name'])." reply to you ".$sys['head'];

$sys['confirm']=substr(sha1($sys['date'].'|'.$sys['mail_parent'].'|'.$GLOBALS['newhash_user']),1,16); // для мгновенной отписки

$tmpl=get_sys_tmp("mail_send.htm"); $s=mpers($tmpl,$sys);

return ((true===($i=sendmail(
    imgicourl_text($sys['name']),'noreply@'.$_SERVER['HTTP_HOST'], // $sys['mail_system'], // ($sys['mail']?$sys['mail']:$sys['mail_system']),
    imgicourl_text($sys['name_parent']),$sys['mail_parent'],
$sys['subj'],$s)))?$sys:0);

}

//=========================================

function send_mail_confirm($mail,$realname='',$p=array()) { if($realname=='') $realname=$mail; // выслать подтверждение email
	global $include_sys,$httphost,$unic,$newhash_user,$admin_name,$admin_mail;
	if(!mail_validate($mail)) idie("Неверный формат ".h($mail));
	$link=$httphost."login?a=mailconfirm"."&u=".$unic."&m=".urlencode($mail)."&h=".md5($mail.$unic.$newhash_user);
$date=date('Y-m-d H:i',time());
	$s=mpers(get_sys_tmp("mail_confirm.htm"),array(
// 'admin_name'=>$admin_name,
'link'=>$link,
'mail'=>$mail,
'realname'=>$realname,

    'unic'=>$GLOBALS['unic'],
    'httphost'=>$GLOBALS['httphost'],
    'mail_system'=>$GLOBALS['admin_mail'],
    // 'mail'=>get_workmail($GLOBALS['IS']),
    // 'mail_parent'=>$m,
    'name'=>$GLOBALS['imgicourl'],
    'name_parent'=>$p['imgicourl'],
    'img'=>hh($GLOBALS['IS']['img']),
    'img_parent'=>hh($p['img']),
    // 'head'=>h(strip_tags(strtr($p['Date'],'/','-')." ".$p['Header'])),
    'date'=>$date,
    'admin_name'=>$GLOBALS["admin_name"],
    // 'link'=>h(getlink($p['Date'])."#".$id),
    // 'date'=>date('Y-m-d H:i',$ara['Time']),
    // 'date_parent'=>date('Y-m-d H:i',$p['Time']),
    // 'text_parent'=>h($p['Text']),
    // 'text'=>h($ara['Text'])
// ); $sys['subj']=$sys['httphost'].": ".imgicourl_text($sys['name'])." reply to you ".$sys['head'];
'mail_parent'=>$mail,
'confirm'=>substr(sha1($date.'|'.$mail.'|'.$GLOBALS['newhash_user']),1,16) // для мгновенной отписки
// $tmpl=get_sys_tmp("mail_send.htm"); $s=mpers($tmpl,$sys);

));
	return sendmail(h($admin_name),h($admin_mail),h($realname),h($mail),$admin_name.": email confirm",$s);
}

/*
в config.php вписываем данные своего почтового сервиса:

$GLOBALS['smtp_mail']='sendmail@lleo.me';
$GLOBALS['smtp_pass']='gbgbrfkrf123';
$GLOBALS['smtp_smtp']='ssl://smtp.yandex.ru';
$GLOBALS['smtp_name']='LLeo';
$GLOBALS['smtp_port']='465';

// https://pdd.yandex.ru/domain/lleo.me/
echo "send: ".sendmail('Отправитель Петров','lleo@aha.ru','Получатель Каганов','lleo@lleo.me','Тема письма','Сам: http://home.lleo.me'); die('OK');
*/

function sendmail($from_name,$from_adr,$to_name,$to_adr,$subj,$text,$headers=false) {

if(!empty($GLOBALS['smtp_mail'])) { // если есть внешний почтовый сервис

    if($headers==false) $headers = "MIME-Version: 1.0\r\n"
    ."From: ".mailchar(wu($from_name))." <".$from_adr.">\r\n"
    ."To: ".mailchar(wu($to_name))." <".$to_adr.">\r\n"
    // ."Date: ".date("r")."\r\n"
    ."X-Dnevnik: ".$GLOBALS['httphost']."\r\n"
    ."Content-type: text/html; charset=utf-8\r\n";

    // $mailSMTP = new SendMailSmtpClass('zhenikipatov@yandex.ru', '****', 'smtp.yandex.ru', 'Evgeniy');
    // $mailSMTP = new SendMailSmtpClass('ipatovsoft@gmail.com', '*****', 'ssl://smtp.gmail.com', 'Evgeniy', 465);
    $mailSMTP = new SendMailSmtpClass($GLOBALS['smtp_mail'],$GLOBALS['smtp_pass'],$GLOBALS['smtp_smtp'],$GLOBALS['smtp_name'],$GLOBALS['smtp_port']);
    // $result =  $mailSMTP->send('lleo@lleo.me', 'Тема письма 2', 'Текст письма 2', $headers);
    return $mailSMTP->send($to_adr,wu($subj),wu($text),$headers);
}

/*
if(!empty($GLOBALS['sendmail_service'])) { // если прописан путь пересылки меж движками
    include $GLOBALS['include_sys']."_files.php"; // применить sendmail_service
    return POST_data($GLOBALS['sendmail_service'],array('from_addr'=>$from_adr,
	'to'=>$to,'subj'=>$subj,'text'=>$text,'headers'=>$headers,'ssp'=>$GLOBALS['sendmail_service_mypass']));
}
*/

// $to = "=?windows-1251?B?".base64_encode($to_name)."?= <".$to_adr.">";
// $subj = "=?windows-1251?B?".base64_encode($subj)."?=";
$to = mailchar(wu($to_name))." <".$to_adr.">";
$subj = mailchar(wu($subj));

// "From: ".mailcharS(wu($from))."\r\n"
//."To: ".mailcharS(wu($to))."\r\n"
// =?windows-1251?B?".base64_encode($from_name)."?= <".$from_adr.">

if($headers==false) $headers = "MIME-Version: 1.0
From: ".mailchar(wu($from_name))." <".$from_adr.">
Date: ".date("r")."
X-Dnevnik: ".$GLOBALS['httphost']."
Content-type: text/html; charset=windows-1251";

return mail($to,$subj,$text,$headers,"-f".$from_adr);

}

//=========================================
// найдено на: http://vk-book.ru/otpravka-pisem-cherez-smtp-s-avtorizaciej-na-php/
// SendMailSmtpClass
// Класс для отправки писем через SMTP с авторизацией
// Может работать через SSL протокол
// Тестировалось на почтовых серверах yandex.ru, mail.ru и gmail.com
// @author Ipatov Evgeniy <admin@ipatov-soft.ru>
// @version 1.0


// function mailSchar($s,$smtp_charset="utf-8") { return $s; }

function mailcharS($e,$smtp_charset="utf-8") {
    $e=( strstr($e,',') ? explode(',',$e) : array($e) );
    foreach($e as $n=>$el) $e[$n]=trim(mailchar($el,$smtp_charset));
    return implode(', ',$e);
}

function mailchar($s,$smtp_charset="utf-8") { // ."0-9A-Za-z \!\"\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\\\]\^\_\`\{\|\}\~"
    if( !preg_match("/[^"." -\~"."]+/s",$s) ) return $s;
    if( preg_match("/^(.+?)\s+\<*([0-9a-z\_\-\.]+\@[0-9a-z\-\.]+\.[0-9a-z]{2,10})\>*\s*$/si", $s, $m) ) return '=?'.$smtp_charset.'?B?'.base64_encode($m[1]).'=?= <'.$m[2].'>';
    return '=?'.$smtp_charset.'?B?'.base64_encode($s).'=?=';
}

function getmime($f) {

$mimetypes=array(
'json' => 'application/json',
'pdf' => 'application/pdf',
'zip' => 'application/zip',
'gzip' => 'application/gzip',
'xml' => 'application/xml',
'doc' => 'application/msword',
'mp4' => 'audio/mp4',
'aac' => 'audio/aac',
'mp3' => 'audio/mpeg',
'mpeg' => 'audio/mpeg',
'ogg' => 'audio/ogg',
'wav' => 'audio/vnd.wave',
'webm' => 'audio/webm',
'gif' => 'image/gif',
'jpg' => 'image/jpeg',
'jpeg' => 'image/jpeg',
'png' => 'image/png',
'svg' => 'image/svg+xml',
'tiff' => 'image/tiff',
'webp' => 'image/webp',
'css' => 'text/css',
'csv' => 'text/csv',
'html' => 'text/html',
'htm' => 'text/html',
'shtml' => 'text/html',
'js' => 'text/javascript',
'txt' => 'text/plain',
'php' => 'text/php',
'xml' => 'text/xml',
'mpeg' => 'video/mpeg',
'mp4' => 'video/mp4',
'mov' => 'video/quicktime',
'webm' => 'video/webm',
'wmv' => 'video/x-ms-wmv',
'flv' => 'video/x-flv',
'3gp' => 'video/3gpp',
'3gpp' => 'video/3gpp',
'3g2' => 'video/3gpp2',
'3gpp2' => 'video/3gpp2',
'xlc' => 'application/vnd.ms-excel',
'ppm' => 'application/vnd.ms-powerpoint',
'docx' => 'application/msword',
'kml' => 'application/vnd.google-earth.kml+xml',
'dvi' => 'application/x-dvi',
'ttf' => 'application/x-font-ttf',
'rar' => 'application/x-rar-compressed',
'tar' => 'application/x-tar'
);

$i=getras($f); return (isset($mimetypes[$i])?$mimetypes[$i]:'application/octet-stream');
}


class SendMailSmtpClass {
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;

    public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_from, $smtp_port = 25, $smtp_charset = "utf-8") {
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_from = $smtp_from;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;
    }

//   $mailTo - получатель письма
//   $subject - тема письма
//   $message - тело письма
//   $headers - заголовки письма
//   В случае отправки вернет true, иначе текст ошибки

function send($mailTo, $subject, $message, $headers) {

    // Йобаный блядский яндекс!!!!!!!!!!!!!!!!
    $headers=preg_replace("/(^|\n)(From\:[^\n]*)<[^>]+>/si","$1$2<".($this->smtp_username).">",$headers);

    $GLOBALS['SendMailSmtpClass_ERRORS']='';

  $contentMail = "Date: ".date(DATE_RSS)."\r\n"
//    $contentMail = "Date: ".date("D, d M Y H:i:s")." UT\r\n"
//    .'Subject: =?'. $this->smtp_charset .'?B?'.base64_encode($subject)."=?=\r\n"
    .'Subject: '.mailchar($subject,$this->smtp_charset)."\r\n"
    .$headers."\r\n"
    .$message."\r\n";

// idie(nl2br(h($contentMail)));

 try {
    if(!$c = @fsockopen($this->smtp_host,$this->smtp_port,$errorNumber,$errorDescription,30)){ throw new Exception($errorNumber.".".$errorDescription); }
    if(!$this->_parseServer($c,"220")){ throw new Exception('Connection error'); }
    $server_name = $_SERVER["SERVER_NAME"];
    $this->puts($c,"HELO $server_name\r\n"); if(!$this->_parseServer($c,"250")) { fclose($c); throw new Exception('Error of command sending: HELO'); }
    $this->puts($c,"AUTH LOGIN\r\n"); if(!$this->_parseServer($c,"334")) { fclose($c); throw new Exception('Autorization error'); }
    $this->puts($c,base64_encode($this->smtp_username)."\r\n",'[smtp_username]'); if(!$this->_parseServer($c,"334")) { fclose($c); throw new Exception('Autorization error'); }
    $this->puts($c,base64_encode($this->smtp_password)."\r\n",'[smtp_password]'); if(!$this->_parseServer($c,"235")) { fclose($c); throw new Exception('Autorization error'); }
    $this->puts($c,"MAIL FROM: <".$this->smtp_username.">\r\n"); if(!$this->_parseServer($c,"250")) { fclose($c); throw new Exception('Error of command sending: MAIL FROM'); }

    //$mailTo=trim($mailTo,'<>');
    $e=$mailTo; $e=( strstr($e,',') ? explode(',',$e) : array($e) );
    foreach($e as $el) {
	if(strstr($el,'<')) $el=preg_replace("/^.*?<(.+?)>.*?$/s","$1",$el);
	$el=trim($el,"\n\r\t <>");
        $this->puts($c,"RCPT TO: <".$el.">\r\n"); if(!$this->_parseServer($c,"250")) { fclose($c); throw new Exception('Error of command sending: RCPT TO: ['.$el.']'); }
    }

    $this->puts($c,"DATA\r\n"); if(!$this->_parseServer($c, "354")) { fclose($c); throw new Exception('Error of command sending: DATA'); }

    if(strstr($contentMail,'[Send-MY-LocalFile:')) {
	$a=explode('[Send-MY-LocalFile:',$contentMail); foreach($a as $n=>$l) {
	    if($n) { list($file,$l)=explode(']',$l,2);
		$file=rpath($file);
		$file_size = filesize($file);
		if(false === ($handle = fopen($file,"rb")) ) idie("Error open file: ".h($file));

			if(false === ($handleR = fopen($file.".txt","wb")) ) idie("Error open file: ".h($file)); // #################

		while(!feof($handle)) {
		    if(false===($content=fread($handle, 57 * 143 ))) break; // return idiep("ERROR: can't send #4.fgets");
			 if(false===fwrite($handleR,chunk_split(base64_encode($content)))) idie("Error wriete file: ".h($file)); // #################
		    $this->puts($c, chunk_split(base64_encode($content), 76, "\r\n") );
		}

		 fclose($handleR); // #################
		fclose($handle);
	    }
	    $this->puts($c,$l);
	}
	$this->puts($c,"\r\n.\r\n");
    } else $this->puts($c, $contentMail."\r\n.\r\n");

	if(!$this->_parseServer($c, "250")) { fclose($c); throw new Exception("E-mail didn't sent: ".(ADMA(1)?nl2br("\n".h($GLOBALS['SendMailSmtpClass_ERRORS'])):"ERROR")); }
    $this->puts($c, "QUIT\r\n");
    fclose($c);
 } catch (Exception $e) { return $e->getMessage(); }
 return true;
}


 private function puts($c,$s,$no=false) {
    $GLOBALS['SendMailSmtpClass_ERRORS'].="---> ".($no===false?$s:$no)."\n";
    return fputs($c,$s);
 }

 private function _parseServer($c, $response) {
    while(@substr($s,3,1) != ' ') {
        $s=fgets($c,256);
	$GLOBALS['SendMailSmtpClass_ERRORS'].=(!$s?'[false]':$s)."\n";
	if(!$s) return false;
    }
    if(!(substr($s,0,3) == $response)) return false;
    return true;
 }

}

// ==========================
function sendmail_files($files, $path, $mailto, $from_mail, $from_name, $replyto='', $subject, $message) {
$uid = md5(uniqid(time()));

$header = "From: ".$from_name." <".$from_mail.">\r\n";
$header .= "Reply-To: ".$replyto."\r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
$header .= "This is a multi-part message in MIME format.\r\n";
$header .= "--".$uid."\r\n";
$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$header .= $message."\r\n\r\n";

    foreach ($files as $filename) { $file = $path.$filename;

        $file_size = filesize($file);
        $handle = fopen($file, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $content = chunk_split(base64_encode($content));

        $header .= "--".$uid."\r\n";
        $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
        $header .= $content."\r\n\r\n";
    }

$header .= "--".$uid."--";

return sendmail($from_name,$from_mail,$to_name,$mailto,$subject,$message,$header);

// return mail($mailto, $subject, "", $header);
}

?>