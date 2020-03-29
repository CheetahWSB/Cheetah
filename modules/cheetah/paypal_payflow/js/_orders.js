/**
 * This work, "Cheetah - https://cheetah.deanbassett.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChPfwOrders(oOptions) {
	this.init(oOptions);
};

ChPfwOrders.prototype = new ChPmtOrders({});

ChPfwOrders.prototype.unsubscribe = function(sType, iId) {
    var $this = this;

    this._getOrderLoading(iId);

    $.post(
        this._sActionsUrl + 'act_cancel_subscription/',
        {
            type: sType,
            id: iId
        },
        function(oData) {
        	$this._getOrderLoading(iId);

        	if(oData.message)
        		alert(oData.message);

            $('#pmt-orders-more').dolPopupHide({});
        },
        'json'
    );
};

ChPfwOrders.prototype._getOrderLoading = function(iId) {
	$('#pfw-order-loading-' + iId).ch_loading();
};
