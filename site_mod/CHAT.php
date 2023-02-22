<?php /* Беседы в чате

{_CHAT:
Привет!
 Ну привет...
Чо делаешь?
 Телек смотрю.
 А что?
Сам дурак!
_}

*/

function CHAT($e) {
	$cf=array_merge(array(
'WIDTH'=>500,
'COLOR'=>'white',
'SWAP'=>false,
'HEAD'=>false
),parse_e_conf($e));

// ($cf['COLOR']?'#F0F0EA':'white')
// ddddddddddddclear:both;

STYLES("Чаты",'
.chathead {font-weight:bold;}

.chat0fr,.chat0to { margin:0; padding:0; width: '.$cf['WIDTH'].'px; }
.chat0fr { text-align:right; }
.chat0to { text-align:left; }

.chatfr,.chatto { text-align:left; position:relative; border-radius:15px; padding:8px 20px; font-family:"Helvetica Neue"; font-size:16px; font-weight:normal; margin-bottom:8px;}

.chatfr P,.chatto P { margin-top: 0.5em;  margin-bottom: 0.5em; }

.chatfr:before,.chatto:before,.chatfr:after,.chatto:after { content:""; position:absolute; bottom:-2px; height:20px; }
.chatfr:before,.chatto:before { transform:translate(0,-2px); }
.chatfr:after,.chatto:after { width:26px; transform:translate(-30px,-2px); background:'.$cf['COLOR'].'; }

.chatfr { color: white; background: #0B93F6; display: inline-block; align:center; }
.chatfr:before { z-index:0; right:-7px; border-right:20px solid #0B93F6; border-bottom-left-radius: 16px 14px; }
.chatfr:after { z-index:1; right:-56px; border-bottom-left-radius:10px; }

.chatto { color: black; background: #E5E5EA; display: inline-block; }
.chatto:before { z-index: 2; left:-7px; border-left:20px solid #E5E5EA; border-bottom-right-radius: 16px 14px; }
.chatto:after { z-index: 3; left:4px; border-bottom-right-radius:10px; }
');

	$e=explode("\n\n",($cf['body'])); if(sizeof($e)<2) $e=explode("\n",($cf['body']));

	$tg0=array('chat0to','chatto');
	$tg1=array('chat0fr','chatfr');

dier($cf);

	$s=''; foreach($e as $l) {
	    if($l=='') continue;

	    if(' '==substr($l,0,1)) { $tg=1; $l=substr($l,1); }
	    else $tg=0;

	    if($cf['HEAD']=='first') { list($head,$l)=explode("\n",$l,2); idie('ok'); }
	    else $head='';

	    if($cf['SWAP']) $tg=!$tg;

	    $s.="<div class='".($tg?$tg1[0]:$tg0[0])."'>"
		."<div class='".($tg?$tg1[1]:$tg0[1])."'>"
		.($head==''?'':"<div class='chathead'>".$head."</div>")
		.str_replace("\n","<p>",c($l))
		."</div></div>";
	}
	return "<div style='position:relative;'>".$s."</div>"
}
