<?php

SCRIPT_ADD($GLOBALS['wwwhost']."extended/gallery2/slider.function.js");
STYLE_ADD($GLOBALS['wwwhost']."extended/gallery2/slider.style.css");

SCRIPTS('gallery2',"

const setting = {
	setup1: {
		// �������� ������������ ���������
		dots: true,
		// �������� ���������� � ���������� ��������� ��������� '������ / �����'
		keyControl: true,
		// ��������� ������� � ����������� �� ����������
		adaptive: {
			// ��������� �������� � ��������� ���������� 320-560px
			320: {
				// ������������ ��������� 1 �������
				visibleItems: 1,
				// ���������� ����� ������������� 5px
				margin: 5,
				// ��������� ������������ ���������
				dots: false
			},
			// ��������� �������� � ��������� ���������� 560-768px
			560: {
				// ������������ ��������� 1 �������
				visibleItems: 2,
				// ���������� ����� ������������� 5px
				margin: 5,
				// ��������� ������������ ���������
				dots: false
			},
			// ��������� �������� � ��������� ���������� 768-1024px
			768: {
				// ������������ ��������� 2 ��������
				visibleItems: 3,
			},
			// ��������� �������� � ��������� ���������� 1024 � ����
			1024: {
				// ������������ ��������� 3 ��������
				visibleItems: 4
			}
		}
	},
	setup2: {
		// ������������ ��������� 4 ��������
		visibleItems: 4,
		// ��������� ������������ ���������
		dots: true,
		// ��������� ���������� � ���������� ��������� ��������� '������ / �����'
		keyControl: true,
		// ��������� ������������
		responsive: false
	}
};
");

function GALLERY2($e) {

$p=explode("\n",$e);
$o='';

// dier($p);

$img1=''; foreach($p as $l) { if(empty($l)) continue;

    if(strstr($l,'|')) {
	list($l,$desc)=explode('|',$l,2); $l=trim($l); $desc=trim($desc);
    } elseif(strstr($l,' ')) {
	list($l,$desc)=explode(' ',$l,2); $l=trim($l); $desc=trim($desc);
    } else $desc='';

    if(empty($img1)) $img1=$l;
    $ll=dirname($l)."/pre/".basename($l);
    // $o.="<li><img src='".h($ll)."' /></li>";
    $o.="<div><img ".($desc==''?'':"alt=\"".h($desc)."\" ")."src='".h($ll)."' onclick='bigfoto(this.src.replace(/\\/pre\\//,\"/\")"
.($desc==''?'':",\"".$desc."\"")
.")'></div>";
}

return '{_nobr:
<div class="gallery2" data-setting="setup1">
<div class="slider">
<div class="stage">
'.$o.'
</div>
</div>

<div class="control">
    <div class="nav-ctrl" data-hidden="true">
	<span class="prev" data-shift="prev">prev</span>
	<span class="next" data-shift="next">next</span>
    </div>
    <ul class="dots-ctrl" data-hidden="true"></ul>
</div>

</div>_}';

}
