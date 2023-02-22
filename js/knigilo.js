page_onstart.push('knigagraf(kniga)');

function d00(i) { return (1*i>9?'':'0')+i; }
function d00i(d) { d=d.split('-'); return d[0]*10000+d[1]*100+1*d[2]; }

if(typeof(kniga)=='undefined') kniga={
    max: 600000,
    start: '2020-03-15',
    finish: '2020-07-15',
    id: 'cvs',
    files: [
	'glt-alcoserge',
	'glt'
    ]
//    lastdata:0,
//    calculates:[],
//    dates:{}
};


function kniga_obradat(KG,s,ee) {

    if(!s || s.indexOf('|') < 0 ) { alert('File error: '+ee.responseURL); KG.fileslength--; }
    else {

    var M={};
    var x=s.split("\n");

// alert(print_r(KG));

    for(var i in x) { if(x[i].indexOf('|')<0) continue;
	var l=x[i].split('|'),d=l[0].split('_')[0];
	// M[d]=Math.max(M[d]|0,1*l[1]);
	M[d]=1*l[1];
	// alert(x[i]+" = "+" ["+d+"] = "+M[d]);
	// KG.lastdata=Math.max(KG.lastdata|0,d00i(d));
    }

    if(!KG.calculates) KG.calculates=[];
    KG.calculates.push(M);

    }

    if(KG.calculates.length != KG.fileslength) return;

    // дата сегодня
    var nw=new Date(); KG.lastdata=nw.getFullYear()+d00(nw.getMonth()+1)+d00(nw.getDate());

    // --------
    KG.N=0;
    var caLL=(new Array(KG.calculates.length)).fill(0); // .[]; for(var i=0;i<KG.calculates.length;i++) caLL.push(0);

    var l=0,N=0; for(var i in KG.dates) { KG.N++;
	if(d00i(i)>KG.lastdata) continue;

	for(var j in KG.calculates) {
	    var c=KG.calculates[j][i]; if(c!==undefined) caLL[j] = c;
	    KG.dates[i] += caLL[j];
	}
    }

// dier(KG);
    kniga_rgraph(KG);
}

function knigagraf(KG) {
    var a=KG.start.split('-'),ds=new Date(1*a[0],1*a[1]-1,1*a[2]);
    a=KG.finish.split('-'); var df=new Date(1*a[0],1*a[1]-1,1*a[2]);
    KG.dates={}; var stop=1000; while( --stop && ( ds <= df ) ) {
	var dt=ds.getFullYear()+'-'+d00(ds.getMonth()+1)+'-'+d00(ds.getDate());
	KG.dates[dt]=0;
	ds.setDate(ds.getDate()+1);
    }

/*
LOADS([
    "https://www.rgraph.net/libraries/RGraph.common.core.js",
    "https://www.rgraph.net/libraries/RGraph.bar.js",
    "https://www.rgraph.net/libraries/RGraph.line.js",
    "https://www.rgraph.net/libraries/RGraph.drawing.yaxis.js"
],function(){ kniga_dates() });
*/

    KG.fileslength = 0+KG.files.length;

    for(var i in KG.files) {
	var url = wwwhost+'tmp/upload-doc/'+KG.files[i]+'/journal.txt';
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange=function(){
	    if(this.readyState==4 && this.status==200 && this.responseText!=null) kniga_obradat(KG,this.responseText,url);
	    else if(this.status==404) kniga_obradat(KG,false,this);
	    // else alert('err: ['+this.readyState+' / '+this.status+'] -> '+url);
	};
	xhr.open('GET',url);
	xhr.send();
    }

// AJ(wwwhost+'tmp/upload-doc/'+KG.files[i]+'/journal.txt',function(s){ kniga_obradat(KG,s) });
}




