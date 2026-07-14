$(window).on('scroll', function() {
	// スマホのみ？
	$('header').toggleClass('fixed', $(this).scrollTop() > 32)
});

$(function(){
	// #で始まるリンクをクリックしたら実行されます

	var pagetop = $('#page_top');
	pagetop.hide();
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {  //100pxスクロールしたら表示
			pagetop.fadeIn(300);
		} else {
			pagetop.fadeOut(300);
		}
	});
	pagetop.click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 500); //0.5秒かけてトップへ移動
		return false;
	});
});
