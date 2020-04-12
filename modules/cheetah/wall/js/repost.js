/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWallRepost(oOptions) {
	this._sActionsUri = oOptions.sActionUri;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallRepost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : oOptions.iOwnerId;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;
}

ChWallRepost.prototype.repostItem = function(oLink, iOwnerId, sType, sAction, iId) {
	var $this = this;
	var oParams = $.extend(this._getDefaultParams(), {
		owner_id: iOwnerId,
		type: sType,
		action: sAction,
		object_id: iId
	});

	var oLoading = $('#ch-wall-view-loading');
    if(oLoading)
    	oLoading.ch_loading(true);

	jQuery.post(
        this._sActionsUrl + 'repost/',
        oParams,
        function(oData) {
        	if(oLoading)
            	oLoading.ch_loading(false);

        	if(oData && oData.msg != undefined && oData.msg.length > 0)
                alert(oData.msg);

        	if(oData && oData.counter != undefined) {
        		var sCounter = $(oData.counter).attr('id');
        		/*
        		 * Full replace (with link)
        		$('#' + sCounter).replaceWith(oData.counter);
        		*/
        		$('#' + sCounter + ' i').html(oData.count);
        		$('#' + sCounter).parents('.wall-repost-counter-holder:first').ch_anim(oData.count > 0 ? 'show' : 'hide');
        	}

        	if(oData && oData.disabled)
    			$(oLink).removeAttr('onclick').addClass($(oLink).hasClass('ch-btn') ? 'ch-btn-disabled' : 'wall-repost-disabled');
        },
        'json'
    );
};

ChWallRepost.prototype.toggleByPopup = function(oLink, iId) {
	var $this = this;
    var oParams = this._getDefaultParams();
    oParams['id'] = iId;

	var oLoading = $('#ch-wall-view-loading');
    if(oLoading)
    	oLoading.ch_loading(true);

    jQuery.get(
    	this._sActionsUrl + 'get_reposted_by/',
        oParams,
        function(oData) {
        	if(oLoading)
            	oLoading.ch_loading(false);

        	$('#' + $this._aHtmlIds['by_popup'] + iId).remove();

        	$(oData.content).hide().prependTo('body').dolPopup({
                fog: {
    				color: '#fff',
    				opacity: .7
                }
            });
        },
        'json'
    );

	return false;
};

ChWallRepost.prototype._getDefaultParams = function () {
	var oDate = new Date();
    return $.extend({}, this._oRequestParams, {_t:oDate.getTime()});
};
