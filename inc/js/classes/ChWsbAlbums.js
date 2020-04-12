/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWsbAlbums() {
	var $this = this;
	jQuery(document).ready(function($){
		$('.sys_aa_open').on('click', function(){
			var oParent = $(this).parents('.sys_album_unit:first');

			if(oParent.hasClass('sys_au_active')) {
				oParent.removeClass('sys_au_active');
				$this.hideNavigation(oParent.find('.sys_album_navigation'));
			}
			else {
				oParent.addClass('sys_au_active');
				$this.updateNavigation(oParent.find('.sys_album_navigation'), oParent.find('.sys_album'));
			}
		});

		$('.no-touch .sys_album_unit').hover(
			function(){
				$this.updateNavigation($(this).find('.sys_album_navigation').eq(0), $(this).find('.sys_album').eq(0));
			},
			function(){
				$this.hideNavigation($(this).find('.sys_album_navigation').eq(0));
			}
		);

		$('.sys_album_navigation a').on('click', function(){
			var oLink = $(this);
			var oContainer = oLink.parents('.sys_album_unit:first').find('.sys_album');

			if(oLink.hasClass('sys_an_next'))
				$this.showNext(oContainer);
			else
				$this.showPrevious(oContainer);

			$this.updateNavigation(oLink.parents('.sys_album_navigation:first'), oContainer);
		});
	});
}

ChWsbAlbums.prototype.showNext = function(oContainer) {
	var oItemToHide = oContainer.find('.sys-ai-front');
	var oItemToShow = oContainer.find('.sys-ai-middle');
	var oItemMiddle = oContainer.find('.sys-ai-back');
	var oItemToBack = oContainer.find('.sys-ai-out').eq(0);

	oItemToHide.addClass('sys-ai-move-right').removeClass('sys-ai-front');
	var iStop = setInterval(function() {
		oItemToHide.addClass('sys-ai-hidden');
		window.clearInterval(iStop);
	}, 200);

	oItemToShow.addClass('sys-ai-front').removeClass('sys-ai-middle');
	oItemMiddle.addClass('sys-ai-middle').removeClass('sys-ai-back');
	oItemToBack.addClass('sys-ai-back').removeClass('sys-ai-out');
};

ChWsbAlbums.prototype.showPrevious = function(oContainer) {
	var oItemToMiddle = oContainer.find('.sys-ai-front');
	var oItemToBack = oContainer.find('.sys-ai-middle');
	var oItemToShow = oContainer.find('.sys-ai-move-right').slice(-1);
	var oItemToOut = oContainer.find('.sys-ai-back');

	oItemToShow.removeClass('sys-ai-hidden').addClass('sys-ai-front');
	oItemToMiddle.removeClass('sys-ai-front').addClass('sys-ai-middle');
	oItemToBack.removeClass('sys-ai-middle').addClass('sys-ai-back');
	oItemToOut.removeClass('sys-ai-back').addClass('sys-ai-out');

	var iStop = setInterval(function() {
		if(!oItemToShow.hasClass('sys-ai-hidden')) {
			oItemToShow.removeClass('sys-ai-move-right');
			window.clearInterval(iStop);
		}
	}, 100);
};

ChWsbAlbums.prototype.updateNavigation = function(oNavigation, oContainer) {
	var oPrev = oNavigation.find('.sys_an_prev');
	if(!oContainer.find('.sys_album_item:first').hasClass('sys-ai-empty') && !oContainer.find('.sys_album_item:first').hasClass('sys-ai-front'))
		oPrev.addClass('sys-ain-visible');
	else
		oPrev.removeClass('sys-ain-visible');

	var oNext = oNavigation.find('.sys_an_next');
	if(oContainer.find('.sys-ai-middle').length > 0)
		oNext.addClass('sys-ain-visible');
	else
		oNext.removeClass('sys-ain-visible');
};

ChWsbAlbums.prototype.hideNavigation = function(oNavigation) {
	oNavigation.find('a').removeClass('sys-ain-visible');
};

var oAlbums = new ChWsbAlbums();
