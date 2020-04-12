/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function onCreate() {
    $('#adm-langs-add-key').dolPopup();
}
function onResult(sType, oResult) {
    var sContentKey = '#adm-langs-' + sType + '-key-content';

    if(parseInt(oResult.code) == 0) {
        parent.document.forms['adm-langs-' + sType + '-key-form'].reset();

        $(sContentKey + ' > form').ch_anim('hide', 'fade', 'slow', function() {
            $(sContentKey).prepend(oResult.message);
            setTimeout("$('" + sContentKey + " > :first').ch_anim('hide', 'fade', 'slow', function(){$(this).remove();$('" + sContentKey + " > form').ch_anim('show', 'fade', 'slow', function(){$('#adm-langs-" + sType + "-key').dolPopupHide({});});})", 3000);
        });
    }
    else {
        $(sContentKey + ' > form').ch_anim('hide', 'fade', 'slow', function() {
            $(sContentKey).prepend(oResult.message);
            setTimeout("$('" + sContentKey + " > :first').ch_anim('hide', 'fade', 'slow', function(){$(this).remove();$('" + sContentKey + " > form').ch_anim('show', 'fade', 'slow');})", 3000);
        });
    }
}
function onEditKey(iId) {
    if ($('#adm-langs-edit-key').size())
        $('#adm-langs-edit-key').remove();
    $.post(
        sAdminUrl + 'lang_file.php',
        {action: 'get_edit_form_key', id: iId},
        function(oResult) {
            $('#adm-langs-holder').html(oResult.code).show();
            $('#adm-langs-edit-key').dolPopup();
        },
        'json'
    );
}
function onChangeType(oLink) {
    var $this = this;
    var sType = $(oLink).attr('id').replace('adm-langs-btn-', '');
    var sName = '#adm-langs-cnt-' + sType;

    $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();
    $(sName).siblings('div:visible').ch_anim('hide', 'fade', 'slow', function(){
        $(sName).ch_anim('show', 'fade', 'slow');
    });
}
function onEditLanguage(iId) {
    if ($('#adm-langs-wnd-edit').size()) {
        $('#adm-langs-wnd-edit').dolPopup();
        return;
    }
    $.post(
        sAdminUrl + 'lang_file.php',
        {action: 'get_edit_form_language', id: iId},
        function(oResult) {
            $('#adm-langs-holder').html(oResult.code);
            $('#adm-langs-wnd-edit').dolPopup();
        },
        'json'
    );
}
