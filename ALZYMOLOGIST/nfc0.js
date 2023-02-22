  try {
    const ndef = new NDEFReader(); await ndef.scan();
    ndef.onreadingerror = (ev) => { idie("Error! Cannot read the NFC tag."); };
    ndef.onreading = (ev) => {
	const R = ev.message.records;
        idie('Serial Number: <b><font color=green>'+h(ev.serialNumber)+'</font></b>');
    };
    ajaxon();
  } catch(error) { idie('Error NFC: '+h(error)); }
