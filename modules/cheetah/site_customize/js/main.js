/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChSctrMain(oOptions)
{
    this._sCustomBlock = 'site_customize';
    this._sPublishBlock = 'site_customize_popup';

    this.sBaseUrl = oOptions.sBaseUrl;
    this.sReset = oOptions.sReset == undefined ? 'Reset?' : oOptions.sReset;
    this.sErrThemeName = oOptions.sErrThemeName == undefined ? 'Please fill name of theme' : oOptions.sErrThemeName;
    this.sErrChooseTheme = oOptions.sErrChooseTheme == undefined ? 'Choose any theme' : oOptions.sErrChooseTheme;
    this.sDeleteTheme = oOptions.sDeleteTheme == undefined ? 'Delete theme?' : oOptions.sDeleteTheme;
    this.sResetPage = oOptions.sResetPage == undefined ? 'Reset page?' : oOptions.sResetPage;
}

ChSctrMain.prototype.showBlock = function() {
	var $this = this;
	var oDate = new Date();
	var iOpen = $('#' + this._sCustomBlock).is(':visible') ? 0 : 1;

	$.get(
		this.sBaseUrl + 'open/' + iOpen,
		{
			_t:oDate.getTime()
		},
		function() {
			$('#' + $this._sCustomBlock).ch_anim('toggle', 'fade', 'slow', function() {
			});
		},
		'json'
	);
};

ChSctrMain.prototype.updateBlock = function(sName, sUrl)
{
    var oBlock = $('#' + sName);

    if ($(oBlock).length > 0)
        getHtmlData(oBlock, sUrl, null);
};

ChSctrMain.prototype.saveChanges = function(oCallback)
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

ChSctrMain.prototype.reloadCustomizeBlock = function(sUrl, isReset)
{
	var oForm = $('#' + this._sCustomBlock + ' form');
	if ($(oForm).length <= 0) {
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

ChSctrMain.prototype.resetCustom = function(sPage, sTarget, sAction)
{
	var $this = this;
	$(document).dolPopupConfirm({
		message: this.sReset,
		onClickYes: function() {
			var oForm = $('#' + $this._sCustomBlock + ' form');
	        if ($(oForm).length > 0) {
	            var sNewAction = $(oForm).attr('action') + '/1';
	            $(oForm).attr('action', sNewAction);

	            $(oForm).find("[name='action']").val(sAction);
	            $(oForm).ajaxSubmit({
	                success: function(data) {
	                	window.location.href = window.location.href;
	                }
	            });
	        }
		}
	});
};

ChSctrMain.prototype.reloadCustom = function(sPage, sTarget, sAction)
{
	$("#" + this._sCustomBlock + " form [name='action']").val(sAction);

    this.saveChanges(function(data) {
    	window.location.href = window.location.href;
    });
};

ChSctrMain.prototype.showPublish = function(sUrl)
{
    var oForm = $('#' + this._sCustomBlock + ' form');
    if(!$(oForm).length)
    	return;

	var $this = this;
	$(oForm).ajaxSubmit({
		success: function(data) {
			if(!$('#' + $this._sPublishBlock).length)
				$('<div id="' + $this._sPublishBlock + '" style="width:490px; display:none;"></div>').prependTo('body');

			$('#' + $this._sPublishBlock).load(sUrl, function() {
				$(this).dolPopup();
			});
		}
	});
};

ChSctrMain.prototype.savePublish = function()
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

ChSctrMain.prototype.selectTheme = function(oElement, iThemeId)
{
    var oRadio = $(oElement).find('input[type=radio]');

    if (oRadio.length > 0)
        oRadio.attr('checked', 1);
};

ChSctrMain.prototype.previewTheme = function()
{
    var iSelectTheme = this.getSelectTheme();

    if (iSelectTheme != -1) {
    	var oForm = $('#' + this._sCustomBlock + ' form');
    	var oOptions = {
            success: function(data) {
            	window.location.href = window.location.href;
            }
        };

    	$(oForm).ajaxSubmit(oOptions);
    }
    else
        alert(this.sErrChooseTheme);
};

ChSctrMain.prototype.saveTheme = function()
{
    var iSelectTheme = this.getSelectTheme();
    if(iSelectTheme != -1)
    	getHtmlData($('#' + this._sCustomBlock), $('#save_url').val() + iSelectTheme, function() {
    		window.location.href = window.location.href;
    	});
    else
        alert(this.sErrChooseTheme);
};

ChSctrMain.prototype.deleteTheme = function(sUrl)
{
    var iSelectTheme = this.getSelectTheme();

    if (iSelectTheme == -1) {
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

ChSctrMain.prototype.resetAll = function(sUrl)
{
	var $this = this;
	$(document).dolPopupConfirm({
		message: this.sResetPage,
		onClickYes: function() {
			getHtmlData($('#' + $this._sCustomBlock), sUrl, function() {
	    		window.location.href = window.location.href;
	    	});
		}
	});
};

ChSctrMain.prototype.getSelectTheme = function()
{
    var oSelectTheme = $('#' + this._sCustomBlock + ' form input[type=radio]:checked');

    if ($(oSelectTheme).length > 0)
        return $(oSelectTheme).val();

    return -1;
};
