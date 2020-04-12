/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ShowHideEditCRSSForm(speed) {
	if ($('#manage_crss_block').css('display') == 'block') { //hide
		// Toggle switcher in notActive state
		$('#tm_crss_edit_button').removeClass().addClass('notActive');

		// Hiding of edit form
		$('#manage_crss_block').hide(speed);

		// Receive updated RSS content
		var sLink2 = location.href + '?action=re_gen_loaded_rss&mode=ajax2';
		getHtmlData('member_rss_list_loaded', sLink2, '', 'post');

		// Showing of loaded RSS block
		$('#member_rss_list_loaded').show(speed);
	} else { //show
		// Toggle switcher in Active state
		$('#tm_crss_edit_button').removeClass().addClass('active');

		// Showing of edit form
		$('#manage_crss_block').show(speed);

		// Clen content of member_rss_list_loaded
		$('#member_rss_list_loaded').text('');

		// Hiding of loaded RSS block
		$('#member_rss_list_loaded').hide(speed);
	}
}
