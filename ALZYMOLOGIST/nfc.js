function isNFC() { return ("NDEFReader" in window); }

let NFCignore=1;

const scanNFC_engine = async (m) => {
  try {
    const ndef = new NDEFReader();
    ndef.onreadingerror = (ev) => { NFCignore=1; idie("Error! Cannot read the NFC tag."); ajaxoff(); };
    ndef.onreading = (ev) => { if(NFCignore) return; NFCignore=1;
        if(!m) idie('Serial Number: <b><font color=green>'+h(ev.serialNumber)+'</font></b>');
	else if(m=='scan') majax('module.php',{mod:'LOGIN',a:'logNFC',NFC:ev.serialNumber});
	else if(m=='login') majax('module.php',{mod:'LOGIN',a:'logNFC',NFC:ev.serialNumber});
	ajaxoff();
	// ndef.scan({signal:true});
    };
    await ndef.scan(); // {signal:true}
    NFCignore=0;
    ajaxon();
  } catch(error) { idie('Error NFC: '+h(error)); }
};


// https://github.com/GoogleChrome/samples/blob/gh-pages/web-nfc/index.js
// https://developer.mozilla.org/en-US/docs/Web/API/NDEFRecord/
// https://w3c.github.io/web-nfc/#write-data-and-print-out-existing-data

page_onstart.push('GO();');
// log = ChromeSamples.log;


function glog(s) { zabil('buka',vzyal('buka')+'<br>'+s); }
function gerror(s) { glog("Error: <font color=red>"+s+"</font>"); }


function GO() {

    if(!("NDEFReader" in window)) {
	glog("Web NFC is not available: use Android + Chrome");
	return;
    }
    otkryl('kpanel');
    glog('Web NFC is present');

scanButton.addEventListener("click", async () => {
  glog("Scan...");

  try {
    const ndef = new NDEFReader();
    await ndef.scan();
    glog("Scan started...");

    ndef.addEventListener("readingerror", () => {
      glog("Error! Cannot read data from the NFC tag. Try another one?");
    });

/*
    ndef.addEventListener("reading", ({ message, serialNumber }) => {
      glog(`Serial Number: <b><font color=green>${serialNumber}</font></b>`);
      glog(`Records: ${message.records.length}`);
	var l=message.records[0].toRecords();
	dier(l); // [0].toRecords(); // .toRecords()
    });
*/

    ndef.onreading = (ev) => {
	const R = ev.message.records;
        glog('<hr>Serial Number: <b><font color=green>'+ev.serialNumber+'</font></b> Records: '+R.length);
	// dier(ev);
	//.find(
	//	    (record) => record.type === "text" // "example.com:smart-poster"
	//	);

	if(R.length == 0) {
	    dier(R);
	} else for(var i=0;i<R.length;i++) {
	    // e.toRecords();
	    var e=R[i];

	    var enc = e.encoding;
	    if(!enc) enc='utf-8';

	    const decoder = new TextDecoder(enc);
	    var text = decoder.decode(e.data);

	    glog('&nbsp;&nbsp;&nbsp;&nbsp;'+i+') recordType = '+e.recordType
 +(e.mediaType?' MIME type: ' + e.mediaType:'')
);
//	    if(e.recordType !== "text" && e.recordType !== "url" ) {
//		dier(e);
		// var text=''; for(var j=0;j<16;j++) text+=" | "+e.data.getUint8(j);
	        // var text = e.data.getUint8(16);
		// alert(typeof(text)+'['+text+']');
//	    }

	      glog('&nbsp;&nbsp;&nbsp;&nbsp;text('+e.encoding+') = <i><font color=red>'+text+'</font></i>');

	    // dier(e);
	/*
	    if (rc.recordType === "text") {
	      const decoder = new TextDecoder(rc.encoding);
	      text = decoder.decode(rc.data);
	    } else if (rc.recordType === ":act") {
	      action = rc.data.getUint8(0);
	    }
	*/
	}

	/*
	  switch (action) {
	    case 0: // do the action
	      console.log(`Post "${text}" to timeline`);
	      break;
	    case 1: // save for later
	      console.log(`Save "${text}" as a draft`);
	      break;
	    case 2: // open for editing
	      console.log(`Show editable post with "${text}"`);
	      break;
	  }
	*/
    };




  } catch(error) { gerror(error); }
});































writeButton.addEventListener("click", async () => {
  glog("Write...");

//    var text = decoder.decode(e.data);

    const encoder = new TextEncoder();

  try {
    const ndef = new NDEFReader();
//    var DA = await (await fetch('/ALZYMOLOGIST/png/demo.png'));
//    var DA = await fetch('/ALZYMOLOGIST/png/demo.png');
//    DA=DA.arrayBuffer();
//const DA = new Int8Array(8);
//    DA[0] = 'L';
// dier(DA.length,'oki');
//    var DA=new Int8Array(80);

//    await ndef.write(
//	await (await fetch('/ALZYMOLOGIST/png/demo.png')).arrayBuffer()
//    );


    var D=await (await fetch('/ALZYMOLOGIST/png/demo.txt')).arrayBuffer();
//    var D=await (await fetch('/ALZYMOLOGIST/png/jura.png')).arrayBuffer();
    var W=new Uint8Array(D);

//    var len=W.length;
//    var w=new Uint8Array(6);
//    for(var i=0;i<5;i++) w[i]=W[i];
//     var DW = new DataView(D, 5, 10);
//    var DWW = new Uint8Array(DW);

/*
return    dier('<hr>D='+typeof(D)+' ['+D.length+']<br>'+D
+'<hr>W='+typeof(W)+' ['+W.length+']<br>'+W
// +'<hr>w='+typeof(w)+' ['+w.length+']<br>'+w
);
*/

    var ARA=[];
    var i=0;

    var LENGTH=40; // 251;
    var LENY=200; // 51; /// 42



//    var maxk=1;

    while(i<W.length) {
	var j=Math.min(i+LENGTH,W.length);
	var mlen=j-i; if(mlen<=0) break;

	var dat=new Uint8Array(mlen);
	for(var x=0;x<mlen;x++) dat[x]=W[i+x];

	ARA.push({
	    recordType: "unknown",
//	    recordType: "text",
	    data: dat // new ArrayBuffer(dat)
	});
	i=j;

	// if(--maxk == 0) break;
    }

    var x=0; while(x < ARA.length) {
	// dier(ARA[x]);
	var ARB=[];
	for(var y=0;(y < LENY/LENGTH && x<ARA.length);y++) ARB.push(ARA[x++]);
//	dier(ARB);

//	try { await ndef.write( {records: [ARA[x]] }); glog("Write: "+x);
	try { await ndef.write( {records: ARB }); glog("Write: "+x+'/'+ARA.length);
	} catch(e) { gerror("Errr: "+x+'/'+ARA.length); }
//	x++;
    }


//  dier({records: ARA});

//    await ndef.write({records: ARA});

    glog("Message written");
  } catch(error) { gerror(error); }

});


makeReadOnlyButton.addEventListener("click", async () => {
  glog("Read-only..");

  try {
    const ndef = new NDEFReader();
    await ndef.makeReadOnly();
    glog("> NFC tag has been made permanently read-only");
  } catch(error) { gerror(error); }
});

// idd('scanButton').click();



}