/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWallPost(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallPost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    this._iGlobAllowHtml = 0;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;
}

ChWallPost.prototype.changePostType = function(oElement) {
    var $this = this;
    var sId = $(oElement).attr('id');
    var sType = sId.substr(sId.lastIndexOf('-') + 1, sId.length);

    var sSubType = '';
    if($(oElement).is('select'))
    	sSubType = $(oElement).val();

    this.loading();

    //--- Change Control ---//
    if($(oElement).is('a'))
    	$(oElement).parent().siblings('.active:visible').hide().siblings('.notActive:hidden').show().siblings('#' + sId + '-pas:visible').hide().siblings('#' + sId + '-act:hidden').show();

    //--- Change Content ---//
    var oContents = $(oElement).parents('.disignBoxFirst').find('.wall-ptype-cnt');
    if((sType == 'photo' || sType == 'sound' || sType == 'video') && sSubType != '' ) {
        jQuery.post (
            $this._sActionsUrl + 'get_uploader/' + this._iOwnerId + '/' + sType + (sSubType && sSubType.length >0 ? '/' + sSubType : ''),
            {},
            function(sResult) {
            	if($.trim(sResult).length) {
            		var oContent = oContents.filter('.wall_' + sType);
            		if(oContent.is(':visible')) {
	            		oContent.bxwallanim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
	            			$(this).html(sResult).addWebForms().bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
	            				$this.loading(false);
	            			});
	            		});

	            		return;
            		}

            		oContent.html(sResult).addWebForms();
            		$this._animContent(oElement, sType);
            	}
            }
        );
    }
    else
        this._animContent(oElement, sType);
};


ChWallPost.prototype.postSubmit = function(oForm) {
	this.loading();

    return true;
};

ChWallPost.prototype.loading = function(bShow) {
	$('#' + this._aHtmlIds['loading']).ch_loading(bShow);
};

ChWallPost.prototype._getPost = function(oElement, iPostId) {
    var $this = this;
    var oData = this._getDefaultData();
    oData['WallPostId'] = iPostId;

    // Hide Loading in Post block.
    this.loading(false);

    // Show Loading in View block.
    var oLoading = $('#ch-wall-view-loading');
    if(oLoading)
    	oLoading.ch_loading();

    jQuery.post (
        this._sActionsUrl + 'get_post/',
        oData,
        function(sResult) {
        	if(oLoading)
            	oLoading.ch_loading();

        	if($.trim(sResult).length) {
        		if(!$('.wall-view .wall-events div.wall-divider-today').is(':visible'))
                    $('.wall-view .wall-events div.wall-divider-today').show();

        		if(!$('.wall-view .wall-events div.wall-load-more').is(':visible'))
                    $('.wall-view .wall-events div.wall-load-more').show();

        		if($('.wall-view .wall-events .wall-empty').is(':visible'))
                    $('.wall-view .wall-events .wall-empty').hide();

                $('.wall-view .wall-events div.wall-divider-today').after($(sResult)).next('.wall-event:hidden').bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                	$(this).find('a.ch-link').dolEmbedly();
                });
        	}
        }
    );
};

ChWallPost.prototype._getDefaultData = function () {
    return {WallOwnerId: this._iOwnerId};
};

ChWallPost.prototype._animContent = function(oElement, sType) {
    var $this = this;
    this.loading();

    $(oElement).parents('.disignBoxFirst').find('.wall-ptype-cnt:visible').bxwallanim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
        $(this).siblings('.wall-ptype-cnt').filter('.wall_' + sType).bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
        	$this.loading(false);
        });
    });
};

ChWallPost.prototype._err = function (oElement, bShow, sMessage) {
	if (bShow && !$(oElement).next('.wall-post-err').length)
        $(oElement).after(' <b class="wall-post-err">' + sMessage + '</b>');
    else if (!bShow && $(oElement).next('.wall-post-err').length)
        $(oElement).next('.wall-post-err').remove();
};
