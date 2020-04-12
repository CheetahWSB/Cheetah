/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWallOutline(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallOutline' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : oOptions.iOwnerId;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;

    this.sIdView = '#ch-wall-outline';

    this.sClassMasonry = 'wall-outline-masonry';
	this.sClassItems = 'wall-outline-items';
	this.sClassItem = 'wall-oi-item';
	this.sClassItemImage = 'wall-oii-image';

	var $this = this;
    $(document).ready(function() {
    	$this.initMasonry();
    	$('.' + $this.sClassItem).resize(function() {
    		$this.reloadMasonry();
    	});
    	$('img.' + $this.sClassItemImage).load(function() {
    		$this.reloadMasonry();
    	});
    });
}

ChWallOutline.prototype.isMasonry = function() {
	return $(this.sIdView + ' .' + this.sClassItems).hasClass(this.sClassMasonry);
};

ChWallOutline.prototype.isMasonryEmpty = function() {
	return $(this.sIdView + ' .' + this.sClassItems + ' .' + this.sClassItem).length == 0;
};

ChWallOutline.prototype.initMasonry = function() {
	var oItems = $(this.sIdView + ' .' + this.sClassItems);

	if(oItems.find('.' + this.sClassItem).length > 0) {
		oItems.addClass(this.sClassMasonry).masonry({
		  itemSelector: '.' + this.sClassItem,
		  columnWidth: '.wall-oi-sizer'
		});
	}
};

ChWallOutline.prototype.reloadMasonry = function() {
	$(this.sIdView + ' .' + this.sClassItems).masonry('reloadItems').masonry('layout');
};

ChWallOutline.prototype.destroyMasonry = function() {
	$(this.sIdView + ' .' + this.sClassItems).removeClass(this.sClassMasonry).masonry('destroy');
};

ChWallOutline.prototype.appendMasonry = function(oItems) {
	var $this = this;
	var oItems = $(oItems);
	oItems.find('img.' + this.sClassItemImage).load(function() {
		$this.reloadMasonry();
	});
	$(this.sIdView + ' .' + this.sClassItems).append(oItems).masonry('appended', oItems);
};

ChWallOutline.prototype.prependMasonry = function(oItems) {
	var $this = this;
	var oItems = $(oItems);
	oItems.find('img.' + this.sClassItemImage).load(function() {
		$this.reloadMasonry();
	});
	$(this.sIdView + ' .' + this.sClassItems).prepend(oItems).masonry('prepended', oItems);
};

ChWallOutline.prototype.changePage = function(iStart, iPerPage) {
	this._oRequestParams.WallStart = iStart;
    this._oRequestParams.WallPerPage = iPerPage;

    this.getPosts('page');
};

ChWallOutline.prototype.getPosts = function(sAction) {
    var $this = this;
    var oLoading = null;

    switch(sAction) {
		case 'page':
			oLoading = $('#wall-load-more .ch-btn');
			oLoading.ch_btn_loading();
			break;

		default:
			oLoading = $('#ch-wall-view-loading');
			oLoading.ch_loading();
			break;
    }

    jQuery.post(
        this._sActionsUrl + 'get_posts_outline/',
        this._getDefaultData(),
        function(oData) {
        	switch(sAction) {
        		case 'page':
        			if(oLoading)
                		oLoading.ch_btn_loading();

        			var oItems = $(oData.items);
        			$this.appendMasonry(oItems);
		            $('#ch-wall-outline .wall-load-more').replaceWith(oData.paginate);
        			break;

        		default:
        			if(oLoading)
                		oLoading.ch_loading();
        			break;
        	}
        },
        'json'
    );
};

ChWallOutline.prototype._getDefaultData = function () {
	var oDate = new Date();
	this._oRequestParams._t = oDate.getTime();
    return this._oRequestParams;
};
