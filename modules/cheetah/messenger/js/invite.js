/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

var ChMsgInvitationHeight = 300;
var ChMsgInviteInterval;
var sChMsgTemplate = "";
var aChMsgMessages = new Array();
var aChMsgInvitations = new Object();

function ChMsgUpdate() {
    var oDate = new Date();
    $.get (
        sChMsgUpdateUrl,
        {_t: oDate.getTime()},
        function(xXml) {
			var aMessages = xXml.getElementsByTagName("msg");
			for(var i=0; i<aMessages.length; i++)
				aChMsgMessages.push({
					'sender': aMessages[i].getAttribute("sender"),
					'nick': aMessages[i].getAttribute("nick"),
					'profile': aMessages[i].getAttribute("profile"),
					'text': aMessages[i].firstChild.nodeValue
				});
			if(aChMsgMessages.length) ChMsgShowInvitations();
        },
		'xml'
    );
	ChMsgInviteInterval = setTimeout('ChMsgUpdate();', ChMsgUpdateInterval);
}

function ChMsgShowInvitations() {
    if (sChMsgTemplate.length) {
		for(var i=0; i<aChMsgMessages.length; i++)
			ChMsgShowInvitation(aChMsgMessages[i]);
		aChMsgMessages.length = 0;
    } else {
        $.get(
            sChMsgGetUrl + "get_invitation",
            {},
            function(data) {
                // trim needed for Safari. LOL
				sChMsgTemplate = $.trim(data);
				ChMsgShowInvitations();
            },
            'html'
        );
    }
}

function ChMsgShowInvitation(oMessage) {
	if(aChMsgInvitations[oMessage["sender"]]) return;

	var sContents = sChMsgTemplate.split("__sender_id__").join(oMessage["sender"]);
	sContents = sContents.split("__sender_nickname__").join(oMessage["nick"]);
	sContents = sContents.split("__sender_profile__").join(oMessage["profile"]);
	sContents = sContents.split("__invitation_text__").join(oMessage["text"]);

	$.get(
		sChMsgGetUrl + "get_thumbnail/" + oMessage["sender"],
		{},
		function(data) {
			// trim needed for Safari. LOL
			sContents = sContents.split("__sender_thumbnail__").join($.trim(data));
			aChMsgInvitations[oMessage["sender"]] = $(sContents).prependTo('body');
			ChMsgRefreshPositions();
		},
		'html'
	);
}

function ChMsgRemoveInvitation(iSender) {
	aChMsgInvitations[iSender].remove();
	aChMsgInvitations[iSender] = null;
	ChMsgRefreshPositions();
}

function ChMsgRefreshPositions() {
	var iTopCount = 0;
	for(var i in aChMsgInvitations)
	{
		if(aChMsgInvitations[i] == null) continue;
		aChMsgInvitations[i].attr('style', "top:" + (ChMsgTopMargin + iTopCount * ChMsgInvitationHeight) + "px");
		iTopCount++;
	}
}

function ChMsgPerformAction(iSender, sAction) {
	switch(sAction) {
		case "accept":
			openRayWidget("im", "user", sChMsgMemberId, sChMsgMemberPassword, iSender);
			break;
		case "spam":
		case "block":
            $.post(sChMsgSiteUrl + 'list_pop.php?action=' + sAction, { ID: iSender } );
			break;
		case "decline":
		default:
			break;
	}
	ChMsgRemoveInvitation(iSender);
}
