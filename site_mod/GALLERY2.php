<?php

SCRIPT_ADD($GLOBALS['wwwhost']."extended/gallery2/slider.function.js");
STYLE_ADD($GLOBALS['wwwhost']."extended/gallery2/slider.style.css");

SCRIPTS('gallery2',"

const setting = {
	setup1: {
		// включаем постраничную навигацию
		dots: true,
		// включаем управление с клавиатуры клавишами навигации 'вправо / влево'
		keyControl: true,
		// настройки галереи в зависимости от разрешения
		adaptive: {
			// настройка работает в диапазоне разрешений 320-560px
			320: {
				// одновременно выводится 1 элемент
				visibleItems: 1,
				// расстояние между изображениями 5px
				margin: 5,
				// запрещаем постраничную навигацию
				dots: false
			},
			// настройка работает в диапазоне разрешений 560-768px
			560: {
				// одновременно выводится 1 элемент
				visibleItems: 2,
				// расстояние между изображениями 5px
				margin: 5,
				// запрещаем постраничную навигацию
				dots: false
			},
			// настройка работает в диапазоне разрешений 768-1024px
			768: {
				// одновременно выводятся 2 элемента
				visibleItems: 3,
			},
			// настройка работает в диапазоне разрешений 1024 и выше
			1024: {
				// одновременно выводятся 3 элемента
				visibleItems: 4
			}
		}
	},
	setup2: {
		// одновременно выводится 4 элемента
		visibleItems: 4,
		// разрешаем постраничную навигацию
		dots: true,
		// разрешаем управление с клавиатуры клавишами навигации 'вправо / влево'
		keyControl: true,
		// выключаем адаптивность
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
