/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChMbpJoin(oOptions) {
    this._sSystem = oOptions.sSystem;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oMbpJoin' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._sTxtPay = oOptions.sTxtPay == undefined ? 'Pay' : oOptions.sTxtPay;
    this._sTxtSelect = oOptions.sTxtSelect == undefined ? 'Select' : oOptions.sTxtSelect;
    this._sErrSelectLevel = oOptions.sErrSelectLevel == undefined ? _t('_Error occured') : oOptions.sErrSelectLevel;
    this._sErrSelectProvider = oOptions.sErrSelectProvider == undefined ? _t('_Error occured') : oOptions.sErrSelectProvider;
}

ChMbpJoin.prototype.onSelect = function(oElement) {
	var bPaid = parseInt($(oElement).attr('ch-data-price')) > 0;
	var oBlock = $(oElement).parents('.mbp-select-level:first');

	oBlock.find('.mbp-select-provider').ch_anim(bPaid ? 'show' : 'hide', this._sAnimationEffect, this._iAnimationSpeed);
	oBlock.find('input[name="mbp-checkout"]').val(bPaid ? this._sTxtPay : this._sTxtSelect);
};

ChMbpJoin.prototype.onSubmit = function(oForm) {
	var oForm = $(oForm);
	var oDescriptor = oForm.find(":radio[name = 'descriptor']:checked");

	if(!oDescriptor.length) {
		alert(this._sErrSelectLevel);
		return false;
	}

	if(parseInt(oDescriptor.attr('ch-data-price')) > 0 && !oForm.find(":radio[name = 'provider']:checked").length && !oForm.find(":hidden[name = 'provider']").val()) {
		alert(this._sErrSelectProvider);
		return false;
	}

	return true;
};
