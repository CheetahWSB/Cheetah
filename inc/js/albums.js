/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function check_album_name_for_fields(oSelect) {
    var oForm = $(oSelect).parents('form:first');
    var oTitle = oForm.find('.ch-form-element:has(input[name=title])');
    var oPrivacy = oForm.find('.ch-form-element:has(select[name=AllowAlbumView])');

    if ($(oSelect).val() != 0) {
        oTitle.hide();
        oPrivacy.hide();
    } else {
        oTitle.show();
        oPrivacy.show();
    }
}

function redirect_with_closing(sUrl, iTime) {
    window.setTimeout(function () {
        window.parent.opener.location = sUrl;
        window.parent.close();
    }, iTime * 1000);
}

function submit_quick_upload_form(sUrl, sFields) {
    const urlParams = new URLSearchParams(sFields);
    if (urlParams.get('album') == 'select') {
        alert('You must select an album');
        return false;
    }
    sUrlReq = sUrl + 'upload_submit/?' + sFields;
    $.getJSON(sUrlReq, function (oJson) {
        if (oJson.status == 'OK')
            window.location.href = sUrl + 'albums/my/add_objects/' + oJson.album_uri + '/owner/' + oJson.owner_name;
        else
            alert(oJson.error_msg);
    });
    return false;
}


window.setTimeout(function () {
    var oForm = $('.form_input_select').parents('form:first');
    var oTitle = oForm.find('.ch-form-element:has(input[name=title])');
    var oPrivacy = oForm.find('.ch-form-element:has(select[name=AllowAlbumView])');
    // Locate and set the value of the last option in the album list to 0.
    $('#albumList option:last-child').val(0);
    // Hide the title and privacy options.
    oTitle.hide();
    oPrivacy.hide();
}, 50);
