/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChPmtCart(oOptions) {
	this.init(oOptions);
}

ChPmtCart.prototype.init = function(oOptions) {
	if($.isEmptyObject(oOptions))
		return;

	this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oPmtCart' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;

    this._sErrNothingSelected = '_payment_err_nothing_selected';
};

ChPmtCart.prototype.addToCart = function(iVendorId, iModuleId, iItemId, iItemCount, iNeedRedirect) {
    var oDate = new Date();
    var $this = this;

    if(!(iNeedRedirect = parseInt(iNeedRedirect)))
    	iNeedRedirect = 0;

    $.post(
        this._sActionsUrl + 'act_add_to_cart/' + iVendorId + '/' + iModuleId + '/' + iItemId + '/' + iItemCount + '/',
        {
            _t:oDate.getTime()
        },
        function(oData){
        	alert(oData.message);

            if(oData.code == 0) {
            	$('#pmt-tbar-total-quantity').html(oData.total_quantity);
                $('#pmt-tbar-content').replaceWith(oData.content);

            	if(iNeedRedirect == 1)
            		window.location.href = $this._sActionsUrl + 'cart';
            }
        },
        'json'
    );
};

/**
 * Isn't used yet.
 */
ChPmtCart.prototype.deleteFromCart = function(iVendorId, iModuleId, iItemId) {
    var oDate = new Date();
    var $this = this;

    $.post(
        this._sActionsUrl + 'act_delete_from_cart/' + iVendorId + '/' + iModuleId + '/' + iItemId,
        {
            _t:oDate.getTime()
        },
        function(oData) {
            alert(oData.message);

            if(oData.code == 0) {
                $('#item-' + iVendorId + '-' + iModuleId + '-' + iItemId).ch_anim(
                    'hide',
                    $this._sAnimationEffect,
                    $this._iAnimationSpeed,
                    function() {
                        $(this).remove();
                    }
                );
            }
        },
        'json'
    );
};

/**
 * Isn't used yet.
 */
ChPmtCart.prototype.emptyCart = function(iVendorId) {
    var oDate = new Date();
    var $this = this;

    $.post(
        this._sActionsUrl + 'act_empty_cart/' + iVendorId,
        {
            _t:oDate.getTime()
        },
        function(oData){
            if(oData.code == 0) {
                //TODO: delete vendor from vendors's list in the cart
            }
            alert(oData.message);
        },
        'json'
    );
};

ChPmtCart.prototype.toggle = function(oDiv) {
    $(oDiv).parent('.pmt-box-cpt').next('.pmt-box-cnt').ch_anim('toggle', 'slide', this._iAnimationSpeed, function() {
        $(oDiv).css('background-position', '0px ' + ($(this).is(':hidden') ? '-16' : '0') + 'px');
    });
};

ChPmtCart.prototype.onSubmit = function(oForm) {
    if($(oForm).find(":checkbox[name='items[]']:checked").length > 0)
    	return true;

    alert(_t(this._sErrNothingSelected));
	return false;
};
