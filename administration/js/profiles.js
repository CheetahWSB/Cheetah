/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChManageProfiles(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oMP' : oOptions.sObjName;
    this._sViewType = oOptions.sViewType == undefined ? 'geeky' : oOptions.sViewType;
    this._sCtlType = oOptions.sCtlType == undefined ? 'qlinks' : oOptions.sCtlType;
    this._oCtlValue = {};
    this._iStart = oOptions.iStart == undefined ? 0 : parseInt(oOptions.iStart);
    this._iPerPage = oOptions.iPerPage == undefined ? 30 : parseInt(oOptions.iPerPage);
    this._sOrderBy = oOptions.sOrderBy == undefined ? '' : oOptions.sOrderBy;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

/*--- Controls Functions ---*/
ChManageProfiles.prototype.changeFilterQlinks = function (sBy, sValue) {
    this._oCtlValue['by'] = sBy;
    this._oCtlValue['value'] = sValue;

    this.getMembers(function() {
        $('#adm-mp-members-form > .adm-mp-members-wrapper:hidden').html('');
    });
};
ChManageProfiles.prototype.changeFilterTags = function(sTag) {
    this._oCtlValue['value'] = sTag;

    this.getMembers(function() {
        $('#adm-mp-members-form > .adm-mp-members-wrapper:hidden').html('');
    });
};
ChManageProfiles.prototype.changeFilterSearch = function () {
    var sValue = $("[name='adm-mp-filter']").val();
    if(sValue.length <= 0)
        return;

    this._oCtlValue['value'] = sValue;

    this.getMembers(function() {
        $('#adm-mp-members-form > .adm-mp-members-wrapper:hidden').html('');
    });
};
ChManageProfiles.prototype.changeTypeControl = function(oLink) {
    var $this = this;
    var sType = $(oLink).attr('id').replace('ctl-type-', '');
    this._sCtlType = sType;
    $("[name = 'adm-mp-members-ctl-type']").val(sType);

    $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();

    if($('#adm-mp-control > :visible').length > 0)
        $('#adm-mp-control > :visible').ch_anim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
            $('#adm-mp-control > #adm-mp-ctl-' + sType).ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
        });
    else
        $('#adm-mp-control > #adm-mp-ctl-' + sType).ch_anim('show', this._sAnimationEffect, this._iAnimationSpeed);
};
ChManageProfiles.prototype.reloadTypeControl = function() {
	var $this = this;

	var oOptions = {
	        action: 'get_controls',
	        ctl_type: this._sCtlType
	    };

	$('#adm-mp-controls-loading').ch_loading();

    $.post(
        this._sActionsUrl,
        oOptions,
        function(oResult) {
            $('#adm-mp-controls-loading').ch_loading();

            $('#adm-mp-ctl-' + $this._sCtlType).ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
            	$(this).replaceWith(oResult.content);
            });
        },
        'json'
    );
};

/*--- View Functions ---*/
ChManageProfiles.prototype.changeTypeView = function(oLink) {
    var $this = this;
    var sType = $(oLink).attr('id').replace('view-type-', '');
    this._sViewType = sType;
    $("[name = 'adm-mp-members-view-type']").val(sType);

    $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();

    var oView = $("#adm-mp-members-form > .adm-mp-members-wrapper:visible");
    oView.find("input[name='members[]'], .admin-actions-select-all").attr('checked', false);

    if($('#adm-mp-members-' + sType).children().length)
    	oView.ch_anim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
            $('#adm-mp-members-' + sType).ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
        });
    else
        this.getMembers();
};
ChManageProfiles.prototype.reload = function() {
    this.getMembers();
    this.reloadTypeControl();
};

ChManageProfiles.prototype.reloadSelected = function() {
    this._sOrderBy = $('select[name ="order_by"]').val();
    this.getMembers();
    this.reloadTypeControl();
};


/*--- Paginate Functions ---*/
ChManageProfiles.prototype.changePage = function(iStart) {
    this._iStart = iStart;
    this.getMembers();
};
ChManageProfiles.prototype.changeOrder = function(oSelect) {
    this._sOrderBy = oSelect.value;
    this.getMembers();
};
ChManageProfiles.prototype.changePerPage = function(oSelect) {
    this._iPerPage = parseInt(oSelect.value);
    this.getMembers();
};

ChManageProfiles.prototype.getMembers = function(onSuccess) {
    var $this = this;

    if(onSuccess == undefined)
        onSuccess = function(){};

    $('#adm-mp-members-loading').ch_loading();

    var oOptions = {
        action: 'get_members',
        view_type: this._sViewType,
        view_start: this._iStart,
        view_per_page: this._iPerPage,
        view_order: this._sOrderBy,
        ctl_type: this._sCtlType
    };

    oOptions['ctl_value[]'] = new Array();
    $.each(this._oCtlValue, function(sKey, sValue) {
        oOptions['ctl_value[]'].push(sKey + '=' + sValue);
    });

    $.post(
        this._sActionsUrl,
        oOptions,
        function(oResult) {
            $('#adm-mp-members-loading').ch_loading();

            $('#adm-mp-members-form > .adm-mp-members-wrapper:visible').ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $('#adm-mp-members-' + $this._sViewType).html(oResult.content).ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });

            onSuccess();
        },
        'json'
    );
};

ChManageProfiles.prototype.actionBan = function(oButton) {
	$(document).dolPopupPrompt({
		message: _t('_adm_btn_mp_ban_duration'),
		onClickOk: function(oPopup) {
			var iDuration = parseInt(oPopup.getValue());
			if(isNaN(iDuration) || iDuration <= 0)
				return;

			$('input[name = "adm-mp-members-ban-duration"]').val(iDuration);

			$(oButton).off('onclick').removeAttr('onclick').trigger('click');
		}
	});

	return false;
};
