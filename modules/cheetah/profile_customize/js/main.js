/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChProfileCustimizer(oOptions)
{
    this._sCustomBlock = 'profile_customize';
    this._sProfileBlock = 'divUnderCustomization';
    this._sPublishBlock = 'dynamicPopup';

    this.sReset = oOptions.sReset == undefined ? 'Reset?' : oOptions.sReset;
    this.sErrThemeName = oOptions.sErrThemeName == undefined ? 'Please fill name of theme' : oOptions.sErrThemeName;
    this.sErrChooseTheme = oOptions.sErrChooseTheme == undefined ? 'Choose any theme' : oOptions.sErrChooseTheme;
    this.sDeleteTheme = oOptions.sDeleteTheme == undefined ? 'Delete theme?' : oOptions.sDeleteTheme;;
    this.sResetPage = oOptions.sResetPage == undefined ? 'Reset page?' : oOptions.sResetPage;
}

ChProfileCustimizer.prototype.updateBlock = function(sName, sUrl)
{
    var oBlock = $('#' + sName);

    if ($(oBlock).length > 0)
        getHtmlData(oBlock, sUrl, null);
};

ChProfileCustimizer.prototype.saveChanges = function(oCallback)
{
    var oForm = $('#' + this._sCustomBlock + ' form');

    if ($(oForm).length > 0)
    {
        var options = {
            success: function(data) {
                oCallback(data);
            }
        };

        $(oForm).ajaxSubmit(options);
    }
};

ChProfileCustimizer.prototype.reloadCustomizeBlock = function(sUrl, isReset)
{
	var oForm = $('#' + this._sCustomBlock + ' form');
	if($(oForm).length <= 0) {
		getHtmlData($('#' + this._sCustomBlock), sUrl, null);
		return;
	}

	var $this = this;
	var fPerform = function() {
        $(oForm).ajaxSubmit({
            success: function(data) {
                getHtmlData($('#' + $this._sCustomBlock), sUrl, null);
            }
        });
	};

	if(!isReset) {
		fPerform();
		return;
	}

	$(document).dolPopupConfirm({
		message: this.sReset,
		onClickYes: function() {
			var sNewAction = $(oForm).attr('action') + '/1';
    	    $(oForm).attr('action', sNewAction);

    	    fPerform();
		}
	});
};

ChProfileCustimizer.prototype.reloadProfileBlock = function(sUrl)
{
	var oForm = $('#' + this._sCustomBlock + ' form');
	var $this = this;

	if ($(oForm).length > 0)
	{

	    var options = {
            success: function(data) {
	           $this.updateBlock($this._sProfileBlock, sUrl);
	        }
	    };

		$(oForm).ajaxSubmit(options);
	}
};

ChProfileCustimizer.prototype.resetCustom = function(sCustomUrl, sProfileUrl)
{
	var $this = this;
	$(document).dolPopupConfirm({
		message: this.sReset,
		onClickYes: function() {
			var oForm = $('#' + $this._sCustomBlock + ' form');
	        if ($(oForm).length > 0) {
	            var sNewAction = $(oForm).attr('action') + '/1';
	            $(oForm).attr('action', sNewAction);

	            $(oForm).ajaxSubmit({
	                success: function(data) {
	                    $this.updateBlock($this._sCustomBlock, sCustomUrl);
	                    $this.updateBlock($this._sProfileBlock, sProfileUrl);
	                }
	            });
	        }
		}
	});
};

ChProfileCustimizer.prototype.reloadCustom = function(sCustomUrl, sProfileUrl)
{
    var $this = this;

    this.saveChanges(function(data) {
        $this.updateBlock($this._sCustomBlock, sCustomUrl);
        $this.updateBlock($this._sProfileBlock, sProfileUrl);
    });
};

ChProfileCustimizer.prototype.showPublish = function(sUrl)
{
    var oForm = $('#' + this._sCustomBlock + ' form');
    if(!$(oForm).length)
    	return;

	var $this = this;
    $(oForm).ajaxSubmit({
        success: function(data) {
            if (!$('#' + $this._sPublishBlock).length)
                $('<div id="' + $this._sPublishBlock + '" style="width:490px; display:none;"></div>').prependTo('body');

            $('#' + $this._sPublishBlock).load(sUrl, function() {
            	$(this).dolPopup();
            });
        }
    });
};

ChProfileCustimizer.prototype.savePublish = function()
{
    var oForm = $('#' + this._sPublishBlock + ' form');
    if(!$(oForm).length)
    	return;

    if (oForm.find('.form_input_text').val()) {
        var $this = this;
        var options = {
            success: function(data) {
                var oPublishBlock = $('#' + $this._sPublishBlock);
                if ($(oPublishBlock).length > 0)
                    $(oPublishBlock).html(data);
            }
        };

        $(oForm).ajaxSubmit(options);
    }
    else
        alert(this.sErrThemeName);
};

ChProfileCustimizer.prototype.selectTheme = function(oElement, iThemeId)
{
    var oRadio = $(oElement).find('input[type=radio]');

    if (oRadio.length > 0)
        oRadio.attr('checked', 1);
};

ChProfileCustimizer.prototype.previewTheme = function()
{
    var iSelectTheme = this.getSelectTheme();

    if (iSelectTheme != -1)
        this.updateBlock(this._sProfileBlock, $('#preview_url').val() + iSelectTheme);
    else
        alert(this.sErrChooseTheme);
};

ChProfileCustimizer.prototype.saveTheme = function()
{
    var iSelectTheme = this.getSelectTheme();

    if (iSelectTheme != -1)
        this.updateBlock(this._sProfileBlock, $('#save_url').val() + iSelectTheme);
    else
        alert(this.sErrChooseTheme);
};

ChProfileCustimizer.prototype.deleteTheme = function(sUrl)
{
    var iSelectTheme = this.getSelectTheme();
    if(iSelectTheme == -1) {
    	alert(this.sErrChooseTheme);
    	return;
    }

    var $this = this;
    $(document).dolPopupConfirm({
		message: this.sDeleteTheme,
		onClickYes: function() {
			getHtmlData($('#' + $this._sCustomBlock), sUrl + iSelectTheme);
		}
	});
};

ChProfileCustimizer.prototype.resetAll = function(sUrl)
{
	var $this = this;
	$(document).dolPopupConfirm({
		message: this.sResetPage,
		onClickYes: function() {
			$this.updateBlock($this._sProfileBlock, sUrl);
		}
	});
};

ChProfileCustimizer.prototype.getSelectTheme = function()
{
    var oSelectTheme = $('#' + this._sCustomBlock + ' form input[type=radio]:checked');

    if ($(oSelectTheme).length > 0)
        return $(oSelectTheme).val();

    return -1;
};
