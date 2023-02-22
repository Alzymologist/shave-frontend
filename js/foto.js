//function basename(path) { return path.replace(/^.*[\/\\]/g,''); }

treeselected={};
var treeicon=50;
var treefolder='/';

function treehasClass(elem,className){ return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className); }
function treeshowLoading(on,id){ try { idd(id).getElementsByTagName('DIV')[0].className=on?'ExpandLoading':'Expand'; } catch(e){ alert('Error treeshowLoading on=['+on+'] id=['+id+']');} }

function treeonLoaded(data,id) {
    var node=idd(id); if(!node) {

	ohelpc('fotoalbum',"фотоальбом","\
<div id=treehelp class=br>&nbsp;</div>\
<div>\
<i title='Reload' class='knop e_kr_invert' onclick='treereload()'></i>&nbsp;\
<i title='Delete selected' class='knop e_remove' onclick=\"var i=1*vzyal('treese'); if(!i||confirm('Delete '+i+' files?')) majax('foto.php',{a:'albumdel',sel:mja(treeselected)})\"></i>&nbsp;\
<i title='Move selected' class='knop e_redo' onclick=\"var i=1*vzyal('treese'); if(!i||confirm('Move '+i+' files to '+treefolder+' ?')) majax('foto.php',{a:'filemove',sel:mja(treeselected),dir:treefolder})\"></i>&nbsp;\
<i title='Copy selected' class='knop e_redo-ltr' onclick=\"var i=1*vzyal('treese'); if(!i||confirm('Copy '+i+' files to '+treefolder+' ?')) majax('foto.php',{a:'filecopy',sel:mja(treeselected),dir:treefolder})\"></i>&nbsp;\
 &nbsp; \
<i title='Create new file' class='e_kontact_journal' onclick=\"majax('foto.php',{a:'createfile',dir:treefolder});\"></i>&nbsp;\
<i title='Upload new file' class='e_kontact_notes' onclick=\"majax('foto.php',{a:'uploadnewfile',dir:treefolder});\"></i>&nbsp;\
 &nbsp; \
<i onmouseover=\"treeh('Снять все выделение')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_list-remove' onclick='treeremove()'></i>&nbsp;\
<i onmouseover=\"treeh('Выделить все, что в последней раскрытой папке')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_list-add' onclick='treeadd()'></i>&nbsp;\
<i onmouseover=\"treeh('Увеличить масштаб просмотра')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_viewmagp' onclick='treeiconp()'></i>&nbsp;\
<i onmouseover=\"treeh('Уменьшить масштаб просмотра')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_viewmagm' onclick='treeiconm()'></i>&nbsp;\
<i onmouseover=\"treeh('Повернуть выделенные на 270')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_rotate_left' onclick=\"if(confirm('Повернуть на 270?')){ openwait(); majax('foto.php',{a:'rotate',degree:270,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i onmouseover=\"treeh('Повернуть выделенные на 90')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_rotate_right' onclick=\"if(confirm('Повернуть на 90?')){ openwait(); majax('foto.php',{a:'rotate',degree:90,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i onmouseover=\"treeh('Повернуть выделенные на 180')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_blend' onclick=\"if(confirm('Повернуть на 180?')){ openwait(); majax('foto.php',{a:'rotate',degree:180,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i onmouseover=\"treeh('Удалить превью #')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_edit-clear' onclick=\"if(confirm('Delete previews in '+treefolder+'?')) { openwait(); majax('foto.php',{a:'delpre',sel:mja(treeselected),dir:treefolder}); }\"></i>&nbsp;\
<i onmouseover=\"treeh('Создать превью #')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_image' onclick='treemakekpre(treefolder,1)'></i>&nbsp;\
<i onmouseover=\"treeh('Обрезать превью 100x100 #')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_crop' onclick=\"if(confirm('Cut selected previews 100x100 in '+treefolder+'?')) { openwait(); majax('foto.php',{a:'pre100x100',sel:mja(treeselected),dir:treefolder}); }\"></i>&nbsp;\
<i onmouseover=\"treeh('Выйти, вставив картинки в заметку #')\" onmouseout=\"treeh('&nbsp;')\" class='knop e_finish' onclick='treefinish()'></i>&nbsp;\
<span class=r id=treese>0</span>\
</div>\
<div id='/' class='ExpandClose' onclick='treeonclick(event,1)' onDblClick='treeonclick(event,2)'><ul class='Container'></ul></div>\
");

    node=idd(id);
    }
    for(var i=0;i<data.length;i++) {
	var li=document.createElement('LI');
	li.className="Node Expand"+(data[i][2]==1?'Closed':'Leaf');
	    if(i==data.length-1) li.className+=' IsLast';
	li.innerHTML='\n\t<div class=Expand></div><div class=Con3>'+data[i][1]+'</div>';
	if(data[i][2]==1) {
	    li.innerHTML+='\n\t<ul class=Container></ul>';
	    li.id=data[i][0];
	}
	node.getElementsByTagName('UL')[0].appendChild(li);
    }
    node.isLoaded=true;
    treetoggleNode(node);
}

