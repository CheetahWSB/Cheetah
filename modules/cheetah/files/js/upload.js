/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function shFileEnableSubmit(bMode) {
	var oButton = $('#file_upload_form .form_input_submit');
	if(bMode)
		oButton.removeAttr('disabled');
	else
		oButton.attr('disabled', 'disabled');
}

function ChFileUpload(oOptions) {
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
}

ChFileUpload.prototype.genSendFileInfoForm = function(iMID, sForm) {
	$(sForm).appendTo('#file_accepted_files_block').addWebForms();
	this.changeContinueButtonStatus();
}

ChFileUpload.prototype.getType = function() {
	return $('#file_upload_form').attr("name");
}

ChFileUpload.prototype.changeContinueButtonStatus = function () {
	switch(this.getType()) {
		case 'upload':
			var sFileVal = $('#file_upload_form .file_upload_form_wrapper .form_input_file').val();
			var sAcceptedFilesBlockVal = $('#file_accepted_files_block').text();
			shFileEnableSubmit(sFileVal != null && sFileVal != '' && sAcceptedFilesBlockVal == '');
			break;
		default:
			break;
	}
}

ChFileUpload.prototype.doValidateFileInfo = function(oButtonDom, iFileID) {
	var bRes = true;
	if ($('#send_file_info_' + iFileID + ' input[name=title]').val()=='') {
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').show().attr('float_info', _t('_ch_files_val_title_err'));
		bRes = false;
	}
	else
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').hide();

	return bRes; //can submit
}

ChFileUpload.prototype.cancelSendFileInfo = function(iMID, sWorkingFile) {
	if(iMID == "")
		this.cancelSendFileInfoResult("");
    else if(iMID > 0 && sWorkingFile == "")
		this.cancelSendFileInfoResult(iMID);
	else
	{
		var $this = this;
		$.post(ch_append_url_params(sWorkingFile, "action=cancel_file&file_id=" + iMID), function(data){
			if (data==1)
				$this.cancelSendFileInfoResult(iMID);
		});
	}
}

ChFileUpload.prototype.cancelSendFileInfoResult = function(iMID) {
	$('#send_file_info_'+iMID).remove();
	this.changeContinueButtonStatus();

    $('#file_accepted_files_block script').remove();
    if ($('#file_accepted_files_block').text() == '')
        window.location.href = window.location.href;
}

ChFileUpload.prototype.onSuccessSendingFileInfo = function(iMID) {
	$('#send_file_info_'+iMID).remove();

	setTimeout( function(){
		$('#file_success_message').show(1000)
		setTimeout( function(){
			$('#file_success_message').hide(1000);
		}, 3000);
	}, 500);

	this.changeContinueButtonStatus();

    $('#file_accepted_files_block script').remove();
    if ($('#file_accepted_files_block').text() == '')
        window.location.href = window.location.href;

	switch(this.getType()) {
		case 'upload':
			this.resetUpload();
			break
	}
}

ChFileUpload.prototype.showErrorMsg = function(sErrorCode) {
	var oErrorDiv = $('#' + sErrorCode);

	var $this = this;

	setTimeout( function(){
		oErrorDiv.show(1000)
		setTimeout( function(){
			oErrorDiv.hide(1000);
			$this._loading(false);
		}, 3000);
	}, 500);

}

ChFileUpload.prototype.onFileChangedEvent = function (oElement) {
	this.changeContinueButtonStatus();
};

ChFileUpload.prototype._loading = function (bShow) {
    $('.upload-loading-container').ch_loading(bShow);
}

ChFileUpload.prototype.resetUpload = function () {
	var oCheck = $('#file_upload_form [type="checkbox"]');
	oCheck.removeAttr("checked");

	var oFiles = $('#file_upload_form .input_wrapper_file');
	var oFileIcons = $('#file_upload_form .multiply_remove_button');
	if (oFiles.length>1) {
		oFiles.each( function(iInd) {
			if (iInd != 0) {
				$(this).remove();
			}
		});
		oFileIcons.each( function(iIndI) {
			$(this).remove();
		});
	}

	var oFile = $('#file_upload_form [type="file"]');
	oFile.val("");

	shFileEnableSubmit(false);
}
