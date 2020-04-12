/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChManageModules(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oMM' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

ChManageModules.prototype.checkForUpdates = function(oButton) {
	var sUpdateKey = 'mi-update';
    var oParent = $(oButton).parents('.disignBoxFirst');

    oParent.find('.' + sUpdateKey).remove();
    oParent.find("[name = 'pathes[]']").each(function() {
    	var oCheckbox = $(this);
    	if(parseInt(oCheckbox.attr('ch_can_update')) != 1)
    		return;

    	oCheckbox.parent().append('<span class="' + sUpdateKey + ' ch-def-font-grayed">' + aWsbLang['_sys_txt_btn_loading'] + '</span>');
    	$.post(
    		this._sActionsUrl,
    		{
    			action: 'check_for_updates',
    		    path: oCheckbox.val()
    		},
    	    function(oResult) {
    			if(oResult.content && oResult.content.length > 0)
    				oCheckbox.siblings('.' + sUpdateKey).replaceWith(oResult.content);
    		},
    		'json'
    	);
    });
};

ChManageModules.prototype.downloadUpdate = function(sLink) {
	var $this = this;

	$.post(
		this._sActionsUrl,
		{
			action: 'download_updates',
		    link: sLink
		},
	    function(oResult) {
			if(oResult.message && oResult.message.length > 0)
				alert(oResult.message);

			if(oResult.code == 0)
				window.location.href = $this._sActionsUrl;
		},
		'json'
	);
};

ChManageModules.prototype.onSubmitUninstall = function(oButton) {
	$(document).dolPopupConfirm({
		message: _t('_adm_txt_modules_data_will_be_lost'),
		onClickYes: function() {
			$(oButton).removeAttr('onclick').trigger('click');
		}
	});

	return false;
};
