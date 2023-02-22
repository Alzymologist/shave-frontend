// Р­РЅРµСЂРѕРіРѕРєРѕРјРїР°РЅРёСЏ: https://web.fortum.fi/api/v2/spot-price-anonymous?priceListKey=637&from=2022-12-01T00:00:00&to=2022-12-05T23:59:59

nonav=1;
page_onstart.push("GO();");


function dd(i) { return (1*i>9?i:'0'+i); }
function myd(D) { return D.getFullYear()+'-'+dd(D.getMonth()+1)+"-"+dd(D.getDate()); }

var dayWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

function GO() {

swf=function(e){ // -left right up down
    var l=e.detail.dir;
    idd('buka').addEventListener('swiped',swf);

    if(l=='left') idd('goLeft').click();
    if(l=='right') idd('goRight').click();
    if(l=='up') idd('goNow').click();

//  console.log(e.target); // element that was swiped
//  console.log(e.detail); // see event data below
//  console.log(e.detail.dir); // swipe direction
};

idd('buka').addEventListener('swiped',swf);


    var D=new Date();
    try {
	var v=decodeURI(document.location.hash).replace(/^\#/g,'');
	if(v!='') { v=v.split('-'); D=new Date(1*v[0],1*v[1]-1,1*v[2]); }
    } catch(e){}

    // http://cssworld.ru/datepicker/
    zabil('bucalendar','<input id="dda" type="text" value="'+myd(D)+'" size="10"'
+' onChange="Electro(this.value)"'
+' onClick="xCal(this,{fn:\'Electro\',order:1,delim:\'-\'})" onKeyUp="xCal()">'

+" <input type='submit' value='ok'>"
+" <input type='submit' id='goLeft' onclick='setDat(-1)' value='<'>"
+" <input type='submit' id='goNow' onclick='setDat(0)' value='now'>"
+" <input type='submit' id='goRight' onclick='setDat(+1)' value='>'>"
);
    Electro(myd(D));

/*
 for(var i of m) {
	var nd=new Date(new Date().getTime() + i*24*60*60*1000);
	o+=" <input type='button' value='"+myd(nd)+" "+dayWeek[nd.getDay()]+"'"
	    +(i?'':" style='padding:10px;'")
	    +" onclick='mElectro(this,"+i+")'> ";
	if(!i) Electro(myd(nd));
    }
    zabil('bukakn',o);
*/
}

function setDat(x) {
    if(!x) var N=new Date();
    else {
	var v=idd('dda').value.split('-');
	var N=new Date(1*v[0],1*v[1]-1,1*v[2]);
	N.setDate(N.getDate() + x);
    }
    idd('dda').value=myd(N);
    Electro(myd(N));
}

/*
function mElectro(e,ii) {
    var pp=document.querySelectorAll("#bukakn INPUT");
    for(var p of pp) p.style.padding='unset';
    e.style.padding='10px';
    Electro(e.value.replace(/ .+$/g,''),ii);
}

function prPal() { return;
    var s='';
    for(var i=0;i<256;i++) s+="<div style='width:10px;height:10px;background-color:rgb("+i+",255,0)'></div> ";
    s+="<p>";
    for(var i=0;i<256;i++) s+="<div style='width:10px;height:10px;background-color:rgb(255,"+(255-i)+",0)'></div> ";
    idie(s);
}
*/

function prColor(x) { x=1*x;
    var from=10;
    var to=75;

    var k = (to-from) / 512;

    var R=0,G=0,B=0;

    var l = (x-from) / k;

    if(l<0) l=0;
    if(l>512) l=512;

    R = Math.round( Math.min(255,l) );
    G = Math.round( Math.min(255, 511-l ) );

    var rgb="rgb("+R+","+G+","+B+")";
//    dier('x='+x+' ['+(Math.round(x)/100)+'] l='+l+' k='+k+" "+rgb);
    return rgb;
// 0 — это зеленый цвет RGB = (0, 255, 0)
// 20 — это желтый цвет RGB = (255, 255, 0)
// 40 — это красный цвет RGB = (255, 0, 0)
}


function prColor1(x) { x=1*x;
    var k=(TAB.max - TAB.min)/3;
    if(x < TAB.min+k) return 'green';
    if(x < TAB.min+k*2) return 'orange';
    return 'red';
}

function prEuro(l) {
    x=Math.round(l)/100;
    var c=(''+x).length;
    if( c==3 ) x=x+'0';
    else if( c==1 ) x=x+'.00';
    return "<font color='"+prColor1(l)+"'>"+x+"</font>";
}

function Electro(d) {

    var nowd=( myd(new Date()) == d ? 1 : 0);
    if(nowd) history.replaceState(null,null,' ');
    else document.location.hash=d;

//    zabil('buka','');

//    var d=D.getFullYear()+'-'+dd(D.getMonth()+1)+"-"+dd(D.getDate());
    var from=d+'T00:00:00';
    var   to=d+'T23:59:59';
//    var url="https://web.fortum.fi/api/v2/spot-price-anonymous?priceListKey=637&from=2022-12-04T00:00:00&to=2022-12-05T23:59:59";
    var url="https://web.fortum.fi/api/v2/spot-price-anonymous?priceListKey=637&from="+from+"&to="+to;

    var nh=dd(new Date().getHours())+':00';

// prPal();

    AJ('/ZYMO/cache.php',function(s){
        try {
    	    var j=JSON.parse(s); // dier(j);
	    var p={};
	    for(var x of j.series) { p[x.startDate.replace(/^[^T]+T/,'')]=Math.trunc(1000*x.value)/1000; }
	    p = Object.keys(p).sort().reduce(  (obj, key) => { obj[key] = p[key]; return obj; },{} );

	    TAB={min:999,max:-999};
//	    dier(p);
	    for(var i in p) { var x=p[i];
		if(x>TAB.max) TAB.max=x;
		if(x<TAB.min) TAB.min=x;
	    }

	    var o='<div style="font-weight:bold;font-size:16px;margin:0 0 10px 0;">'+j.rawResponse.params.StartDate.replace(/T00\:00$/g,'')
		+'<br>'+prEuro(TAB.min)+'~'+prEuro(TAB.max)+'€ kw/h</div>';

	    for(var i in p) {
		o+="<div style='font-size:9px;'>"+i
		    +" "+prEuro(p[i])

		    +" <div style='width:"+(p[i]*4)+"px;height:10px;text-align:center;"
			+"background-color:"+prColor(p[i])+";"
		    +"display:inline-block;'>"+(nowd && nh==i?"you are here":'')+"</div>"
		    +"</div>";

	    }
	    zabil('buka',o);
	} catch(r){ idie('eror'); }
    },url);
}
