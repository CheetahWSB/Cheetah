/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWsbNotifications(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oChWsbNotifications' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._iDisplayCount = oOptions.iDisplayCount == undefined ? 3 : oOptions.iDisplayCount;
    this._iTimeoutLength = oOptions.iTimeoutLength == undefined ? 10000 : oOptions.iTimeoutLength;
    this._iTimeoutId = 0;
}
ChWsbNotifications.prototype.correctLayout = function() {

}
ChWsbNotifications.prototype.toggle = function() {
    if(this._iTimeoutId == 0) {
        var $this = this;
        this._iTimeoutId = setTimeout(this._sObjName + '.update();', this._iTimeoutLength);
    }
    else
        clearTimeout(this._iTimeoutId);
}
ChWsbNotifications.prototype.update = function() {
    var $this = this;
    var oDate = new Date();
    $.post (
        this._sActionsUrl,
        {action: 'update', _t: oDate.getTime()},
        function(sResult) {
            if(!$.trim(sResult)) return;

            var iCountHide = $('#sys-ntns > .sys-ntn:visible').length + $(sResult).length - $this._iDisplayCount;
            var oResponseHide = function() {
                iCountHide--;
                if($(this).next('.sys-ntn:visible') && iCountHide > 0) {
                    $(this).next('.sys-ntn:visible').ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseHide);
                }
                else if(iCountHide <= 0 && $.trim(sResult)) {
                    var iCountShow = $(sResult).length;
                    var oResponseShow = function() {
                        iCountShow--;
                        if($(this).next('.sys-ntn:hidden') && iCountShow > 0)
                            $(this).next('.sys-ntn:hidden').ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseShow);
                    }
                    $(this).nextAll('.sys-ntn:last').after($(sResult).hide()).next('.sys-ntn:hidden').ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseShow);
                }
                $(this).remove();
            }
            if(iCountHide > 0)
                $('#sys-ntns > .sys-ntn:first').ch_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseHide);
            else {
                var iCountShow = $(sResult).length;
                var oResponseShow = function() {
                    iCountShow--;
                    if($(this).next('.sys-ntn:hidden') && iCountShow > 0)
                        $(this).next('.sys-ntn:hidden').ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseShow);
                }
                $('#sys-ntns > .clear_both').before($(sResult).hide()).prevAll('.sys-ntn:hidden:last').ch_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed, oResponseShow);
            }

        }
    );
    this._iTimeoutId = setTimeout(this._sObjName + '.update();', this._iTimeoutLength);
}
