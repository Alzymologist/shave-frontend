<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ����������� � �������

// $s=file_get_contents($GLOBALS['filehost'].'design/ico.png');

$url = 'http://canada.home.lleo.me/xprinter.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($ch, CURLOPT_TIMEOUT,2);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS,serialize(array(
    'Name'          => $ara['Name'], // Lianid Kaganov
    'Text'          => $ara['Text'], // �����! ����� ������, ��� ��� ��� �� ������ � ������ �� �� ����������. ���� ������� �� ���� ����� ������� ��� ������?
    'Time'          => $ara['Time'], // 1638134515
    'id'            => $ara['id'], // 354674
    'Parent'        => $ara['Parent'], // 354672
    'unic'          => $ara['unic'], // 4
    'DateID'        => $ara['DateID'], // 5084
    'Header'        => $p['Header'], // ��� �� ������ ����� ������� ��������� ����������������
    'Date'          => $p['Date'], // 2021/11/27
    'parent_Text'   => $p['Text'], // ����� ����� �� ��� ��� � ��� � ������������ � ��������� ��������� ������ ��������� �������, ������ �� ������ ��-61 � � ��������� �������. � ���� ���
    'parent_id'     => $p['id'], // 354672
    'parent_unic'   => $p['unic'], // 8843651
    'parent_Name'   => $p['Name'] // too_lazy
)));
$response = curl_exec($ch);
curl_close($ch);

// array_merge($p,$ara);
// file_put_contents($GLOBALS['include_sys']."onComment.txt",print_r($pp,1));

?>