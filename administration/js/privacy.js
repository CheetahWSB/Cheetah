/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function onChangeType(oLink) {
    var $this = this;

    var sType = $(oLink).attr('id').replace('adm-pvc-btn-', '');
    var sName = '#adm-pvc-cnt-' + sType;

    $(oLink).parent('.notActive').hide().siblings('.notActive:hidden').show().siblings('.active').hide().siblings('#' + $(oLink).attr('id') + '-act').show();
    $(sName).siblings('div:visible').ch_anim('hide', 'fade', 'slow', function(){
        $(sName).ch_anim('show', 'fade', 'slow');
    });
}
