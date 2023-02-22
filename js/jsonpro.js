// page_onstart.push("MYSTART();LOADS([www_css+'chat.css?'+Math.random()]);");

// MYSTART=function(){

/*
    AJ(www_ajax+'json.php?a=mails&start=0&limit=20&his&time=0&new&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(p){
	if(p!='[]') LOADS([www_js+'jsonpro.js?'+Math.random(),www_css+'chat.css?'+Math.random()],function(){Mail.New(p)});
    });
*/

//    Mail.New();
//    Mail.Tread(9093028);
//    Mail.Tread(4);

//};

httpurl=function(s){ var l=document.location; return l.protocol+'//'+l.host+wwwhost+s.replace(/^\//,''); };
UNICS={};
UNIC=function(uc){ uc=1*uc;
    if(!UNICS[uc]) return {img:'',user:"DELETED",imgicourl:'DELETED'};
    var r=UNICS[uc];
    if(r.user=='') r.user='#'+uc;
    if(r.imgicourl=='') r.imgicourl=r.user;
    return r;
};

D00=function(x){ return (x<10?'0'+x:x); };
U2date=function(S,UT) {
    var a = new Date(UT*1000);
    S=S.replace(/Y/g,a.getFullYear());
    S=S.replace(/m/g,D00(a.getMonth()+1));
    S=S.replace(/d/g,D00(a.getDate()));
    S=S.replace(/H/g,D00(a.getHours()));
    S=S.replace(/i/g,D00(a.getMinutes()));
    S=S.replace(/s/g,D00(a.getSeconds()));
    return S;
};

Obracom={

    Code:function(t0,t1,s,t3) {
	/*
        // s=htmlspecialchars_decode($t[1]);
        s=s.replace("<br>","\n");
        s=s.replace(/^\n+/g,'').replace(/\n+$/g,'');
        // s=highlight_string("<?php\n".$s."\n?>",1);
	s=s.replace("<br />","\n");
        s=s.replace("\n\n\n","\n");
        s=s.replace('<span style="',"<span style='").replace('">',"'>");
        s=s.replace("<span style='color: #000000'>\n<span style='color: #0000BB'>&lt;?php\n",'');
        s=s.replace("<span style='color: #0000BB'>?&gt;</span>\n</span>\n",'');
        s=s.replace("?&gt;</span>\n</span>\n",'');
        s=s.replace(/^\n+/g,'').replace(/\n+$/g,'');
        s=s.replace("\n","<br>");
        s=s.replace('<span style="',"<span style='").replace('">',"'>");
	*/
	return "<div style='width:90%;margin-left:20px;border:1px dotted #ccc;background-color:#eee;border-radius:5px;padding:5px;'>"+s+"</div>";
    },

    AddBB:function(s) {
        function link_lj_var(t0,href,text) {
            href=href.replace('&quot;','"').replace(/^[\'\"\s]+/g,'').replace(/[\'\"\s]+$/g,''); //'
	    text=text.replace('&quot;','"').replace(/^[\'\"\s]+/g,'').replace(/[\'\"\s]+$/g,''); //'
    	    text=text.replace(/\&lt\;wbr\&gt\;\&lt\;\/wbr\&gt\;/gi,'');
    	    return (href==text ? text : text+" ("+href+")" );
	} s=s.replace(/&lt;a href=(.*?)&gt;(.*?)&lt;\/a&gt;/gi,link_lj_var);

        s=s.replace(/\&quot\;/g,'"');

        s=s.replace(/\[big\]([^\000]+?)\[\/big\]/gi,                                 "<font size='+2'>$1</font>");
        s=s.replace(/\[h\]([^\000]+?)\[\/h\]/gi,                                     "<div class=ll onclick='cot(this)'>[...]</div><div style='display:none'>$1</div>");
        s=s.replace(/\[b\]([^\000]+?)\[\/b\]/gi,                                     '<b>$1</b>');
        s=s.replace(/&lt;b&gt;([^\000]+?)&lt;\/b&gt;/gi,                             '<b>$1</b>');
        s=s.replace(/&lt;strong&gt;([^\000]+?)&lt;\/strong&gt;/gi,                   '<b>$1</b>');
        s=s.replace(/\[i\]([^\000]+?)\[\/i\]/gi,                                     '<i>$1</i>');
        s=s.replace(/&lt;i&gt;([^\000]+?)&lt;\/i&gt;/gi,                             '<i>$1</i>');
        s=s.replace(/&lt;em&gt;([^\000]+?)&lt;\/em&gt;/gi,                           '<i>$1</i>');
        s=s.replace(/\[u\]([^\000]+?)\[\/u\]/gi,                                     '<u>$1</u>');
        s=s.replace(/&lt;u&gt;([^\000]+?)&lt;\/u&gt;/gi,                             '<u>$1</u>');
        s=s.replace(/\[s\]([^\000]+?)\[\/s\]/gi,                                     '<s>$1</s>');
        s=s.replace(/&lt;s&gt;([^\000]+?)&lt;\/s&gt;/gi,                             '<s>$1</s>');
        s=s.replace(/&lt;quote&gt;([^\000]+?)&lt;\/quote&gt;/gi,                     '<i><font color=gray>$1</font></i>');
        s=s.replace(/&lt;cite&gt;([^\000]+?)&lt;\/cite&gt;/gi,                       '<i><font color=gray>$1</font></i>');
        s=s.replace(/&gt;([^\&\n<]+)/gi,                                       '<font color=gray>&gt;$1</font>');
        s=s.replace(/\[img\](.*?)\[\/img\]/gi,                                 ' $1 ');
        s=s.replace(/\[url\](.*?)\[\/url\]/gi,                                 ' $1 ');

        s=s.replace(/\[url\=([^\>\<\'\"\=\:\)\(\;\#]*?)\](.*?)\[\/url\]/gi,    '<a href=\'$1\'>$2</a>');
        s=s.replace(/\[tab\]([^\000]+?)\[\/tab\]/gi,                                 '<div class=rama>$1</div>'); // табличка
        s=s.replace(/\[code\]([^\000]+?)\[\/code\]/gi,Obracom.Code);
        s=s.replace(/\"/g,'&quot;'); // "
	return s;
    },

    hyperlink:function(s,k) {
	var papki="[0-9a-zA-Z\\!\\#\\$\\%\\(\\)\\*\\+\\,\\-\\.\\/\\:\\;\\=\\[\\]\\\\\\^\\_\\`\\{\\}\\|\\~]+";
	var lastaz="[0-9a-zA-Z\\/]";
	var quer="[0-9a-zA-Z\\!\\#\\$\\%\\&\\(\\)\\*\\+\\,\\-\\.\\/\\:\\;\\=\\?\\@\\[\\]\\\\\\^\\_\\`\\{\\}\\|\\~]+"; // {
	var lastquer="[0-9a-zA-Z\\#\\$\\&\\(\\)\\*\\/\\=\\@\\]\\\\\\^\\_\\`\\}\\|\\~]";

	var REG = new RegExp("([\\s\\>\\(\\:])"+ //) символы перед [1]
	    "(" // [2]
	    +"([a-z]+\\:\\/\\/|www\\.)" // http:// или www. [3]
	    +"([0-9a-zA-Z][0-9a-zA-Z_\\.\\-]*[a-zA-Z]{2,6})" // aaa.bb-bb.c_c_c [4]
	            +"(\\:\\d{1,6}|\\/|)" // порт йопта блять или пустота [5]
	            +"(" //[6]
	                        +"\\/"+papki+lastaz+"\\?"+quer+lastquer // /papka/papka.html?QUERY_STRING#HASH
	                    +"|"+"\\?"+quer+lastquer // ?QUERY_STRING#HASH
	                    +"|"+"\\/"+papki+lastaz // /papka/papka
	            +"|)"
	    +")"
	    +"(" // символы после [7]
	    +"[\\.\\?\\:][^0-9a-zA-Z\\/]"
	    +"|" // (
	    +"[\\s"+(k?"<>":'')+"\\,\\)\\$]"
	    +")"
	,'g');

        return s.replace(REG,Obracom.url_present);
    },

    url_present:function(t0,before,link,http,site,port,file,after,offset){ // dier([t0,before,link,http,site,port,file,after,offset]);
	// Obracom.media_play=  or $opt['Comment_media']=='all'  or $opt['Comment_media']=='my' && explode_last('://',$p[3].$p[5])==explode_last('://',$httpsite)?1:0
        var r=decodeURI(file);
        if(r.indexOf('.')<0) r=''; else { r=r.split('.'); r=r[r.length-1]; r=r.toLowerCase(); }
	var dl=document.location; if(http=='www.') link=dl.protocol+'//'+link;
        if(dl.protocol=='https:') { var reg=new RegExp("^http\\:\\/\\/"+dl.host.replace(/\./g,'\\.'),'g'); link=link.replace(reg,'https://'+dl.host); } // патчим HTTPS родного сайта

        if(in_array(r,['mp3','ogg'])) return before+Obracom.Play.R(link,'MP3')+after; // вставка mp3
        if(in_array(r,['mp4','mkv','flv','avi'])) return before+Obracom.Play.R(link,'VIDEO')+after; // вставка video
        if(in_array(site,['www.youtube.com','youtu.be','m.youtube.com'])) return before+Obracom.Play.R(link,'YOUTUBE')+after; // вставка роликов с ютуба
        if(!file.match(/module=/) && ( // картинки
	    in_array(r,['jpg','gif','jpeg','png','webp'])
    	    || link.match(/https\:\/\/pix2\.blogs\.yandex\.net\/getavatar/i)
            || link.match(/https\:\/\/avatars\.yandex\.net/i)
          )
        ) return before+Obracom.Play.R(link,'IMG')+after; // вставка картинок

	if(http=='area://') return before+'<a target="_blank" href="http://fghi.pp.ru/?'+link+'">'+file+'</a>'+after; // FIDO блять?!

	return before+'<noindex><a href="'+link+'" rel="nofollow" target="_blank">'+Obracom.reduceurl(link)+'</a></noindex>'+after;
    },

    Play:{
	R: function(s,TYPE) { return (Obracom.media_play ? Obracom.Play[TYPE](s) : Obracom.url_click(s,TYPE) ); },

	IMG:function(s) {
	    return '<img style="max-width:900px;max-height:800px" src="'+s+'"'+(s.match(/\&amp\;prefix\=normal/)?' align=left hspace=10':'')+'>';
	},

	MP3: function(s) {
	    return "<div style='white-space:nowrap;' class='ll pla mv' title='Play' onclick='changemp3x(\""+h(s)+"\",\""+h(s)+"\",this)'>"
	    +"<img style='vertical-align:middle;padding-right:5px;' src='"+www_design+"img/play.png' width='22' height='22'>"
	    +s+"</div>";
	},

	VIDEO: function(s) {
	    return "<div style='white-space:nowrap;' class='ll pla mv' title='Play Video' onclick='changemp3x(\""+h(s)+"\",\""+h(s)+"\",this)'>"
	    +"<img style='vertical-align:middle;padding-right:5px;' src='"+www_design+"img/play.png' width='22' height='22'>"
	    +s+"</div>";
	},


	YOUTUBE:function(s) {
            var m=s.match(/(v=|youtu\.be\/)([0-9a-z\_\-]+)/i);
            var i,t=0,x=s.replace(/\&amp\;/g,'&'); if( x.match(/[\?\&]t\=/g) ) { // время старта в секундах, если указано
                if((i=x.match(/(\?|\&)t=[\dmsh]*?(\d+)h/))) t+=i[2]*60*60;
                if((i=x.match(/(\?|\&)t=[\dmsh]*?(\d+)m/))) t+=i[2]*60;
                if((i=x.match(/(\?|\&)t=[\dmsh]*?(\d+)s/))) t+=i[2]*1;
            } t=(t?"?start="+t:'');
            return "<div alt='play'>"+h(s)+t+" "
            +"<div style='border: 1px solid #ccc;box-shadow: 0px 5px 5px 5px rgba(0,0,0,0.6); position:relative;width:320px;height:180px;display:inline-block;"
	    +"background-image:url(https://img.youtube.com/vi/"+h(m[2])+"/mqdefault.jpg);'>"
            +"<i style='position:absolute;top:70px;left:150px;' class='mv e_play-youtube'></i>"
            +"</div>"
            +"</div>";
	},

    },

    PlayX:function(TYPE,e) {
	    var s=e.getAttribute('origurl'); if(s=='') return;
	    e.setAttribute('origurl','');
	    e.style.display='block';
	    e.innerHTML=Obracom.Play[TYPE](s);
    },

    reduceurl:function(s,l) { return (s.length > l ? s.substring(0,l)+"[...]" : s); },

    url_click:function(s,TYPE) {
	    return "<div title='Media "+TYPE+"' origurl=\""+h(s)+"\" class='l' onclick=\"Obracom.PlayX('"+TYPE+"',this)\">"+Obracom.reduceurl(s,60)+"</div>";
    },

    search_podsveti:function(s) { return s; },

    media_play: 1
};









Mail={ // Все процедуры Mail


J:function(p){ if(0!=p.indexOf('/****/')) return JSON.parse(p); else eval(p); },

mk_Tread:function(p,Header,Adder) {
	Mail.Unics(p,function(){
		var o=''; for(var i in p) o+=Mail.One(p[i],Adder).join('');
		o="<div class='chat mailchat' style='--chat-color:#F0F0EA;'>"+o+"</div>";
	        ohelpc('mail_tread',Header,o);
		idd('mail_tread').setAttribute('uc',uc);
	});
},

New:function(p){
    function dodo(p){
      p=Mail.J(p);
      doclass('mail_count',function(w){zabil(w,p.length)});
      if(p.length) {
	var Adder="<div class='mail_newpan'>"
	    +"<input class='mv' type='button' value='OK' onclick='Mail.read(this)'>"
	    +"<input class='mv' type='button' value='Answer' onclick='Mail.answer(this)'>"
	    +"<input class='mv' type='button' value='Delete' onclick='Mail.del(this)'>"
	    +"<input class='mv' type='button' value='Mailbox' onclick='Mail.All()'>"
	+"</div>";
	Mail.mk_Tread(p,"My unread Mail",Adder);
       }
    }
    if(p) dodo();
    AJ(www_ajax+'json.php?a=mails&start=0&limit=20&his&time=0&new&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(p){dodo(p)});
},


// Mail.Tread(uc) - вывести всю переписку с абонентом uc
Tread:function(uc){ uc=1*uc; // вся переписка с абонентом uc
 Mail.unic2go(uc,function(x){
    AJ(www_ajax+'json.php?a=mails&unic='+uc+'&limit=10000&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(p){
	    Mail.mk_Tread(Mail.J(p),"My conversations with <input type='button' value=\""+h(UNIC(uc).user)+"\" onclick='Mail.write({to:"+uc+"})'>");
    });
 });
},

/////////////////////////////////////////////////////////
Unic:function(uc,func){ // get 1 unic by id
    AJ(www_ajax+'json.php?a=unic&unic='+(1*uc)+'&opt=imgicourl,user,img',function(s){ s=Mail.J(s); for(var i in s) UNICS[1*i]=s[i]; if(func)func(); });
},

Unics:function(R,func){ // get all unic for letters
    var Q={};
    for(var i in R) {
	var x=1*R[i].unicfrom; if(unic==x) x=1*R[i].unicto;
	if(!UNICS[x]) Q[x]=1;
    }
    var D=[]; for(var i in Q) D.push(i);
    if(D.length) AJ(www_ajax+'json.php?a=unic&opt=imgicourl,user,img',function(s){ s=Mail.J(s); for(var i in s) UNICS[1*i]=s[i]; if(func)func(); },{unic:D.join(',')});
    else if(func)func();
},


/////////////////////////////////////////////////////////
One:function(p,Adder){ // напечатать одно письмо
        p.my=(p.unicto==unic?0:1);
        p.uc=(p.my?p.unicto:p.unicfrom);
	p.dr=(p.my?'to':'fr');
	p.user=h(UNIC(p.uc).user);
        var s=h(p.text);
        s=Obracom.AddBB(s);
        s="\n"+s+"\n";
        s=Obracom.hyperlink(s);
        s=s.replace(/^\s+/g,'').replace(/\s+$/g,''); // c(s);
        s=s.replace(/\{(\_.*?\_)\}/g,"&#123;$1&#125;"); // удалить подстыковки нахуй из пользовательского текста!
        s=s.replace(/&amp;(#[\d]+;)/gi,"&$1"); // отображать спецсимволы и национальные кодировки
        s=s.replace(/\{/g,'&#123;'); // } чтоб модули не срабатывали
        s=Obracom.search_podsveti(s);
	s=s.replace(/\n/g,'<br>');

    return [
            "<div id='mail"+p.id+"' answerid='"+p.answerid+"' unicfrom='"+p.unicfrom+"' unicto='"+p.unicto+"' timeread='"+p.timeread+"' class='chat0"+p.dr+"'"
	    +(Adder?'':" onclick='Mail.chat(this)'")+">",

            "<div class='chat"+p.dr+"'>"
            +"<div style='font-size:10px'>"+U2date('Y-m-d H:i',p.timecreate)
            +(p.my?'':" &nbsp; <span class='mailUser' style='font-size:13px'>"+p.user+"</span>")
            +"</div>"
            +"<br>"+s +(Adder?Adder:'')+"</div>",

            "</div>"
    ];
},

/////////////////////////////////////////////////////////
All:function(){ var we=window.event; if(we) we.stopPropagation();

    ohelpc('mail_list','My friends',"<img src='"+www_design+"img/ajax.gif'>");

    AJ(www_ajax+'json.php?a=mailgroup&start=0&limit=20&time=0&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(s){
	var R=Mail.J(s);
	Mail.Unics(R,function(){

    var o=''; for(var i in R) { var p=R[i]; if(1*p.timecreate==0) continue;
        var my=(p.unicfrom==unic?1:0);
        var uc=(my?p.unicto:p.unicfrom);
	var name=UNIC(uc).imgicourl; name="<div onclick='kus("+uc+")' class='chat_name'>"+name+"</div>";
        var text=h(p.text); text=text.replace(/\n/g,' '); // text=text.substring(0,100);
        var img=UNIC(uc).img; if(img!='') img="<div class='chat_blockimg'><img class='chat_img' src='"+h(img)+"'></div>";
        var date="<span style='font-size:7px'>"+U2date('Y-m-d H:i',p.timecreate)+"</span>";

        o="<div id='with"+uc+"'>"
            +"<div class='in chat_blockname'>"+img+name+"</div>"
            +"<div onclick='Mail.Tread("+uc+")' class='in chat_blocktext'>"+date+"<br>"+text+"</div>"
            +"</div>"+o;
    }
    ohelpc('mail_list','My friends',o);

	});
    })
},
/////////////////////////////////////////////////////////

chat:function(e,deli){
    var w=e,id,x;
    while(w && !(id=w.id.match(/^mail(\d+)$/))) w=w.parentNode;
    // ТЕНЬ: убрать все
    x=document.querySelectorAll(".chatrama"); for(var i=0;i<x.length;i++) x[i].classList.remove("chatrama");
    x=document.querySelectorAll(".chatpan"); for(var i=0;i<x.length;i++) clean(x[i]);
    if(deli) return;
    // и добавить нужный
    id=1*id[1];
    var from=1*w.getAttribute('unicfrom');
    var to=1*w.getAttribute('unicto');
    var answer=1*w.getAttribute('answer');
    var timeread=1*w.getAttribute('timeread');
    var inbox=(to==unic?1:0);

    var w=e.querySelector("[class^='chat']"); w.classList.add("chatrama");

    var s='';
    if(inbox) s+="<input class='mv' type='button' value='Answer' onclick='Mail.answer(this)'>"
		+"<span class='readbutt'>"
		+"<input class='mv"+(timeread?' no':'')+"' type='button' value='OK' onclick='Mail.read(this)'>"
		+"<input class='mv"+(timeread?'':' no')+"' type='button' value='Unread' onclick='Mail.read(this,1)'>"
		+"</span>";
    else if(!timeread) s="<input class='mv' type='button' value='Edit' onclick='Mail.Edit(this)'>";
    s+="<input class='mv' type='button' value='Delete' onclick='Mail.del(this)'>";

    newdiv(s,{cls:'chatpan'},w);
},

r:function(e){ var r={id:Mail.id(e)},p=Mail.p(e),i,d=['answerid','unicfrom','unicto','timeread']; for(i in d) r[d[i]]=p.getAttribute(d[i]); return r; },
p:function(e){ return e.closest("DIV[id^='mail']"); },
id:function(e){ return 1*Mail.p(e).id.replace(/mail/,''); },

Del_This_And_mailchat:function(e){ // удалить DOM.Object(e), а затем посмотреть, не стал ли пуст блок .mailchat и не пора ли убрать и его тоже
    var w=e.closest(".mailchat");
    clean(Mail.p(e));
    if(w && !w.querySelectorAll('DIV').length) clean(w.closest(".pop2"));
},

// Mail: READ/UNREAD Mail.read(id |  DOM.object, 0 - read (default), 1 - unread);
read:function(e,i){ var we=window.event; if(we) we.stopPropagation();
    var id=(typeof(e)=='object'?Mail.id(e):1*e);
    AJ(www_ajax+'json.php?a=mail_read&id='+id+(i?'&unread':''),function(p){
        if(p=='[]' && typeof(e)=='object') {
            var w=e.closest('SPAN.readbutt');
	    if(w) { w=w.querySelectorAll('INPUT'); for(var i=0;i<w.length;i++) w[i].classList.toggle("no"); }
	    else Mail.Del_This_And_mailchat(e);
        }
    });
},

// Mail: DELETE Mail.del(id | DOM.object);
del:function(e){ var we=window.event; if(we) we.stopPropagation();
    var id=(typeof(e)=='object'?Mail.id(e):1*e);
    if(confirm('Delete ?')) AJ(www_ajax+'json.php?a=mail_del&id='+id,function(p){
        if(p=='[]' && typeof(e)=='object') Mail.Del_This_And_mailchat(e);
    });
},

// Mail: ANSWER Mail.answer(DOM.object);
answer:function(r){ var we=window.event; if(we) we.stopPropagation();
    r=Mail.r(r); Mail.write({answerid:r.id,to:r.unicfrom});
},

// Mail: WRITE Mail.write(unic | {to:(unicto),?text,?id});
write:function(r){ var we=window.event; if(we) we.stopPropagation();
 if(typeof(r)!='object') r={to:r};
 Mail.unic2go(r.to,function(x){
    AJ(httpurl("/template/json/mail_write.html"),function(s){
	r.hid='mail_'+(Mail.hid++);
	s=mpers(s,r);
	ohelpc(r.hid,"To: "+UNIC(r.to).user,s); // +' ['+r.to+']'
	Edit.Start(r.hid);
    });
 },r);
},

// Mail: EDIT Mail.Edit(id | DOM.object)
Edit:function(e){ var we=window.event; if(we) we.stopPropagation();
    var id=(typeof(e)=='object'?Mail.id(e):1*e);
    AJ(www_ajax+'json.php?a=mail&opt=id,unicto,text,timeread&id='+id,function(p){
	p=Mail.J(p); p.to=p.unicto;
	Mail.write(p);
    });
},

unic2go:function(uc,f,p){ uc=1*uc;
    if(!UNICS[uc]) Mail.Unic(uc,function(x,p){ if(p) f(p); else f(); });
    else if(p) f(p); else f();
},

hid:0

}; // конец блока Mail










Edit={
    getW:function(e){ return e.closest('.pop2'); },
    getA:function(e){ return Edit.getW(e).querySelector('TEXTAREA'); },
    Fti:function(e,s){ ti(Edit.getA(e),s); },

    SelectFile:function(w,id){
	var e=w.files,A=Edit.getA(w);
	A.value=A.value.replace(/\n*\[IMG\]\n*@?/gi,'')+'[IMG]';
        var I=Edit.getW(w).querySelector('.ImageLoadArea');
	doclass('loadfotos',function(e){clean(e)},'',I);

        I.style.maxWidth=A.clientWidth+'px';

        for(var i=0;i<e.length;i++) { var file=e[i]; /*>*/
    	    if(!(/^image\//).test(file.type)) { idie('Image Only!'); return; }
    	    try{
                var im=document.createElement('img');
		im.className='loadfotos';
                I.appendChild(im);
                var reader=new FileReader();
        	reader.onload=(function(x){ return function(e){x.src=e.target.result} })(im); /*A.style.height=I.clientHeight+'px';*/
                reader.readAsDataURL(file);
        	}catch(x){}
	    }
    },

    fclipboard:function(e){ var A=Edit.getA(e); if(!A) return;
	var m=l_read('clipboard_mode'); if(m!='') {
          if(m=='Copy link') {
	    var l=l_read('clipboard_link'),t=l_read('clipboard_text');
            ti(A,l+"\n\n[i]"+t+"[/i]\n{select}");
            l_save('clipboard_mode','');
	  }
	}
	setTimeout(function(){Edit.fclipboard(e)},1000);
    },

    keydown:function(e) {
	var scroll=e.scrollHeight, def=1*e.getAttribute('defheight'), len=e.value.length, deflen=1*e.getAttribute('deflen'), h=e.clientHeight;
	if(!def) e.setAttribute('defheight',(def=h));

//	if(idd('buka')) zabil('buka','style.minHeight='+e.style.minHeight
//	    +'<br>len='+len+' deflen='+deflen
//	    +'<br>def=['+def+'] scroll=['+scroll+"]"
//	    +"<br>e.scrollTop="+e.scrollTop+" clientHeight="+h);

	if(len<deflen && scroll==h && h>def) setTimeout(function(){ // если текста уменьшилось, и прокрутки нет, и размер больше стало меньше, попробовать убавить

	    var h=e.clientHeight; if(e.scrollHeight==h && h>1*e.getAttribute('deflen')) {
		e.style.minHeight=def+'px'; e.style.height='auto';
		setTimeout(function(){ var s=e.scrollHeight; if(s > e.clientHeight) e.style.height=s+'px' },10);
	    }

	},1000);
	else if(e.scrollTop && scroll > h && scroll<def*4) setTimeout(function(){
	    var s=e.scrollHeight;
	    if(s > e.clientHeight) e.style.height=s+'px';
	},1000);

	e.setAttribute('deflen',len);
        f_save('clip',e.value);
    },

    SEND:{
	MAIL:function(e,ara){
	    var file=e.querySelector('.files'); for(var i=0;i<file.files.length;i++) ara['file_'+i]=file.files[i]; /*>*/
	    ara.text=e.querySelector('TEXTAREA').value;
	    AJ(www_ajax+'json.php?a=mail_save',function(s,q,ara){ s=Mail.J(s); if(s && 1*s.id) {
		f_save('clip',''); // удалить буфер клавиатуры, раз удачно отправилось

		if(ara.id && idd('mail'+ara.id)) { // если это было редактирование сообщения и оно имеется на экране
		    AJ(www_ajax+'json.php?a=mail&id='+ara.id+'&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(p){
			zabil('mail'+ara.id,Mail.One(Mail.J(p))[1]);
		    });
		} else if(
		    idd('mail_tread') &&
		    !(ara.answerid && document.querySelector("#mail"+ara.answerid+" .mail_newpan"))
		 ) {
		    AJ(www_ajax+'json.php?a=mail&id='+s.id+'&opt=id,unicfrom,unicto,timecreate,timeread,text,answerid',function(p){
	    		var o=Mail.One(Mail.J(p)).join(''); // eeeeeeee
			var e=idd('mail_tread').querySelector('.mailchat');
			zabil(e,o+vzyal(e));
		    });
		}

		if(ara.answerid) { // если это был ответ на сообщение
		 Mail.read(ara.answerid); // пометить исходное как прочитанное
		 // удалить исходное с экрана если оно показано в числе новых (т.е. в нем имелся блок кнопок class='mail_newpan')
		 var w=idd("mail"+ara.answerid);
		 if(w && (w=w.querySelector(".mail_newpan")) ) Mail.Del_This_And_mailchat(w); // удалить исходник
		 else Mail.chat(w,1); // иначе просто убрать панель с кнопками ответа
		}

		clean(Edit.getW(e)); // удалить ненужную больше форму редактирования
	    } },ara);
	    return false;
	}
    },

    Start:function(hid) {
	var hid=idd(hid).querySelector('TEXTAREA');
	var f=hid.closest('FORM'); f.style.width=Math.min(getWinW()-30,f.clientWidth)+'px';
	hid.focus();
	setkey('Comma','ctrl shift',function(e){ tin(hid,'„') },true,1);
	setkey('Period','ctrl shift',function(e){ tin(hid,'“') },true,1);
	setkey('Comma','ctrl',function(e){ tin(hid,'{?chr:171?}') },true,1);
	setkey('Period','ctrl',function(e){ tin(hid,'{?chr:187?}') },true,1);
	setkey('Digit8','ctrl',function(e){ ti(hid,'{?chr:171?}{select}{?chr:187?}') },true,1);
	setkey('Space','ctrl',function(e){ tin(hid,'{?chr:160?}')},true,1);
	setkey('Digit6','ctrl',function(e){ tin(hid,'{?chr:151?}')},true,1);
	setkey('Minus','alt',function(e){ tin(hid,'{?chr:151?}')},true,1);
	setkey('Enter','ctrl',function(){ f.querySelector("INPUT[type='submit']").click() },false,1);
	setkey('Escape','',function(e){ if( hid.value.length<2 || confirm('Close?')) clean(getW(hid)); },false,1); /*>*/
	var v=f_read('clip'); if(v!='') hid.value=v;
	Edit.fclipboard(hid);
    }
};
