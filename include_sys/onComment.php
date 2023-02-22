<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// спамоборона и фильтры

// $s=file_get_contents($GLOBALS['filehost'].'design/ico.png');

$url = 'http://canada.home.lleo.me/xprinter.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($ch, CURLOPT_TIMEOUT,2);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS,serialize(array(
    'Name'          => $ara['Name'], // Lianid Kaganov
    'Text'          => $ara['Text'], // Супер! Очень похоже, что это тот же чипсет и вообще та же разработка. Блок питания на моих фотах узнаете или другой?
    'Time'          => $ara['Time'], // 1638134515
    'id'            => $ara['id'], // 354674
    'Parent'        => $ara['Parent'], // 354672
    'unic'          => $ara['unic'], // 4
    'DateID'        => $ara['DateID'], // 5084
    'Header'        => $p['Header'], // Как мы чинили Самый Большой Советский Микрокалькулятор
    'Date'          => $p['Date'], // 2021/11/27
    'parent_Text'   => $p['Text'], // Очень похож на тот что у нас в арендованном в техникуме помещении делала небольшая контора, правда на основе МК-61 и с сенсорной указкой. Я даже соб
    'parent_id'     => $p['id'], // 354672
    'parent_unic'   => $p['unic'], // 8843651
    'parent_Name'   => $p['Name'] // too_lazy
)));
$response = curl_exec($ch);
curl_close($ch);

// array_merge($p,$ara);
// file_put_contents($GLOBALS['include_sys']."onComment.txt",print_r($pp,1));

?>