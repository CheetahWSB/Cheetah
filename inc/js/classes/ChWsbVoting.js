/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWsbVoting (oOptions)
{
	this._sSystem = oOptions.sSystem;
	this._iObjId = oOptions.iObjId;
	this._sUrl = oOptions.sBaseUrl + 'vote.php';

	this._iSize = oOptions.iSize;
	this._iMax = oOptions.iMax;

	this._sHtmlId = oOptions.sHtmlId;

	this._iSaveWidth = -1;
}

ChWsbVoting.prototype.over = function (i)
{
	var oSlider = $('#' + this._sHtmlId + ' .votes_slider');
	this._iSaveWidth = parseInt(oSlider.width());
	oSlider.width(i * this._iSize);
};

ChWsbVoting.prototype.out = function ()
{
	var oSlider = $('#' + this._sHtmlId + ' .votes_slider');
	oSlider.width(parseInt(this._iSaveWidth));
};

ChWsbVoting.prototype.setRate = function (fRate)
{
	var oSlider = $('#' + this._sHtmlId + ' .votes_slider');
	oSlider.width(fRate * this._iSize);
};

ChWsbVoting.prototype.setCount = function (iCount)
{
	$('#' + this._sHtmlId + ' .votes_count i').html(iCount);
};

ChWsbVoting.prototype.vote = function (i)
{
	var $this = this;
	var oData = this._getDefaultActions();
	oData['vote_send_result'] = i;

	jQuery.post(this._sUrl, oData, function(oData) {
		if(!oData || oData.rate == undefined || oData.count == undefined) {
			$this.onvotefail();
			return;
		}

		$this._iSaveWidth = i * $this._iSize;

		$this.setRate(oData.rate);
		$this.setCount(oData.count);

        $this.onvote(oData.rate, oData.count);
	}, 'json');
};

ChWsbVoting.prototype.onvote = function(fRate, iCount) {};
ChWsbVoting.prototype.onvotefail = function() {};

ChWsbVoting.prototype._getDefaultActions = function() {
    return {
    	'sys': this._sSystem,
    	'id': this._iObjId
    };
};
