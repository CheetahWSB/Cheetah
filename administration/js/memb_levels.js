/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function onEditAction(iLevelId, iActionId) {
    if ($('#adm-mlevels-action').size())
        $('#adm-mlevels-action').remove();
    $.post(
        sAdminUrl + 'memb_levels.php',
        {action: 'get_edit_form_action', level_id: iLevelId, action_id: iActionId},
        function(oResult) {
            $('#adm-mlevels-holder').html(oResult.code);
            $('#adm-mlevels-action').dolPopup({
                closeOnOuterClick: false
            });

            $(document).addWebForms();

        },
        'json'
    );
}
function onResult(oResult) {
    var sContentKey = '#adm-mlevels-action-content';

    if(parseInt(oResult.code) == 0) {
        $(sContentKey + ' > form').ch_anim('hide', 'fade', 'slow', function() {
            $(sContentKey).prepend(oResult.message);
            setTimeout("$('" + sContentKey + " > :first').ch_anim('hide', 'fade', 'slow', function(){$(this).remove(); $('#adm-mlevels-action').dolPopupHide({});})", 3000);
        });
    }
    else {
        $(sContentKey + ' > form').ch_anim('hide', 'fade', 'slow', function() {
            $(sContentKey).prepend(oResult.message);
            setTimeout("$('" + sContentKey + " > :first').ch_anim('hide', 'fade', 'slow', function(){$(this).remove();$('" + sContentKey + " > form').ch_anim('show', 'fade', 'slow');})", 3000);
        });
    }
}
function onChangeType(oLink) {
    var $this = this;
    var sType = $(oLink).attr('id').replace('adm-mlevels-btn-', '');
    var sName = '#adm-mlevels-cnt-' + sType;

    $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();
    $(sName).siblings('div:visible').ch_anim('hide', 'fade', 'slow', function(){
        $(sName).ch_anim('show', 'fade', 'slow');
    });
}
