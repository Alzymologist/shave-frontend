// https://translated.turbopages.org/proxy_u/en-ru.ru.f3cd9e61-63c69e5d-e5cf8525-74722d776562/https/www.crossref.org/blog/dois-and-matching-regular-expressions/

(function(){

    var o=''+((document.selection)?document.selection.createRange().text:window.getSelection());
    var s=(o.length ? o : document.body.innerHTML);

// q=document.body;
// q.innerHTML='<div%20style=position:absolute;top:10px;left:50px;z-index:99999;><img%20src=http://lleo.me/dnevnik/site_module/REKOMENDA.php?p=blagovest&l='+encodeURIComponent(location)+'&t='+encodeURIComponent(''+o)+'></div>'+q.innerHTML;
// void(0);

function rc(RG,d) {
    var g = s.match(new RegExp("(^|[>\\s\\/])"+RG+"([<\\s]|$)","gi"));
    if(g) {
	for(var i=0;i<g.length;i++) {
	    var l=g[i].replace(/^[>\s\/]+/g,'').replace(/[<\s]+$/g,'');

	    if(!d[l]) d[l]='';
	}
    }
    return d;
}

    var d={};
    d=rc("10\\.\\d{4,9}\\/[\\-\\.\\_\\;\\(\\)\\/\\:A-Z0-9]+",d);
    d=rc("10\\.1002\\/[^\\s]+",d);
    d=rc("10\\.\\d{4}\\/\\d+\-\\d+X?(\\d+)\\d+<[\\d\\w]+\\:[\\d\\w]*>\\d+\\.\\d+\\.\\w+\\;\\d",d);
    d=rc("10\\.1021\\/\\w\\w\\d+",d);
    d=rc("10\\.1207\\/[\\w\\d]+\\&\\d+\\_\\d+",d);

    var o=''; var k=''; for(var i in d) { o+="\n"+i; k++; }
//    alert('k='+k+"\n"+o);

    var ifrm = document.createElement("iframe");
    ifrm.setAttribute("src", "//alzymologist.lleo.me/ALZYMOLOGIST/shave/doi.htm?"+Math.random()+"-"+encodeURI(o));
    ifrm.style.width = "100%";
    ifrm.style.height = "100%";
    ifrm.style.position = 'fixed';
    ifrm.style.zIndex = 9999999999999999999;
    ifrm.style.top = 0;
    document.body.appendChild(ifrm);

//    var q=document.body;
//    q.innerHTML='<div%20style=position:absolute;top:10px;left:50px;z-index:99999;><img%20src=http://lleo.me/dnevnik/site_module/REKOMENDA.php?p=blagovest&l='+encodeURIComponent(location)+'&t='+encodeURIComponent(''+o)+'></div>'+q.innerHTML;


/*
    /^10.\d{4,9}/[-._;()/:A-Z0-9]+$/i
Для 74,9 млн DOI, которые мы видели, это соответствует 74,4млн из них.
Если вам нужно использовать только один шаблон, используйте этот.
Остальные 500 Тыс. в основном относятся к ранним дням Crossref,
Вы можете получить на 300 тыс. больше DOI с
    /^10.1002/[^\s]+$/i
Хотя найденный DOI, скорее всего, является DOI в тексте, он также может содержать завершающие символы,
Добавление следующих 3 выражений к предыдущим 2 оставляет только 72 Тыс. DOI неперехваченными.
Чтобы перехватить эти 72 КБ, потребуется дюжина или более дополнительных шаблонов.
К сожалению, каждый дополнительный шаблон снижает общую точность catch. Больше прилова.
    /^10.\d{4}/\d+-\d+X?(\d+)\d+<[\d\w]+:[\d\w]*>\d+.\d+.\w+;\d$/i
    /^10.1021/\w\w\d++$/i
    /^10.1207/[\w\d]+\&\d+_\d+$/i
*/

})();
