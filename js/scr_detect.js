/*

всякое старое говно в теле заметки сидело:

{_NO: SETTINGS:
SOCIALMEDIA_NO=*
SOCIALMEDIA_ONLY=vk*

<script>{_nobr:

page_onstart.push('seneka();');

function scr_detect(e) { var s=e.src!=''?e.src:e.textContent;

var m=[
",{mod:'WEBINDEX',a:'ask'",
'function seneka()',
'{try{a=l[i].getAttribute',
'/cdn-cgi/cl/',
'cf-hash',
'agent.newrelic',
'/js/kuku.js',
'/js/ipad.js',
'/js/transportm.js',
'/js/main.js',
'var uc=',
'function ipadfinger',
'var openstat',
'idd(\'loginobr\').onclick',
'i[\'GoogleAnalyticsObject\']=r;',
'//www.google-analytics.com/',
'//counter.yadro.ru/',
'openstat.net/'];

if(s=='') return 0;

for(var i in m) if(-1!=s.indexOf(m[i])) return 0; return h(s.substr(0,200));
}

function seneka() { var l,n=0,s=document.getElementsByTagName('script'),i=s.length,o='';

while(n<(i)) { l=scr_detect(s[n++]);
if(l) o=o+"<div><img src='http://lleo.me/dnevnik/img/false.png' align=left><blockquote style='border: 1px dashed rgb(255,0,0); padding: 20px; margin: 0 50px 10px 50px; background-color: rgb(255,252,223);'>"+h(l)+"</blockquote></div></div>";
}

zabil('buka',o==''?'<img src=http://lleo.me/dnevnik/img/true.png> <b><font color=green>посторонних скриптов не обнаружено</font></b>':o);

}

_}</script>
_}

*/

page_onstart.push('seneka();');


function scr_detect(e) {
    if(typeof(e)=='undefined') return [0,'ОШИБКА: НЕСУЩЕСТВУЮЩИЙ ОБЪЕКТ'];
//    if(typeof(e.src)=='undefined') return [-1,0];
// alert(typeof(e.src) + ' ' + typeof(e.textContent));
    var s='';
    if(typeof(e.src)!='undefined') s=e.src;
    if(s=='' && typeof(e.textContent)!='undefined') s=e.textContent;
    if(s=='') return [-1,'ОШИБКА: пустой объект'];

// 'function seneka()':'наш скрипт проверочный',


    var m={

"window.NREUM||(NREUM={})":'какая-то полезная херь от newrelic',
"<![CDATA[ */(function(d,s,a,i,j,r,l,m,t)":"Какой-то антисканинг email от Cloudflare: придурки в 21 веке",
",{mod:'WEBINDEX',a:'ask'":'какая-то моя херь от движка: majax(module.php,{mod:WEBINDEX,a:ask...',
'johntravolta.js':'шутка с картинками Джона Траволты johntravolta.js',
'/2015/03/scr_detect.js':'собственно наш скрипт проверочный scr_detect.js',
'scr_detect_all':'это к нашему проверочному скрипту',
's,a,i,j,r,c,l':'какая-то херь, может тоже newrelic: s,a,i,j,r,c,l=document.getElementsByTagName("a"),t=document.createElement("textarea")',
'/cdn-cgi/cl/':'какая-то херь, наверно от newrelic: try{(function(a){var b="http://",c="lleo.me",d="/cdn-cgi/cl/",e="img.gif",f=new a;f.src=[b,c,d,e].join("")})(Image)}',
// 'cf-hash':'какая-то херь в cf-hash, кажется тоже newrelic',
'agent.newrelic':'скрипты системы newrelic',
'/js/kuku.js':'кукушечка движка kuku.js',
'2020/03/karantin.js':'скрипт маски karantin.js',
'/js/ipad.js':'скрипт тайчкринов движка ipad.js',
'/js/transportm.js':'транспортный скрипт движка transportm.js',
'/js/main.js':'главный скрипт движка main.js',
'var uc=':'таблица переменных движка',
'function ipadfinger':'еще какой-то тачскрин движка ipadfinger',
'var openstat':'переменные счетчика openstat',
"idd('loginobr').onclick":'блок логина движка loginobr',
"i['GoogleAnalyticsObject']=r;":'счетчик GoogleAnalytic',
'//www.google-analytics.com/':'скрипт GoogleAnalytic',
'//counter.yadro.ru/':'счетчик counter.yadro.ru',
'//mc.yandex.ru/metrika/':'Скрипт Яндекс-метрики',
'function(m,e,t,r,i,k,a)':'Вызов Яндекс-метрики',
'openstat.net/':'счетчик openstat.net'
};

for(var i in m) if(-1!=s.indexOf(i)) return [1,m[i]];
return [0,h(s.substr(0,200))];
}

function seneka() {
    var l,n=0,s=document.getElementsByTagName('script'),i=s.length,o='',z='';
    while(n<(i)) {
	l=scr_detect(s[n++]);
	if(l[0]==-1) continue;
	if(l[0]==0) { // неведомая хуйня
	    o=o+"<div><img src='http://lleo.me/dnevnik/img/false.png' align=left><blockquote style='border: 1px dashed rgb(255,0,0); padding: 20px; margin: 0 50px 10px 50px; background-color: rgb(255,252,223);'>"+l[1]+"</blockquote></div></div>";
	} else { // знакомая хуйня
	    z=z+"<div><img src='http://lleo.me/dnevnik/img/true.png' width=20> "+l[1]+"</div>";
	}
    }

// if(typeof(scr_detect_all)!='undefined') 

    var s;
    if(o=='') s='<img src=http://lleo.me/dnevnik/img/true.png> <b><font color=green>посторонних скриптов не обнаружено</font></b>';
    else s='<img src=http://lleo.me/dnevnik/img/false.png> <b><font color=green>найдены чуждые скрипты:</font></b>'+o;
    zabil('scrdetected',s);
    if(idd('scrdetected_true')) zabil('scrdetected_true',z);
}
