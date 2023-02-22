<?php /* Для постинга кода

Полезно при постингах кода.

{_VIEWFILE: 2019/06/files/FILE.C_}

*/

function VIEWFILE($e) {
    $cf=array_merge(array(
	'charset'=>'',
	'template'=>"<span class=ll onclick=\"majax('foto.php',{a:'treeact',id:'{body}',charset:'{charset}'})\">{name}</span>",
	'name'=>'{basename}',
    ),parse_e_conf($e));
    $cf['basename']=basename($cf['body']);

    return mpers($cf['template'],$cf);
}

?>