/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

// jQuery plugin - Cheetah RSS Aggregator
(function($){
	$.fn.dolRSSFeed = function(sForceUrl) {
		return this.each( function(){

			var $Cont = $(this);
			var iRSSID = $Cont.attr( 'rssid' );
			if( !iRSSID && sForceUrl == undefined )
				return false;

			var iMaxNum = parseInt( $Cont.attr( 'rssnum' ) || 0 );
			var iMemID  = parseInt( $Cont.attr( 'member' ) || 0 );

			var sFeedURL = (sForceUrl != undefined) ? sForceUrl : site_url + 'get_rss_feed.php?ID=' + iRSSID + '&member=' + iMemID;

			$Cont.ch_loading(true);

            $.getFeed( {
				url: sFeedURL,
				success: function(feed) {

					if (feed != undefined && feed.items) {
						var sCode =
							'<div class="rss_feed_wrapper ch-def-bc-margin">';
						var sTarget, iCount = 0;
						for( var iItemId = 0; iItemId < feed.items.length; iItemId ++ ) {
							var item = feed.items[iItemId];
							var sDate = '', oDate, a;

                            if (null != (a = item.updated.match(/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)Z/))) {
                                oDate = new Date( a[1], a[2]-1, a[3], a[4], a[5], a[6], 0 );
                                sDate = oDate.toLocaleString();
                            } else if (item.updated.length > 0) {
    							oDate = new Date(item.updated.replace(/z$/i, "-00:00"));
                                sDate = isNaN(oDate) ? '' : oDate.toLocaleString();
                            }

                            sTarget = '';
                            if (item.link.substring(0, site_url.length) != site_url) // open external links in new window
                                sTarget = 'target="_blank"';

							sCode +=
								'<hr class="ch-def-hr ch-def-margin-sec-top ch-def-margin-sec-bottom" />' +
								'<div class="rss_item_wrapper">' +
									'<div class="rss_item_header ch-def-font-h2">' +
										'<a href="' + item.link + '" ' + sTarget + '>' + item.title + '</a>' +
									'</div>' +
									'<div class="rss_item_desc">' + item.description + '</div>' +
									'<div class="rss_item_info ch-def-font-small ch-def-font-grayed">' +
										'<span>' +
											sDate +
										'</span>' +
									'</div>' +
								'</div>';

							iCount ++;
							if( iCount == iMaxNum )
								break;
						}

                        sTarget = '';
                        if (feed.link.substring(0, site_url.length) != site_url) // open external links in new window
                            sTarget = 'target="_blank"';

						sCode +=
							'</div>' +

                            '<div class="rss_read_more ch-def-padding-left ch-def-padding-right">' +
                                '<a href="' + feed.link + '" ' + sTarget + ' class="rss_read_more_link">' + feed.title + '</a>' +
                            '</div>' +

                            '<div class="clear_both"></div>';

						$Cont.html( sCode );
					}
				}
			} );

		} );
	};
})(jQuery);
