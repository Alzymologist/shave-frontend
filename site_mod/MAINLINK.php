<?php /* ����������� �������� ����� �������, ���� ��������� ��� - �� ������� ����������� */

function MAINLINK($e) {
    if(empty($e)) return (empty($GLOBALS['article']['LINK'])?'':$GLOBALS['article']['LINK']);
    $GLOBALS['article']['LINK']=$e;
    if(isset($GLOBALS['_PAGE'])) $GLOBALS['_PAGE']['LINK']=$e;
    return '';
}
?>