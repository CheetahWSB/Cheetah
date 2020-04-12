/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function validateLoginForm(eForm) {
	if (! eForm)
		return false;

    $(eForm).ajaxSubmit({
        success: function(sResponce) {
            if(sResponce == 'OK')
                eForm.submit();
            else
                alert(_t('_PROFILE_ERR'));
        }
    });
}
