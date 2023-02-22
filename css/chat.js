chat_id=function(e){ return 1*e.closest("DIV[id^='mail']").id.replace(/mail/,''); };

chat=function(e){
    var id=chat_id(e),pan=e.querySelector('.mailpan');
    if(pan) clean(pan); else {
	pan=idd('mail_tread').querySelectorAll('.mailpan'); for(var i in pan) clean(pan[i]);
	var ee=e.closest("DIV[id^='mail']");
	var inbound=1*ee.getAttribute('inbound');
	var timeread=1*ee.getAttribute('timeread');

	var s='';
	if(inbound) s+="<input class='mv' type='button' value='Answer' onclick='chat_a(this)'>";
	s+=(s==''?'':" &nbsp; &nbsp; ")+"<input class='mv' type='button' value='Delete' onclick='chat_d(this)'>";
	if(!inbound && !timeread) s+=(s==''?'':" &nbsp; &nbsp; ")+"<input class='mv' type='button' value='Edit' onclick='chat_e(this)'>";
	if(inbound && !timeread) s+=(s==''?'':" &nbsp; &nbsp; ")+"<input class='mv' type='button' value='OK' onclick='chat_r(this)'>";

	zabil(e,"<div class='mailpan'>"+s+"</div>"+vzyal(e));

	var W=e.clientWidth,H=e.clientHeight;
        pan=e.querySelector('.mailpan');
        var w=pan.clientWidth,h=pan.clientHeight;
        pan.style.top=(H-h)/2+'px';
        pan.style.left=(W-w)/2+'px';
    }
};

chat_d=function(e){ var id=chat_id(e); if(confirm('Delete?')) majax('mailbox.php',{a:'del_id',id:id}); };
chat_r=function(e){ var id=chat_id(e); majax('mailbox.php',{a:'readed',id:id}); }
chat_e=function(e){ majax('mailbox.php',{a:'editmail',id:chat_id(e)}); };

chat_a=function(e){ var id=(e?chat_id(e):0);
    var uc=idd('mail_tread').getAttribute('uc');
    majax('mailbox.php',{a:'newform',unic:uc,answerid:id});
};

onMailSend=function(id) { majax('mailbox.php',{a:'mail_one',id:id}); };

mail_button_new=function(){
    var e=idd('mailchat');
    if(!e.querySelector('#newmail')) zabil(e,"<input class='chat_but' style='margin-bottom:20px' id='newmail' class='mv' type='button' value='New' onclick='chat_a()'>"+vzyal(e));
};