function treeload(id) { treeshowLoading(true,id); majax('foto.php',{a:'albumgo',id:id}); }

function treetoggleNode(node,r) { // заменить класс
    treefolder=node.id;
    if(typeof r == 'undefined') var newClass=treehasClass(node,'ExpandOpen')?'ExpandClosed':'ExpandOpen';
    else var newClass='ExpandClosed';
    node.className=node.className.replace(/(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/,'$1'+newClass+'$3');
    //<---->zabil('albumdir',node.id);
}

//===================================================================================

// if($a=='album') { ADMA(); otprav("LOADS([www_js+'foto.js',www_css+'tree.css'],function(){".albumdir('/')."});"); }
// treeonLoaded([['2006/','2006',1],['2009/','2009',0],['unic-colors.txt.dat','unic-colors.txt.dat',0]],'/');
// treeshowLoading(false,'/');

mja=function(e) { var s=[]; for(var i in e) s.push(i); return s.join('|'); };

treereload=function(i){ if(typeof i == 'undefined') i=treefolder;
	idd(i).getElementsByTagName('UL')[0].innerHTML='';
	majax('foto.php',{a:'albumgo',id:i,tog:1});
};

treeh=function(s){
if(s.indexOf('#')!=-1) {
	var i=vzyal('treese'); if(i=='0') i='(папка <u>'+treefolder+'</u>)'; else i='('+i+'&nbsp;шт)';
	s=s.replace(/\#/g,i);
} zabil('treehelp',s);
};

treem=function(e){
zabil('treehelp',e.src.replace(/^.{{httphost_len}}(.*?\/)pre\/([^\/]+)\$/g,'\$1\$2').replace(/\?\d+\$/g,''));
};

treeallimgicon=function(n){ var findimg=[];
var pp=idd('/').getElementsByTagName('LI'); for(var i in pp) { if(isNaN(i)||typeof pp[i] == 'undefined') continue;
var e=pp[i].getElementsByTagName('DIV')[1].getElementsByTagName('IMG'); for(var k in e) { if(e[k].id) findimg.push(e[k]); }
} for(var i in findimg) { var e=findimg[i]; e.style.width=n+'px'; e.style.height=n+'px'; if(n>150) e.src=e.src.replace(/\/pre\//g,'/'); }
};

openwait=function(){ ohelpc('wait','workind...',"<img src='"+www_design+"img/ajax.gif'>"); };

treeiconp=function(){ if(treeicon<1000) treeicon+=10; treeallimgicon(treeicon); };
treeiconm=function(){ if(treeicon>10) treeicon-=10; treeallimgicon(treeicon); };

/* clickdel=function(){ var s=''; for(var i in treeselected) s='\n'+i+' ('+treeselected[i]+')'+s; alert(s);};
		if(dbl==2 || event.shiftKey) return treemakekpre(e.id);
 */

treefinish=function(){ var a='',b='',wh=(mnogouser?'{myfiles}':'')+wwwhost;
	for(var i in treeselected){ if(treeselected[i]=='img') a=a+'\n'+wh+i; else b=b+'\n'+wh+i; }
	if(a!='') a='{_FOTOS: WIDTH=120\nmode=fotom'+a+'\n_}';
	var s=a+b;
	if(s.length){ if(mnogouser) majax('editor.php',{a:'xclipboard',text:s}); else { l_save('clipboard_text',s); l_save('clipboard_mode','plain'); } }
	clean('fotoalbum');
};

treeremove=function(){ for(var i in treeselected) { if(idd(i)) idd(i).style.border='2px solid transparent'; } treeselected={};treepr();};

setkey('KeyE','',function(g){ var e; for(var i in treeselected) e=i; majax('foto.php',{a:'treeact',id:e}); },false);

treeadd=function(){
    var p=idd(treefolder);

    var se={},pp1=p.getElementsByTagName('DIV'); if(pp1.length) for(var j in pp1){ var p1=pp1[j]; 
	if(p1.id!=undefined && p1.id!=''){ se[p1.id]=0; break; }
    }
    var pp1=p.getElementsByTagName('IMG'); for(var j in pp1) if(pp1[j].id) se[pp1[j].id]='img';

    for(var v in se) { var i=se[v]; if(/*folderclo || */v.indexOf('/')!=-1) { idd(v).style.border='2px dotted red'; treeselected[v]=i; }}
    treepr();
};

treen=function(){ var i=0; for(var k in treeselected) i++; return i; };
treepr=function(){ zabil('treese',treen()); };

treemakekpre=function(id,x){ if(x) id+='*.jpg'; majax('foto.php',{a:'treeact',id:id}); return; };

treeonclick=function(event,dbl){ event=event||window.event; var e=event.target||event.srcElement;

	if(treehasClass(e,'Expand')) e=e.parentNode;
	else while((e.id == '' || e.id == undefined) && e.parentNode != undefined) e=e.parentNode;
		if(e.isLoaded||e.getElementsByTagName('LI').length){ treetoggleNode(e); return; }
		if(treehasClass(e,'ExpandClosed')) { treeload(e.id); return; }
		if(dbl==2 || event.shiftKey) return treemakekpre(e.id);
		if(!treeselected[e.id]) { treeselected[e.id]=(e.tagName=='IMG'?'img':1); e.style.border='2px dotted red'; 
if(adm && treen()==1) { treeid=e.id;/* if(admin)alert(treeid); */
/* majax('foto.php',{a:'treeact',id:e.id}); return; */

if(e.tagName=='IMG') var t="\
<i title='Удалить' class='knop e_remove.png' onclick=\"if(treen()==1 && confirm('Delete?')) majax('foto.php',{a:'albumdel',sel:mja(treeselected)})\"></i>&nbsp;\
<i title='Повернуть на 270' class='knop e_rotate_left' onclick=\"if(treen()==1 && confirm('Повернуть на 270?')){ openwait(); majax('foto.php',{a:'rotate',degree:270,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i title='Повернуть на 90' class='knop e_rotate_right' onclick=\"if(treen()==1 && confirm('Повернуть на 90?')){ openwait(); majax('foto.php',{a:'rotate',degree:90,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i title='Повернуть на 180' class='knop e_blend' onclick=\"if(treen()==1 && confirm('Повернуть на 180?')){ openwait(); majax('foto.php',{a:'rotate',degree:180,sel:mja(treeselected)}); }\"></i>&nbsp;\
<i title='Создать превью' class='knop e_image' onclick='treemakekpre(treefolder,1)'></i>&nbsp;\
<i title='Вставить в заметку' class='knop e_finish' onclick='if(treen()==1) treefinish()'></i>&nbsp;";
else { var t="\
<i title='Delete' class='knop e_remove' onclick=\"if(treen()==1 && confirm('Delete?')) majax('foto.php',{a:'albumdel',sel:mja(treeselected)})\"></i>&nbsp;\
<i title='View' class='knop e_blend' onclick=\"majax('foto.php',{a:'treeact',id:treeid});\"></i>&nbsp;\
<i title='Edit' class='knop e_kontact_journal' onclick=\"majax('foto.php',{a:'edit_text',file:treeid});\"></i>&nbsp;";
if(treeid.replace(/\.s*html*$/gi,'')!=treeid) t=t+"<i title='Import from file to blog' class='knop e_finish' onclick=\"if(treen()==1 && confirm('Import file and rename to *.old?')) majax('editor.php',{a:'fileimport',id:treeid})\"></i>&nbsp;";
if(treeid.replace(/\.mp3$/gi,'')!=treeid) t=t+"<img style='width:36px;height:16px' src='"+www_design+"img/play.png' title='Play' onclick='changemp3x(\""+h(treeid)+"\",\"\",this)'>";
}

setTimeout("ohelpc('fotooper',\"/"+treeid+"\",\""+t.replace(/\"/g,'\\"')+"\")",50); /* \\' */

} else clean('fotooper');

} else { delete(treeselected[e.id]); e.style.border='2px solid transparent'; clean('fotooper'); }
	treepr();
};