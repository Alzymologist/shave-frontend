<?php

if($_SERVER['QUERY_STRING']=='list') {
    header("Content-Type: text/html; charset=utf-8");
    highlight_string(file_get_contents($_SERVER['SCRIPT_FILENAME']));
    exit;
}

// header("Content-type: text/plain; charset=utf-8");
// die( bredogon_mihail() );

function fake_mikel() { global $TP;

$tmpl=trim("{SAD}
{GOD}
{CITE}
{NEED}
{WELCOME}

Father O'Mikel
");

$e=explode("\n","
SAD
It's sad that people waste their lives on things like this.
You've wasted a lot of time.
You are wasting your time.
You are not doing what you should be doing.
You are not looking for the truth there.
Another lost soul...
Tell me, are you interested in doing something like this?
Are you really interested in what you do?
Who did you do it for? What for?
What is your work for?

GOD
Instead, you could think about God.
If you want to understand life, read the Bible.
God is unknowable, but we can get close to him.
Everything has already been described in the Bible.
Have you tried reading the Gospel?
There is a book where all your questions have already been answered.

CITE
A good mind is agreeable; but the way of the wicked is cruel. (Prov. 13:15)
The teaching of the wise is the source of life, moving away from the nets of death. (Prov. 13:15)
Inspiring this to the brethren, you will be a good servant of Jesus Christ, nourished by the words of faith and the good teaching that you have followed. (Tim. 4:6)
Until I come, be engaged in reading, instructing, teaching. (Tim. 4:13)
Delve into yourself and into the teaching; do this constantly: for by doing so, you will save yourself and those who listen to you. (Tim. 4:16)
The fear of the Lord is the instruction of wisdom, and before honor comes humility. (Proverbs 15:33)
Teach me understanding and knowledge, because I believe in your commandments. (Psalm 119:66)
This is the fear of the Lord, that is, wisdom, and to depart from evil is understanding. (Job 28:28)
Whoever is wise, let him give heed to these things and consider the great love of the Lord. (Psalm 107:43)
The fear of the Lord is the beginning of wisdom, and the knowledge of the Holy One is understanding. (Proverbs 9:10)
Strive to present yourself to God as an approved worker who has no need to be ashamed and who properly handles the word of truth. (Timothy 2:15)
All Scripture is inspired by God and is useful for teaching, for reproof, for correction, for teaching in righteousness. (Timothy 3:16)
The mind of the prudent acquires knowledge, but the ear of the wise seeks knowledge. (Proverbs 18:15)
And whatever you do, in word or deed, do it all in the name of the Lord Jesus, give thanks to God the Father through him. (Colossians 3:17)
Keep this Book of the Law always on your lips; meditate on it day and night so that you can be careful to do whatever is written in it. Then you will be prosperous and successful. (Joshua 1:8)

NEED
You must open your heart.
Let Jesus into your heart.
Think about it before it's too late.
These words are not given in vain.

WELCOME
We can discuss this if you're in Cleveland.
If you come to preach with me, we can talk about it.
Come to our community, we can study the Bible together.
I am ready to answer all your questions.

");

  $TP=array(); $m=''; foreach($e as $i=>$s) {
    $s=rtrim($s);
    if($s=='') $m='';
    elseif($m=='') $m=$s;
    else {
	if(!isset($TP[$m])) $TP[$m]=array();
	$TP[$m][]=$s;
    }
  }

// die(print_r($TP,1));

  $akey=array_keys($TP);
  foreach($akey as $name) {
    $GLOBALS['preg_replace_callback_name']=$name;
    $tmpl = preg_replace_callback("/\{".$name."\}/",function() {
	    $p=$GLOBALS['TP'][$GLOBALS['preg_replace_callback_name']];
	    return str_replace("\\n", "\n", $p[rand(0, sizeof($p)-1)]);
    }, $tmpl);
  }

return $tmpl."\n";
}
?>