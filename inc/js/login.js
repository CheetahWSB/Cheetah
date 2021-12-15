/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function validateLoginForm(eForm) {
	if (!eForm)
		return false;

	var formError = false;

	$('#login_box_form .ch-form-error').hide();

	if (eForm.ID.value == '') {
		chShowError(eForm, 'ID', 'You must enter Username');
		formError = true;
	}

	if (eForm.Password.value == '') {
		chShowError(eForm, 'Password', 'You must enter Password');
		formError = true;
	}

	if (formError) return false;

	$(eForm).ajaxSubmit({
		success: function(sResponce) {
			if (sResponce == 'OK') {
				eForm.submit();
			} else {
				if (sResponce == 'Invalid Username') {
					chShowError(eForm, 'ID', 'Username not found');
				}
				if (sResponce == 'Invalid Password') {
					chShowError(eForm, 'Password', 'Invalid password');
				}
        if (sResponce == 'NoLoginById') {
					chShowError(eForm, 'ID', 'Logins by member ID are not allowed');
				}
        if (sResponce == 'NoLoginByNick') {
					chShowError(eForm, 'ID', 'Logins by member NickName are not allowed');
				}
        if (sResponce == 'NoLoginByEmail') {
					chShowError(eForm, 'ID', 'Logins by email address are not allowed');
				}
				if (sResponce == 'Unknown Error') {
					alert('A unknown error occured when submitting the login form. Please try again.');
				}
			}
		}
	});
}

function chShowError(eForm, sField, sError) {
	var $Field = $("[name='" + sField + "']", eForm);
	$Field.parents('.ch-form-element:first').find('.ch-form-error').show();
	$Field.parents('.ch-form-element:first').find('.ch-form-error-div').html('<i class="sys-icon exclamation-circle ch-form-error-icon"></i>');
	$Field.parents('.ch-form-element:first').find('.ch-form-error-div i').after(sError);
}
