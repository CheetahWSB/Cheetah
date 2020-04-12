/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChFeedbackMain(oOptions) {
    this._sSystem = oOptions.sSystem;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oFeedbackMain' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}
ChFeedbackMain.prototype.changePage = function(iStart, iPerPage, sType, sTypeParams) {
	var $this = this;
    var oDate = new Date();
    var oParams = {
    	_t:oDate.getTime()
    }

    if(sTypeParams)
    	oParams['params'] = sTypeParams;

    if($('#feedback-filter-chb:checked').length > 0 && $('#feedback-filter-txt').val().length > 0)
    	oParams['filter_value'] = $('#feedback-filter-txt').val();

    var sLoadingId = '#feedback-' + sType + '-loading';
    $(sLoadingId).ch_loading();

    $.post(
        this._sActionsUrl + 'act_get_feedback/' + (sType ? sType + '/' : '') + iStart + '/' + iPerPage + '/',
        oParams,
        function(sData) {
        	$(sLoadingId).ch_loading();

            $('.feedback-view #feedback-content-' + sType).ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $(this).replaceWith(sData);
            });
        },
        'html'
    );
}
ChFeedbackMain.prototype.deleteEntry = function(iId) {
	var $this = this;

	$.post(
		this._sActionsUrl + "act_delete/",
		{id:iId},
		function(sData) {
			var iCode = parseInt(sData);
			if(iCode == 1) {
				alert(aWsbLang['_feedback_msg_success_delete']);
				window.location.href = $this._sActionsUrl
			}
			else
				alert(aWsbLang['_feedback_msg_failed_delete']);
		}
	)
}
