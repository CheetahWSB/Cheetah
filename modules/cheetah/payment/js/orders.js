/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChPmtOrders(oOptions) {
	this.init(oOptions);
}

ChPmtOrders.prototype.init = function(oOptions) {
	if($.isEmptyObject(oOptions))
		return;

	this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oPmtOrders' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._iTimeout = (oOptions.iTimeout == undefined ? 5 : oOptions.iTimeout) * 1000;
};

ChPmtOrders.prototype.showResultInline = function(oData, onHide) {
    var $this = this;

    if(!onHide)
        onHide = function(){}

    if(parseInt(oData.code) == 0) {
        parent.location.href = parent.location.href;
        return;
    }

    $('#' + oData.parent_id).prepend($(oData.message).hide()).children(':first').ch_anim('toggle', this._sAnimationEffect, this._iAnimationSpeed, function(){
        setTimeout($this._sObjName + ".hideResultInline('" + oData.parent_id + "', " + onHide + ")", $this._iTimeout);
    });
};

ChPmtOrders.prototype.hideResultInline = function(sId, onHide) {
    $('#' + sId + ' > :first.MsgBox').ch_anim('toggle', this._sAnimationEffect, this._iAnimationSpeed, function() {
        $(this).remove();
        onHide();
    });
};

/*--- Manual Order function ---*/
ChPmtOrders.prototype.addManually = function(oLink) {
    $('#pmt-manual-order').dolPopup();
};

ChPmtOrders.prototype.selectModule = function(oSelect) {
    var $this = this;
    var oDate = new Date();
    var iModuleId = parseInt(oSelect.value);

    var sLoadingId = '#pmt-order-manual-loading';
    var sParentId = 'pmt-mo-items';

    if(!$(oSelect).parents('.ch-form-element:first').next('.ch-form-element').find(' > .ch-form-value > div').is('#' + sParentId))
        $(oSelect).parents('.ch-form-element:first').after('<div class="ch-form-element ch-def-margin-top clearfix"><div class="ch-form-value clearfix"><div id="' + sParentId + '" class="' + sParentId + '"></div></div></div>');
    else
        $(oSelect).parents('.ch-form-element:first').next('.ch-form-element').show();

    $(sLoadingId).ch_loading();

    $.get(
        this._sActionsUrl + 'act_get_items/' + iModuleId + '/',
        {
            _t:oDate.getTime()
        },
        function(oData) {
        	$(sLoadingId).ch_loading();

            $('#' + sParentId).html('');

            if(parseInt(oData.code) != 0) {
                $this.showResultInline({
                    code:oData.code,
                    message:oData.message,
                    parent_id: sParentId
                });
                return;
            }

            $('#' + sParentId).html(oData.data);
            if(oData.vendor_id != undefined)
            	$("[name = 'vendor']").val(oData.vendor_id);
        },
        'json'
    );
};

/*--- View Orders functions ---*/
ChPmtOrders.prototype.more = function(sType, iId) {
    var $this = this;

    this._getOrdersLoading(sType);

    $.post(
        this._sActionsUrl + 'act_get_order/',
        {
            type: sType,
            id: iId
        },
        function(oData) {
        	$this._getOrdersLoading(sType);

            $('#pmt-om-content').html(parseInt(oData.code) != 0 ? oData.message : oData.data);
            $('#pmt-orders-more').dolPopup();
        },
        'json'
    );
};

ChPmtOrders.prototype.changePage = function(sType, iStart, iPerPage, iSellerId) {
	this._getOrdersLoading(sType);

    var oOptions = {
        type: sType,
        start: iStart,
        per_page: iPerPage,
        seller_id: iSellerId,
        filter: $('#pmt-filter-enable-' + sType).is(':checked') ? $('#pmt-filter-text-' + sType).val() : ''
    };

    this._getOrders(oOptions);
};

ChPmtOrders.prototype.applyFilter = function(sType, oCheckbox) {
	this._getOrdersLoading(sType);

    var sFilter = '';
    if(oCheckbox.checked)
        sFilter = $('#pmt-filter-text-' + sType).val();

    this._getOrders({
        type: sType,
        filter: sFilter
    });
};

ChPmtOrders.prototype._getOrders = function(oParams) {
    var $this = this;
    var oDate = new Date();

    oParams['_t'] = oDate.getTime();

    $.post(
        this._sActionsUrl + 'act_get_orders/',
        oParams,
        function(oData) {
        	$this._getOrdersLoading(oParams.type);

            if(parseInt(oData.code) != 0) {
                $this.showResultInline({code: oData.code, message: oData.message, parent_id: 'pmt-form-' + oParams.type});
                return;
            }

            $('#pmt-orders-' + oParams.type + ' .pmt-orders-content').ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $(this).html();
                $(this).html(oData.data).ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            })
        },
        'json'
    );
};

ChPmtOrders.prototype._getOrdersLoading = function(sType) {
	$('#pmt-orders-' + sType + '-loading').ch_loading();
};
