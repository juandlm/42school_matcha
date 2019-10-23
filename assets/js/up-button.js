$(document).ready(function(){
	$('body').append('<div id="go-top" title="Top" style="z-index: 9000000;"><i class="fas fa-chevron-up text-white fa-lg fa-fw"></i></div>');
});

$(function() {
	$.fn.scrollToTop = function() {
		if ($(window).scrollTop() >= "250")
			$(this).fadeIn("slow")
		var scrollDiv = $(this);
		$(window).scroll(function() {
			if ($(window).scrollTop() <= "250")
				$(scrollDiv).fadeOut("slow")
			else
				$(scrollDiv).fadeIn("slow")
		});
		$(this).click(function() {
			$("html, body").animate({scrollTop: 0}, "slow")
		})
	}
});

$(function() {
	$("#go-top").scrollToTop();
});
