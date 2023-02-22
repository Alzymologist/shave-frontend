<?php
    $GLOBALS['filehost']='/home/work/www/';
    $url=file_get_contents('php://input');

//    $out=$GLOBALS['filehost'].'ZYMO/cache/'.blake3($url,10).'.json';
//    if(is_file($out)) die(file_get_contents($out));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,[
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:107.0) Gecko/20100101 Firefox/107.0',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
	// 'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        // 'Pragma: no-cache',
        // 'Cache-Control: no-cache'
	]);
    $html = curl_exec($ch);
    curl_close($ch);
//    file_put_contents($out,$html); chmod($out,0666);
    die($html);

?>