function kniga_rgraph(KG) { // ==================== DROW =======================

    marginLeft = 25;

    var i = idd(KG.id);
    var Mx = 1*i.width; if(!Mx) i.width=Mx=(getWinW()-50);
    var My = 1*i.height; if(!My) i.height=My=(getWinH()-150);

    var Tdata = [];
    var Xname = [];

    var Ymax=Math.floor(KG.max/1000);
    var Ndata = []; // [0,Ymax];

    var k=0,kp=Ymax/KG.N;

    var colY = My / 30;
    var colX = Mx / 30;

    var xxp = KG.N / colX;

var dayes=' янв фев мар апр май июнь июль авг сент окт ноя дек'.split(' ');
var lastmon='';

xx=0; for(var i in KG.dates) {
    if( xx++ > xxp ) {
	var mon=dayes[1*i.split('-')[1]];
	if(mon!=lastmon) { lastmon=mon; mon+=': '; } else mon='';
	var mi=mon+i.split('-')[2];
	Xname.push(mi); xx=0;
    }
    xx++;
    var n=Math.floor(KG.dates[i]/1000);
    Tdata.push(n);
    Ndata.push(k);
    k+=kp;
}



// =========================================================================================

    lineika = new RGraph.Line({ id: KG.id,
        data: [],
        options: {
// yaxisLabelsCount: 20,
//            spline: true,
// yaxisLabelsCount: 5, // colX,
//            tickmarksStyle: 'filledcircle',
//            tickmarksSize: 2,
// backgroundGrid: 0, backgroundGridVlines: 0, backgroundGridHlines: 0,

backgroundGrid: 1,
backgroundGridVlines: 1,
backgroundGridHlines: 1,
backgroundGridColor: '#eee', //	The color of the background grid.	#ddd
// backgroundGridLinewidth: 3, //	The linewidth that the background grid lines are. Decimals (eg 0.5) are permitted.	1
// backgroundGridBorder: 1, //	Determines whether a border line is drawn around the grid.	true
// backgroundGridHlines: 1, // 	Determines whether to draw the horizontal grid lines.	true
backgroundGridHlinesCount: KG.max/5000, //colY, //	Determines how many horizontal grid lines you have. The default is linked to how many scale labels that you have.	null
backgroundGridVlinesCount: Xname.length,
yaxisLabelsCount: colY,
// backgroundGridVlines: 1, // 	Determines whether to draw the vertical grid lines.	true
// backgroundGridVlinesCount: 8, //	Determines how many vertical grid lines you have. The default is to be linked to how many scale labels that you have.	null
// backgroundGridDashed: 1, // 	You can specify a dashed background grid by setting this to true. This option takes precedence over the dotted variant.	false
// backgroundGridDotted: 1, //	You can specify a dotted background grid by setting this to true.	false
    xaxisLabels: Xname,
    xaxisLabelsSize: 7,
    yaxisLabelsSize: 7,
    yaxisScaleUnitsPost: 'k',

//            shadow: false,
//            linewidth: 4,
            marginLeft:marginLeft,
            marginBottom: 5,
            xaxis: false,
            yaxis: false,
//            yaxisLabels: false,
//            colors: ['gray', 'red'],
            yaxisScaleMax: Ymax,
marginInner: 30,
// adjustable: true,
//            yaxisScale: false
        }
    }).draw();


// =================================================================


    line = new RGraph.Line({ id: KG.id,
        data: [Ndata],
        options: {
            spline: true,
	    backgroundGrid: 0,
            shadow: false,
            linewidth: 4,
            marginLeft:marginLeft,
            marginBottom: 5,
            xaxis: false,
            yaxis: false,
            colors: ['gray', 'red'],
            yaxisScaleMax: Ymax,
	    marginInner: 30,
	    // adjustable: true,
            yaxisScale: false
        }
    }).draw();

    // Create the Bar chart. The marginInnerGrouped setting is set to -15 and this is what gives the chart the overlapping appearance.
    bar = new RGraph.Bar({ id: KG.id,
        data: [Tdata],
        options: {
            marginBottom: 5,
            marginLeft:marginLeft,
            colors: ['red'],
	    backgroundGrid: 0,
            yaxisScale: false,
            xaxis: false,
            yaxis: false,
            yaxisLabels: false,
            yaxisScaleMax: Ymax, // Math.floor(kniga.max/1000), // 20,
	    marginInnerGrouped: 1,
        }
    }).draw();
/*
.wave({frames: 30}).responsive([
        {maxWidth: 900,width: 300,height: 150,options: {xaxisLabels: ['Mon','Tue','Wed','Thu','Fri','Sat'],marginInnerGrouped: -5,marginInner: 15,textSize: 8},callback: function () {axes.x = 315;axes.set('textSize', 8);axes.set('linewidth', 2);axes2.set('textSize', 8);axes2.set('linewidth', 2);axes2.canvas.parentNode.parentNode.style.cssFloat = 'none';axes.x = 270;}},
        {maxWidth: null, width: 650, height: 250, options: {xaxisLabels: ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],marginInnerGrouped: -15, marginInner: 30, textSize: 14}, callback: function () {axes.x = 625;axes.set('textSize', 16);axes.set('linewidth', 2);axes2.set('textSize', 16);axes2.set('linewidth', 2);axes2.canvas.parentNode.parentNode.style.cssFloat = 'right';axes.x = 625;}}
    ]);
*/
//.responsive([
//        {maxWidth:900,width:400,height:150,options: {textSize:10,xaxisLabels:['Mon\n(yuck!)','Tue','Wed','Thu','Fri\n(woo!)','Sat','Sun'],marginInner: 10}},
//        {maxWidth:null,width:750,height:250,options: {textSize:14,xaxisLabels: ['Monday\n(yuck!)','Tuesday','Wednesday','Thursday','Friday\n(woo!)','Saturday','Sunday'],marginInner: 20}}
//    ]);
/*
.trace().responsive([
        {maxWidth: 900, options: {linewidth: 3,tickmarksSize: 3,marginInner: 30}},
        {maxWidth: null, options: {linewidth: 5,tickmarksSize: 7,marginInner: 55}}
    ]);
*/
// =========================================================================================


}
