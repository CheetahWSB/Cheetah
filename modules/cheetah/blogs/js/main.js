/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function PushEditAtBlogOverview(iBlogID, sDescription, iMemberID) {
	$('#edited_blog_div #EditBlogID').val(iBlogID);
	$('#edited_blog_div #Description').val(sDescription);
	$('#edited_blog_div #EOwnerID').val(iMemberID);
	$('#edited_blog_div').slideToggle('slow');
}

function BlogpostImageDelete(sUrl, sUnitID) {
	$.post(sUrl, function(data) {
		if (data==1) {
			$('#'+sUnitID).remove();
		} else {
			$('#'+sUnitID).html(data);
		}
	});
}
