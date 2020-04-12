/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// jQuery plugin - Cheetah RSS Aggregator
(function($){
	$.fn.dolGalleryImages = function(options) {

        var m_aSettings = $.extend({
            'noimg' : 'templates/base/images/icons/no-photo-110.png',
            'icons_size' : 32,
            'icons_opacity' : 0.7,
            'icons_animation_speed' : 400
        }, options);

        var m_eCurrent = null;

        var aMethods = {

            Activate: function (e) {
                var eCont = $(this);
                var iId = aMethods.GetID.apply (eCont, [e]);
                var eImgCont = jQuery('#ch-gallery-img-cont-' + iId);
                var callbackOnLoad = function () {
                    eImgCont.find('.ch-gallery-img')[0].src = this.src;
                    aMethods.Show.apply (eCont, [eImgCont, this.height, this.width]);
                    ch_loading(eCont.find('.ch-gallery-imgs').attr('id'), false);
                };
                var callbackOnError = function () {
                    eImgCont.find('.ch-gallery-img')[0].src = m_aSettings.noimg;
                    aMethods.Show.apply (eCont, [eImgCont, 0, 0]);
                    ch_loading(eCont.find('.ch-gallery-imgs').attr('id'), false);
                };
                ch_loading(eCont.find('.ch-gallery-imgs').attr('id'), true);
                if ($.browser.opera) {
                    var eImg = new Image();
                    eImg.src = eImgCont.attr('data-img');
                    eImg.onload = callbackOnLoad;
                    eImg.onerror = callbackOnError;
                } else {
                    $('<img src="' + eImgCont.attr('data-img') + '" />').filter('img').bind({
                        load: callbackOnLoad,
                        error: callbackOnError
                    });
                }
            },

            Show: function (e, iHeight, iWidth) {
                var eCont = $(this);
                var iId = aMethods.GetID.apply (eCont, [e]);
                var eImgCont = jQuery('#ch-gallery-img-cont-' + iId);
                var eImg = jQuery('#ch-gallery-img-' + iId).get(0);
                var eIcon = jQuery('#ch-gallery-icon-' + iId);
                var eTitle = eImgCont.find('.ch-gallery-img-title');
                var isFixed = aMethods.FixContainerHeightAndCenter.apply (eCont, [eImgCont, iHeight, iWidth]);

                if (null != m_eCurrent)
                    m_eCurrent.fadeOut();

                eImgCont.fadeIn(function () {
                    eTitle.fadeIn();
                    if (!isFixed)
                        aMethods.FixContainerHeightAndCenter.apply (eCont, [eImgCont]);
                });

                eCont.find('.ch-gallery-icon').fadeTo(0, m_aSettings.icons_opacity);
                eIcon.fadeTo(m_aSettings.icons_animation_speed, 1);
                eCont.find('.ch-gallery-icons-rails').animate({marginLeft: (parseInt(eCont.find('.ch-gallery-icons').innerWidth()) / 2 - eIcon.position().left - m_aSettings.icons_size / 2) + 'px'}, m_aSettings.icons_animation_speed);

                m_eCurrent = eImgCont;
            },

            FixContainerHeightAndCenter: function (e, iHeight, iWidth) {
                var eCont = $(this);
                var iId = aMethods.GetID.apply (eCont, [e]);
                var eImgCont = jQuery('#ch-gallery-img-cont-' + iId);
                var eImg = jQuery('#ch-gallery-img-' + iId).get(0);
                var eTitle = eImgCont.find('.ch-gallery-img-title');

                eTitle.hide();

                if (undefined == iHeight || !iHeight)
                    iHeight = eImg != undefined && eImg.complete > 0 ? parseInt(eImg.height) : 0;
                if (undefined == iWidth || !iWidth)
                    iWidth = eImg != undefined && eImg.complete > 0 ? parseInt(eImg.width) : 0;
                if (iWidth && iWidth > eCont.innerWidth()) {
                    var fRatio = iWidth/eCont.innerWidth();
                    iWidth = eCont.innerWidth();
                    iHeight = parseInt(iHeight / fRatio);
                }
                eCont.find('.ch-gallery-imgs').css('height',  iHeight ? iHeight + 'px' : 'auto');
                eImgCont.find('.ch-gallery-img').css('marginLeft', iWidth && eCont.innerWidth() ? (eCont.innerWidth() - iWidth) / 2 + 'px': 0);

                return iHeight > 0 ? true : false;
            },

            GetID: function (e) {
                var sId = e.attr('id');
                if (undefined == sId || !sId.length)
                    return false;
                var aMatches = sId.match(/(\d+)$/);
                if (null == aMatches)
                    return false;
                return parseInt(aMatches[1]);
            }
        };

		return this.each( function() {
			var eCont = $(this);

            eCont.find('.ch-gallery-icon-selector').css('left', (parseInt(eCont.find('.ch-gallery-icons').innerWidth()) / 2 - m_aSettings.icons_size / 2) + 'px');

            eCont.find('.ch-gallery-icon').each(function () {

                $(this).fadeTo(0, m_aSettings.icons_opacity);

                $(this).bind ('click', function () {
                    aMethods.Activate.apply (eCont, [$(this)]);
                });
            });

            eCont.find('.ch-gallery-img-cont').each(function () {

                $(this).bind ('click', function () {
                    var eNextImgCont = $(this).next('.ch-gallery-img-cont');
                    if (eNextImgCont.length)
                        aMethods.Activate.apply (eCont, [eNextImgCont]);
                    else
                        aMethods.Activate.apply (eCont, [eCont.find('.ch-gallery-img-cont:first-child')]);
                });
            });

            if (null == m_eCurrent)
                aMethods.Activate.apply (eCont, [eCont.find('.ch-gallery-img-cont:first-child')]);

		} );
	};

})(jQuery);
