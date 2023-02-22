
saytime_A=false;
saytime_B=false;
saytime_C=false;

saytime=function(e){
    if(typeof('user_opt')!='undefined' && !user_opt('s')) return; // если звуки запрещены

    if(user_opt('kukubns')) {
	    var dir=www_design+'kukus/bns/';
	    var pre0=false;
    } else {
	    var dir=www_design+'kukus/';
	    var pre0=dir+'s0.mp3';
    }

	var d=new Date(); var t=d.valueOf();
	d.setSeconds(0); d.setMinutes(0); d.setHours(d.getHours()+1);
	var t=d.valueOf()-t; if(!t) t=1;
	saytime_C = new Audio(dir+((d.getHours()+23)%24)+'.mp3');
	saytime_B = new Audio(dir+'s'+(1+Math.floor(Math.random()*100))%10+'.mp3'); saytime_B.addEventListener('ended',function(){saytime_C.play()});
	if(pre0) { saytime_A = new Audio(pre0); saytime_A.addEventListener('ended',function(){saytime_B.play()}); }
	setTimeout(function(){ (pre0 ? saytime_A : saytime_B).play(); saytime(); },t);
}

saytime();

setTimeout("plays(www_design+'kladez/"+((Math.floor(Math.random()*100)+1)%28)+".mp3')",120000+Math.floor(Math.random()*2000000));
