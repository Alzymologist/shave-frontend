DEV=0;
admin_logins=["lleo","test1"]; // ‡„‡, ˘‡Á, ‡ ÚÓ ˇ Ëı ÌÂ ÔÓ‚Â˛ Ì‡ ·˝ÍÂÌ‰Â, ˝ÚÓ ‚ÓÓ·˘Â ÓÚÎ‡‰Ó˜Ì‡ˇ ÙÛÈÌˇ

page_onstart.push("GO();");

function myTest(){

	AJE(function(j){ dier(j); },{action:'Test_recount_all'});


//    JA.Find('nanomagnets');

//    JA.Find('fuck');

//    JA.CommentsTo(1);

//        AJE( function(j){ dier(j); } ,{action:'CommentsTo',pid:1});

//        AJE(function(j){ dier(j) },{action:'CommentTest'});

}

function GO(){

// alert(212+26+37+20+12+40.5+100+200);
// alert(348+162+45+55+55.01+673);

    if(DEV) {
	var e=document.querySelector('HEADER.menu');
	var s="<div style='position:absolute;top:0;left:20px;color:red;font-size:60px;"
+"text-shadow: 1px 1px 2px red, 0 0 1em blue, 0 0 0.2em blue;'>SHAVE-DEV</div>";
	newdiv(s,{},e);
    }

    nonav=1;
// ppage("Test<br>Proverko");
    JA.log();

    wintempl="<div id='{id}_body'>{s}</div><i id='{id}_close' title='Close' class='can4'></i>";
    wintempl_cls='pop4 zoomIn animated';

    cookie_policy();

    if(!idd('mys')) return;

    var x=decodeURI(document.location.hash);
    if(x!='') {
	x=x.replace(/^\#/g,'');
	idd('myfind').value=x;
	JA.Find(x);
    } else {
	 if(idd('mys') && shave_login!='' ) {
	    JA.Find('MyFriends');
	    JA.Find('MyLikes');
	}
//JA.Find('fuck');
    }


FRIENDS=false;
FRIENDSREV=false;

lasttop=0;
domotal=0;
setInterval(function(){ // –ø—Ä–µ–¥–µ–ª—ã - –æ—Ç 1(<getWinH()) –¥–æ getDocH() - getWinH(), –≤–∑—è—Ç—å –∑–∞ 100%
try {
    var hh=getScrollH();
    if(hh == lasttop) return;
    // ppage("hh="+hh+" wH="+getWinH()+" dH="+getDocH());
    lasttop=hh;
    if(domotal) return;

    var Q=idd('mys');
    if(!Q) return;

    if( hh > 0.8*( getDocH()-getWinH() ) ) {
        domotal=1;

	progress2();
	var name='progress2';

	Q=Q.querySelectorAll('DIV.newsearch')[0];
	    var query=Q.querySelector('.myQuery').innerHTML;
	    var start=1*(Q.querySelector('.myStart').innerHTML);
	    var total=1*(Q.querySelector('.myTotal').innerHTML);
	    var perpage=1*(Q.querySelector('.myPerpage').innerHTML);
        var offset=start+perpage;

	if(offset >= total) {
	    newdiv("<div style='text-align:center;font-size:16px;font-weight:bold;padding:10px;'>the end</div>",{},Q,'last');
	    return;
	}

	newdiv("<center><div id='"+name+"_tab' style='text-align:left;width:"+Math.floor(getWinW()/2)+"px;border:1px solid #666;margin-bottom:30px;'>\
<div id='"+name+"_bar' style='width:10px;height:10px;background-color:red;'></div>\
</div></center>",{class:'LoadNew',id:name},Q,'last');

        AJE(function(j){

//	    var s="<p class=z>Search: <font color='green'>"+h(j.query)+"</font> results " +h(j.start)+"/"+h(j.total) +" ("+h(j.perpage)+")</p>";

	    var Q=idd('mys').querySelectorAll('DIV.newsearch')[0];
	    // –ø–æ–ø—Ä–∞–≤–∏—Ç—å –¥–∞–Ω–Ω—ã–µ
	    Q.querySelector('.myStart').innerHTML=1*(j.start);
	    Q.querySelector('.myPerpage').innerHTML=1*(j.perpage);

//	    newdiv(s,{cls:'addsearch'},Q,'last');
	    // progress2();

	    JA.addPR( Q, JA.prList(j,1,offset) );

	    domotal=0;
	},{action:'Find',txt:Q.querySelector('.myQuery').innerHTML
// idd('myfind').value
,offset:offset});

    }

} catch(err){}

},200);

}


// JSON multipart AJAX: https://tokmakov.msk.ru/blog/item/182?ysclid=lbjemf8thb281169178

AJEinterval=false;
AJEprogress=0;

function AJE(fnn,ara) {
    if(AJEinterval) { clearInterval(AJEinterval); AJEinterval=false; }
    AJEprogress=0;
    AJEinterval=setInterval(function(){
	if(++AJEprogress>500) { clearInterval(AJEinterval); AJEinterval=false; }
	if(AJEprogress>10) progress2(AJEprogress,500);
    },50);

    ara.cookie=shave_cookie;
    AJ(
DEV?'/ALZYMOLOGIST/shave-dev/json.php'
:'/ALZYMOLOGIST/shave/json.php'
,function(j){
	    if(AJEinterval) { clearInterval(AJEinterval); AJEinterval=false; }
	    progress2();
	if(j===false) return salert("Server error",500);
	try{
	    j=JSON.parse(j);
	    if(j.error) {
		if(j.error == "login error") return JA.formLogin();
		return idie(  (j.message?h(j.message).replace(/\n/g,"<p>"):''),"Error: "+h(j.error) );
	    }

	    if(j.act) {
		if(j.act=='dier') return dier(j,'Server dier console');
		if(j.act=='idie') return idie(j,'Server idie console');
	    }

//	    if(!j.length==0)

	    fnn(j);
	} catch(e) {
	    idie('Json Error: '+h(e.name+":"+h(e.message).replace(/\n/g,"<p>"))+"<p>"+h(e.stack).replace(/\n/g,"<p>"));
	}
    },JSON.stringify(ara));
    return false;
}



JA={
//          _                _
//         | |    ___   __ _(_)_ __
//         | |   / _ \ / _` | | '_ \
//         | |__| (_) | (_| | | | | |
//         |_____\___/ \__, |_|_| |_|
//                     |___/

    formLogin: function(){
	melpc(
	    "<div style='max-width:400px;'>"+
	    "<h2>Login</h2>"+
	    "<form><table border='0' cellspacing='10'>"+
	    "<tr><td>Login: </td><td>"
+"<div class='password'>"
+"<input type='text' name='login' size='20'>"
+"</div>"
+"</td></tr>\n"+
	    "<tr><td>Password: </td><td>"
+"<div class='password'>"
+"<input type='password' name='password' size='20'>"
+"<a href='#' class='password-control' onclick='passwordCtrl(this)'></a>"
+"</div>"


+"</td></tr>\n"+
	    "<tr><td></td><td><input type='submit' value='Login' onclick=\""+
		"JA.Login(this.form.login.value,this.form.password.value);return false;"+
	    "\"></td></tr>\n"+
	    "<tr><td></td><td><div class='ll' onclick=\""+
		"JA.newLogin(this.closest('FORM').login.value,this.closest('FORM').password.value);"+
	    "\">New User</div></td></tr>\n"+
	    "</table></form>"+
	    "</div>");
    },

    log: function(login,cookie){
	if(login || login==='') {
    	    shave_login=login; f_save('shave_login',shave_login);
	    shave_cookie=cookie; f_save('shave_cookie',shave_cookie);
	} else {
	    shave_login=f_read('shave_login'); if(!shave_login) shave_login=''; // false
	    shave_cookie=f_read('shave_cookie'); // false
	}
	[...document.querySelectorAll('.rname')].forEach(p => p.innerHTML=h(shave_login));
    },

    // JA.Logout();
    Logout: function(){ JA.log('',''); salert('Logout',500); },

    // JA.newLogin('Pavel','Prikol123');
    newLogin: function(login,password){
        AJE(function(j){
	    JA.log(j.login,j.cookie); mclean(); salert('User created: '+h(j.login),500);
	},{action:arguments.callee.name,login:login,password:password});
    },

    // JA.Login('lleo','Prikol123');
    Login: function(login,password){
        AJE(function(j){
	    JA.log(j.login,j.cookie); mclean(); salert('Logged as '+h(j.login),500);
	},{action:arguments.callee.name,login:login,password:password});
    },

    aboutLogin: function(login,password){
        AJE(function(j){
	    dier(j);
	},{action:'listLogin'});
    },

//          _____ _           _
//         |  ___(_)_ __   __| |
//         | |_  | | '_ \ / _` |
//         |  _| | | | | | (_| |
//         |_|   |_|_| |_|\__,_|
//

    prLikes: function(j,array,offset) {
        if(FRIENDS===false) { return JA.loadFriends(function(){JA.prLikes(j,array,offset)}); }
        if(FRIENDSREV===false) { FRIENDSREV={}; for(var i in FRIENDS) FRIENDSREV[FRIENDS[i]]=i;	}

	// return dier(FRIENDS);
	 var es={},i=0;
	 for(var i=0;i<j.total;i++) { var e=j[i];
	    if(offset!=undefined) offset++;
	    // Author
	    es[' '+e.id]=(offset?"<div class='ofs'>"+offset+".</div>":'')
		+"<b>"+h(FRIENDSREV[e.uid])+"</b> set "
		    +(e.like=='like'?"<font color='green'>LIKE</font>":"<font color='red'>DISLIKE</font>")
		+"</div>"
		+" to <div class='ll' onclick=\"JA.Find('"+h(e.DOI)+"')\">"+h(e.author)+"</div>"
// +" ´"+h(e.about).replace(/\n/g,'<br>')+"ª"
// +"<div class='rama'>"+print_r(e)+"</div>"
		+"</div>";
	 }
	// dier(es);
	if(array) return es;
	var s=''; for(i in es) s+="<div class='snippet' pid='"+h(i).replace(/\s/g,'')+"'>"+es[i]+"</div>";
	return s;
    },


    prList: function(j,array,offset) {

	 var SORT=[];
	 var es={},i=0;
	 while(j[i]) { SORT.push({index:i,comments:1*j[i].commTO}); i++; }
	 SORT.sort(function(b,a){return 1*a.comments - 1*b.comments;});
//	 dier(SORT);
//return;
	 var es={}; for(var k of SORT) { var e=j[k.index];

	    if(offset!=undefined) offset++;
//	    idie(k.index+' : '+j[k.index].commTO);
//	    continue;

//	 ,i=0; while(j[i]) { var e=j[i++];
	    // s=+="<div class='snippet'>" //    s+="<div class='comm snippet'>"

	    // Author
	    es[' '+e.id]=
		( e.commFROM ? "<div class='ss toid'><i class='mv e_yes' onclick='JA.CommentsFrom(this)'></i></div>":'')
	    +(offset?"<div class='ofs'>"+offset+".</div>":'')
	    +"<div class='ss author'>"+h(e.author)+"</div>"
	    // Date
	    +"<div class='ss'>Date"+(e.Date_name?" ("+h(e.Date_name)+")":'')+": "+h(JA.doDate(e.Date))+"</div>"
	    // DOI
	    +(e.raw?
		 "<div class='ss'>DOI: <a target='_blank' href='http://dx.doi.org/"+h(e.DOI)+"' pid='"+h(e.id)+"' class='DOI'>"+h(e.DOI)+"</a></div>"
		:"<div style='display:block' pid='"+h(e.id)+"' class='DOI'></div>"
	    )
	    // Abstract
	    +"<div class='ss abstract'>"+h(e.about).replace(/\n/g,'<br>')+"</div>"
	    // More
	    +(e.raw?
		"<div class='ll r' onclick='oclo(this)'>[more]</div>"
		+"<div style='margin:10px;display:none;max-width:"+(getWinW()-200)+"px' class='ss r rama'>"
		// +olddat+"<hr>"
		+print_r(JSON.parse(e.raw),0,3).replace(/\n/g,'<br>')+'</div>'
		:''
	    )
	    // knPanel
	    +"<div class='knPanel'>"

	    +"<i class='kna mv0' onclick='JA.LikeList(this)' style='display:"+(1*e.Like+1*e.Dislike?'inline-block':'none')+"'></i>"
	    +"<i class='knp mv0' onclick='JA.Like(this,1)'></i><i class='kxp'>"+(1*e.Like?e.Like:'')+"</i>"
	    +"<i class='knm mv0' onclick='JA.Like(this,0)'></i><i class='kxm'>"+(1*e.Dislike?e.Dislike:'')+"</i>"

	    +"<button class='btn-hover color-5 mv0' onclick='JA.CommAdd(this)'>Comment</button>"
	    +"<div onclick='JA.CommentsTo(this)' class='commentscount mv0'"
		+( 1*e.commTO ? "style='display:inline-block'>"+e.commTO : ">")
		+"</div>"

	    +(in_array(shave_login,admin_logins)?"<i class='mv del_kn e_cancel1' onclick='JA.CommDel(this)'></i>":'')

	    +"</div>"
	    ;
	 }

	if(array) return es;
	var s=''; for(i in es) s+="<div class='snippet' pid='"+h(i).replace(/\s/g,'')+"'>"+es[i]+"</div>";
	return s;
    },

    addPR: function(e,es){
	for(var i in es) newdiv(es[i],{cls:'snippet',attr:{pid:(''+i).replace(/\s/g,'')}},e,'last');
    },

    prNEWDIV: function(j) {
	// —Å–∫–∞—á–∞—Ç—å –∏–º–µ–Ω–∞ —Ñ—Ä–µ–Ω–¥–æ–≤
        if(j.query=='MyLikes' && FRIENDS===false && j.total) { return JA.loadFriends(function(){JA.prNEWDIV(j)}); }

	var id='mySearch:'+j.query;

	newdiv(
	    "<div class='z'>"
	    +"Search: <span class='myQuery'>"+h(j.query)+"</span>"
	    +" results <span class='myStart'>"+h(j.start)+"</span>"
	    +"/<span class='myTotal'>"+h(j.total)+"</span>"
	    +" <span class='myPerpage'>"+h(j.perpage)+"</span>"
	    +"</div>"
	    ,{cls:'newsearch',id:id},idd('mys'),0);

	JA.addPR( idd(id), j.query=='MyLikes'?JA.prLikes(j,1,0):JA.prList(j,1,0) );
    },

    Find: function(txt){
	if(txt!='MyFriends' && txt!='MyLikes') setTimeout(function(){document.location.hash=txt;},10);
	// CLose all previus window
        var pp=idd('mys').querySelectorAll('DIV.newsearch');
	for(var p of pp) lleo_a(p,'zoomOut',function(){clean(p)});
        AJE(function(j){
	 progress2();
	 if(!j.total) return;
	 idd("checkbox").checked = false; // —á—Ç–æ–±—ã –∑–∞–∫—Ä—ã—Ç—å —Ö—É–π–Ω—é –≤ –º–æ–±–∏–ª—å–Ω–æ–π –≤–µ—Ä—Å–∏–∏
	 if(!j.query) return salert('Error results',1500);
	 JA.prNEWDIV(j);
       },{action:arguments.callee.name,txt:txt});
    },

//          _____     _                _
//         |  ___| __(_) ___ _ __   __| |___
//         | |_ | '__| |/ _ \ '_ \ / _` / __|
//         |  _|| |  | |  __/ | | | (_| \__ \
//         |_|  |_|  |_|\___|_| |_|\__,_|___/
//

    loadFriends(fn) { // Load FRIENDS tab immediately!
	AJE(function(j){
	    FRIENDS={}; var i=0; while(j[i]) { var x=j[i++]; FRIENDS[x.login]=x.uid; }
	    if(fn) fn();
	},{action:'listFriends'});
    },

    FindKey: function(txt){

	if(FRIENDS===false) return JA.loadFriends(function(){JA.FindKey(txt)});

	AJE(function(j){ j=j.response;
	    if(j=='') return ppage();
	    var s='', p=j.split("\n");
	    for(var l of p) s+="<label><div><input type='checkbox'"
		+( FRIENDS[l] ? ' checked' : '' )
		+" onchange='JA.FriendChange(this)'"
		+">&nbsp;<span class='Friend'>"+h(l)+"</span></div></label>"; //  &nbsp; ";
	    // ohelpc('FriendPage','Friends',s);
	//    clesalert(s,-1);
	    s="<div class='friends' style='max-width:500px;'>"+s+"</div>";

	    var w=document.querySelector('#salert .friends');
	    if(w) w.innerHTML=s;
	    else { salert(s,99999999999999999999); idd('loginfind').focus(); }

	},{action:'FindLogin',txt:txt});
    },

    FriendChange: function(e) {
	var login=e.closest('LABEL').querySelector('.Friend').innerHTML;
	var ch=(e.checked ? 1: 0);
	AJE(function(j){ JA.loadFriends() },{action:'setFriend',login:login,value:ch});
    },

//    FindFocus: function(txt){ progress2(1,1); },
//    FindBlur: function(txt){ ppage(); },

    Friends: function(){
	if(FRIENDS===false) return JA.loadFriends(function(){JA.Friends()});
	var s=''; for(var l in FRIENDS) s+="<div><label><input type='checkbox' checked"
		+" onchange='JA.FriendChange(this)'"
		+">&nbsp;<span class='Friend'>"+h(l)+"</span></label></div>";
	clean('idie');
	idie(s,'My friends');
    },


//           ____                                     _
//          / ___|___  _ __ ___  _ __ ___   ___ _ __ | |_
//         | |   / _ \| '_ ` _ \| '_ ` _ \ / _ \ '_ \| __|
//         | |__| (_) | | | | | | | | | | |  __/ | | | |_
//          \____\___/|_| |_| |_|_| |_| |_|\___|_| |_|\__|
//
    doDate: function(d) {
	if(d.indexOf('-')>=0 || 1*d < 4000) return d;
	d=new Date(1000*d); return d.getFullYear()+'-'+dd(d.getMonth()+1)+'-'+dd(d.getDate())
	+" "+dd(d.getHours())
	+":"+dd(d.getMinutes())
	+":"+dd(d.getSeconds());
    },
    getID: function(e) { return e.closest('.snippet').getAttribute('pid'); },
    getDOI: function(e) { var i=e.closest('.snippet').querySelector('.DOI'); return (i?i.innerHTML:''); },

    CommDel: function(e) { // Del commentary to Object.e
        var id=JA.getID(e), DOI=JA.getDOI(e);
	if(!confirm('Delete #'+id+" "+h(DOI)+"?")) return;

	AJE(function(j){
	    if(j.response=='OK') return clean(e.closest('.snippet'));
	    dier(j);
	},{action:arguments.callee.name,pid:id});
    },

    CommAdd: function(e) { // Add commentary to Object.e
        if(shave_login=='') return JA.formLogin();
	// Load template if need
        if(typeof(comment_templ)=='undefined' || !comment_templ) return AJ("/ALZYMOLOGIST/shave/comment.htm",function(s){ comment_templ=s; JA.CommAdd(e); });

        var id=JA.getID(e), hid='comm'+id, DOI=JA.getDOI(e);
	var author=e.closest('.snippet').querySelector('.author').innerHTML;
        ohelpc(hid,'Comment to: '+h(author), mpers(comment_templ,{id:id,DOI:DOI}) );
        var p=document.querySelector(".commentform TEXTAREA");
        p.style.width=(getWinW()*0.9)+'px';
        p.style.height=(getWinH()*0.7)+'px';
        center(hid);
        var v=f_read('comment'); if(v!='') p.value=v;
        p.focus();
    },

    delDubleNode: function(j,e) {
	// Del dubles
	var x=-1,p,P; while(j[++x]) {
	    P=document.querySelectorAll("DIV.snippet[pid='"+j[x].id+"']");
	    // if parent - no del
	    if(e && (p=e.closest("DIV.snippet[pid='"+j[x].id+"']")) ) { j[x]=false; continue; }
	    for(var p of P) {
	        lleo_a(p,'zoomOut',function(){clean(p)});
	        p.style.border='10px solid green';
	    }
	}
	return j;
    },

    CommentsTo: function(e) {
	var w=e.closest('.snippet');
	clean(w.querySelector(".commPlace"));
	AJE(function(j){
	    JA.delDubleNode(j);
	    var s=JA.prList(j);
	    newdiv(s,{cls:'commPlace'},w,'last');
	},{action:arguments.callee.name,pid:JA.getID(e)});
	// clean(e);
    },

    // –°—Ç–∞–≤–∏–º –ª–∞–π–∫–∏ –∑–∞–º–µ—Ç–∫–∞–º
    Like: function(e,like) {
	AJE(function(j){
	    var w=e.closest('.snippet');
	    w.querySelector('.kxp').innerHTML=(1*j.Like?j.Like:'');
	    w.querySelector('.kxm').innerHTML=(1*j.Dislike?j.Dislike:'');
	    w.querySelector('.kna').style.display=(1*j.Like+1*j.Dislike?'inline-block':'none');
	},{action:arguments.callee.name,pid:JA.getID(e),like:like});
    },

    LikeList: function(e) {
	AJE(function(j){
	    ohelpc('LikeList','Like list',"<table border=0 cellspacing='20' cellpadding='0' style='min-width:300px'><tr valign='top'>"
		+"<td><i class='knp'></i><i class='kxp'>"+j.Like+"</i><br>"+j.LikeList.join("<br>")+"</td>"
		+"<td><i class='knm'></i><i class='kxm'>"+j.Dislike+"</i><br>"+j.DislikeList.join("<br>")+"</td>"
		+"</tr></table>");
	},{action:arguments.callee.name,pid:JA.getID(e)});
    },


    CommentsFrom: function(e) {
	var pid=JA.getID(e);
	var w=e.closest('.snippet');
	var lev=1*w.style.paddingLeft;
	w.style.paddingLeft=(lev+100)+'px';
        AJE(function(j){
	 j=JA.delDubleNode(j,w);
	 var s=JA.prList(j);
	 newdiv(s,{cls:'commFrom'},w,0);
	 var W=w.querySelector('.commFrom')
	 W.style.marginLeft='-130px';
	 // lleo_a(W,'zoomOut',function(){lleo_a(W,'zoomIn');});
       },{action:arguments.callee.name,pid:pid});
       clean(e);
    },


    onNewComment: function(j) {
	f_save('comment','');
	clean('comm'+j.toid);
	var p=document.querySelector(".snippet .DOI[pid='"+j.toid+"']");
	JA.CommentsTo(p);
    },

};


//                         _
//          _ __ ___   ___| |_ __  ___
//         | '_ ` _ \ / _ \ | '_ \/ __|
//         | | | | | |  __/ | |_) \__ \
//         |_| |_| |_|\___|_| .__/|___/
//                          |_|


mclean=function() {
    var e=document.querySelector('.md');
    if(!e) e=document.querySelector('#md');

    if(e) {
        addEvent(e,'animationend',function(){clean(e)});
        e.classList.remove('ifadeIn');
        e.classList.add('ifadeOut'); // e.classList.add('zoomOut');
    }
}

function melpc(s) { return ohelpc('md','',s); }

//           ____            _    _
//          / ___|___   ___ | | _(_) ___
//         | |   / _ \ / _ \| |/ / |/ _ \
//         | |__| (_) | (_) |   <| |  __/
//          \____\___/ \___/|_|\_\_|\___|
//

function cookie_policy(){
    var s=
    "<div style='max-width:400px;'>"+
    "<h2>Cookie policy</h2>\n\n"+
    "We don't use fucking Cookies because it's mammoth shit."
	    +" Only dumbass use Cookies in "+(new Date().getFullYear())+". We use LocalStorage. Do you agree?"
	    +"<center><input type='button' class='agree' value='Agree' onclick='cookie_agree(this)'>"
	    +"<input type='button' class='agree' value='Disagree' onclick='cookie_disagree(this)'></center>"
    +"</div>"

    var x=f_read('alzymoid');
    if(x===false) {
	cookie_agree=function(e){ mclean(); f_save('alzymoid','none'); };
	cookie_disagree=function(){ document.location.href='https://natribu.org/fi'; };
	melpc(s);
    }
}



function oclo(e) { e=e.nextSibling.style; e.display=(e.display=='none'?'block':'none'); }


function set_doi_count(dois) {
        var p=document.querySelectorAll('.snippet .DOI');
        for(var e of p) {
	    var n=dois[e.innerHTML];
            if(n) {
		e=e.closest('.snippet').querySelector('.commentscount');
		e.innerHTML=n;
		e.style.display='inline-block';
    	    }
	}
}






function lleo_noa(e) { e.className=(e.className||'').replace(/ *[a-z0-9]+ animated/gi,''); };

function lleo_a(e,i,fn){
    lleo_noa(e);
    var c=e.className;
    e.className=(c==''?i:c+' '+i)+' animated';
    var fs=function(){
	lleo_rE(e,'animationend',fs);
	lleo_noa(e);
	if(fn)fn();
    };
    lleo_aE(e,'animationend',fs);
}

function lleo_aE(e,evType,fn){if(e.addEventListener){e.addEventListener(evType,fn,false);return true;}if(e.attachEvent){var r=e.attachEvent('on'+evType,fn);return r;} e['on'+evType]=fn; }
function lleo_rE(e,evType,fn){if(e.removeEventListener){e.removeEventListener(evType,fn,false);return true;}if(e.detachEvent)e.detachEvent('on'+evType,fn);}

function dd(i) { return (1*i>9?i:'0'+i); }

progress2=function(now,total) { name='progress2';
    if(!idd(name)) { if(!total) return;
 newdiv("<div id='"+name+"_tab' style='width:"+Math.floor(getWinW()/2)+"px;border:1px solid #666;'>\
<div id='"+name+"_bar' style='width:0;height:10px;background-color:red;'></div>\
</div>",{cls:'progreshave',id:name});
    } else if(!total) return clean(name);
    var proc=Math.floor(1000*(now/total))/10;
    var W=1*idd(name+'_tab').style.width.replace(/[^\d]+/g,'');
    idd(name+'_bar').style.width=Math.floor(proc*(W/100))+'px';
};

ppage=function(txt) { name='ppage';
    if(!idd(name)) {
	if(txt==undefined) return;
	newdiv(txt,{cls:'progreshave',id:name});
    } else {
	if(txt==undefined) return clean(name);
	zabil(name,txt);
    }
};


function passwordCtrl(e) {
    var v=e.closest('.password').querySelector('INPUT');
    if(v.type == 'password'){ e.classList.add('view'); v.type='text'; }
    else { e.classList.remove('view'); v.type='password'; }
    return false;
}
