/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWsbSubscription(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'ChWsbSubscription' : oOptions.sObjName;
    this._sVisitorPopup = oOptions.sVisitorPopup == undefined ? 'sbs_visitor_popup' : oOptions.sVisitorPopup;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

ChWsbSubscription.prototype.send = function(oForm) {
    var $this = this;

    $(oForm).ajaxSubmit({
        dataType: 'json',
		success: function(oData) {
			alert( oData.message );

			if(parseInt(oData.code) == 0)
                $('#' + $this._sVisitorPopup).dolPopupHide();
		}
	});
	return false;
};

ChWsbSubscription.prototype.subscribe = function(iUserId, sUnit, sAction, iObjectId, onResult) {
    var oParams = {
        direction: 'subscribe',
        unit: sUnit,
        action: sAction,
        object_id: iObjectId
    };

    iUserId = parseInt(iUserId);
    if(iUserId != 0) {
        oParams['user_id'] = iUserId;
        this._sbs_action(oParams, onResult);
    }
    else {
        $("#" + this._sVisitorPopup + " [name='direction']").val('subscribe');
        $("#" + this._sVisitorPopup + " [name='unit']").val(sUnit);
        $("#" + this._sVisitorPopup + " [name='action']").val(sAction);
        $("#" + this._sVisitorPopup + " [name='object_id']").val(iObjectId);
        $("#" + this._sVisitorPopup).dolPopup();
    }
};

ChWsbSubscription.prototype.unsubscribe = function(iUserId, sUnit, sAction, iObjectId, onResult) {
    var oParams = {
        direction: 'unsubscribe',
        unit: sUnit,
        action: sAction,
        object_id: iObjectId
    };

    iUserId = parseInt(iUserId);
    if(iUserId != 0) {
        oParams['user_id'] = iUserId;
        this._sbs_action(oParams, onResult);
    }
};

ChWsbSubscription.prototype.unsubscribeConfirm = function(sUrl) {
	$(document).dolPopupConfirm({
		message: _t('_sbs_wrn_unsubscribe'),
		onClickYes: function() {
			$.get(
				sUrl + '&js=1',
				{},
				function(oData){
					alert(oData.message);

					if(oData.code == 0)
						window.location.href = window.location.href;
				},
				'json'
			);
		}
	});
};

ChWsbSubscription.prototype._sbs_action = function(oParams, onResult) {
    if(onResult == undefined)
        onResult = function(oData) {
            alert(oData.message);
        }

    $.post(
        this._sActionsUrl,
        oParams,
        onResult,
        'json'
    );
};
